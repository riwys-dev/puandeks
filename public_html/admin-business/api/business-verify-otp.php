<?php
session_start();
header('Content-Type: application/json');

require_once('/home/puandeks.com/backend/config.php');

ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Geçersiz istek.']);
    exit;
}

$otp = trim($_POST['otp'] ?? '');

if ($otp === '') {
    echo json_encode(['status' => 'error', 'message' => 'Kod girilmedi.']);
    exit;
}

// OTP session kontrol
if (!isset($_SESSION['otp_company_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Oturum bulunamadı.']);
    exit;
}

$company_id = $_SESSION['otp_company_id'];

try {

    $stmt = $pdo->prepare("
        SELECT id, otp_code, otp_expiry 
        FROM companies 
        WHERE id = ?
        LIMIT 1
    ");
    $stmt->execute([$company_id]);
    $company = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$company) {
        echo json_encode(['status' => 'error', 'message' => 'İşletme bulunamadı.']);
        exit;
    }

    // süre kontrol
    if ($company['otp_expiry'] < date('Y-m-d H:i:s')) {
        echo json_encode(['status' => 'error', 'message' => 'Kod süresi doldu.']);
        exit;
    }

    // kod kontrol
    if ($company['otp_code'] != $otp) {
        echo json_encode(['status' => 'error', 'message' => 'Kod hatalı.']);
        exit;
    }

    // =========================
    // BAŞARILI OTP
    // =========================

    // kullanıcı giriş
    $_SESSION['company_id'] = $company['id'];
    $_SESSION['role'] = 'business';

    // phone verified + otp temizle
    $clear = $pdo->prepare("
        UPDATE companies 
        SET 
            phone_verified = 1,
            otp_code = NULL, 
            otp_expiry = NULL 
        WHERE id = ?
    ");
    $clear->execute([$company_id]);

    // geçici session sil
    unset($_SESSION['otp_company_id']);

    echo json_encode([
        'status' => 'success'
    ]);
    exit;

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Sunucu hatası'
    ]);
    exit;
}