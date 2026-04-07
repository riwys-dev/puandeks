<?php
session_start();
require_once('/home/puandeks.com/backend/config.php');

header('Content-Type: application/json');

// SADECE USER şikayet edebilir
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'user') {
    echo json_encode([
        'success' => false,
        'message' => 'Bu işlem için giriş yapmalısınız.'
    ]);
    exit;
}

$review_id = intval($_POST['review_id'] ?? 0);
$reason    = trim($_POST['reason'] ?? '');

if ($review_id <= 0 || $reason === '') {
    echo json_encode([
        'success' => false,
        'message' => 'Eksik veri.'
    ]);
    exit;
}

$reported_by_id   = $_SESSION['user_id'];
$reported_by_role = 'user';

try {

    // Aynı user aynı yorumu tekrar şikayet edemesin
    $check = $conn->prepare("
        SELECT id 
        FROM review_reports 
        WHERE review_id = ? 
        AND reported_by_id = ? 
        AND reported_by_role = 'user'
    ");
    $check->execute([$review_id, $reported_by_id]);

    if ($check->fetch()) {
        echo json_encode([
            'success' => false,
            'message' => 'Bu yorumu zaten şikayet ettiniz.'
        ]);
        exit;
    }

    // Şikayet kaydı
    $stmt = $conn->prepare("
        INSERT INTO review_reports 
        (review_id, reported_by_id, reported_by_role, reason, created_at)
        VALUES (?, ?, 'user', ?, NOW())
    ");
    $stmt->execute([$review_id, $reported_by_id, $reason]);

    // Admin notification
    $notif = $conn->prepare("
        INSERT INTO admin_notifications 
        (title, content, created_at, is_read)
        VALUES (?, ?, NOW(), 0)
    ");
    $notif->execute([
        'Yeni Şikayet',
        'Bir inceleme için yeni şikayet oluşturuldu.'
    ]);

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Sunucu hatası.'
    ]);
}
