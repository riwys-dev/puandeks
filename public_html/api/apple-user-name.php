<?php

require_once('/home/puandeks.com/backend/config.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

/* session kontrol */
if (!isset($_SESSION['apple_complete_profile'])) {

    echo json_encode([
        "success" => false
    ]);
    exit;
}

$user_id = (int)$_SESSION['apple_complete_profile'];

/* inputlar */
$name = trim($_POST['name'] ?? '');
$surname = trim($_POST['surname'] ?? '');

if ($name === '' || $surname === '') {

    echo json_encode([
        "success" => false
    ]);
    exit;
}

/* db update */
$stmt = $conn->prepare("
UPDATE users
SET name = ?, surname = ?
WHERE id = ?
");

$stmt->execute([$name, $surname, $user_id]);

/* kullanıcıyı login yap */
$stmt = $conn->prepare("
SELECT id,name,email,role
FROM users
WHERE id = ?
LIMIT 1
");

$stmt->execute([$user_id]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

session_regenerate_id(true);

$_SESSION['user_id'] = (int)$user['id'];
$_SESSION['user_name'] = $user['name'];
$_SESSION['user_email'] = $user['email'];
$_SESSION['user_role'] = $user['role'];

/* artık bu sessiona gerek yok */
unset($_SESSION['apple_complete_profile']);

echo json_encode([
    "success" => true
]);
exit;