<?php
header('Content-Type: application/json');
require_once('/home/puandeks.com/backend/config.php');

$search = $_GET['search'] ?? '';
$page   = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int)$_GET['page'] : 1;
$limit  = 6;
$offset = ($page - 1) * $limit;

$params = [];
$where  = "WHERE status = 1";

// ARAMA FİLTRESİ
if (!empty($search)) {
    $where .= " AND (title LIKE :search OR content LIKE :search)";
    $params['search'] = "%$search%";
}

// TOPLAM KAYIT
$countSql = "SELECT COUNT(*) FROM business_blog_posts $where";
$countStmt = $pdo->prepare($countSql);
$countStmt->execute($params);
$total = $countStmt->fetchColumn();

// BLOG LİSTELEME
$sql = "
    SELECT id, title, image, content, created_at 
    FROM business_blog_posts
    $where
    ORDER BY created_at DESC
    LIMIT :offset, :limit
";

$stmt = $pdo->prepare($sql);

foreach ($params as $key => $val) {
    $stmt->bindValue(":$key", $val);
}

$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);

$stmt->execute();
$blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// GÖRSEL YOLU
foreach ($blogs as &$b) {
    if (empty($b['image'])) {
        $b['image'] = "/uploads/blogs-business/placeholder.webp";
    }
}

echo json_encode([
    "success" => true,
    "blogs"   => $blogs,
    "total"   => $total,
    "page"    => $page,
    "limit"   => $limit
]);
