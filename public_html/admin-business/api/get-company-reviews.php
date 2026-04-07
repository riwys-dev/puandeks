<?php
session_start();
header('Content-Type: application/json; charset=UTF-8');

if (!isset($_SESSION['company_id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Oturum bulunamadı.'
    ]);
    exit;
}

require_once('/home/puandeks.com/backend/config.php');

$company_id = (int)$_SESSION['company_id'];
$filter     = $_GET['filter'] ?? 'all';
$page       = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;

$limit  = 10;
$offset = ($page - 1) * $limit;

try {

    /* =========================================================
       FILTER CONDITION
    ========================================================= */
    $whereFilter = '';
    if ($filter === 'replied') {
        $whereFilter = " AND r.reply IS NOT NULL";
    } elseif ($filter === 'pending') {
        $whereFilter = " AND r.reply IS NULL";
    } elseif ($filter === 'auto') {
        $whereFilter = " AND r.reply_type = 'auto'";
    } elseif ($filter === 'manual') {
        $whereFilter = " AND (r.reply_type IS NULL OR r.reply_type = 'manual')";
    }

    /* =========================================================
       TOTAL COUNT
    ========================================================= */
    $countSql = "
        SELECT COUNT(*)
        FROM reviews r
        WHERE r.company_id = :company_id
        $whereFilter
    ";

    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute(['company_id' => $company_id]);
    $total = (int)$countStmt->fetchColumn();

    /* =========================================================
       PAGINATED DATA
    ========================================================= */
    $dataSql = "
        SELECT 
            r.id,
            r.user_id,
            u.name AS user_name,
            r.title,
            r.comment AS message,
            r.rating AS score,
            r.created_at,
            r.updated_at AS edited_at,
            r.reply AS company_reply,
            r.reply_type,
            r.status,

            (
            SELECT COUNT(*) 
            FROM review_media rm 
            WHERE rm.review_id = r.id
            ) AS media_count,

            CASE 
                WHEN r.status = 0 THEN 'Beklemede'
                WHEN r.status = 1 THEN 'Onaylı'
                WHEN r.status = 2 THEN 'Reddedildi'
                ELSE 'Bilinmiyor'
            END AS status_text

        FROM reviews r
        LEFT JOIN users u ON r.user_id = u.id
        WHERE r.company_id = :company_id
        $whereFilter
        ORDER BY r.created_at DESC
        LIMIT :limit OFFSET :offset
    ";

    $stmt = $pdo->prepare($dataSql);
    $stmt->bindValue(':company_id', $company_id, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'status'  => 'success',
        'reviews' => $reviews,
        'total'   => $total,
        'page'    => $page,
        'limit'   => $limit
    ]);
    exit;

} catch (PDOException $e) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Veritabanı hatası'
    ]);
    exit;
}
