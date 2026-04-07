<?php
session_start();
header("Content-Type: application/json; charset=UTF-8");

if (!isset($_SESSION['admin_id'])) {
    echo json_encode([
        "success" => false,
        "error" => "Not logged in"
    ]);
    exit;
}

require_once('/home/puandeks.com/backend/config.php');

$admin_id = $_SESSION['admin_id'];

// Mevcut admin bilgilerini çek
$stmt = $pdo->prepare("SELECT * FROM admin_users WHERE id = ?");
$stmt->execute([$admin_id]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$admin) {
    echo json_encode([
        "success" => false,
        "error" => "Admin not found"
    ]);
    exit;
}

/* ============================================================
   1) FULL NAME GÜNCELLE
   ============================================================ */
$full_name = isset($_POST['admin_name']) ? trim($_POST['admin_name']) : $admin['full_name'];

/* ============================================================
   2) AVATAR YÜKLEME
   ============================================================ */
$avatar_filename = $admin['avatar']; // mevcut avatar

if (isset($_FILES['admin_avatar']) && $_FILES['admin_avatar']['error'] === UPLOAD_ERR_OK) {

    $allowed = ['image/jpeg', 'image/jpg', 'image/png'];
    $mime = $_FILES['admin_avatar']['type'];

    if (!in_array($mime, $allowed)) {
        echo json_encode([
            "success" => false,
            "error"   => "Yalnızca JPG veya PNG yükleyebilirsiniz."
        ]);
        exit;
    }

    $upload_dir = "/home/puandeks.com/public_html/uploads/admin/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // webp dosya adı
    $new_name = "admin_" . $admin_id . "_" . time() . ".webp";
    $target = $upload_dir . $new_name;

    // resmi GD kütüphanesi ile aç
    if ($mime === "image/png") {
        $img = imagecreatefrompng($_FILES['admin_avatar']['tmp_name']);
    } else {
        $img = imagecreatefromjpeg($_FILES['admin_avatar']['tmp_name']);
    }

    // webp olarak kaydet (kalite 80)
    if (!imagewebp($img, $target, 80)) {
        echo json_encode([
            "success" => false,
            "error"   => "WebP dönüştürme başarısız."
        ]);
        exit;
    }

    imagedestroy($img);
    // eski avatarı sil
    if (!empty($admin['avatar'])) {
        $oldPath = $upload_dir . $admin['avatar'];
        if (file_exists($oldPath)) {
            unlink($oldPath);
        }
    }

    // DB'ye kayıt
    $avatar_filename = $new_name;
}


/* ============================================================
   3) ŞİFRE GÜNCELLEME (Eski şifre doğrulama)
   ============================================================ */
$old_password = isset($_POST['old_password']) ? $_POST['old_password'] : "";
$new_password = isset($_POST['new_password']) ? $_POST['new_password'] : "";
$hashed_password = $admin['password']; // mevcut hash

if (!empty($old_password) || !empty($new_password)) {

    if (empty($old_password) || empty($new_password)) {
        echo json_encode([
            "success" => false,
            "error"   => "Old and new password required"
        ]);
        exit;
    }

    // Eski şifre doğru mu?
    if (!password_verify($old_password, $admin['password'])) {
        echo json_encode([
            "success" => false,
            "error"   => "Old password is incorrect"
        ]);
        exit;
    }

    // Yeni şifre hashle
    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
}

/* ============================================================
   4) VERITABANI GÜNCELLE
   ============================================================ */
$update = $pdo->prepare("
    UPDATE admin_users 
    SET full_name = ?, avatar = ?, password = ?
    WHERE id = ?
");

$ok = $update->execute([
    $full_name,
    $avatar_filename,
    $hashed_password,
    $admin_id
]);

if ($ok) {
    echo json_encode([
        "success" => true,
        "message" => "Admin updated successfully"
    ]);
} else {
    echo json_encode([
        "success" => false,
        "error"   => "Update failed"
    ]);
}
exit;
