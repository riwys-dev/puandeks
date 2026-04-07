<?php
session_start();
header('Content-Type: application/json');

require_once('/home/puandeks.com/backend/config.php');

// Yalnızca POST isteklerine izin ver
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode([
    'success' => false,
    'message' => 'Sadece POST isteklerine izin verilir.'
  ]);
  exit;
}

// Giriş kontrolü
if (!isset($_SESSION['user_id'])) {
  http_response_code(401);
  echo json_encode([
    'success' => false,
    'message' => 'Yetkisiz erişim.'
  ]);
  exit;
}

// JSON'dan ID verisini al
$data = json_decode(file_get_contents("php://input"), true);
$notificationId = isset($data['id']) ? (int)$data['id'] : 0;

if ($notificationId <= 0) {
  http_response_code(400);
  echo json_encode([
    'success' => false,
    'message' => 'Geçersiz bildirim ID.'
  ]);
  exit;
}

try {

  // Kullanıcıya ait bildirimi sil
  $stmt = $pdo->prepare("
    DELETE FROM notifications 
    WHERE id = ? AND user_id = ?
  ");

  $stmt->execute([$notificationId, $_SESSION['user_id']]);

  if ($stmt->rowCount() > 0) {
    echo json_encode(['success' => true]);
  } else {
    http_response_code(404);
    echo json_encode([
      'success' => false,
      'message' => 'Bildirim bulunamadı ya da silinemedi.'
    ]);
  }

} catch (PDOException $e) {
  http_response_code(500);
  echo json_encode([
    'success' => false,
    'message' => 'Veritabanı hatası.'
  ]);
}
