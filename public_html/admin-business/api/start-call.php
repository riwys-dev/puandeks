<?php
session_start();
require_once('/home/puandeks.com/backend/config.php');

header('Content-Type: application/json');

if (!isset($_SESSION['company_id'])) {
    echo json_encode(["status" => "error", "message" => "Yetkisiz"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$reviewId = intval($data['review_id'] ?? 0);

if (!$reviewId) {
    echo json_encode(["status" => "error", "message" => "Review ID yok"]);
    exit;
}

$stmt = $pdo->prepare("SELECT user_id FROM reviews WHERE id = ?");
$stmt->execute([$reviewId]);
$review = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$review) {
    echo json_encode(["status" => "error", "message" => "Review bulunamadı"]);
    exit;
}

$stmt = $pdo->prepare("SELECT phone FROM users WHERE id = ?");
$stmt->execute([$review['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || !$user['phone']) {
    echo json_encode(["status" => "error", "message" => "Kullanıcı telefonu yok"]);
    exit;
}

$stmt = $pdo->prepare("SELECT phone FROM companies WHERE id = ?");
$stmt->execute([$_SESSION['company_id']]);
$company = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$company || !$company['phone']) {
    echo json_encode(["status" => "error", "message" => "İşletme telefonu yok"]);
    exit;
}

$caller = $company['phone'];
$called = $user['phone'];

$username = "2129091794";
$password = "M6.DKPA4";

$baseUrl = "http://crmsntrl.netgsm.com.tr:9111/2129091794/linkup";

$url = $baseUrl
    . "?username=" . urlencode($username)
    . "&password=" . urlencode($password)
    . "&caller=" . urlencode($caller)
    . "&called=" . urlencode($called)
    . "&ring_timeout=30"
    . "&crm_id=" . time()
    . "&wait_response=1"
    . "&originate_order=if"
    . "&trunk=2129091794";

$response = file_get_contents($url);

if ($response === false) {
    echo json_encode([
        "status" => "error",
        "message" => "NetGSM request failed"
    ]);
    exit;
}

echo json_encode([
    "status" => "success",
    "message" => "Arama başlatıldı"
]);