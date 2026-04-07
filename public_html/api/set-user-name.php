<?php
session_start();
require_once('/home/puandeks.com/backend/config.php');
header('Content-Type: application/json');

// === [1] Giriş kontrolü
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
  echo json_encode(['status' => 'error', 'message' => 'Yetkisiz erişim.']);
  exit;
}

$user_id = $_SESSION['user_id'];

// === [2] POST verilerini al
$name    = isset($_POST['name']) ? trim($_POST['name']) : '';
$surname = isset($_POST['surname']) ? trim($_POST['surname']) : '';

// === [3] Alanlar boşsa hata ver
if ($name === '' || $surname === '') {
  echo json_encode(['status' => 'error', 'message' => 'Lütfen ad ve soyad girin.']);
  exit;
}

// === [4] Daha önce kayıtlı mı kontrol et
$stmt = $pdo->prepare("SELECT name, surname FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!empty($user['name']) || !empty($user['surname'])) {
  echo json_encode(['status' => 'error', 'message' => 'Ad soyad zaten kayıtlı, değiştirilemez.']);
  exit;
}

// === [5] Güncelleme işlemi
$stmt = $pdo->prepare("UPDATE users SET name = ?, surname = ? WHERE id = ?");
if ($stmt->execute([$name, $surname, $user_id])) {
  echo json_encode(['status' => 'success', 'message' => 'Ad soyad başarıyla kaydedildi.']);
} else {
  echo json_encode(['status' => 'error', 'message' => 'Veritabanı hatası.']);
}
