<?php
session_start();

$client = require_once __DIR__ . '/google-client.php';
require_once '/home/puandeks.com/backend/config.php';

if (!isset($_GET['code'])) {
    die("Google code parametresi yok.");
}

try {

    // Token al
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    if (isset($token['error'])) {
        die("Google oturum açma hatası.");
    }

    // Kullanıcı bilgisi al
    $userInfo = $client->getUserInfo($token['access_token']);
    if (empty($userInfo['email']) || empty($userInfo['id'])) {
        die("Google kullanıcı bilgisi eksik.");
    }

    $googleId = trim($userInfo['id']);
    $email = strtolower(trim($userInfo['email']));
    $fullName = trim($userInfo['name'] ?? '');
    $picture = $userInfo['picture'] ?? null;

    // Ad soyad ayır
    $nameParts = explode(' ', $fullName, 2);
    $name = $nameParts[0] ?? '';
    $surname = $nameParts[1] ?? '';

    /* =========================================
       1️⃣ PROVIDER KONTROLÜ (Google ID ile)
    ========================================= */

    $stmt = $conn->prepare("
        SELECT id, name, role 
        FROM users 
        WHERE provider_id = :provider_id 
        AND login_source = 'google'
        LIMIT 1
    ");
    $stmt->execute([':provider_id' => $googleId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Mevcut Google kullanıcısı → giriş yap
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $email;
        $_SESSION['user_role'] = $user['role'];

        header("Location: https://puandeks.com");
        exit;
    }

    /* =========================================
       2️⃣ EMAIL ÇAKIŞMA KONTROLÜ
    ========================================= */

    $emailCheck = $conn->prepare("
        SELECT id 
        FROM users 
        WHERE email = :email 
        LIMIT 1
    ");
    $emailCheck->execute([':email' => $email]);
    $existingEmail = $emailCheck->fetch(PDO::FETCH_ASSOC);

    if ($existingEmail) {

    $_SESSION['login_error'] = "Bu e-posta adresi farklı bir giriş yöntemi ile kayıtlı.";

    header("Location: /login");
    exit;
}
    /* =========================================
       3️⃣ YENİ GOOGLE KULLANICISI OLUŞTUR
    ========================================= */

    $insert = $conn->prepare("
        INSERT INTO users (
            name, surname, email,
            login_source, provider_id,
            profile_image,
            role,
            verified,
            status,
            email_verified,
            gmail_verified,
            created_at
        ) VALUES (
            :name, :surname, :email,
            'google', :provider_id,
            :profile_image,
            'user',
            1,
            'active',
            1,
            1,
            NOW()
        )
    ");

    $insert->execute([
        ':name' => $name,
        ':surname' => $surname,
        ':email' => $email,
        ':provider_id' => $googleId,
        ':profile_image' => $picture
    ]);

    $userId = $conn->lastInsertId();

    $_SESSION['user_id'] = $userId;
    $_SESSION['user_name'] = $name;
    $_SESSION['user_email'] = $email;
    $_SESSION['user_role'] = 'user';

    header("Location: https://puandeks.com");
    exit;

} catch (Exception $e) {
    die('Google giriş hatası.');
}