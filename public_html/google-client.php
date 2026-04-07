<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once '/home/puandeks.com/backend/config.php';
require_once __DIR__ . '/google/GoogleClientLite.php';

// Google OAuth yapılandırması
$client = new GoogleClientLite(
    '',
    '',
    'https://puandeks.com/login-google-callback.php'
);
return $client;