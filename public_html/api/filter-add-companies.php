<?php
header("Content-Type: application/json");
require_once("/home/puandeks.com/backend/config.php");

$query = $_GET['q'] ?? '';
$query = trim($query);

if (strlen($query) < 2) {
    echo json_encode([]);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id, name FROM companies WHERE name LIKE :q ORDER BY name ASC LIMIT 10");
    $stmt->execute(['q' => "%$query%"]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($results);
} catch (Exception $e) {
    echo json_encode([]);
}
