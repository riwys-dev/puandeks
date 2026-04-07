<?php
header('Content-Type: application/json; charset=utf-8');

require_once '/home/puandeks.com/backend/config.php';
require_once '/home/puandeks.com/backend/helpers/mailer.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Geçersiz istek.']);
    exit;
}

$email = strtolower(trim($_POST['email'] ?? ''));

if (empty($email)) {
    echo json_encode(['status' => 'error', 'message' => 'E-posta adresi gerekli.']);
    exit;
}

/* ==================================================
   SADECE EMAIL LOGIN_SOURCE OLAN HESAPLARI BUL
================================================== */

$stmt = $pdo->prepare("
    SELECT id, name, status, role
    FROM users
    WHERE email = :email
    AND login_source = 'email'
    LIMIT 1
");
$stmt->execute(['email' => $email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Bu e-posta adresi kayıtlı değil.'
    ]);
    exit;
}

if ($user['status'] !== 'active' || $user['role'] !== 'user') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Bu hesap şu anda şifre sıfırlama için uygun değil.'
    ]);
    exit;
}

/* ==================================================
   GEÇİCİ ŞİFRE OLUŞTUR
================================================== */

$temp_pass = bin2hex(random_bytes(4)); // 8 karakter
$hashed_pass = password_hash($temp_pass, PASSWORD_BCRYPT);

/* ==================================================
   ŞİFREYİ GÜNCELLE
================================================== */

$update = $pdo->prepare("
    UPDATE users 
    SET password = :password 
    WHERE id = :id
");

$update->execute([
    'password' => $hashed_pass,
    'id' => $user['id']
]);

/* ==================================================
   MAIL GÖNDER
================================================== */

$subject = "Puandeks - Geçici Şifreniz";

$body = "
<div style='font-family:Arial,sans-serif; color:#333;'>
  <h2>Merhaba {$user['name']},</h2>
  <p>Puandeks hesabınız için geçici şifreniz aşağıdadır:</p>
  <div style='background:#f5f5f5; border:1px solid #ddd; padding:10px 15px; font-size:16px; letter-spacing:1px; font-weight:bold; display:inline-block; border-radius:6px;'>
    {$temp_pass}
  </div>
  <p style='margin-top:15px;'>Bu şifreyle giriş yaptıktan sonra <b>Ayarlar</b> sayfasından yeni bir şifre belirleyebilirsiniz.</p>
  <p>Sevgiler,<br><b>Puandeks Ekibi</b></p>
</div>
";

$sent = sendMail($email, $subject, $body, $user['name']);

if ($sent) {
    echo json_encode([
        'status' => 'success',
        'message' => 'Yeni geçici şifreniz e-posta adresinize gönderildi.'
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'E-posta gönderiminde bir sorun oluştu. Lütfen tekrar deneyin.'
    ]);
}

exit;
?>