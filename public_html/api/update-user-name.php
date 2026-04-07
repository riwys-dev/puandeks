<?php
session_start();
require_once('/home/puandeks.com/backend/config.php');

header('Content-Type: application/json');

// === [1] Giriş kontrolü
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
  echo json_encode(['success' => false, 'message' => 'Yetkisiz erişim.']);
  exit;
}

$user_id = $_SESSION['user_id'];
$name = trim($_POST['name'] ?? '');
$surname = trim($_POST['surname'] ?? '');

// === [2] Boş alan kontrolü
if (empty($name) || empty($surname)) {
  echo json_encode(['success' => false, 'message' => 'Ad ve soyad boş olamaz.']);
  exit;
}

// === [3] Kullanıcının adı zaten var mı kontrol et
$stmt = $pdo->prepare("SELECT name, surname FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!empty($user['name']) && !empty($user['surname'])) {
  echo json_encode(['success' => false, 'message' => 'Ad ve soyad zaten kayıtlı.']);
  exit;
}

// === [4] Güncelleme işlemi
$stmt = $pdo->prepare("UPDATE users SET name = ?, surname = ? WHERE id = ?");
if ($stmt->execute([$name, $surname, $user_id])) {
  $_SESSION['name'] = $name;
  $_SESSION['surname'] = $surname;

  echo json_encode(['success' => true, 'message' => 'Ad ve soyad güncellendi.']);
} else {
  echo json_encode(['success' => false, 'message' => 'Veritabanı hatası.']);
}
