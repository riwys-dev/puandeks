<?php
require_once('/home/puandeks.com/backend/config.php');
header('Content-Type: application/json');

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
  echo json_encode(['success' => false, 'message' => 'Geçersiz blog ID.']);
  exit;
}

try {
  $stmt = $pdo->prepare("DELETE FROM blog_posts WHERE id = ?");
  $stmt->execute([$id]);

  if ($stmt->rowCount() > 0) {
    echo json_encode(['success' => true]);
  } else {
    echo json_encode(['success' => false, 'message' => 'Blog bulunamadı.']);
  }
} catch (Exception $e) {
  echo json_encode(['success' => false, 'message' => 'Veritabanı hatası.', 'error' => $e->getMessage()]);
}
