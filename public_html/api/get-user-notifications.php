<?php
session_start();
require_once('/home/puandeks.com/backend/config.php');

header('Content-Type: application/json');

// Güvenlik: sadece giriş yapmış 'user' rolü erişebilir
if (!isset($_SESSION['user_id'])) {
  http_response_code(403);
  echo json_encode(['success' => false, 'message' => 'Yetkisiz erişim']);
  exit;
}

$userId = $_SESSION['user_id'];

// Bildirimleri çek
$stmt = $conn->prepare("
  SELECT id, title, content, created_at
  FROM notifications
  WHERE user_id = ?
  ORDER BY created_at DESC
");
$stmt->execute([$userId]);
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Hepsini okundu olarak işaretle (okunmamış olanları)
$conn->prepare("
  UPDATE notifications
  SET status = 'read'
  WHERE user_id = ? AND status = 'unread'
")->execute([$userId]);

// Başarılı dönüş
echo json_encode([
  'success' => true,
  'notifications' => $notifications
]);
