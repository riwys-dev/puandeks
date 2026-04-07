<?php

require_once('/home/puandeks.com/backend/config.php');

// RAW DATA
$input = file_get_contents("php://input");

// LOG (ilk test için)
file_put_contents(
    '/home/puandeks.com/backend/logs/lidio-callback.log',
    date("Y-m-d H:i:s") . "\n" . $input . "\n\n",
    FILE_APPEND
);

// JSON parse
$data = json_decode($input, true);

if (!$data) {
    http_response_code(400);
    echo "Invalid JSON";
    exit;
}

// HASH KONTROL (Lidio)
$merchantKey = '38f48f4b-2a58-4858-a130-2dd1fa11b58d';
$apiPassword = 'DgmNgq4QcxiHq6nvfEVq';

// Lidio doc'a göre parametreleri birleştirmen gerekiyor
// (şimdilik basic kontrol bırakıyoruz)
$incomingHash = $data['parameterhash'] ?? '';

$rawString = $merchantKey . $apiPassword;
$generatedHash = hash('sha256', $rawString);

// Şimdilik hash check'i logla (blocklama yapma)
file_put_contents(
    '/home/puandeks.com/backend/logs/lidio-callback.log',
    "HASH CHECK:\nIncoming: $incomingHash\nGenerated: $generatedHash\n\n",
    FILE_APPEND
);

// Ödeme durumu
$status = $data['status'] ?? 'unknown';
$orderId = $data['orderId'] ?? null;

if ($status === 'success' && $orderId) {

// SUCCESS
$stmt = $pdo->prepare("
    UPDATE company_subscriptions 
    SET status = 'active',
        last_payment_date = NOW(),
        start_date = start_date,
        end_date = CASE 
            WHEN package_type = 'monthly' THEN DATE_ADD(end_date, INTERVAL 1 MONTH)
            WHEN package_type = 'yearly' THEN DATE_ADD(end_date, INTERVAL 1 YEAR)
            ELSE end_date
        END
    WHERE id = ?
    AND (
        last_payment_date IS NULL 
        OR last_payment_date < DATE_SUB(NOW(), INTERVAL 1 DAY)
    )
");
    $stmt->execute([$orderId]);

}

// FAILED
if ($status === 'failed' && $orderId) {

    $stmt = $pdo->prepare("
        UPDATE company_subscriptions 
        SET retry_count = retry_count + 1,
            next_payment_date = DATE_ADD(NOW(), INTERVAL 1 DAY),
            status = CASE 
                WHEN retry_count + 1 >= 3 THEN 'expired'
                ELSE status
            END
        WHERE id = ?
    ");

    $stmt->execute([$orderId]);

}

// LOG status
file_put_contents(
    '/home/puandeks.com/backend/logs/lidio-callback.log',
    "STATUS: $status\n----------------------\n",
    FILE_APPEND
);

// Şimdilik sadece OK dön
http_response_code(200);
echo "OK";

