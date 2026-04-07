<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Geçersiz istek.'
    ]);
    exit;
}

require_once('/home/puandeks.com/backend/config.php');
require_once('/home/puandeks.com/backend/helpers/mailer.php');

/* ================================
   INPUTS (yeni form name'lere göre)
================================ */

$first_name   = trim($_POST['first_name'] ?? '');
$last_name    = trim($_POST['last_name'] ?? '');
$email        = trim($_POST['email'] ?? '');
$phone        = trim($_POST['phone'] ?? '');
$website      = trim($_POST['website'] ?? '');
$company_name = trim($_POST['company_name'] ?? '');

/* ================================
   VALIDATION
================================ */

if (
    empty($first_name) ||
    empty($last_name) ||
    empty($email) ||
    empty($phone)
) {
    echo json_encode([
        'success' => false,
        'message' => 'Lütfen zorunlu alanları doldurun.'
    ]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'success' => false,
        'message' => 'Geçerli bir e-posta giriniz.'
    ]);
    exit;
}

/* ================================
   MAIL CONTENT
================================ */

$subject = "Yeni Affiliate Başvurusu - Puandeks";

$html = "
<div style='font-family:Arial; font-size:14px; color:#333;'>
    <h2>Yeni Partner Başvurusu</h2>
    <p><strong>İsim:</strong> {$first_name}</p>
    <p><strong>Soyisim:</strong> {$last_name}</p>
    <p><strong>E-posta:</strong> {$email}</p>
    <p><strong>Telefon:</strong> {$phone}</p>
    <p><strong>Web Sitesi:</strong> {$website}</p>
    <p><strong>Firma Adı:</strong> {$company_name}</p>
    <hr>
    <p>Bu başvuru business.puandeks.com affiliate formu üzerinden gönderilmiştir.</p>
</div>
";

/* ================================
   SEND TO ADMIN
================================ */

$mailSent = sendMail(MAIL_ADMIN_ADDRESS, $subject, $html);

if ($mailSent) {
    echo json_encode([
        'success' => true
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Mail gönderilirken hata oluştu.'
    ]);
}
