<?php
require_once('/home/puandeks.com/backend/config.php');

header('Content-Type: application/json');

try {
    $stmt = $pdo->query("
        SELECT c.id, c.name, COUNT(co.id) AS company_count
        FROM categories c
        LEFT JOIN companies co ON co.category_id = c.id
        GROUP BY c.id, c.name
        ORDER BY c.name ASC
    ");

    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "status" => "success",
        "categories" => $categories
    ]);
} catch (PDOException $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Veriler alınamadı"
    ]);
}
