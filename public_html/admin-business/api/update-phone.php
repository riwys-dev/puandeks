<?php
session_start();
require_once('/home/puandeks.com/backend/config.php');
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['company_id'])) {
  echo json_encode(['success' => false, 'message' => 'Oturum yok']);
  exit;
}

$company_id   = (int)$_SESSION['company_id'];
$country      = trim($_POST['country'] ?? '');
$phone_prefix = trim($_POST['phone_prefix'] ?? '');
$phone        = trim($_POST['phone'] ?? '');

if ($country === '' || $phone_prefix === '' || $phone === '') {
  echo json_encode(['success' => false, 'message' => 'Eksik veri']);
  exit;
}

/* -------------------------------------------------
   Mevcut telefonu çek
------------------------------------------------- */
$stmt = $pdo->prepare("
  SELECT phone, phone_prefix
  FROM companies
  WHERE id = ?
");
$stmt->execute([$company_id]);
$current = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$current) {
  echo json_encode(['success' => false, 'message' => 'İşletme bulunamadı']);
  exit;
}

/* -------------------------------------------------
   Telefon değişti mi kontrol et
------------------------------------------------- */
$phone_changed = (
  $current['phone'] !== $phone ||
  $current['phone_prefix'] !== $phone_prefix
);

/* -------------------------------------------------
   Güncelleme
------------------------------------------------- */
if ($phone_changed) {

$stmt = $pdo->prepare("
  UPDATE companies 
    SET 
      country = ?, 
      phone_prefix = ?, 
      phone = ?,
      phone_verified = 0
    WHERE id = ?
  ");

  $ok = $stmt->execute([
    $country,
    $phone_prefix,
    $phone,
    $company_id
  ]);

} else {

  $stmt = $pdo->prepare("
    UPDATE companies 
    SET 
      country = ?
    WHERE id = ?
  ");

  $ok = $stmt->execute([
    $country,
    $company_id
  ]);

}

echo json_encode(['success' => $ok]);