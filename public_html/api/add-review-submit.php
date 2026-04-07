<?php
session_start();
require_once('/home/puandeks.com/backend/config.php');
require_once('/home/puandeks.com/backend/vendor/autoload.php');

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
   RESPONSE
===================================================== */
header('Content-Type: application/json');

/* =====================================================
   AUTH CHECK
===================================================== */
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Please log in to submit a review.',
        'redirect' => '/login'
    ]);
    exit;
}

if (($_SESSION['role'] ?? '') === 'business') {
    echo json_encode([
        'success' => false,
        'message' => 'Businesses cannot submit reviews.'
    ]);
    exit;
}

/* =====================================================
   INPUT
===================================================== */
$company_id = intval($_POST['company_id'] ?? 0);
$rating     = intval($_POST['rating'] ?? 0);
$title      = trim($_POST['title'] ?? '');
$comment    = trim($_POST['comment'] ?? '');
$csrf       = $_POST['csrf_token'] ?? '';

/* =====================================================
   VALIDATION
===================================================== */
if ($company_id <= 0 || $rating < 1 || $rating > 5 || $title === '' || $comment === '') {
    echo json_encode([
        'success' => false,
        'message' => 'Missing or invalid fields.'
    ]);
    exit;
}

if (!isset($_SESSION['csrf_token']) || $csrf !== $_SESSION['csrf_token']) {
    echo json_encode([
        'success' => false,
        'message' => 'CSRF validation failed.'
    ]);
    exit;
}

/* =====================================================
   PHONE CHECK
===================================================== */
$userCheck = $conn->prepare("SELECT phone_verified, phone FROM users WHERE id = ?");
$userCheck->execute([$_SESSION['user_id']]);
$userData = $userCheck->fetch(PDO::FETCH_ASSOC);

if ((int)($userData['phone_verified'] ?? 0) !== 1 || empty($userData['phone'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Telefon doğrulaması gerekli.',
        'require_phone_verification' => true
    ]);
    exit;
}

/* =====================================================
   INSERT REVIEW
===================================================== */
try {

    $stmt = $conn->prepare("
        INSERT INTO reviews (user_id, company_id, rating, title, comment, status, created_at)
        VALUES (?, ?, ?, ?, ?, 0, NOW())
    ");

    $stmt->execute([
        $_SESSION['user_id'],
        $company_id,
        $rating,
        $title,
        $comment
    ]);

    $review_id = $conn->lastInsertId();

    /* =====================================================
       MEDIA UPLOAD (R2)
    ===================================================== */
    if (!empty($_FILES['media']['name'][0])) {

        foreach ($_FILES['media']['tmp_name'] as $key => $tmp_name) {

            $file_tmp  = $_FILES['media']['tmp_name'][$key];
            $file_name = $_FILES['media']['name'][$key];

            if (!$file_tmp || !is_uploaded_file($file_tmp)) {
                continue;
            }

            $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            $new_name = 'review_' . $review_id . '_' . time() . '_' . $key . '.' . $ext;

            $r2_url = uploadToR2($file_tmp, $new_name);

            if (!$r2_url) {
                continue;
            }

            $media_type = str_starts_with(mime_content_type($file_tmp), 'video')
                ? 'video'
                : 'image';

            /* DB KAYIT */
            $mediaInsert = $conn->prepare("
                INSERT INTO review_media (review_id, media_type, media_url, created_at)
                VALUES (?, ?, ?, NOW())
            ");

            $mediaInsert->execute([
                $review_id,
                $media_type,
                $r2_url
            ]);
        }
    }

    /* =====================================================
       NOTIFICATIONS 
    ===================================================== */
    $userStmt = $conn->prepare("SELECT name FROM users WHERE id = ?");
    $userStmt->execute([$_SESSION['user_id']]);
    $user = $userStmt->fetch(PDO::FETCH_ASSOC);
    $userName = $user ? $user['name'] : 'Bir kullanıcı';

    $companyStmt = $conn->prepare("SELECT name FROM companies WHERE id = ?");
    $companyStmt->execute([$company_id]);
    $companyData = $companyStmt->fetch(PDO::FETCH_ASSOC);
    $companyName = $companyData ? $companyData['name'] : 'İşletme';

    $conn->prepare("
        INSERT INTO admin_notifications (title, content, created_at, is_read)
        VALUES (?, ?, NOW(), 0)
    ")->execute([
        "Yeni inceleme",
        $userName . " isimli kullanıcı " . $companyName . " işletmesine bir inceleme bıraktı."
    ]);

    $conn->prepare("
        INSERT INTO company_notifications (company_id, title, content, created_at, is_read)
        VALUES (?, ?, ?, NOW(), 0)
    ")->execute([
        $company_id,
        "Yeni inceleme",
        $userName . " isimli kullanıcı işletmene bir inceleme bıraktı."
    ]);

    /* =====================================================
       AUTO REPLY 
    ===================================================== */
    $autoStmt = $conn->prepare("
        SELECT auto_reply_enabled, auto_reply_message
        FROM companies
        WHERE id = ?
    ");
    $autoStmt->execute([$company_id]);
    $auto = $autoStmt->fetch(PDO::FETCH_ASSOC);

    if ((int)($auto['auto_reply_enabled'] ?? 0) === 1 && !empty($auto['auto_reply_message'])) {

        $messages = json_decode($auto['auto_reply_message'], true);
        $starKey  = (string)$rating;

        if (isset($messages[$starKey]) && trim($messages[$starKey]) !== '') {

            $conn->prepare("
                UPDATE reviews
                SET reply = ?, reply_type = 'auto', updated_at = NOW()
                WHERE id = ?
            ")->execute([
                trim($messages[$starKey]),
                $review_id
            ]);
        }
    }

    /* =====================================================
       RESPONSE
    ===================================================== */
    $stmt = $conn->prepare("SELECT slug FROM companies WHERE id = ?");
    $stmt->execute([$company_id]);
    $slug = $stmt->fetchColumn();

    echo json_encode([
        'success' => true,
        'slug' => $slug
    ]);
    exit;

} catch (Exception $e) {

    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
    exit;
}