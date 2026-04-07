<?php
require_once('/home/puandeks.com/backend/config.php');
header('Content-Type: application/json');

$title = $_POST['title'] ?? '';
$content = $_POST['content'] ?? '';
$status = isset($_POST['status']) ? (int)$_POST['status'] : 1;
$imagePath = null;

// Zorunlu alan kontrolü
if (empty($title) || empty($content)) {
  echo json_encode(['success' => false, 'message' => 'Başlık ve içerik zorunludur.']);
  exit;
}

// Görsel yükleme işlemi
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
  $uploadDir = '/home/puandeks.com/public_html/admin/uploads/blog/';
  $fileName = uniqid('blog_') . '_' . basename($_FILES['image']['name']);
  $targetPath = $uploadDir . $fileName;

  // MIME kontrolü yapılabilir (isteğe bağlı)
  $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
  if (!in_array($_FILES['image']['type'], $allowedTypes)) {
    echo json_encode(['success' => false, 'message' => 'Sadece JPEG, PNG veya WEBP yüklenebilir.']);
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
  $stmt = $pdo->prepare("INSERT INTO blog_posts (title, content, image, status) VALUES (?, ?, ?, ?)");
  $stmt->execute([$title, $content, $imagePath, $status]);

  echo json_encode(['success' => true, 'message' => 'Blog yazısı eklendi.']);
} catch (Exception $e) {
  echo json_encode(['success' => false, 'message' => 'Veritabanı hatası.', 'error' => $e->getMessage()]);
}
