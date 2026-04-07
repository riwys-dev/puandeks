<?php
header('Content-Type: application/json');
require_once('/home/puandeks.com/backend/config.php');

$term = isset($_GET['term']) ? trim($_GET['term']) : '';

if (strlen($term) < 2) {
    echo json_encode([]);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT id, name, slug
        FROM categories 
        WHERE name LIKE :term 
        ORDER BY name ASC 
        LIMIT 10
    ");
    $stmt->execute(['term' => '%' . $term . '%']);

    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));

} catch (Exception $e) {
    echo json_encode([]);
}
