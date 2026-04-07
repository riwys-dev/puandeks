<?php
session_start();
require_once('/home/puandeks.com/backend/config.php');
header('Content-Type: application/json');

if (!isset($_SESSION['company_id'])) {
  echo json_encode(['success' => false, 'message' => 'Oturum bulunamadı']);
  exit;
}

$company_id  = (int)$_SESSION['company_id'];
$category_id = isset($_POST['category_id']) ? (int)$_POST['category_id'] : 0;

if ($category_id <= 0) {
  echo json_encode(['success' => false, 'message' => 'Kategori seçilmedi']);
  exit;
}

try {
  $stmt = $conn->prepare("UPDATE companies SET category_id = ? WHERE id = ?");
  $stmt->execute([$category_id, $company_id]);

  echo json_encode(['success' => true]);
} catch (Exception $e) {
  echo json_encode(['success' => false, 'message' => 'Kategori güncellenemedi']);
}
