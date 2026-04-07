
<?php
session_start();
header('Content-Type: application/json; charset=UTF-8');

if (!isset($_SESSION['company_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once('/home/puandeks.com/backend/config.php');

/* ================= CONFIG ================= */
$companyId = (int) $_SESSION['company_id'];
$maxSize   = 5 * 1024 * 1024; // 5MB

$uploadDir = '/home/puandeks.com/public_html/uploads/company/logo/';
$publicDir = 'https://puandeks.com/uploads/company/logo/';

/* ================= FILE CHECK ================= */
if (!isset($_FILES['logo']) || $_FILES['logo']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'File missing']);
    exit;
}

$file = $_FILES['logo'];

if ($file['size'] > $maxSize) {
    echo json_encode(['success' => false, 'message' => 'File too large']);
    exit;
}

/* ================= MIME CHECK ================= */
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime  = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

$allowed = ['image/png', 'image/jpeg', 'image/webp'];
if (!in_array($mime, $allowed)) {
    echo json_encode(['success' => false, 'message' => 'Invalid file type']);
    exit;
}

/* ================= FILE NAME ================= */
$newName = 'company_' . $companyId . '.webp';
$target  = $uploadDir . $newName;

/* ================= IMAGE PROCESS ================= */
$img = null;

if ($mime === 'image/png') {
    $img = imagecreatefrompng($file['tmp_name']);
} elseif ($mime === 'image/jpeg') {
    $img = imagecreatefromjpeg($file['tmp_name']);
} elseif ($mime === 'image/webp') {
    if (!move_uploaded_file($file['tmp_name'], $target)) {
        echo json_encode(['success' => false, 'message' => 'Upload failed']);
        exit;
    }
}

/* convert to webp if needed */
if ($img) {
    imagepalettetotruecolor($img);
    imagewebp($img, $target, 85);
    imagedestroy($img);
}

/* ================= DB UPDATE ================= */
$newLogoUrl = $publicDir . $newName;

$stmt = $conn->prepare("UPDATE companies SET logo = ? WHERE id = ?");
$stmt->execute([$newLogoUrl, $companyId]);

echo json_encode([
    'success' => true,
    'logo'    => $newLogoUrl
]);
