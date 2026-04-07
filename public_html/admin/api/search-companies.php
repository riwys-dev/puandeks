<?php
require_once('/home/puandeks.com/backend/config.php');
header('Content-Type: application/json');

$query = $_GET['q'] ?? '';
$results = [];

if (!empty($query)) {
  $stmt = $pdo->prepare("SELECT id, name FROM companies WHERE name LIKE :q ORDER BY name ASC LIMIT 10");
  $stmt->execute(['q' => '%' . $query . '%']);
  $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

echo json_encode($results);
