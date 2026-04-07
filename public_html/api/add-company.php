<?php
header("Content-Type: application/json; charset=utf-8");
require_once('/home/puandeks.com/backend/config.php');

// 1. Gerekli alanları al
$user_id       = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
$company_name  = isset($_POST['company_name']) ? trim($_POST['company_name']) : '';
$website       = isset($_POST['website']) ? trim($_POST['website']) : '';
$rating        = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
$title         = isset($_POST['title']) ? trim($_POST['title']) : '';
$comment       = isset($_POST['comment']) ? trim($_POST['comment']) : '';

if ($user_id <= 0 || (!$company_name && !$website) || !$rating || !$title || !$comment) {
    echo json_encode([
        "success" => false,
        "message" => "Eksik veri gönderildi."
    ]);
    exit;
}

try {
    // 2. İşletme zaten var mı kontrol et (isim veya website ile)
    $stmt = $pdo->prepare("
        SELECT id FROM companies 
        WHERE name = :name OR website = :website 
        LIMIT 1
    ");
    $stmt->execute([
        ':name' => $company_name,
        ':website' => $website
    ]);
    $existingCompany = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existingCompany) {
        $company_id = $existingCompany['id'];
    } else {
        // 3. Yeni işletme oluştur (verified=0)
        $stmt = $pdo->prepare("
            INSERT INTO companies (name, website, verified, status, added_by_user_id, created_at)
            VALUES (:name, :website, 0, 'pending', :added_by, NOW())
        ");
        $stmt->execute([
            ':name' => $company_name ?: $website,
            ':website' => $website,
            ':added_by' => $user_id
        ]);
        $company_id = $pdo->lastInsertId();

        // 4. Admin'e bildirim oluştur
        $notif_title = "Yeni işletme eklendi";
        $notif_content = "Kullanıcı (ID: {$user_id}) tarafından '{$company_name ?: $website}' işletmesi eklendi.";
        $stmt = $pdo->prepare("
            INSERT INTO admin_notifications (title, content, is_read, created_at)
            VALUES (:title, :content, 0, NOW())
        ");
        $stmt->execute([
            ':title' => $notif_title,
            ':content' => $notif_content
        ]);
    }

    // 5. Yorumu ekle
    $stmt = $pdo->prepare("
        INSERT INTO reviews (user_id, company_id, rating, title, comment, status, created_at)
        VALUES (:user_id, :company_id, :rating, :title, :comment, 1, NOW())
    ");
    $stmt->execute([
        ':user_id' => $user_id,
        ':company_id' => $company_id,
        ':rating' => $rating,
        ':title' => $title,
        ':comment' => $comment
    ]);

    // 6. Başarılı yanıt
    echo json_encode([
        "success" => true,
        "message" => "İşletme eklendi ve yorum kaydedildi.",
        "company_id" => $company_id
    ]);

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Hata: " . $e->getMessage()
    ]);
}
?>
