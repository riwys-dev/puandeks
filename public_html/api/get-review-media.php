<?php
require_once('/home/puandeks.com/backend/config.php');
header('Content-Type: application/json');

$review_id = isset($_GET['review_id']) ? (int)$_GET['review_id'] : 0;

if (!$review_id) {
  echo json_encode([]);
  exit;
}

$stmt = $pdo->prepare("
  SELECT media_url AS url, media_type AS type
  FROM review_media
  WHERE review_id = ?
");
$stmt->execute([$review_id]);

$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($data);