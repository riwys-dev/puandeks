<?php
session_start();
header('Content-Type: application/json');

// Admin login control
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(["success" => false, "message" => "Yetkisiz işlem"]);
    exit;
}

require_once('/home/puandeks.com/backend/config.php');

$company_id = intval($_POST['company_id'] ?? 0);
$field      = $_POST['field'] ?? '';
$value      = trim($_POST['value'] ?? '');

if (!$company_id || $field === '') {
    echo json_encode(["success" => false, "message" => "Geçersiz veri"]);
    exit;
}


$allowed_fields = ['name', 'category_id'];

if (!in_array($field, $allowed_fields)) {
    echo json_encode(["success" => false, "message" => "İzin verilmeyen alan"]);
    exit;
}

// Kategori select 
if ($field === 'category_id') {

    $value = intval($value);

    // Kategori control
    $cat = $pdo->prepare("SELECT name FROM categories WHERE id = ?");
    $cat->execute([$value]);
    $catName = $cat->fetchColumn();

    if (!$catName) {
        echo json_encode(["success" => false, "message" => "Kategori bulunamadı"]);
        exit;
    }

    // Update
    $stmt = $pdo->prepare("UPDATE companies SET category_id = ? WHERE id = ?");
    $stmt->execute([$value, $company_id]);

    echo json_encode([
        "success" => true,
        "message" => "Kategori güncellendi.",
        "new_value" => $catName
    ]);

    exit;
}


// C NAME update
if ($field === 'name') {

    if ($value === '') {
        echo json_encode(["success" => false, "message" => "İşletme adı boş olamaz"]);
        exit;
    }

    $stmt = $pdo->prepare("UPDATE companies SET name = ? WHERE id = ?");
    $stmt->execute([$value, $company_id]);

    echo json_encode([
        "success" => true,
        "message" => "İşletme adı güncellendi.",
        "new_value" => htmlspecialchars($value)
    ]);

    exit;
}
