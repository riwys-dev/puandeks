<?php
session_start();
header("Content-Type: application/json");

// Giriş kontrolü
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'user') {
  http_response_code(401);
  echo json_encode(["status" => "error", "message" => "Yetkisiz erişim."]);
  exit;
}

// POST verilerini al
$oldPass = $_POST['old_pass'] ?? '';
$newPass = $_POST['new_pass'] ?? '';

if (!$oldPass || !$newPass) {
  http_response_code(400);
  echo json_encode(["status" => "error", "message" => "Eksik veri gönderildi."]);
  exit;
}

// Şifre kuralları
if (strlen($newPass) < 8 || !preg_match('/[A-Z]/', $newPass) || !preg_match('/[0-9]/', $newPass) || !preg_match('/[\W]/', $newPass)) {
  http_response_code(400);
  echo json_encode(["status" => "error", "message" => "Yeni şifre güvenli değil."]);
  exit;
}

require_once('/home/puandeks.com/backend/config.php');

// Mevcut kullanıcıyı al
$stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row || !password_verify($oldPass, $row['password'])) {
  http_response_code(403);
  echo json_encode(["status" => "error", "message" => "Eski şifre hatalı."]);
  exit;
}

// Yeni şifreyi hashle ve güncelle
$newHashed = password_hash($newPass, PASSWORD_BCRYPT);
$stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
$success = $stmt->execute([$newHashed, $_SESSION['user_id']]);

if ($success) {
  echo json_encode(["status" => "success"]);
} else {
  http_response_code(500);
  echo json_encode(["status" => "error", "message" => "Şifre güncellenemedi."]);
}
