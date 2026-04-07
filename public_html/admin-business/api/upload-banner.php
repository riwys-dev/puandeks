<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

require_once('/home/puandeks.com/backend/config.php');
require_once('/home/puandeks.com/backend/vendor/autoload.php');

use Aws\S3\S3Client;

session_start();

/* AUTH */
if (!isset($_SESSION['company_id'])) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$companyId = $_SESSION['company_id'];

$stmt = $pdo->prepare("SELECT banner_url FROM companies WHERE id = ?");
$stmt->execute([$companyId]);
$currentBanner = $stmt->fetchColumn();

/* PACKAGE CONTROL */
$stmt = $pdo->prepare("
    SELECT status 
    FROM company_subscriptions 
    WHERE company_id = ? 
    ORDER BY id DESC 
    LIMIT 1
");
$stmt->execute([$companyId]);
$sub = $stmt->fetch(PDO::FETCH_ASSOC);

$allowed = ['trial', 'active'];

if (!$sub || !in_array($sub['status'], $allowed)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Paketiniz bu işlem için uygun değil'
    ]);
    exit;
}

/* FILE CHECK */
if (!isset($_FILES['banner'])) {
    echo json_encode(['status' => 'error', 'message' => 'Dosya yok']);
    exit;
}

$file = $_FILES['banner'];

/* REAL MIME CHECK */
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

$allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];

if (!in_array($mime, $allowedTypes)) {
    echo json_encode(['status' => 'error', 'message' => 'Format hatalı']);
    exit;
}
if ($file['size'] > 2 * 1024 * 1024) {
    echo json_encode(['status' => 'error', 'message' => 'Max 2MB']);
    exit;
}

/* IMAGE SIZE CHECK */
/* FINAL SECURITY CHECK */
if (!is_uploaded_file($file['tmp_name'])) {
    echo json_encode(['status' => 'error', 'message' => 'Geçersiz upload']);
    exit;
}
$imageInfo = getimagesize($file['tmp_name']);

if (!$imageInfo) {
    echo json_encode(['status' => 'error', 'message' => 'Geçersiz görsel']);
    exit;
}

$width = $imageInfo[0];
$height = $imageInfo[1];

if ($width != 1200 || $height != 550) {
    echo json_encode(['status' => 'error', 'message' => 'Banner boyutu 1200x550 olmalıdır']);
    exit;
}

/* FILE NAME */
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$allowedExt = ['jpg', 'jpeg', 'png', 'webp'];

if (!in_array($ext, $allowedExt)) {
    echo json_encode(['status' => 'error', 'message' => 'Dosya uzantısı hatalı']);
    exit;
}

$fileName = "banners/company_" . $companyId . "." . $ext;

/* R2 CONNECT */
$s3 = new S3Client([
    'version' => 'latest',
    'region' => 'auto',
    'endpoint' => R2_ENDPOINT,
    'credentials' => [
        'key' => R2_ACCESS_KEY,
        'secret' => R2_SECRET_KEY
    ]
]);

/* UPLOAD */
try {
    if (!empty($currentBanner)) {

        $oldKey = str_replace(R2_PUBLIC_URL . '/', '', $currentBanner);

        try {
            $s3->deleteObject([
                'Bucket' => R2_BUCKET,
                'Key' => $oldKey
            ]);
        } catch (Exception $e) {
            // sessiz geç
        }
    }

    $s3->putObject([
        'Bucket' => R2_BUCKET,
        'Key' => $fileName,
        'SourceFile' => $file['tmp_name'],
        'ACL' => 'public-read',
        'ContentType' => $mime
    ]);

    $bannerUrl = R2_PUBLIC_URL . '/' . $fileName;
    $bannerLink = isset($_POST['banner_link']) ? trim($_POST['banner_link']) : '';

    /* DB UPDATE */
    $stmt = $pdo->prepare("UPDATE companies SET banner_url = ?, banner_link = ? WHERE id = ?");
    $stmt->execute([$bannerUrl, $bannerLink, $companyId]);

    echo json_encode([
        'status' => 'success',
        'url' => $bannerUrl
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}