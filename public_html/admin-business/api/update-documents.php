<?php
session_start();
header('Content-Type: application/json; charset=UTF-8');

if (!isset($_SESSION['company_id'])) {
  echo json_encode(['success' => false, 'message' => 'Unauthorized']);
  exit;
}

require_once('/home/puandeks.com/backend/config.php');

$company_id = (int)$_SESSION['company_id'];

$baseDir = '/home/puandeks.com/public_html/uploads/documents/companies/';
$baseUrl = 'https://puandeks.com/uploads/documents/companies/';

$map = [
  'vergi'    => 'vergi/',
  'faaliyet' => 'faaliyet/',
  'sicil'    => 'sicil/'
];

// mevcut belgeler
$stmt = $pdo->prepare("SELECT documents, name FROM companies WHERE id = ?");
$stmt->execute([$company_id]);
$company = $stmt->fetch(PDO::FETCH_ASSOC);

$currentDocs = $company['documents'] ?? null;
$companyName = $company['name'] ?? 'İşletme';

$docs = [];
if ($currentDocs) {
  $decoded = json_decode($currentDocs, true);
  if (is_array($decoded)) {
    $docs = $decoded;
  }
}

$uploadedSomething = false;

foreach ($map as $key => $folder) {

  if (!isset($_FILES[$key])) continue;
  if ($_FILES[$key]['error'] !== UPLOAD_ERR_OK) continue;

  $uploadedSomething = true;

  // eski dosyayı sil
  if (!empty($docs[$key])) {
    $oldPath = parse_url($docs[$key], PHP_URL_PATH);
    $oldFile = $_SERVER['DOCUMENT_ROOT'] . $oldPath;
    if (is_file($oldFile)) {
      unlink($oldFile);
    }
  }

  $ext = pathinfo($_FILES[$key]['name'], PATHINFO_EXTENSION);
  $fileName = "company_{$company_id}_{$key}_" . time() . "." . $ext;

  $targetPath = $baseDir . $folder . $fileName;

  if (!move_uploaded_file($_FILES[$key]['tmp_name'], $targetPath)) {
    continue;
  }

  $docs[$key] = $baseUrl . $folder . $fileName;
}

$stmt = $pdo->prepare("UPDATE companies SET documents = ? WHERE id = ?");
$stmt->execute([json_encode($docs, JSON_UNESCAPED_SLASHES), $company_id]);

/* ======================================================
   ADMIN BİLDİRİM EKLE
====================================================== */
if ($uploadedSomething) {

  $title   = "📄 Belge Güncellendi";
  $content = $companyName . " işletmesi belgelerini yükledi veya güncelledi.";

  $stmtNotif = $pdo->prepare("
      INSERT INTO admin_notifications (title, content, created_at, is_read)
      VALUES (?, ?, NOW(), 0)
  ");
  $stmtNotif->execute([$title, $content]);
}

echo json_encode(['success' => true, 'documents' => $docs]);
