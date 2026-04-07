<?php
header('Content-Type: application/json');

require_once('/home/puandeks.com/backend/config.php');
require_once('/home/puandeks.com/backend/helpers/mailer.php');

// POST kontrolü
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Geçersiz istek.']);
    exit;
}

// Email kontrol
$email = trim($_POST['email'] ?? '');
if ($email === '') {
    echo json_encode(['status' => 'error', 'message' => 'E-posta gerekli.']);
    exit;
}

// Email var mı?
$stmt = $pdo->prepare("SELECT id FROM companies WHERE email = ? LIMIT 1");
$stmt->execute([$email]);
$company = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$company) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Bu e-posta ile kayıtlı bir işletme bulunamadı.'
    ]);
    exit;
}

// ---- GEÇİCİ ŞİFRE ----
$tempPassword = bin2hex(random_bytes(4)); // 8 karakter
$hashed = password_hash($tempPassword, PASSWORD_BCRYPT);

// DB güncelle
$update = $pdo->prepare("UPDATE companies SET password = ? WHERE id = ?");
$update->execute([$hashed, $company['id']]);

// ---- MAİL GÖNDER ----
$subject = "Yeni Geçici Şifreniz - Puandeks";
$message = "
    Merhaba,<br><br>
    Puandeks işletme hesabınız için geçici şifreniz:<br><br>
    <strong style='font-size:18px;'>$tempPassword</strong><br><br>
    Lütfen giriş yaptıktan sonra şifrenizi değiştirin.<br><br>
    <a href='https://business.puandeks.com/login'>Giriş yapmak için tıklayın</a><br><br>
    Puandeks Ekibi
";

$sent = sendMail($email, $subject, $message);

if ($sent) {
    echo json_encode([
        'status' => 'success',
        'message' => 'Geçici şifre e-posta adresinize gönderildi.'
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'E-posta gönderilemedi. Lütfen tekrar deneyin.'
    ]);
}
?>
