<?php
session_start();

require_once __DIR__ . '/../backend/config.php';
require_once __DIR__ . '/facebook/FacebookClientLite.php';

if (!isset($_GET['code'])) {
    die('Facebook authorization code alınamadı.');
}

if (!isset($_GET['state']) || $_GET['state'] !== ($_SESSION['fb_state'] ?? null)) {
    die('State dorulaması başarısız.');
}

$code = $_GET['code'];

$fb_app_id = '1281570340143741';
$fb_app_secret = '78a3fbbc943fe1748fb0f24d0d23c096';
$redirect_uri = 'https://puandeks.com/login-facebook-callback.php';

/* FACEBOOK CLIENT */
$fbClient = new FacebookClientLite($fb_app_id, $fb_app_secret, $redirect_uri);

$accessToken = $fbClient->getAccessToken($code);
if (!$accessToken) {
    die('Access token alınamadı.');
}

$user_data = $fbClient->getUser($accessToken);
if (!$user_data || !isset($user_data['id'])) {
    die('Facebook user bilgisi alınamadı.');
}

$facebook_id = $user_data['id'];

$fullName = $user_data['name'] ?? '';
$nameParts = explode(' ', $fullName);

$firstName = $nameParts[0] ?? '';
$lastName = isset($nameParts[1]) ? implode(' ', array_slice($nameParts, 1)) : '';

$email = $user_data['email'] ?? null;
$profile_image = $user_data['picture']['data']['url'] ?? null;

/* ================================
  Provider control
================================ */
$stmt = $pdo->prepare("SELECT id,role FROM users WHERE provider_id = ? AND login_source = 'facebook' LIMIT 1");
$stmt->execute([$facebook_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {

    session_regenerate_id(true);

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_role'] = $user['role'];

    header("Location: https://puandeks.com");
    exit;
}

/* ================================
   Email CRASH control
================================ */
if ($email) {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingUser) {

        $_SESSION['login_error'] = "Bu e-posta adresi farklı bir giriş yöntemi ile kayıtlı.";

        header("Location: /login");
        exit;
    }
}

/* ================================
   Create new USER 
================================ */
if ($email) {

    $insert = $pdo->prepare("
        INSERT INTO users 
        (name, surname, email, provider_id, login_source, facebook_verified, email_verified, profile_image, role, created_at)
        VALUES (?, ?, ?, ?, 'facebook', 1, 1, ?, 'user', NOW())
    ");

    $insert->execute([
    $firstName,
    $lastName,
    $email,
    $facebook_id,
    $profile_image
    ]);

    $newUserId = $pdo->lastInsertId();

    $_SESSION['user_id'] = $newUserId;
    $_SESSION['user_role'] = 'user';

    header("Location: https://puandeks.com");
    exit;
}

die('Email bilgisi alınamadı.');