<?php
session_start();
require_once('/home/puandeks.com/backend/config.php');

header('Content-Type: application/json; charset=utf-8');

$userId = intval($_GET['user_id'] ?? 0);

// Session fallback
if ($userId <= 0 && isset($_SESSION['user_id'])) {
    $userId = intval($_SESSION['user_id']);
}

if ($userId <= 0) {
    echo json_encode(['status' => 'success', 'reviews' => []]);
    exit;
}

$category = $_GET['category'] ?? '';
$rating   = $_GET['rating'] ?? '';
$date     = $_GET['date'] ?? '';

$orderBy = 'r.created_at DESC';
if ($date === 'asc') {
    $orderBy = 'r.created_at ASC';
}

$sql = "
    SELECT 
        r.id,
        r.title,
        r.comment AS content,
        r.rating,
        r.created_at,
        r.status,
        r.company_id,
        c.name AS company_name,
        c.slug AS company_slug,

        u.name AS user_name,
        u.profile_image,

        (
            SELECT COUNT(*) 
            FROM reviews r2
            WHERE r2.user_id = u.id AND r2.status = 1
        ) AS approved_count,

        r.edit_count,
(
  SELECT COUNT(*) 
  FROM review_media rm 
  WHERE rm.review_id = r.id
) AS media_count
    FROM reviews r
    INNER JOIN companies c ON r.company_id = c.id
    INNER JOIN users u ON r.user_id = u.id
    WHERE r.user_id = :uid
";

$params = ['uid' => $userId];

// Category filter
if ($category !== '') {
    $sql .= " AND c.category_id = :catid";
    $params['catid'] = $category;
}

// Rating filter
if ($rating !== '') {
    $sql .= " AND r.rating = :rating";
    $params['rating'] = $rating;
}

$sql .= " ORDER BY $orderBy";

try {
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Total review count
    $countStmt = $conn->prepare("SELECT COUNT(*) FROM reviews WHERE user_id = ?");
    $countStmt->execute([$userId]);
    $totalCount = (int) $countStmt->fetchColumn();

    foreach ($reviews as &$r) {

        /* ============================
           NAME / DISPLAY / INITIAL
        ============================ */
        $rawName = trim($r['user_name'] ?? '');

        $r['display_name'] = htmlspecialchars($rawName, ENT_QUOTES, 'UTF-8');

        $r['user_initial'] = $rawName !== ''
            ? mb_strtoupper(mb_substr($rawName, 0, 1, 'UTF-8'), 'UTF-8')
            : 'U';

        /* ============================
           AVATAR (NO PLACEHOLDER)
        ============================ */
        if (!empty($r['profile_image'])) {
            if (strpos($r['profile_image'], 'http') === 0) {
                $r['user_image'] = $r['profile_image'];
            } elseif (strpos($r['profile_image'], 'uploads/') === 0) {
                $r['user_image'] = 'https://puandeks.com/' . $r['profile_image'];
            } else {
                $r['user_image'] = 'https://puandeks.com/uploads/users/' . $r['profile_image'];
            }
        } else {
            $r['user_image'] = null;
        }

        /* ============================
           DATE / COUNTS
        ============================ */
        $r['experience_date']     = date('d.m.Y', strtotime($r['created_at']));
        $r['approved_count']      = (int) $r['approved_count'];
        $r['total_review_count']  = $totalCount;

        /* ============================
           EDIT PERMISSION
        ============================ */
        $r['can_edit'] = false;

        if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $userId) {
            $createdTime = strtotime($r['created_at']);
            if (time() <= strtotime('+90 days', $createdTime) && (int)$r['edit_count'] < 1) {
                $r['can_edit'] = true;
            }
        }

        /* ============================
           BADGE
        ============================ */
        if ($r['approved_count'] >= 500) {
            $r['badge_label'] = 'Lider';
            $r['badge_color'] = '#D14B00';
        } elseif ($r['approved_count'] >= 100) {
            $r['badge_label'] = 'Elite';
            $r['badge_color'] = '#AA00FF';
        } elseif ($r['approved_count'] >= 50) {
            $r['badge_label'] = 'Uzman';
            $r['badge_color'] = '#0066FF';
        } elseif ($r['approved_count'] >= 10) {
            $r['badge_label'] = 'Yeni';
            $r['badge_color'] = '#1b7d2f';
        } else {
            $r['badge_label'] = '';
            $r['badge_color'] = '';
        }
    }

    echo json_encode([
        'status'  => 'success',
        'reviews' => $reviews
    ]);

} catch (Throwable $e) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Sunucu hatası'
    ]);
}
