<?php
require_once('/home/puandeks.com/backend/config.php');

$token = $_GET['token'] ?? '';

if ($token === '') {
    echo "Geersiz doğrulama bağlantısı.";
    exit;
}

// token kontrol
$stmt = $pdo->prepare("
    SELECT id 
    FROM companies 
    WHERE verification_token = ? 
    LIMIT 1
");
$stmt->execute([$token]);
$company = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$company) {
    echo "Doğrulama linki geçersiz veya süresi dolmuş.";
    exit;
}

// e-mail doğrula
$stmt = $pdo->prepare("
    UPDATE companies 
    SET email_verified = 1,
        verification_token = NULL
    WHERE id = ?
");
$stmt->execute([$company['id']]);

// login sayfasına yönlendir
header("Location: /login?verified=1");
exit;
