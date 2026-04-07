<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$client_id = "com.puandeks.web.login";
$redirect_uri = "https://puandeks.com/login-apple-callback.php";

/*
Apple login parametreleri
name + email scope zorunlu
response_mode = form_post olmalı ki
Apple first login'de name bilgisini POST etsin
*/

$params = http_build_query([
    "response_type" => "code id_token",
    "response_mode" => "form_post",
    "client_id" => $client_id,
    "redirect_uri" => $redirect_uri,
    "scope" => "name email",
    "state" => bin2hex(random_bytes(16))
]);

$apple_login_url = "https://appleid.apple.com/auth/authorize?$params";

header("Location: $apple_login_url");
exit;