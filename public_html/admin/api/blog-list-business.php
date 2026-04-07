<?php
header('Content-Type: application/json');
require_once('/home/puandeks.com/backend/config.php');

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// *** DOĞRU TABLO ADI ***
$stmt = $pdo->prepare("
    SELECT id, title, image, created_at
    FROM business_blog_posts
    ORDER BY created_at DESC
    LIMIT :offset, :limit
");
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);
$stmt->execute();
$blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalStmt = $pdo->query("SELECT COUNT(*) FROM business_blog_posts");
$total = $totalStmt->fetchColumn();

echo json_encode([
    "success" => true,
    "blogs"   => $blogs,
    "total"   => $total
]);
