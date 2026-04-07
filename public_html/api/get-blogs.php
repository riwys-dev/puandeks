<?php
// get-blogs.php – Arama ve sayfalama ile blog listesi API'si
require_once('/home/puandeks.com/backend/config.php');

$search = $_GET['search'] ?? '';
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 6;
$offset = ($page - 1) * $limit;

$params = [];
$where = "WHERE status = 1";

// Arama filtresi
if (!empty($search)) {
  $where .= " AND (title LIKE :search OR content LIKE :search)";
  $params['search'] = "%$search%";
}

// Toplam kayıt sayısı
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM blog_posts $where");
$countStmt->execute($params);
$total = $countStmt->fetchColumn();

// Sayfalı veriyi çek
$stmt = $pdo->prepare("
  SELECT id, title, content, image, created_at
  FROM blog_posts
  $where
  ORDER BY created_at DESC
  LIMIT $limit OFFSET $offset
");
$stmt->execute($params);
$blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// JSON çıktısı
echo json_encode([
  'success' => true,
  'blogs' => $blogs,
  'total' => (int)$total
]);
