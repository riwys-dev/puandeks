<?php
session_start();
require_once('/home/puandeks.com/backend/config.php');
require_once('/home/puandeks.com/backend/vendor/autoload.php');

header('Content-Type: application/json');

/* =====================================================
   R2 UPLOAD
===================================================== */
function uploadToR2($file_tmp, $file_name) {

    $s3 = new Aws\S3\S3Client([
        'version' => 'latest',
        'region'  => 'auto',
        'endpoint' => R2_ENDPOINT,
        'credentials' => [
            'key'    => R2_ACCESS_KEY,
            'secret' => R2_SECRET_KEY,
        ],
    ]);

    $s3->putObject([
        'Bucket' => R2_BUCKET,
        'Key' => 'reviews/' . $file_name,
        'SourceFile' => $file_tmp,
        'ACL'    => 'public-read',
        'ContentType' => mime_content_type($file_tmp),
    ]);

    return rtrim(R2_PUBLIC_URL, '/') . '/reviews/' . $file_name;
}

/* =====================================================
   AUTH
===================================================== */
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'user') {
    echo json_encode(["success" => false, "message" => "Only user accounts can perform this action."]);
    exit;
}

$user_id = (int) $_SESSION['user_id'];

/* =====================================================
   CSRF
===================================================== */
$csrf = $_POST['csrf_token'] ?? '';
if (!$csrf || $csrf !== ($_SESSION['csrf_token'] ?? '')) {
    echo json_encode(["success" => false, "message" => "CSRF validation failed."]);
    exit;
}

/* =====================================================
   INPUT
===================================================== */
$new_company = trim($_POST['new_company'] ?? '');
$website     = trim($_POST['website'] ?? '');
$rating      = (int) ($_POST['rating'] ?? 0);
$title       = trim($_POST['title'] ?? '');
$comment     = trim($_POST['comment'] ?? '');

/* =====================================================
   VALIDATION
===================================================== */
if ($new_company === '' && $website === '') {
    echo json_encode(["success" => false, "message" => "Company name or website required."]);
    exit;
}

if ($rating < 1 || $rating > 5) {
    echo json_encode(["success" => false, "message" => "Invalid rating."]);
    exit;
}

if (strlen($title) < 3 || strlen($comment) < 10) {
    echo json_encode(["success" => false, "message" => "Review too short."]);
    exit;
}

/* =====================================================
   WEBSITE NORMALIZE
===================================================== */
if ($website) {
    $website = strtolower($website);
    $website = preg_replace('/^https?:\/\//', '', $website);
    $website = preg_replace('/^www\./', '', $website);
    $website = rtrim($website, "/");
}

/* =====================================================
   SLUG
===================================================== */
function createSlug($text) {
    $text = strtolower(trim($text));
    $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim($text, '-') ?: 'company';
}

try {

    $pdo->beginTransaction();

    /* COMPANY */
    $base = $new_company ?: $website;
    $slug_base = createSlug($base);
    $slug = $slug_base;
    $i = 1;

    while (true) {
        $check = $pdo->prepare("SELECT id FROM companies WHERE slug = ?");
        $check->execute([$slug]);
        if (!$check->fetch()) break;
        $slug = $slug_base . '-' . $i++;
    }

    $stmt = $pdo->prepare("
        INSERT INTO companies (name, slug, website, status, added_by_user_id, created_at)
        VALUES (?, ?, ?, 'pending', ?, NOW())
    ");

    $stmt->execute([
        $new_company ?: $website,
        $slug,
        $website ?: null,
        $user_id
    ]);

    $company_id = $pdo->lastInsertId();

    /* REVIEW */
    $stmt = $pdo->prepare("
        INSERT INTO reviews (company_id, user_id, rating, title, comment, status, created_at)
        VALUES (?, ?, ?, ?, ?, 0, NOW())
    ");

    $stmt->execute([
        $company_id,
        $user_id,
        $rating,
        $title,
        $comment
    ]);

    $review_id = $pdo->lastInsertId();

    /* =====================================================
       MEDIA UPLOAD
    ===================================================== */
    if (!empty($_FILES['media']['name'][0])) {

        foreach ($_FILES['media']['tmp_name'] as $key => $tmp_name) {

            $file_tmp  = $_FILES['media']['tmp_name'][$key];
            $file_name = $_FILES['media']['name'][$key];

            if (!$file_tmp || !is_uploaded_file($file_tmp)) continue;

            $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $new_name = 'review_' . $review_id . '_' . time() . '_' . $key . '.' . $ext;

            $r2_url = uploadToR2($file_tmp, $new_name);
            if (!$r2_url) continue;

            $media_type = str_starts_with(mime_content_type($file_tmp), 'video')
                ? 'video'
                : 'image';

            $pdo->prepare("
                INSERT INTO review_media (review_id, media_type, media_url, created_at)
                VALUES (?, ?, ?, NOW())
            ")->execute([
                $review_id,
                $media_type,
                $r2_url
            ]);
        }
    }

    /* ADMIN NOTIFY */
    $pdo->prepare("
        INSERT INTO admin_notifications (title, content, is_read, created_at)
        VALUES (?, ?, 0, NOW())
    ")->execute([
        "Yeni işletme",
        "Kullanıcı yeni işletme ekledi: " . ($new_company ?: $website)
    ]);

    $pdo->commit();

    echo json_encode([
        "success" => true,
        "slug" => $slug,
        "redirect" => "/company/" . $slug
    ]);

} catch (Exception $e) {

    $pdo->rollBack();

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}