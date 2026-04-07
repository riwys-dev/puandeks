<?php
session_start();
require_once('/home/puandeks.com/backend/config.php');

header('Content-Type: application/json');

// 1️⃣ Yalnızca POST isteğine izin ver
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Geçersiz istek yöntemi.']);
    exit;
}

// 2️⃣ Oturum kontrolü
if (empty($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'user') {
    echo json_encode(['status' => 'error', 'message' => 'Oturum bulunamadı veya yetkisiz erişim.']);
    exit;
}

$reviewId = $_POST['id'] ?? null;
$userId   = (int)$_SESSION['user_id'];

// 3️⃣ ID kontrolü
if (!$reviewId || !is_numeric($reviewId)) {
    echo json_encode(['status' => 'error', 'message' => 'Geçersiz yorum ID.']);
    exit;
}

try {
    // 4️⃣ Yorumu getir
    $stmt = $conn->prepare("SELECT id, created_at FROM reviews WHERE id = ? AND user_id = ?");
    $stmt->execute([$reviewId, $userId]);
    $review = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$review) {
        echo json_encode(['status' => 'error', 'message' => 'Yorum bulunamadı veya bu kullanıcıya ait değil.']);
        exit;
    }

    // 5️⃣ 24 saat kontrolü (86400 sn)
    $createdAt = strtotime($review['created_at']);
    if ((time() - $createdAt) > 86400) {
        echo json_encode(['status' => 'error', 'message' => 'Silme süresi dolmuş (24 saati geçti).']);
        exit;
    }

    // 6️⃣ Silme işlemi
    $delete = $conn->prepare("DELETE FROM reviews WHERE id = ? AND user_id = ?");
    $delete->execute([$reviewId, $userId]);

    if ($delete->rowCount() > 0) {
        echo json_encode([
            'status'  => 'success',
            'message' => 'Yorum başarıyla silindi.',
            'debug'   => ['review_id' => $reviewId, 'user_id' => $userId]
        ]);
    } else {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Silme işlemi başarısız oldu (rowCount=0).',
            'debug'   => ['review_id' => $reviewId, 'user_id' => $userId]
        ]);
    }
    exit;

} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Veritabanı hatası: ' . $e->getMessage()]);
    exit;
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Sunucu hatası: ' . $e->getMessage()]);
    exit;
}
