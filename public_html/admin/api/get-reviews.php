<?php
require_once('/home/puandeks.com/backend/config.php');
header('Content-Type: application/json');

$status = $_GET['status'] ?? '';
$page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int)$_GET['page'] : null;

$limit = 10;
$offset = $page ? ($page - 1) * $limit : null;

$statusMap = [
  'pending' => 0,
  'approved' => 1,
  'rejected' => 2
];

if (!isset($statusMap[$status])) {
  echo json_encode(['data' => [], 'total' => 0]);
  exit;
}

try {

  $statusValue = $statusMap[$status];

  /* TOTAL */
  $total = 0;
  if ($page) {
    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM reviews WHERE status = ?");
    $countStmt->execute([$statusValue]);
    $total = (int)$countStmt->fetchColumn();
  }

  /* MAIN QUERY */
  $sql = "
    SELECT 
      r.id, 
      r.comment, 
      r.rating, 
      r.created_at,
      c.name AS company_name,
      u.name AS user_name
    FROM reviews r
    LEFT JOIN companies c ON r.company_id = c.id
    LEFT JOIN users u ON r.user_id = u.id
    WHERE r.status = ?
    ORDER BY r.created_at DESC
  ";

  if ($page) $sql .= " LIMIT $limit OFFSET $offset";

  $stmt = $pdo->prepare($sql);
  $stmt->execute([$statusValue]);
  $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

  /* MEDIA MANUAL FETCH (DAHA STABİL) */
  foreach ($reviews as &$r) {

    $mediaStmt = $pdo->prepare("
      SELECT media_url AS url, media_type AS type 
      FROM review_media 
      WHERE review_id = ?
    ");
    $mediaStmt->execute([$r['id']]);
    $media = $mediaStmt->fetchAll(PDO::FETCH_ASSOC);

    $r['media'] = array_map(function($m) {
      return [
        'type' => $m['type'] ?? 'image',
        'url' => $m['url'] ?? null
      ];
    }, $media ?: []);
  }

  if ($page) {
    echo json_encode([
      'data' => $reviews,
      'total' => $total,
      'page' => $page,
      'limit' => $limit,
      'perPage' => $limit
    ]);
  } else {
    echo json_encode($reviews);
  }

} catch (Exception $e) {
  echo json_encode([
    'data' => [],
    'total' => 0,
    'error' => $e->getMessage()
  ]);
}