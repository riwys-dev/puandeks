<?php

/* TIMEZONE */
date_default_timezone_set('Europe/Istanbul');

/* DATABASE */
$host = '127.0.0.1';
$dbname = 'puan_puandeks';
$username = 'puan_puandeks_user';
$password = 'u!Hhfh-J%dg684o@';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo = $conn;
} catch (PDOException $e) {
    die("Veritabanı bağlantı hatası: " . $e->getMessage());
}

/* ================================
   MAIL CONFIG
================================ */

define('MAIL_HOST', 'puandeks.com');
define('MAIL_USERNAME', 'no-reply@puandeks.com');
define('MAIL_PASSWORD', 'NoReply2025!Pu');
define('MAIL_PORT', 465);
define('MAIL_ENCRYPTION', 'ssl');
define('MAIL_FROM_ADDRESS', 'no-reply@puandeks.com');
define('MAIL_FROM_NAME', 'Puandeks');

/* ADMIN & LEGAL */
define('MAIL_ADMIN_ADDRESS', 'info@puandeks.com');
define('MAIL_LEGAL_ADDRESS', 'destek@puandeks.com');

/* ================================
   NETGSM CONFIG
================================ */

define('NETGSM_USERNAME', '2129091794');
define('NETGSM_PASSWORD', '5F3$6C6');
define('NETGSM_ORIGINATOR', 'PUANDEKS');
define('NETGSM_URL', 'https://api.netgsm.com.tr/sms/rest/v2/otp');

/* ================================
   BREVO CONFIG
================================ */
define('BREVO_SMTP_HOST', 'smtp-relay.brevo.com');
define('BREVO_SMTP_PORT', 587);
define('BREVO_SMTP_USER', '93ce16001@smtp-brevo.com');
define('BREVO_SMTP_PASS', 'GERÇEK_SMTP_PASSWORD');
define('BREVO_SMTP_ENCRYPTION', 'tls');

/* ================================
   CLOUDFLARE R2 CONFIG (FINAL)
================================ */

/* S3 endpoint */
define('R2_ENDPOINT', 'https://00cf661b070045cff8ef794074009eb6.r2.cloudflarestorage.com');

/* Bucket name */
define('R2_BUCKET', 'puandeks-media');

/* Public  domain  */
define('R2_PUBLIC_URL', 'https://media.puandeks.com');

/* Access  */
define('R2_ACCESS_KEY', '3755659244e36cb33ae6235ff54ad7b8');
define('R2_SECRET_KEY', 'a3bfeb97e64884302955ff83db8e4811acee50b82ece3ac22cc63c0679b3307f');

?>