<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['company_id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Oturum bulunamadı.'
    ]);
    exit;
}

require_once('/home/puandeks.com/backend/config.php');

$company_id = (int)$_SESSION['company_id'];
$data = json_decode(file_get_contents('php://input'), true);

$review_id = isset($data['review_id']) ? (int)$data['review_id'] : 0;
$reply     = trim($data['reply'] ?? '');

if ($review_id === 0 || $reply === '') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Eksik veri gönderildi.'
    ]);
    exit;
}

try {

    /* =========================================================
       1) REVIEW SAHİPLİK + ZAMAN KONTROLÜ
    ========================================================= */
    $stmt = $pdo->prepare("
        SELECT id, updated_at, user_id
        FROM reviews
        WHERE id = ? AND company_id = ?
        LIMIT 1
    ");
    $stmt->execute([$review_id, $company_id]);
    $review = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$review) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Yorum bulunamadı veya yetkiniz yok.'
        ]);
        exit;
    }

    /* =========================================================
       2) 24 SAAT KURALI (AUTO + MANUAL İÇİN ORTAK)
    ========================================================= */
    if (!empty($review['updated_at'])) {
        $lastEdit = strtotime($review['updated_at']);
        if ($lastEdit && (time() - $lastEdit) > 86400) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Yanıt düzenleme süresi (24 saat) dolmuştur.'
            ]);
            exit;
        }
    }

    /* =========================================================
       3) YANITI YAZ / GÜNCELLE
       (AUTO OLSA BİLE MANUAL MÜDAHALE SERBEST)
    ========================================================= */
    $update = $pdo->prepare("
        UPDATE reviews
        SET reply = ?,
            reply_type = 'manual',
            updated_at = NOW()
        WHERE id = ?
    ");
    $update->execute([$reply, $review_id]);

    /* =========================================================
       4) USER BİLDİRİMİ OLUŞTUR
    ========================================================= */
    if (!empty($review['user_id'])) {

        // İşletme adını çek
        $stmtCompany = $pdo->prepare("
            SELECT name
            FROM companies
            WHERE id = ?
            LIMIT 1
        ");
        $stmtCompany->execute([$company_id]);
        $companyData = $stmtCompany->fetch(PDO::FETCH_ASSOC);

        $companyName = $companyData['name'] ?? 'İşletme';

        $title   = "İncelemenize yanıt geldi";
        $content = $companyName . " yazdığınız incelemeye yanıt verdi.";

        $insertNotif = $pdo->prepare("
            INSERT INTO notifications (user_id, title, content, status, created_at)
            VALUES (?, ?, ?, 'unread', NOW())
        ");
        $insertNotif->execute([
            (int)$review['user_id'],
            $title,
            $content
        ]);
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'Yanıt başarıyla kaydedildi.'
    ]);
    exit;

} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Veritabanı hatası.'
    ]);
    exit;
}
