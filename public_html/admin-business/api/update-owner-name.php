<?php
session_start();
header('Content-Type: application/json; charset=UTF-8');

if (!isset($_SESSION['company_id'])) {
  echo json_encode([
    'status' => 'error',
    'message' => 'Oturum bulunamadı.'
  ]);
  exit;
}

require_once('/home/puandeks.com/backend/config.php');

$data = json_decode(file_get_contents("php://input"), true);
$owner_name = trim($data['owner_name'] ?? '');

if ($owner_name === '') {
  echo json_encode([
    'status' => 'error',
    'message' => 'Yetkili adı zorunludur.'
  ]);
  exit;
}

try {
  $stmt = $pdo->prepare("
    UPDATE companies 
    SET owner_name = ?
    WHERE id = ?
  ");
  $stmt->execute([$owner_name, $_SESSION['company_id']]);

  echo json_encode([
    'status' => 'success',
    'message' => 'Yetkili adı güncellendi.'
  ]);

} catch (Exception $e) {
  echo json_encode([
    'status' => 'error',
    'message' => 'Güncelleme sırasında hata oluştu.'
  ]);
}
