<?php
header('Content-Type: application/json');
require_once('/home/puandeks.com/backend/config.php');

$title   = $_POST['title'] ?? '';
$content = $_POST['content'] ?? '';
$status  = isset($_POST['status']) ? (int)$_POST['status'] : 1;

$meta_title = $_POST['meta_title'] ?? null;
$meta_description = $_POST['meta_description'] ?? null;
$tags = $_POST['tags'] ?? null;

if (empty($title) || empty($content)) {
    echo json_encode([
        "success" => false,
        "message" => "Başlık ve içerik zorunludur."
    ]);
    exit;
}

/* ============================================================
   KLASÖR — SUNUCU DOSYA YOLU
   /home/puandeks.com/public_html/admin/uploads/blogs-business/
   ============================================================ */
$uploadDir = "/home/puandeks.com/public_html/admin/uploads/blogs-business/";

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0775, true);
}

$imageUrl = "/admin/uploads/blogs-business/placeholder.webp";

/* ============================================================
   GÖRSEL YÜKLEME + WEBP
   ============================================================ */
if (!empty($_FILES['image']['tmp_name'])) {

    $allowed = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];

    if (!in_array($_FILES['image']['type'], $allowed)) {
        echo json_encode([
            "success" => false,
            "message" => "Sadece JPG, PNG ve WEBP yükleyebilirsiniz."
        ]);
        exit;
    }

    $tmp = $_FILES['image']['tmp_name'];
    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

    $fileName = uniqid('blog_', true) . '.webp';
    $savePath = $uploadDir . $fileName;

    switch ($ext) {
        case 'png':
            $img = imagecreatefrompng($tmp);
            break;
        case 'webp':
            $img = imagecreatefromwebp($tmp);
            break;
        default:
            $img = imagecreatefromjpeg($tmp);
    }

    imagewebp($img, $savePath, 80);
    imagedestroy($img);

    $imageUrl = "/admin/uploads/blogs-business/" . $fileName;
}

/* ============================================================
   DATABASE
   ============================================================ */
$stmt = $pdo->prepare("
    INSERT INTO business_blog_posts
    (title, content, image, status, meta_title, meta_description, tags, created_at)
    VALUES
    (:title, :content, :image, :status, :meta_title, :meta_description, :tags, NOW())
");

$ok = $stmt->execute([
    ':title' => $title,
    ':content' => $content,
    ':image' => $imageUrl,
    ':status' => $status,
    ':meta_title' => $meta_title,
    ':meta_description' => $meta_description,
    ':tags' => $tags
]);

echo json_encode([
    "success" => $ok,
    "message" => $ok ? "Blog başarıyla eklendi." : "Kayıt sırasında hata oluştu."
]);
