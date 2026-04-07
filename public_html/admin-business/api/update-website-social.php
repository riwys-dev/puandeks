<?php
session_start();
header('Content-Type: application/json; charset=UTF-8');

if (!isset($_SESSION['company_id'])) {
  echo json_encode(['success' => false, 'message' => 'Unauthorized']);
  exit;
}

require_once('/home/puandeks.com/backend/config.php');

$company_id = (int)$_SESSION['company_id'];

$website        = trim($_POST['website'] ?? '');
$linkedin_url   = trim($_POST['linkedin_url'] ?? '');
$facebook_url   = trim($_POST['facebook_url'] ?? '');
$instagram_url  = trim($_POST['instagram_url'] ?? '');
$x_url          = trim($_POST['x_url'] ?? '');
$youtube_url    = trim($_POST['youtube_url'] ?? '');

try {
  $stmt = $pdo->prepare("
    UPDATE companies SET
      website = :website,
      linkedin_url = :linkedin_url,
      facebook_url = :facebook_url,
      instagram_url = :instagram_url,
      x_url = :x_url,
      youtube_url = :youtube_url
    WHERE id = :id
  ");

  $stmt->execute([
    ':website'       => $website ?: null,
    ':linkedin_url'  => $linkedin_url ?: null,
    ':facebook_url'  => $facebook_url ?: null,
    ':instagram_url' => $instagram_url ?: null,
    ':x_url'         => $x_url ?: null,
    ':youtube_url'   => $youtube_url ?: null,
    ':id'            => $company_id
  ]);

  echo json_encode(['success' => true]);
} catch (Exception $e) {
  echo json_encode([
    'success' => false,
    'message' => 'DB update failed'
  ]);
}
