<?php
require_once('/home/puandeks.com/backend/config.php');

// ID kontrolü
$userId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$order = $_GET['order'] ?? 'rating';
$category = $_GET['category'] ?? '';
$rating = $_GET['rating'] ?? '';
$date = $_GET['date'] ?? '';

if ($userId <= 0) {
  echo json_encode(['status' => 'error', 'message' => 'Geçersiz kullanıcı ID.']);
  exit;
}

// Sıralama
$orderBy = 'r.rating DESC';
if ($date === 'asc') {
  $orderBy = 'r.created_at ASC';
} elseif ($date === 'desc') {
  $orderBy = 'r.created_at DESC';
}

// SQL
$sql = "
  SELECT 
    r.id, 
    r.title, 
    r.comment AS content, 
    r.rating, 
    r.created_at AS experience_date,
    r.company_id, 
    c.name AS company_name,
    u.name AS user_name,
    u.profile_image AS user_image,
    (SELECT COUNT(*) FROM reviews WHERE user_id = :uid AND status = 1) AS total_review_count
  FROM reviews r
  INNER JOIN companies c ON r.company_id = c.id
  INNER JOIN users u ON r.user_id = u.id
  WHERE r.user_id = :uid AND r.status = 1
";

// Filtreler
$params = ['uid' => $userId];

if (!empty($category)) {
  $sql .= " AND c.category_id = :catid";
  $params['catid'] = $category;
}

if (!empty($rating)) {
  $sql .= " AND r.rating = :rating";
  $params['rating'] = $rating;
}

$sql .= " ORDER BY $orderBy";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

// can_edit her zaman false
foreach ($reviews as &$r) {
  $r['can_edit'] = false;
}

echo json_encode([
  'status' => 'success',
  'reviews' => $reviews
]);
