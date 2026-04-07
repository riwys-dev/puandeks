<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['company_id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Oturum bulunamadı'
    ]);
    exit;
}

require_once('/home/puandeks.com/backend/config.php');
require_once('/home/puandeks.com/backend/helpers/mailer.php');

// JSON body al
$input = json_decode(file_get_contents("php://input"), true);
$newEmail = trim($input['email'] ?? '');

$companyId = $_SESSION['company_id'];

if ($newEmail === '') {
    echo json_encode([
        'status' => 'error',
        'message' => 'E-posta alanı zorunludur'
    ]);
    exit;
}

// Geçerli e-posta kontrol
if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Geçerli bir e-posta giriniz'
    ]);
    exit;
}

// Kurumsal e-posta kontrolü
$blockedDomains = [
    'gmail.com',
    'hotmail.com',
    'outlook.com',
    'yahoo.com',
    'icloud.com',
    'yandex.com'
];

$domain = strtolower(substr(strrchr($newEmail, "@"), 1));

if (in_array($domain, $blockedDomains)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Lütfen kurumsal bir e-posta adresi giriniz'
    ]);
    exit;
}

// Aynı e-posta kontrolü
$stmt = $pdo->prepare("SELECT email FROM companies WHERE id = ?");
$stmt->execute([$companyId]);
$currentEmail = $stmt->fetchColumn();

if ($currentEmail === $newEmail) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Bu e-posta zaten kullanımda'
    ]);
    exit;
}

// Token üret
$token = bin2hex(random_bytes(32));

try {
    // E-postayı ve tokenı güncelle
    $stmt = $pdo->prepare("
        UPDATE companies 
        SET email = ?, 
            email_verified = 0,
            verification_token = ?
        WHERE id = ?
    ");
    $stmt->execute([$newEmail, $token, $companyId]);

    // Doğrulama linki
    $verifyLink = "https://business.puandeks.com/verify-update-email?token={$token}";

    // Mail içeriği
    $subject = "E-posta Adresinizi Doğrulayın – Puandeks";
    $html = "
        <div style='font-family:Arial; font-size:15px; color:#333;'>
            <p>Merhaba,</p>
            <p>Puandeks işletme hesabınız için yeni e-posta adresi tanımladınız.</p>
            <p>E-postanızı doğrulamak için aşağıdaki butona tıklayın:</p>
            <p>
                <a href='{$verifyLink}'
                   style='display:inline-block;padding:10px 16px;
                          background:#0C7C59;color:#fff;
                          text-decoration:none;border-radius:6px;'>
                    E-postayı Doğrula
                </a>
            </p>
            <p>Eğer bu işlemi siz yapmadıysanız, bu e-postayı dikkate almayın.</p>
            <p>Puandeks Ekibi</p>
        </div>
    ";

    sendMail($newEmail, $subject, $html);

    echo json_encode([
        'status' => 'success',
        'message' => 'Doğrulama e-postası gönderildi'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Bir hata oluştu'
    ]);
}
