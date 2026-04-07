<?php
require_once('/home/puandeks.com/backend/config.php');
header('Content-Type: application/json');

$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

try {
  // Toplam kayıt sayısı
  $totalStmt = $pdo->query("SELECT COUNT(*) FROM blog_posts");
  $total = (int) $totalStmt->fetchColumn();

  // Sayfalı blog verisi
  $stmt = $pdo->prepare("
    SELECT id, title, created_at 
    FROM blog_posts 
    ORDER BY created_at DESC 
    LIMIT :limit OFFSET :offset
  ");
  $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
  $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
  $stmt->execute();

  $blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);

  echo json_encode([
    'success' => true,
    'total' => $total,
    'blogs' => $blogs
  ]);
} catch (Exception $e) {
  echo json_encode([
    'success' => false,
    'message' => 'Veritabanı hatası.',
    'error' => $e->getMessage()
  ]);
}
