<?php
require_once('/home/puandeks.com/backend/config.php');
header('Content-Type: application/json');

$id = $_POST['id'] ?? null;

if (!$id || !is_numeric($id)) {
  echo json_encode(['success' => false, 'message' => 'Geçersiz ID']);
  exit;
}

try {
  // Önce yorumu detaylarıyla al
  $stmt = $pdo->prepare("SELECT * FROM reviews WHERE id = :id");
  $stmt->execute(['id' => $id]);
  $review = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$review) {
    echo json_encode(['success' => false, 'message' => 'Yorum bulunamadı']);
    exit;
  }

  // Yorum silinmeden önce logla
  $logStmt = $pdo->prepare("
    INSERT INTO review_logs (review_id, company_id, user_id, comment, rating, action, action_by)
    VALUES (:review_id, :company_id, :user_id, :comment, :rating, 'deleted', 'admin')
  ");
  $logStmt->execute([
    'review_id' => $review['id'],
    'company_id' => $review['company_id'],
    'user_id' => $review['user_id'],
    'comment' => $review['comment'],
    'rating' => $review['rating']
  ]);

  // Yorum sil
  $deleteStmt = $pdo->prepare("DELETE FROM reviews WHERE id = :id");
  $success = $deleteStmt->execute(['id' => $id]);

  echo json_encode(['success' => $success]);
} catch (Exception $e) {
  echo json_encode(['success' => false, 'message' => 'Veritabanı hatası']);
}
