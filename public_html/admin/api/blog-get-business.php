<?php
header('Content-Type: application/json');
require_once('/home/puandeks.com/backend/config.php');

$id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("SELECT * FROM business_blog_posts WHERE id = ?");
$stmt->execute([$id]);
$blog = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$blog) {
    echo json_encode(["success" => false, "message" => "Blog bulunamadı"]);
    exit;
}

echo json_encode([
    "success" => true,
    "blog" => $blog
]);
