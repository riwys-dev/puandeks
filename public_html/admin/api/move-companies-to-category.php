<?php
require_once('/home/puandeks.com/backend/config.php');

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
$fromId = (int)($data['from'] ?? 0);
$toId = (int)($data['to'] ?? 0);

if ($fromId <= 0 || $toId <= 0 || $fromId === $toId) {
    echo json_encode(["status" => "error", "message" => "Geçersiz kategori seçimi."]);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE companies SET category_id = ? WHERE category_id = ?");
    $stmt->execute([$toId, $fromId]);

    echo json_encode(["status" => "success", "message" => "İşletmeler başarıyla taşındı."]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Taşıma işlemi başarısız."]);
}
