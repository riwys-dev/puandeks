<?php
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode([
        "success" => false,
        "message" => "Yetkisiz erişim."
    ]);
    exit;
}

require_once('/home/puandeks.com/backend/config.php');

try {

    $stmt = $pdo->prepare("
        SELECT 
            rr.id,
            rr.reason,
            rr.status,
            rr.created_at,

            c.name AS company_name,

            u.name AS review_user_name,
            u.surname AS review_user_surname

        FROM review_reports rr

        INNER JOIN reviews r ON r.id = rr.review_id
        INNER JOIN companies c ON c.id = r.company_id
        INNER JOIN users u ON u.id = r.user_id

        WHERE rr.reported_by_role = 'business'
        ORDER BY rr.created_at DESC
    ");

    $stmt->execute();
    $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Kullanıcı adını "Ad S." formatına çeviriyoruz
    foreach ($reports as &$report) {
        if (!empty($report['review_user_surname'])) {
            $report['review_user_name'] =
                $report['review_user_name'] . ' ' .
                mb_substr($report['review_user_surname'], 0, 1) . '.';
        }
    }

    echo json_encode([
        "success" => true,
        "data" => $reports
    ]);

} catch (Exception $e) {

    echo json_encode([
        "success" => false,
        "message" => "Sunucu hatası",
        "error" => $e->getMessage()
    ]);

}
