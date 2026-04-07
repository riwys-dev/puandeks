<?php
require_once('/home/puandeks.com/backend/config.php');
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Oturum bulunamadı."]);
    exit;
}

$userId = $_SESSION['user_id'];
$phone = preg_replace('/[^0-9]/', '', $_POST['phone'] ?? '');
$prefix = '90'; // Türkiye sabit

if (strlen($phone) != 10 || substr($phone,0,1) != '5') {
    echo json_encode(["success" => false, "message" => "Geçerli bir telefon numarası giriniz."]);
    exit;
}


// Başka kullanıcıda aynı numara var mı?
$stmt = $pdo->prepare("
    SELECT id FROM users 
    WHERE phone = ? 
    AND phone_prefix = ? 
    AND id != ?
    LIMIT 1
");
$stmt->execute([$phone, $prefix, $userId]);

if ($stmt->fetch()) {
    echo json_encode([
        "success" => false,
        "message" => "Bu telefon numarası başka bir hesapta kayıtlı."
    ]);
    exit;
}

// Son OTP gönderim zamanı kontrolü (60 saniye rate limit)
$stmt = $pdo->prepare("
    SELECT otp_last_sent_at 
    FROM users 
    WHERE id = ?
");
$stmt->execute([$userId]);
$lastSent = $stmt->fetchColumn();

if ($lastSent && (time() - strtotime($lastSent) < 60)) {
    $remaining = 60 - (time() - strtotime($lastSent));
    echo json_encode([
        "success" => false,
        "message" => "Lütfen $remaining saniye sonra tekrar deneyin."
    ]);
    exit;
}

/* ===================================================
   KONTROL BİTTİ — DEVAM
   =================================================== */

$otp = random_int(100000, 999999);
$expiry = date('Y-m-d H:i:s', strtotime('+3 minutes'));

// DB güncelle
$stmt = $pdo->prepare("
    UPDATE users 
        SET pending_phone = ?, 
            pending_phone_prefix = ?, 
            phone_verified = 0,
            otp_code = ?, 
            otp_expiry = ?, 
            otp_last_sent_at = NOW()
        WHERE id = ?
");
$stmt->execute([$phone, $prefix, $otp, $expiry, $userId]);

// -------- NETGSM SMS --------
$message = "Puandeks telefon onay kodunuz: $otp";

$payload = json_encode([
    "msgheader" => NETGSM_ORIGINATOR,
    "appname"   => "puandeks",
    "msg"       => $message,
    "no"        => $phone
]);

$ch = curl_init(NETGSM_URL);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json"
]);

curl_setopt($ch, CURLOPT_USERPWD, NETGSM_USERNAME . ":" . NETGSM_PASSWORD);

$response = curl_exec($ch);

if ($response === false) {
    echo json_encode([
        "success" => false,
        "error"   => "Curl hata",
        "details" => curl_error($ch)
    ]);
    curl_close($ch);
    exit;
}

curl_close($ch);

echo json_encode([
    "success" => true,
    "netgsm_response" => $response
]);
exit;