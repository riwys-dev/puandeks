<?php
session_start();
header('Content-Type: application/json');

require_once('/home/puandeks.com/backend/config.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Geçersiz istek.']);
    exit;
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (!$email || !$password) {
    echo json_encode(['status' => 'error', 'message' => 'E-posta ve şifre zorunludur.']);
    exit;
}

# Email domain verify
if (!preg_match('/@puandeks\.com$/', $email)) {
    echo json_encode(['status' => 'error', 'message' => 'Sadece puandeks.com e-postası kullanılabilir.']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$admin) {
        echo json_encode(['status' => 'error', 'message' => 'E-posta bulunamadı.']);
        exit;
    }

    # Pass verify
    if (!password_verify($password, $admin['password'])) {
        echo json_encode(['status' => 'error', 'message' => 'Şifre hatalı.']);
        exit;
    }

    # Success
    $_SESSION['admin_id'] = $admin['id'];
    $_SESSION['admin_email'] = $admin['email'];
    $_SESSION['admin_menus'] = explode(',', $admin['menus'] ?? '');

    echo json_encode(['status' => 'success', 'message' => 'Giriş başarılı.']);
    exit;

} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Sunucu hatası.']);
    exit;
}
