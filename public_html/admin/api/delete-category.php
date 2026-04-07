<?php
require_once('/home/puandeks.com/backend/config.php');

// JSON verisini al
$data = json_decode(file_get_contents("php://input"), true);
$categoryId = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if ($categoryId <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Geçersiz kategori ID']);
    exit;
}

try {
    // 1. Kategori mevcut mu?
    $stmt = $pdo->prepare("SELECT id FROM categories WHERE id = ?");
    $stmt->execute([$categoryId]);
    $categoryExists = $stmt->fetchColumn();

    if (!$categoryExists) {
        echo json_encode(['status' => 'error', 'message' => 'Kategori bulunamadı.']);
        exit;
    }

    // 2. İşletme sayısı kaç?
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM companies WHERE category_id = ?");
    $stmt->execute([$categoryId]);
    $companyCount = $stmt->fetchColumn();

    if ($companyCount > 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Bu kategoride işletme bulunduğu için silinemez. Önce işletmeleri taşıyın veya silin.'
        ]);
        exit;
    }

    // 3. Sil
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$categoryId]);

    echo json_encode(['status' => 'success', 'message' => 'Kategori başarıyla silindi.']);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Silme işlemi başarısız.']);
}
