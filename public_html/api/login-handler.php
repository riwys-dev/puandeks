<?php
require_once('/home/puandeks.com/backend/config.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'message' => 'Geçersiz istek yöntemi.'
    ]);
    exit;
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($email === '' || $password === '') {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'E-posta ve şifre gereklidir.'
    ]);
    exit;
}

try {
    $stmt = $conn->prepare("
        SELECT id, name, email, password, role, status, email_verified, login_source
        FROM users 
        WHERE email = ? 
        LIMIT 1
    ");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Geçersiz e-posta veya şifre.'
        ]);
        exit;
    }

    // Google account control
    if ($user['login_source'] === 'google') {
        echo json_encode([
            'status' => 'error',
            'message' => 'Bu hesap Google ile oluşturulmuştur. Lütfen Google ile giriş yapın.'
        ]);
        exit;
    }

    if (!password_verify($password, $user['password'])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Geçersiz e-posta veya şifre.'
        ]);
        exit;
    }

    if ($user['status'] !== 'active') {
        echo json_encode([
            'status' => 'error',
            'message' => 'Hesabınız aktif değil. Lütfen destek ekibiyle iletişime geçin.'
        ]);
        exit;
    }

    if ((int)$user['email_verified'] === 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Lütfen önce e-posta adresinizi doğrulayın.'
        ]);
        exit;
    }

    // Success
    session_regenerate_id(true);
    $_SESSION['user_id'] = (int)$user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role'] = $user['role'];

    $update = $conn->prepare("
        UPDATE users 
        SET last_login = NOW() 
        WHERE id = ?
    ");
    $update->execute([$user['id']]);

    if ($user['role'] === 'user') {
        $redirect = 'https://puandeks.com';
    } elseif ($user['role'] === 'business') {
        $redirect = '/business-admin';
    } elseif ($user['role'] === 'admin') {
        $redirect = '/admin';
    } else {
        $redirect = '/';
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'Giriş başarılı.',
        'redirect' => $redirect
    ]);
    exit;

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Veritabanı hatası.'
    ]);
    exit;
}
?>