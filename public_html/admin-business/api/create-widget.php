<?php
session_start();
header('Content-Type: application/json');

require_once('/home/puandeks.com/backend/config.php');

if (!isset($_SESSION['company_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Oturum bulunamadı.']);
    exit;
}

$company_id = $_SESSION['company_id'];
$title = $_POST['title'] ?? '';
$theme = $_POST['theme'] ?? 'light';
$status = $_POST['status'] ?? 1;
$color = $_POST['color'] ?? '#000000';
$size = $_POST['size'] ?? 'medium';
$language = $_POST['language'] ?? 'tr';

if (!$title) {
    echo json_encode(['status' => 'error', 'message' => 'Başlık zorunludur.']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO widgets (company_id, title, theme, status, color, size, language, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->execute([$company_id, $title, $theme, $status, $color, $size, $language]);

    echo json_encode(['status' => 'success']);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Veritabanı hatası.']);
}
