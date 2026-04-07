<?php
session_start();
header("Content-Type: application/json");

// Giriş kontrolü
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'user') {
  http_response_code(401);
  echo json_encode(["status" => "error", "message" => "Yetkisiz erişim."]);
  exit;
}

// JSON verisini al
$data = json_decode(file_get_contents("php://input"), true);
if (!$data) {
  http_response_code(400);
  echo json_encode(["status" => "error", "message" => "Geçersiz veri."]);
  exit;
}

// Güvenlik için sadece beklenen anahtarları al
$allowed = ['marketing', 'recommend', 'updates', 'features', 'feedback'];
$updates = [];
$params = [];

foreach ($allowed as $key) {
  if (isset($data[$key])) {
    $updates[] = "$key = ?";
    $params[] = $data[$key] ? 1 : 0;
  }
}

if (empty($updates)) {
  http_response_code(400);
  echo json_encode(["status" => "error", "message" => "Güncellenecek veri yok."]);
  exit;
}

// Veritabanı bağlantısı
require_once('/home/puandeks.com/backend/config.php');

// SQL güncelleme
$params[] = $_SESSION['user_id'];
$sql = "UPDATE users SET " . implode(", ", $updates) . " WHERE id = ?";

$stmt = $conn->prepare($sql);
$success = $stmt->execute($params);

if ($success) {
  echo json_encode(["status" => "success"]);
} else {
  http_response_code(500);
  echo json_encode(["status" => "error", "message" => "Güncelleme başarısız."]);
}
