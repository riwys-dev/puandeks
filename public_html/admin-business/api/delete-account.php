<?php
session_start();
header('Content-Type: application/json; charset=UTF-8');

if (!isset($_SESSION['company_id'])) {
  echo json_encode(['success' => false]);
  exit;
}

require_once('/home/puandeks.com/backend/config.php');

$company_id = (int)$_SESSION['company_id'];

$stmt = $pdo->prepare("UPDATE companies SET status = 'deleted' WHERE id = ?");
$stmt->execute([$company_id]);

echo json_encode(['success' => true]);
