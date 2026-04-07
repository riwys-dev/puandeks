<?php
require_once('/home/puandeks.com/backend/config.php');
header('Content-Type: application/json; charset=utf-8');

session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Oturum bulunamadı.']);
    exit;
}

$userId = $_SESSION['user_id'];
$country = trim($_POST['country'] ?? '');

if ($country === '') {
    echo json_encode(['success' => false, 'message' => 'Ülke bilgisi boş olamaz.']);
    exit;
}

// Ülkenin geçerli olup olmadığını kontrol et
$check = $pdo->prepare("SELECT COUNT(*) FROM countries WHERE name = ? OR name_normalized = ?");
$check->execute([$country, $country]);
if ($check->fetchColumn() == 0) {
    echo json_encode(['success' => false, 'message' => 'Geçersiz ülke adı.']);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE users SET country = ? WHERE id = ?");
    $stmt->execute([$country, $userId]);

    echo json_encode([
        'success' => true,
        'message' => 'Ülke başarıyla güncellendi.',
        'country' => $country
    ], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Veritabanı hatası: ' . $e->getMessage()]);
}
?>
