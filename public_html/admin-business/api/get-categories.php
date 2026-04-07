<?php
require_once('/home/puandeks.com/backend/config.php');
header('Content-Type: application/json');

try {
    $stmt = $conn->query("SELECT id, name FROM categories ORDER BY name ASC");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'categories' => $categories
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Kategori verileri alınamadı.'
    ]);
}
