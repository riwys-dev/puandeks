<?php
session_start();
require_once('/home/puandeks.com/backend/config.php');

$token = $_GET['token'] ?? '';

if (!$token) {
    showError("Geçersiz istek.");
    exit;
}

try {
    // Token kontrolü
    $stmt = $pdo->prepare("
        SELECT id, email_verified 
        FROM companies 
        WHERE verification_token = ? 
        LIMIT 1
    ");
    $stmt->execute([$token]);
    $company = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$company) {
        showError("Geçersiz veya süresi dolmuş doğrulama bağlantısı.");
        exit;
    }

    // Zaten doğrulandıysa → login
    if ($company['email_verified']) {
        header("Location: https://business.puandeks.com/login");
        exit;
    }

    // Claim doğrulaması → email_verified = 1
    $update = $pdo->prepare("
        UPDATE companies
        SET email_verified = 1,
            verification_token = NULL
        WHERE id = ?
    ");
    $update->execute([$company['id']]);

    // İşlem tamamlandı → login sayfasına yönlendir
    header("Location: https://business.puandeks.com/login");
    exit;

} catch (Exception $e) {
    showError("Bir hata oluştu: " . htmlspecialchars($e->getMessage()));
    exit;
}


/* ==========================================
  Error Screen
========================================== */
function showError($message) {
    echo '
    <!DOCTYPE html>
    <html lang="tr">
    <head>
        <meta charset="UTF-8">
        <title>Doğrulama Hatası</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <style>
            body {
                font-family: Arial, sans-serif;
                background:#fafafa;
                display:flex;
                justify-content:center;
                align-items:center;
                height:100vh;
                margin:0;
            }
            .box {
                background:#fff;
                padding:30px;
                border-radius:12px;
                box-shadow:0 3px 10px rgba(0,0,0,0.1);
                max-width:400px;
                width:100%;
                text-align:center;
            }
            h2 {
                font-size:20px;
                margin-bottom:10px;
                color:#d9534f;
            }
            p {
                color:#555;
                font-size:15px;
            }
            a {
                display:inline-block;
                margin-top:18px;
                background:#0C7C59;
                color:white;
                padding:10px 18px;
                border-radius:6px;
                text-decoration:none;
            }
        </style>
    </head>
    <body>
        <div class="box">
            <h2>Doğrulama yapılamadı</h2>
            <p>' . $message . '</p>
            <a href="https://business.puandeks.com/login">Giriş Sayfasına Dön</a>
        </div>
    </body>
    </html>';
}
?>
