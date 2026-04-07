<?php

require_once('/home/puandeks.com/backend/config.php');

// Lidio POST ile döner
$data = $_POST;

// debug için (ilk testte açabilirsin)
// file_put_contents('lidio_return_log.txt', json_encode($data));

// gerekli alanlar
$cardToken = $data['cardToken'] ?? null;
$customerId = $data['customerId'] ?? null;

// kontrol
if (!$cardToken) {
    die('cardToken gelmedi');
}

// company_id (şimdilik test için sabit)
// sonra session’dan alacağız
$company_id = 1;

// subscription_id (varsa al)
$subscription_id = null;

// package_id (test için)
$package_id = 1;

// DB kaydı
$stmt = $pdo->prepare("
    INSERT INTO subscription_payments 
    (company_id, subscription_id, package_id, card_token, status, payment_status)
    VALUES (?, ?, ?, ?, 'trial', 'trial')
");

$stmt->execute([
    $company_id,
    $subscription_id,
    $package_id,
    $cardToken
]);

// yönlendirme
header("Location: /payment-result?status=success");
exit;