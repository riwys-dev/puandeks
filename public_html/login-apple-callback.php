<?php
require_once('/home/puandeks.com/backend/config.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_POST['id_token'])) {
    header("Location: /login");
    exit;
}

$id_token = $_POST['id_token'];

/* JWT parse */
$parts = explode('.', $id_token);

if (count($parts) !== 3) {
    header("Location: /login");
    exit;
}

/* Base64 URL decode */
$payload = json_decode(
    base64_decode(str_replace(['-','_'], ['+','/'], $parts[1])),
    true
);

/* Apple token güvenlik kontrolü */
if (!isset($payload['iss']) || $payload['iss'] !== 'https://appleid.apple.com') {
    header("Location: /login");
    exit;
}

if (!isset($payload['aud']) || $payload['aud'] !== 'com.puandeks.web.login') {
    header("Location: /login");
    exit;
}

if (!$payload) {
    header("Location: /login");
    exit;
}

$email = $payload['email'] ?? null;
$apple_id = $payload['sub'] ?? null;

if (!$apple_id) {
    header("Location: /login");
    exit;
}

/*
Apple name sadece ilk login'de gelir
POST user objesinden okuyacağız
*/
$name = null;
$surname = null;

if (isset($_POST['user'])) {

    $appleUser = json_decode($_POST['user'], true);

    if (isset($appleUser['name']['firstName'])) {
        $name = trim($appleUser['name']['firstName']);
    }

    if (isset($appleUser['name']['lastName'])) {
        $surname = trim($appleUser['name']['lastName']);
    }
}

/*
Apple name gelmezse fallback
*/
if (!$name) {
    $name = ucfirst(explode('@', $email)[0]);
}

if (!$surname) {
    $surname = "AppleID";
}

/* kullanıcı var mı */
$stmt = $conn->prepare("
SELECT id,name,email,role,status,login_source
FROM users
WHERE apple_sub = ?
LIMIT 1
");

$stmt->execute([$apple_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);


if ($user) {

    /* farklı login yöntemi kontrolü */
    if ($user['login_source'] !== 'apple') {

        $_SESSION['login_error'] = "Bu e-posta adresi farklı bir giriş yöntemi ile kayıtlı.";

        header("Location: /login");
        exit;
    }

    session_regenerate_id(true);

    $_SESSION['user_id'] = (int)$user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role'] = $user['role'];

    $update = $conn->prepare("
    UPDATE users
    SET last_login = NOW(), apple_verified = 1
    WHERE id = ?
    ");

    $update->execute([$user['id']]);

} else {

    $insert = $conn->prepare("
    INSERT INTO users
    (name,surname,email,login_source,provider_id,apple_sub,email_verified,apple_verified,role,status,created_at)
    VALUES
    (?, ?, ?, 'apple', ?, 1, 1, 'user', 'active', NOW())
    ");

    $insert->execute([$name, $surname, $email, $apple_id, $apple_id]);

        $user_id = $conn->lastInsertId();

        /* Apple kullanıcı isim kontrolü */
        if ($surname === "AppleID") {

            $_SESSION['apple_complete_profile'] = $user_id;

            header("Location: https://puandeks.com/apple-user-name");
            exit;
        }

        session_regenerate_id(true);

        $_SESSION['user_id'] = (int)$user_id;
        $_SESSION['user_name'] = $name;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_role'] = 'user';
        }

header("Location: https://puandeks.com");
exit;