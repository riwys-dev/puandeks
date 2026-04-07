<?php
require_once('/home/puandeks.com/backend/config.php');

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
$name = trim($data['name'] ?? '');

if ($name === '') {
    echo json_encode(["status" => "error", "message" => "Kategori adı boş olamaz."]);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
    $stmt->execute([$name]);

    echo json_encode(["status" => "success", "message" => "Kategori eklendi."]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Kategori eklenemedi."]);
}
