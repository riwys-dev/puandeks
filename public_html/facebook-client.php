<?php
session_start();

$fb_app_id = '1281570340143741';
$redirect_uri = urlencode('https://puandeks.com/login-facebook-callback.php');

$state = bin2hex(random_bytes(16));
$_SESSION['fb_state'] = $state;

$fb_login_url = "https://www.facebook.com/v18.0/dialog/oauth?client_id={$fb_app_id}&redirect_uri={$redirect_uri}&state={$state}&scope=email";

header("Location: $fb_login_url");
exit;