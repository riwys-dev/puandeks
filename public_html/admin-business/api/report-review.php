<?php
session_start();

if (!isset($_SESSION['company_id'])) {
    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "Yetkisiz erişim"]);
    exit;
}

require_once('/home/puandeks.com/backend/config.php');

$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

if (!$data) {
    $data = $_POST;
}

$review_id = intval($data['review_id'] ?? 0);
$reason = trim($data['reason'] ?? "");
$company_id = $_SESSION['company_id'];

if (!$review_id || !$reason) {
    echo json_encode(["status" => "error", "message" => "Eksik veri"]);
    exit;
}

/* Review gerçekten bu şirkete ait mi kontrol */
$stmt = $pdo->prepare("SELECT id FROM reviews WHERE id = ? AND company_id = ?");
$stmt->execute([$review_id, $company_id]);

if (!$stmt->fetch()) {
    echo json_encode(["status" => "error", "message" => "Geçersiz inceleme"]);
    exit;
}

/* Daha önce şikayet edilmiş mi kontrol */
$check = $pdo->prepare("
    SELECT id FROM review_reports 
    WHERE review_id = ? AND reported_by_id = ? AND reported_by_role = 'business'
");
$check->execute([$review_id, $company_id]);

if ($check->fetch()) {
    echo json_encode(["status" => "error", "message" => "Bu yorumu zaten şikayet ettiniz."]);
    exit;
}

/* Şikayeti kaydet */
$insert = $pdo->prepare("
    INSERT INTO review_reports 
    (review_id, reported_by_id, reported_by_role, reason, status)
    VALUES (?, ?, 'business', ?, 'pending')
");
$insert->execute([$review_id, $company_id, $reason]);

/* Admin bildirimi oluştur */
$notif = $pdo->prepare("
    INSERT INTO admin_notifications (title, content)
    VALUES (?, ?)
");

$notif->execute([
    "Yeni İşletme Şikayeti",
    "Bir işletme bir yorumu şikayet etti."
]);

echo json_encode(["status" => "success"]);
