<?php
session_start();
require_once('/home/puandeks.com/backend/config.php');

$company_id = $_SESSION['company_id'] ?? null;
$id = $_POST['id'] ?? null;

if (!$company_id || !$id) {
  echo 'ERR';
  exit;
}

$stmt = $pdo->prepare("
  DELETE FROM company_notifications
  WHERE id = ? AND company_id = ?
");
$stmt->execute([$id, $company_id]);

echo 'OK';
