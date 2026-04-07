<?php
if (!isset($_GET['token']) || empty($_GET['token'])) {
    die("Geçersiz doğrulama bağlantısı.");
}

$token = $_GET['token'];

$apiUrl = "https://puandeks.com/backend/api/business-verify.php";

$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, ['token' => $token]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);

if ($result && $result['status'] === "success") {

    // .php YOK — DOĞRU URL
    header("Location: https://business.puandeks.com/register-complete?token=" . $token);
    exit;

} else {
    echo "<h3 style='font-family:Arial; color:red; text-align:center; margin-top:40px;'>"
        . ($result['message'] ?? "Doğrulama başarısız oldu.")
        . "</h3>";
    exit;
}
?>
