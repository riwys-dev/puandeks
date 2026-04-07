<?php
session_start();
require_once('/home/puandeks.com/backend/config.php');
header('Content-Type: application/json');

// [1] Giriş kontrolü
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'user') {
  echo json_encode(['success' => false, 'message' => 'Yetkisiz erişim']);
  exit;
}

$user_id = $_SESSION['user_id'];

// [2] POST verilerini al ve temizle
$phone = trim($_POST['phone'] ?? '');
$facebook = trim($_POST['facebook_url'] ?? '');
$google = trim($_POST['google_email'] ?? '');
$apple = trim($_POST['apple_email'] ?? '');

// [3] SQL güncelle
try {
  $stmt = $pdo->prepare("
    UPDATE users SET 
      phone = :phone,
      facebook_url = :facebook,
      google_email = :google,
      apple_email = :apple,
      phone_verified = 0,
      facebook_verified = 0,
      google_verified = 0,
      apple_verified = 0
    WHERE id = :id
  ");

  $stmt->execute([
    'phone' => $phone,
    'facebook' => $facebook,
    'google' => $google,
    'apple' => $apple,
    'id' => $user_id
  ]);

  echo json_encode(['success' => true, 'message' => 'Veriler güncellendi']);

} catch (Exception $e) {
  echo json_encode(['success' => false, 'message' => 'Sunucu hatası']);
}
