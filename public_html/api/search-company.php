<?php
header("Content-Type: application/json");
require_once("/home/puandeks.com/backend/config.php");

$q = $_GET['query'] ?? '';
$q = trim($q);

// Write-review only companies
$businessOnly = (isset($_GET['type']) && $_GET['type'] === 'business');

if (strlen($q) < 2) {
    echo json_encode([]);
    exit;
}

try {

    // ----------------------------------------
    // Company search
    // ----------------------------------------
    $stmt1 = $pdo->prepare("
        SELECT id, slug, name, 'company' AS type
        FROM companies
        WHERE name LIKE :q
        LIMIT 10
    ");
    $stmt1->execute(['q' => "%$q%"]);
    $companies = $stmt1->fetchAll(PDO::FETCH_ASSOC);

    // ----------------------------------------
    // If write-review No Categories
    // ----------------------------------------
    if ($businessOnly) {
        echo json_encode($companies);
        exit;
    }

    // ----------------------------------------
    // Search Category
    // ----------------------------------------
    $stmt2 = $pdo->prepare("
        SELECT id, name, slug, 'category' AS type
        FROM categories
        WHERE name LIKE :q
        LIMIT 10
    ");
    $stmt2->execute(['q' => "%$q%"]);
    $categories = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    // ----------------------------------------
    // Merge Results
    // ----------------------------------------
    $results = array_merge($companies, $categories);

    echo json_encode($results);

} catch (Exception $e) {
    echo json_encode([]);
}
