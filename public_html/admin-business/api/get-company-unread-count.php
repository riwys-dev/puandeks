<?php
session_start();
require_once('/home/puandeks.com/backend/config.php');

header('Content-Type: application/json');

$company_id = $_SESSION['company_id'] ?? null;

if (!$company_id) {
    http_response_code(401);
    echo json_encode(['count' => 0]);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM company_notifications WHERE company_id = ? AND is_read = 0");
    $stmt->execute([$company_id]);
    $count = (int) $stmt->fetchColumn();

    echo json_encode(['count' => $count]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['count' => 0]);
}
