<?php
header('Content-Type: application/json');
require_once('/home/puandeks.com/backend/config.php');

if (!isset($_GET['company_id'])) {
    echo json_encode(['success' => false, 'message' => 'company_id parametresi eksik']);
    exit;
}

$company_id = intval($_GET['company_id']);

/*
  SADECE en güncel aboneliği al
*/
$sql = "
SELECT 
    cs.*, 
    p.name AS package_name
FROM company_subscriptions cs
LEFT JOIN packages p ON cs.package_id = p.id
WHERE cs.company_id = ?
ORDER BY cs.id DESC
LIMIT 1
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$company_id]);
$subscription = $stmt->fetch(PDO::FETCH_ASSOC);

if ($subscription) {

    // EXPIRED ise FREE kabul et
    if ($subscription['status'] === 'expired') {
        echo json_encode([
            'success' => true,
            'data' => null
        ]);
        exit;
    }

    // ACTIVE veya TRIAL
    if (in_array($subscription['status'], ['active','trial'])) {
        echo json_encode([
            'success' => true,
            'data' => $subscription
        ]);
        exit;
    }

}

// Hiç kayıt yoksa da FREE
echo json_encode([
    'success' => true,
    'data' => null
]);