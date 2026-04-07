<?php
session_start();
header('Content-Type: application/json; charset=UTF-8');

if (!isset($_SESSION['company_id'])) {
  echo json_encode(['success' => false, 'message' => 'Unauthorized']);
  exit;
}

require_once('/home/puandeks.com/backend/config.php');

$current = $_POST['current_password'] ?? '';
$new     = $_POST['new_password'] ?? '';

/* basic check */
if ($current === '' || $new === '') {
  echo json_encode(['success' => false, 'message' => 'Missing fields']);
  exit;
}

/* password rules */
$rulesOk =
  strlen($new) >= 8 &&
  preg_match('/[A-Z]/', $new) &&
  preg_match('/[a-z]/', $new) &&
  preg_match('/[0-9]/', $new) &&
  preg_match('/[^A-Za-z0-9]/', $new);

if (!$rulesOk) {
  echo json_encode([
    'success' => false,
    'message' => 'Password rules not met'
  ]);
  exit;
}

$company_id = (int)$_SESSION['company_id'];

/* get current hash */
$stmt = $pdo->prepare("SELECT password FROM companies WHERE id = ?");
$stmt->execute([$company_id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row || !password_verify($current, $row['password'])) {
  echo json_encode(['success' => false, 'message' => 'Wrong password']);
  exit;
}

/* hash new password */
$newHash = password_hash($new, PASSWORD_BCRYPT);

/* update */
$upd = $pdo->prepare("UPDATE companies SET password = ? WHERE id = ?");
$ok  = $upd->execute([$newHash, $company_id]);

echo json_encode(['success' => $ok]);
