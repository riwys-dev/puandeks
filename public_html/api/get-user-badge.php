<?php
session_start();
require_once('/home/puandeks.com/backend/config.php');

header('Content-Type: application/json');

// Kullanıcı oturumu ve rol kontrolü
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'user') {
    echo json_encode([
        'success' => false,
        'message' => 'Yetkisiz erişim'
    ]);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    // SADECE onaylı yorumlar
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM reviews WHERE user_id = ? AND status = 1");
    $stmt->execute([$user_id]);
    $count = (int)$stmt->fetchColumn();

    // Baslangic degerleri
    $badge = null;
    $message = "";
    $next = null;

    // --- Rozet kuralları ---
    if ($count < 10) {
        $badge = null;
        $next = [
            "title" => "Yeni",
            "remaining" => 10 - $count
        ];
        $message = "{$next['remaining']} yorum daha yaparak Yeni rozetini alabilirsin!";
    }
    elseif ($count < 50) {
        $badge = "Yeni";
        $next = [
            "title" => "Uzman",
            "remaining" => 50 - $count
        ];
        $message = "{$next['remaining']} yorum daha yaparak Uzman rozetini alabilirsin!";
    }
    elseif ($count < 100) {
        $badge = "Uzman";
        $next = [
            "title" => "Elite",
            "remaining" => 100 - $count
        ];
        $message = "{$next['remaining']} yorum daha yaparak Elite rozetini alabilirsin!";
    }
    elseif ($count < 500) {
        $badge = "Elite";
        $next = [
            "title" => "Lider",
            "remaining" => 500 - $count
        ];
        $message = "{$next['remaining']} yorum daha yaparak Lider rozetini alabilirsin!";
    }
    else {
        $badge = "Lider";
        $next = null;
        $message = "Tebrikler! Tüm rozetleri kazandınız.";
    }

    // --- JSON output ---
    echo json_encode([
        "success" => true,
        "badge" => $badge,      // Yeni / Uzman / Elite / Lider / null
        "review_count" => $count,
        "next" => $next,        // sonraki rozet + kalan
        "message" => $message   // tam mesaj
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Sunucu hatası'
    ]);
}
