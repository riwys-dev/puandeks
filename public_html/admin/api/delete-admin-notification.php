<?php
require_once('/home/puandeks.com/backend/config.php');
header('Content-Type: application/json');

$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    echo json_encode(['status' => 'error', 'message' => 'Geçersiz ID']);
    exit;
}

$stmt = $pdo->prepare("DELETE FROM admin_notifications WHERE id = ?");
$success = $stmt->execute([$id]);

if ($success) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Silinemedi']);
}
