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

/* -------------------------------------------------
   İşletme bilgilerini çek
------------------------------------------------- */
$stmt = $pdo->prepare("
    SELECT phone, phone_prefix, phone_verified, otp_last_sent_at
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

if (empty($company['phone']) || empty($company['phone_prefix'])) {
    echo json_encode([
        "success" => false,
        "message" => "Telefon bilgisi eksik"
    ]);
    exit;
}

/* -------------------------------------------------
   Rate limit (60 saniye)
------------------------------------------------- */
if (!empty($company['otp_last_sent_at'])) {
    $last_sent = strtotime($company['otp_last_sent_at']);
    if (time() - $last_sent < 60) {
        echo json_encode([
            "success" => false,
            "message" => "Yeni kod için 60 saniye bekleyin"
        ]);
        exit;
    }
}

/* -------------------------------------------------
   OTP üret
------------------------------------------------- */
$otp_code = random_int(100000, 999999);
$otp_expiry = date("Y-m-d H:i:s", time() + 180); // 3 dakika

/* -------------------------------------------------
   DB'ye kaydet
------------------------------------------------- */
$update = $pdo->prepare("
    UPDATE companies
    SET otp_code = ?,
        otp_expiry = ?,
        otp_last_sent_at = NOW()
    WHERE id = ?
");
$update->execute([$otp_code, $otp_expiry, $company_id]);

/* -------------------------------------------------
   NETGSM SMS GÖNDERİM
------------------------------------------------- */

$full_number = $company['phone_prefix'] . $company['phone'];

$message = "Puandeks doğrulama kodunuz: $otp_code. Kod 3 dakika geçerlidir.";

/* BURAYA NETGSM API ÇAĞRISI GELECEK */

$netgsm_user = "NETGSM_USER";
$netgsm_pass = "NETGSM_PASS";
$netgsm_header = "PUANDEKS";

$post_data = [
    'usercode' => $netgsm_user,
    'password' => $netgsm_pass,
    'gsmno'    => $full_number,
    'message'  => $message,
    'msgheader'=> $netgsm_header,
];

$ch = curl_init("https://api.netgsm.com.tr/sms/send/get/");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

/* Basit kontrol */
if (!$response) {
    echo json_encode([
        "success" => false,
        "message" => "SMS gönderilemedi"
    ]);
    exit;
}

echo json_encode([
    "success" => true
]);