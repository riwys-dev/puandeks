<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
require_once('/home/puandeks.com/backend/config.php');
require_once('/home/puandeks.com/backend/helpers/mailer.php');

$name     = trim($_POST['name'] ?? '');
$surname  = trim($_POST['surname'] ?? '');
$email    = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');
$password_confirm = trim($_POST['password_confirm'] ?? '');

if (empty($name) || empty($surname) || empty($email) || empty($password) || empty($password_confirm)) {
    echo json_encode(["status" => "error", "message" => "Tüm alanlar zorunludur."]); exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["status" => "error", "message" => "Geçerli bir e-posta adresi girin."]); exit;
}
if ($password !== $password_confirm) {
    echo json_encode(["status" => "error", "message" => "Şifreler eşleşmiyor."]); exit;
}
if (!preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=\[\]{};:\\\\|,.<>\/?]).{8,}$/', $password)) {
    echo json_encode(["status" => "error", "message" => "Şifre en az 8 karakter, bir büyük harf, bir rakam ve bir özel karakter içermelidir."]); exit;
}

try {
    // E-posta var mı kontrol et
    $check = $conn->prepare("SELECT id FROM users WHERE email = :email");
    $check->execute([':email' => $email]);
    if ($check->rowCount() > 0) {
        echo json_encode(["status" => "error", "message" => "Bu e-posta adresiyle zaten bir hesap mevcut."]); exit;
    }

    // Kayıt işlemi
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $token = bin2hex(random_bytes(32));

    $stmt = $conn->prepare("
        INSERT INTO users (name, surname, email, password, role, verified, email_verified, verification_token, status, created_at)
        VALUES (:name, :surname, :email, :password, 'user', 0, 0, :token, 'active', NOW())
    ");
    $stmt->execute([
        ':name' => $name,
        ':surname' => $surname,
        ':email' => $email,
        ':password' => $hashed_password,
        ':token' => $token
    ]);

    // Doğrulama e-postası gönderimi
    $verification_link = "https://puandeks.com/verify?token=" . $token;
    $subject = "Puandeks Hesabınızı Doğrulayın";
    $message = "
        <div style='font-family:Arial,sans-serif;font-size:15px;line-height:1.6;'>
            <h2 style='color:#05462F;'>Puandeks'e Hoş Geldiniz, $name!</h2>
            <p>Hesabınızı doğrulamak için aşağıdaki bağlantıya tıklayın:</p>
            <p><a href='$verification_link' style='background:#05462F;color:#fff;padding:10px 18px;text-decoration:none;border-radius:5px;'>E-postamı Doğrula</a></p>
            <p>Bağlantı çalışmazsa şu adresi tarayıcınıza yapıştırın:</p>
            <p style='color:#555;'>$verification_link</p>
            <hr>
            <p style='font-size:13px;color:#777;'>Bu e-posta Puandeks sistemi tarafından otomatik gönderilmiştir. Lütfen yanıtlamayın.</p>
        </div>
    ";

    // Mail gönder
    if (sendMail($email, $subject, $message)) {
        echo json_encode(["status" => "success", "message" => "Kayıt başarılı! E-posta adresinize doğrulama bağlantısı gönderildi."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Kayıt tamamlandı fakat e-posta gönderilemedi. Lütfen daha sonra tekrar deneyin."]);
    }

} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Veritabanı hatası: " . $e->getMessage()]);
}
?>
