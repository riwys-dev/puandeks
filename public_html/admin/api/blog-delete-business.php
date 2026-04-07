<?php
header('Content-Type: application/json');
require_once('/home/puandeks.com/backend/config.php');

$id = $_GET['id'] ?? 0;

if (!$id) {
    echo json_encode(["success" => false, "message" => "ID eksik."]);
    exit;
}

$stmt = $pdo->prepare("DELETE FROM business_blog_posts WHERE id = ?");
$ok = $stmt->execute([$id]);

echo json_encode([
    "success" => $ok,
    "message" => $ok ? "Blog silindi" : "Silinemedi"
]);
