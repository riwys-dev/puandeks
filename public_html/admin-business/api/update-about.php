<?php
session_start();
require_once('/home/puandeks.com/backend/config.php');
header('Content-Type: application/json');

if (!isset($_SESSION['company_id'])) {
  echo json_encode(['success' => false, 'message' => 'Oturum bulunamadı']);
  exit;
}

$company_id = (int)$_SESSION['company_id'];
$about = trim($_POST['about'] ?? '');

if ($about === '') {
  echo json_encode(['success' => false, 'message' => 'Metin boş olamaz']);
  exit;
}

// Word Limit (120)
$wordCount = str_word_count(strip_tags($about));
if ($wordCount > 120) {
  echo json_encode(['success' => false, 'message' => 'Maksimum 120 kelime yazabilirsiniz.']);
  exit;
}

try {
  $stmt = $conn->prepare("UPDATE companies SET about = ? WHERE id = ?");
  $stmt->execute([$about, $company_id]);
  echo json_encode(['success' => true]);
} catch (Exception $e) {
  echo json_encode(['success' => false, 'message' => 'Güncelleme başarısız']);
}
