<?php

session_start();
require_once('/home/puandeks.com/backend/config.php');

header('Content-Type: application/json');

// parametreler
$plan = $_GET['plan'] ?? null;
$period = $_GET['period'] ?? 'monthly';

if (!$plan) {
    echo json_encode(["success" => false, "message" => "Plan yok"]);
    exit;
}

// company
$company_id = $_SESSION['company_id'] ?? null;

if (!$company_id) {
    echo json_encode(["success" => false, "message" => "Company yok"]);
    exit;
}

// paket çek
$stmt = $pdo->prepare("SELECT * FROM packages WHERE slug = ?");
$stmt->execute([$plan]);
$package = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$package) {
    echo json_encode(["success" => false, "message" => "Paket yok"]);
    exit;
}

// 1 TL provizyon
$amount = 1;

// order id
$orderId = "LIDIO-" . time() . "-" . $company_id;

// LIDIO URL
$lidioUrl = "https://api.lidio.com/StartHostedAccountManagement";

// payload
$payload = [
    "merchantCode" => "PUANDEKS",
    "orderId" => $orderId,
    "amount" => $amount,
    "currency" => "TRY",

    "returnUrl" => "https://business.puandeks.com/api/lidio-return.php",
    "cancelUrl" => "https://business.puandeks.com/payment-result?status=fail",

    "customer" => [
        "customerId" => (string)$company_id,
        "email" => "test@test.com" // sonra gerçek email koyacağız
    ]
];

// CURL
$ch = curl_init($lidioUrl);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: MxS2S MzhmNDhmNGItMmE1OC00ODU4LWExMzAtMmRkMWZhMTFiNThkOkRnbU5ncTRRY3hpSHE2bnZmRVZx"
]);

$response = curl_exec($ch);

if ($response === false) {
    echo json_encode([
        "success" => false,
        "message" => curl_error($ch)
    ]);
    exit;
}

curl_close($ch);
echo json_encode([
  "success" => false,
  "raw" => $response
]);
exit;

// response parse (Lidio bazen string döner)
$data = json_decode($response, true);

// redirect url yakala (varsayımsal alanlar)
$paymentUrl = $data['redirectUrl'] ?? $data['paymentUrl'] ?? null;

if (!$paymentUrl) {
    echo json_encode([
        "success" => false,
        "message" => "paymentUrl bulunamadı",
        "raw" => $response
    ]);
    exit;
}

// FRONTEND’E DÖN
echo json_encode([
    "success" => true,
    "paymentUrl" => $paymentUrl
]);