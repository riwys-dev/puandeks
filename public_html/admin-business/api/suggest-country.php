<?php
require_once('/home/puandeks.com/backend/config.php');
header('Content-Type: application/json; charset=utf-8');

try {
    $q = isset($_GET['q']) ? trim($_GET['q']) : '';

    // q=all -> tüm ülkeleri döndür
    if (strtolower($q) === 'all') {
        $stmt = $pdo->query("SELECT id, name, code, phone_prefix FROM countries ORDER BY name ASC");
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'count' => count($results),
            'results' => $results
        ]);
        exit;
    }

    // q boşsa -> boş sonuç
    if ($q === '') {
        echo json_encode(['success' => false, 'count' => 0, 'results' => []]);
        exit;
    }

    // Arama yap
    $stmt = $pdo->prepare("SELECT id, name, code 
                           FROM countries 
                           WHERE name LIKE CONCAT('%', ?, '%') 
                              OR name_normalized LIKE CONCAT('%', ?, '%') 
                              OR code LIKE CONCAT('%', ?, '%')
                           ORDER BY name ASC
                           LIMIT 10");
    $stmt->execute([$q, $q, $q]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'count' => count($results),
        'results' => $results
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Veritabanı hatası: ' . $e->getMessage()
    ]);
}
?>
