<?php
require_once '/home/puandeks.com/backend/config.php';

// Oturumu tamamen sonlandır
session_unset();
session_destroy();

// Oturum çerezini de temizle (güvenli kapanış)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Ana sayfaya yönlendir
header("Location: https://puandeks.com/");
exit;
