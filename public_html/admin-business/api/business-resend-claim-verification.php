<?php
session_start();

require_once('/home/puandeks.com/backend/config.php');
require_once('/home/puandeks.com/backend/helpers/mailer.php');

header('Content-Type: application/json; charset=utf-8');

try {

    /* ============================================================
       1) EMAIL AL
    ============================================================ */
    $inputJSON = file_get_contents("php://input");
    $data = json_decode($inputJSON, true);

    $email = $data['email'] ?? null;

    if (!$email) {
        echo json_encode([
            'success' => false,
            'message' => 'E-posta alınamadı.'
        ]);
        exit;
    }

    /* ============================================================
       2) İŞLETMENİN VARLIĞINI DOĞRULA
    ============================================================ */
    $stmt = $pdo->prepare("
        SELECT id, owner_name, name AS business_name
        FROM companies
        WHERE email = ?
        LIMIT 1
    ");
    $stmt->execute([$email]);
    $company = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$company) {
        echo json_encode([
            'success' => false,
            'message' => 'İşletme kaydı bulunamadı.'
        ]);
        exit;
    }

    /* ============================================================
       3) YENİ TOKEN OLUŞTUR
    ============================================================ */
    $newToken = bin2hex(random_bytes(32));

    $upd = $pdo->prepare("
        UPDATE companies 
        SET verification_token = ?, email_verified = 0
        WHERE id = ?
    ");
    $upd->execute([$newToken, $company['id']]);

    /* ============================================================
       4) DOĞRULAMA LİNKİ
    ============================================================ */
    $verifyLink = "https://business.puandeks.com/api/business-claim-verify.php?token=" . urlencode($newToken);

    /* ============================================================
       5) MAİL
    ============================================================ */
    $subject = "İşletme Sahiplenme Doğrulaması – Puandeks";

    $message = "
        <h2>İşletme Sahiplenme Doğrulaması</h2>
        <p>Merhaba <b>{$company['owner_name']}</b>,</p>
        <p><b>{$company['business_name']}</b> işletmesini sahiplenme işlemini doğrulamak için aşağıdaki bağlantıya tıklayın:</p>

        <p>
            <a href='{$verifyLink}' 
               style='padding:10px 18px;background:#0C7C59;color:#fff;border-radius:5px;text-decoration:none;'>
               Sahiplenmeyi Doğrula
            </a>
        </p>
    ";

    sendMail($email, $subject, $message);

    /* ============================================================
       6) RESPONSE
    ============================================================ */
    echo json_encode([
        'success' => true,
        'message' => 'Doğrulama e-postası tekrar gönderildi.'
    ]);
    exit;

} catch (Exception $e) {

    echo json_encode([
        'success' => false,
        'message' => 'Hata: ' . $e->getMessage()
    ]);
    exit;
}
