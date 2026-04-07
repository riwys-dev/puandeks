<?php
session_start();
require_once('/home/puandeks.com/backend/config.php');

header('Content-Type: application/json');

if (!isset($_SESSION['company_id'])) {
    echo json_encode([
        "success" => false,
        "message" => "Oturum bulunamadı"
    ]);
    exit;
}

$company_id = (int)$_SESSION['company_id'];

if (!isset($_POST['code'])) {
    echo json_encode([
        "success" => false,
        "message" => "Kod gönderilmedi"
    ]);
    exit;
}

$input_code = trim($_POST['code']);

if (!preg_match('/^\d{6}$/', $input_code)) {
    echo json_encode([
        "success" => false,
        "message" => "Geçersiz kod formatı"
    ]);
    exit;
}

/* -------------------------------------------------
   DB’den OTP çek
------------------------------------------------- */
$stmt = $pdo->prepare("
    SELECT otp_code, otp_expiry, phone_verified
    FROM companies
    WHERE id = ?
");
$stmt->execute([$company_id]);
$company = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$company) {
    echo json_encode([
        "success" => false,
        "message" => "İşletme bulunamadı"
    ]);
    exit;
}

if ($company['phone_verified'] == 1) {
    echo json_encode([
        "success" => false,
        "message" => "Telefon zaten doğrulanmış"
    ]);
    exit;
}

if (empty($company['otp_code']) || empty($company['otp_expiry'])) {
    echo json_encode([
        "success" => false,
        "message" => "Aktif doğrulama kodu yok"
    ]);
    exit;
}

/* -------------------------------------------------
   Süre kontrolü
------------------------------------------------- */
if (strtotime($company['otp_expiry']) < time()) {
    echo json_encode([
        "success" => false,
        "message" => "Kodun süresi dolmuş"
    ]);
    exit;
}

/* -------------------------------------------------
   Kod kontrolü
------------------------------------------------- */
if ($company['otp_code'] != $input_code) {
    echo json_encode([
        "success" => false,
        "message" => "Kod hatalı"
    ]);
    exit;
}

/* -------------------------------------------------
   Doğrulama başarılı
------------------------------------------------- */
$update = $pdo->prepare("
    UPDATE companies
    SET phone_verified = 1,
        otp_code = NULL,
        otp_expiry = NULL,
        otp_last_sent_at = NULL
    WHERE id = ?
");
$update->execute([$company_id]);

echo json_encode([
    "success" => true
]);