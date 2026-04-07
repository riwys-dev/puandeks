<?php

require_once('/home/puandeks.com/backend/config.php');

file_put_contents(
    '/home/puandeks.com/backend/logs/retry.log',
    "CRON START\n",
    FILE_APPEND
);

// zamanı gelmiş retry kayıtları çek
$stmt = $pdo->prepare("
    SELECT * FROM company_subscriptions 
    WHERE next_payment_date IS NOT NULL
    AND next_payment_date <= NOW()
    AND status != 'expired'
");

$stmt->execute();
$subscriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($subscriptions as $sub) {

    $subscriptionId = $sub['id'];

    // LOG
    file_put_contents(
        '/home/puandeks.com/backend/logs/retry.log',
        date("Y-m-d H:i:s") . " RETRY TRIGGER: ID = $subscriptionId\n",
        FILE_APPEND
    );

    // BURADA LIDIO API çağrısı olacak (şimdilik yok)

}