<?php
session_start();
require_once('/home/puandeks.com/backend/config.php');

header('Content-Type: application/json');

// Admin kontrolü
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Yetkisiz erişim.']);
    exit;
}

$id = intval($_POST['id'] ?? 0);

if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Geçersiz ID.']);
    exit;
}

try {

    $stmt = $conn->prepare("DELETE FROM review_reports WHERE id = ?");
    $stmt->execute([$id]);

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Sunucu hatası.']);
}
