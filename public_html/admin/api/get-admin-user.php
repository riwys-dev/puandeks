<?php
session_start();

header("Content-Type: application/json; charset=UTF-8");

// Admin login kontrolü
if (!isset($_SESSION['admin_id'])) {
    echo json_encode([
        "success" => false,
        "error" => "Not logged in"
    ]);
    exit;
}

require_once('/home/puandeks.com/backend/config.php');

$admin_id = $_SESSION['admin_id'];

$stmt = $pdo->prepare("SELECT id, full_name, email, avatar, role FROM admin_users WHERE id = ?");
$stmt->execute([$admin_id]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$admin) {
    echo json_encode([
        "success" => false,
        "error" => "User not found"
    ]);
    exit;
}

// Avatar yolu
$avatar_url = "img/placeholder/admin-user.png";
if (!empty($admin['avatar'])) {
    $avatar_url = "uploads/admin/" . $admin['avatar'] . "?v=" . time();
}

echo json_encode([
    "success" => true,
    "data" => [
        "id"        => $admin['id'],
        "full_name" => $admin['full_name'],
        "email"     => $admin['email'],
        "role"      => $admin['role'],
        "avatar_url"=> $avatar_url
    ]
]);
exit;
