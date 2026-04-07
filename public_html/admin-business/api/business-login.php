<?php
session_start();
header('Content-Type: application/json');

require_once('/home/puandeks.com/backend/config.php');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Geçersiz istek.']);
    exit;
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($email === '' || $password === '') {
    echo json_encode(['status' => 'error', 'message' => 'E-posta ve şifre zorunludur.']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT id, name, slug, email_verified, password, phone, phone_verified 
        FROM companies 
        WHERE email = :email 
        LIMIT 1
    ");
    $stmt->execute(['email' => $email]);
    $company = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$company) {
        echo json_encode(['status' => 'error', 'message' => 'E-posta bulunamadı.']);
        exit;
    }

    // email doğrulama kontrolü
    if ($company['email_verified'] != 1) {
        echo json_encode(['status' => 'error', 'message' => 'Lütfen önce e-posta adresinizi doğrulayın.']);
        exit;
    }

    // şifre kontrolü
    if (!password_verify($password, $company['password'])) {
        echo json_encode(['status' => 'error', 'message' => 'Şifre hatalı.']);
        exit;
    }

    // =========================
    // PHONE VERIFIED = 1 → DIRECT LOGIN
    // =========================
    if ((int)$company['phone_verified'] === 1) {

        $_SESSION['company_id'] = $company['id'];

        $_SESSION['role'] = 'business';   // Role

        echo json_encode([
            'status' => 'success'
        ]);
        exit;
    }

    // =========================
    // PHONE VERIFIED = 0 → OTP FLOW
    // =========================

    // OTP üret
    $otp = random_int(100000, 999999);
    $expiry = date('Y-m-d H:i:s', strtotime('+3 minutes'));

    // DB update
    $update = $pdo->prepare("
        UPDATE companies 
        SET otp_code = ?, 
            otp_expiry = ?, 
            otp_last_sent_at = NOW()
        WHERE id = ?
    ");
    $update->execute([$otp, $expiry, $company['id']]);

    // SMS gönder
    $message = "Puandeks giriş kodunuz: $otp";

    $payload = json_encode([
        "msgheader" => NETGSM_ORIGINATOR,
        "appname"   => "puandeks",
        "msg"       => $message,
        "no"        => $company['phone']
    ]);

    $ch = curl_init(NETGSM_URL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
    curl_setopt($ch, CURLOPT_USERPWD, NETGSM_USERNAME . ":" . NETGSM_PASSWORD);

    curl_exec($ch);
    curl_close($ch);

    // geçici session
    $_SESSION['otp_company_id'] = $company['id'];

    echo json_encode([
        'status' => 'otp_required'
    ]);
    exit;

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>