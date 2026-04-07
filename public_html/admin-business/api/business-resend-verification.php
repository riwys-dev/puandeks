<?php
session_start();

require_once('/home/puandeks.com/backend/config.php');
require_once('/home/puandeks.com/backend/helpers/mailer.php');

header('Content-Type: application/json; charset=utf-8');

try {
    // SESSION'da e-posta yoksa işlem yapma
    if (!isset($_SESSION['pending_business_email'])) {
        throw new Exception("Bekleyen bir işletme doğrulama isteği bulunamadı.");
    }

    $email = $_SESSION['pending_business_email'];

    // İşletmeyi bul
    $stmt = $pdo->prepare("SELECT id, owner_name, verification_token FROM companies WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $business = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$business) {
        throw new Exception("İşletme kaydı bulunamadı.");
    }

    // Yeni token üret
    $newToken = bin2hex(random_bytes(32));

    // DB güncelle
    $upd = $pdo->prepare("UPDATE companies SET verification_token = ?, email_verified = 0 WHERE id = ?");
    $upd->execute([$newToken, $business['id']]);

    // Doğrulama linki
    $verifyLink = "https://business.puandeks.com/business-register-verify?token=" . urlencode($newToken);

    // Mail oluştur
    $subject = "İşletme Hesabınızı Doğrulayın – Puandeks";
    $message = "
        <h2>Doğrulama E-postası Yeniden Gönderildi</h2>
        <p>Merhaba <b>{$business['owner_name']}</b>,</p>
        <p>Puandeks işletme hesabınızı doğrulamak için aşağıdaki bağlantıya tıklayın:</p>
        <p><a href='{$verifyLink}' style='padding:10px 18px; background:#0044cc; color:#fff; border-radius:5px; text-decoration:none;'>Hesabımı Doğrula</a></p>
    ";

    // Mail gönder
    sendMail($email, $subject, $message);

    echo json_encode([
        'success' => true,
        'message' => 'Doğrulama e-postası tekrar gönderildi.'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
