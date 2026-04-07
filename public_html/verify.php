<?php
require_once('/home/puandeks.com/backend/config.php');

$token = trim($_GET['token'] ?? '');

if (empty($token)) {
    die("<h2 style='font-family:Arial;text-align:center;margin-top:100px;color:#c0392b;'>Geçersiz bağlantı.</h2>");
}

try {
    $stmt = $conn->prepare("SELECT id, verified, email_verified FROM users WHERE verification_token = :token LIMIT 1");
    $stmt->execute([':token' => $token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die("<h2 style='font-family:Arial;text-align:center;margin-top:100px;color:#c0392b;'>Bağlantı geçersiz veya süresi dolmuş.</h2>");
    }

    if ($user['verified'] == 1 && $user['email_verified'] == 1) {
        header("Location: https://puandeks.com/login?verified=1");
        exit;
    }

    $update = $conn->prepare("
        UPDATE users 
        SET verified = 1, email_verified = 1, verification_token = NULL 
        WHERE id = :id
    ");
    $update->execute([':id' => $user['id']]);

    header("Location: https://puandeks.com/login?verified=1");
    exit;

} catch (PDOException $e) {
    echo "<h2 style='font-family:Arial;text-align:center;margin-top:100px;color:#c0392b;'>Bir hata oluştu: " . htmlspecialchars($e->getMessage()) . "</h2>";
    exit;
}
?>
