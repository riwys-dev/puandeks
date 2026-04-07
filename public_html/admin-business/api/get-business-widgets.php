<?php
session_start();
header('Content-Type: application/json');

require_once('/home/puandeks.com/backend/config.php');

if (!isset($_SESSION['company_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Yetkisiz erişim.']);
    exit;
}

$company_id = $_SESSION['company_id'];

try {
    $stmt = $pdo->prepare("SELECT * FROM widgets WHERE company_id = ?");
    $stmt->execute([$company_id]);
    $widgets = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['status' => 'success', 'widgets' => $widgets]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Veriler alınamadı.']);
}
