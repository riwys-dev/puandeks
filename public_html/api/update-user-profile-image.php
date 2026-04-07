<?php
session_start();
require_once('/home/puandeks.com/backend/config.php');
header('Content-Type: application/json');

// === Login control
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
  echo json_encode(['success' => false, 'message' => 'Yetkisiz erişim.']);
  exit;
}

$user_id = $_SESSION['user_id'];

// === File control
if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
  echo json_encode(['success' => false, 'message' => 'Dosya yüklenemedi.']);
  exit;
}

// === uploads
$upload_dir = __DIR__ . '/../uploads/users/';
$web_path   = 'uploads/users/';

if (!is_dir($upload_dir)) {
  mkdir($upload_dir, 0755, true);
}

// === file names
$ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
$tmp_file = $_FILES['photo']['tmp_name'];

// === New Fil > (.webp)
$filename = 'user_' . $user_id . '_' . time() . '.webp';
$destination = $upload_dir . $filename;
$relative_path = $web_path . $filename;

// === Get old pic
$stmt = $pdo->prepare("SELECT profile_image FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$old_image = $stmt->fetchColumn();

// === change to WEBP
$imageResource = null;

switch ($ext) {
    case 'jpg':
    case 'jpeg':
        $imageResource = imagecreatefromjpeg($tmp_file);
        break;

    case 'png':
        $imageResource = imagecreatefrompng($tmp_file);
        break;

    case 'webp':
        // if WEBP 
        if (!move_uploaded_file($tmp_file, $destination)) {
            echo json_encode(['success' => false, 'message' => 'WEBP kaydedilemedi.']);
            exit;
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Desteklenmeyen dosya formatı.']);
        exit;
}

// JPG / PNG  > WEBP 
if ($ext !== 'webp') {
    if (!imagewebp($imageResource, $destination, 85)) {
        echo json_encode(['success' => false, 'message' => 'WEBP dönüşümü başarısız.']);
        exit;
    }
    imagedestroy($imageResource);
}

// === update db
$stmt = $pdo->prepare("UPDATE users SET profile_image = ? WHERE id = ?");
$stmt->execute([$relative_path, $user_id]);

// === delete old pic
if ($old_image && file_exists(__DIR__ . '/../' . $old_image)) {
    unlink(__DIR__ . '/../' . $old_image);
}

// === Session update
$_SESSION['profile_image'] = $relative_path;

// === Response
echo json_encode([
  'success' => true,
  'message' => 'Profil fotoğrafı güncellendi.',
  'image'   => $relative_path
]);
exit;
