<?php
header('Content-Type: application/json');
require_once('/home/puandeks.com/backend/config.php');

$id      = $_POST['id'] ?? 0;
$title   = $_POST['title'] ?? '';
$content = $_POST['content'] ?? '';

$meta_title = $_POST['meta_title'] ?? null;
$meta_description = $_POST['meta_description'] ?? null;
$tags = $_POST['tags'] ?? null;

if (!$id || empty($title) || empty($content)) {
    echo json_encode([
        "success" => false,
        "message" => "Eksik bilgi var."
    ]);
    exit;
}

$stmt = $pdo->prepare("SELECT image FROM business_blog_posts WHERE id = ?");
$stmt->execute([$id]);
$old = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$old) {
    echo json_encode([
        "success" => false,
        "message" => "Blog bulunamadı."
    ]);
    exit;
}

$image = $old['image'];

$uploadDir = "/home/puandeks.com/public_html/admin/uploads/blogs-business/";

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0775, true);
}

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

    $image = "/admin/uploads/blogs-business/" . $fileName;
}

$update = $pdo->prepare("
    UPDATE business_blog_posts 
    SET 
        title = :title,
        content = :content,
        image = :image,
        meta_title = :meta_title,
        meta_description = :meta_description,
        tags = :tags,
        updated_at = NOW()
    WHERE id = :id
");

$ok = $update->execute([
    ':title' => $title,
    ':content' => $content,
    ':image' => $image,
    ':meta_title' => $meta_title,
    ':meta_description' => $meta_description,
    ':tags' => $tags,
    ':id' => $id
]);

echo json_encode([
    "success" => $ok,
    "message" => $ok ? "Güncellendi" : "Güncellenemedi"
]);
