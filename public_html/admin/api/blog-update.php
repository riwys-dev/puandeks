<?php
require_once('/home/puandeks.com/backend/config.php');
header('Content-Type: application/json');

$id = $_POST['id'] ?? null;
$title = $_POST['title'] ?? '';
$content = $_POST['content'] ?? '';
$imagePath = null;

if (!$id || empty($title) || empty($content)) {
  echo json_encode(['success' => false, 'message' => 'Eksik veri.']);
  exit;
}

// Görsel yüklenmiş mi kontrolü
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
  $uploadDir = '/home/puandeks.com/public_html/admin/uploads/blog/';
  $fileName = uniqid('blog_') . '_' . basename($_FILES['image']['name']);
  $targetPath = $uploadDir . $fileName;

  $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
  if (!in_array($_FILES['image']['type'], $allowedTypes)) {
    echo json_encode(['success' => false, 'message' => 'Geçersiz görsel türü.']);
    exit;
  }

  if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
    $imagePath = '/admin/uploads/blog/' . $fileName;
  } else {
    echo json_encode(['success' => false, 'message' => 'Görsel yüklenemedi.']);
    exit;
  }
}

try {
  if ($imagePath) {
    $stmt = $pdo->prepare("UPDATE blog_posts SET title = ?, content = ?, image = ? WHERE id = ?");
    $stmt->execute([$title, $content, $imagePath, $id]);
  } else {
    $stmt = $pdo->prepare("UPDATE blog_posts SET title = ?, content = ? WHERE id = ?");
    $stmt->execute([$title, $content, $id]);
  }

  echo json_encode(['success' => true]);
} catch (Exception $e) {
  echo json_encode(['success' => false, 'message' => 'Veritabanı hatası.', 'error' => $e->getMessage()]);
}
