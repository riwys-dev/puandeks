<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(["success" => false]);
    exit;
}

require_once('/home/puandeks.com/backend/config.php');

$id = $_POST['id'] ?? null;

if (!$id) {
    echo json_encode(["success" => false]);
    exit;
}

try {

    $stmt = $pdo->prepare("DELETE FROM review_reports WHERE id = ?");
    $stmt->execute([$id]);

    echo json_encode(["success" => true]);

} catch (Exception $e) {

    echo json_encode(["success" => false]);
}
