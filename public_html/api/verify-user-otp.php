<?php
require_once('/home/puandeks.com/backend/config.php');
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Oturum bulunamadı."]);
    exit;
}

$userId = $_SESSION['user_id'];
$inputOtp = trim($_POST['otp'] ?? '');

if (strlen($inputOtp) != 6 || !ctype_digit($inputOtp)) {
    echo json_encode(["success" => false, "message" => "Geçersiz kod."]);
    exit;
}

// Get User
$stmt = $pdo->prepare("
    SELECT otp_code, otp_expiry 
    FROM users 
    WHERE id = ?
");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || empty($user['otp_code'])) {
    echo json_encode(["success" => false, "message" => "Kod bulunamadı."]);
    exit;
}

// Duration
if (strtotime($user['otp_expiry']) < time()) {
    echo json_encode(["success" => false, "message" => "Kod süresi doldu."]);
    exit;
}

// Code = control
if ($user['otp_code'] !== $inputOtp) {
    echo json_encode(["success" => false, "message" => "Kod yanlış."]);
    exit;
}

// verify success
$stmt = $pdo->prepare("
    UPDATE users 
    SET phone = pending_phone,
        phone_prefix = pending_phone_prefix,
        pending_phone = NULL,
        pending_phone_prefix = NULL,
        phone_verified = 1,
        otp_code = NULL,
        otp_expiry = NULL,
        otp_last_sent_at = NULL
    WHERE id = ?
");
$stmt->execute([$userId]);

echo json_encode(["success" => true]);