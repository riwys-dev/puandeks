<?php
session_start();
header("Content-Type: application/json");

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'user') {
    http_response_code(401);
    echo json_encode(["status" => "error"]);
    exit;
}

require_once('/home/puandeks.com/backend/config.php');

$pdo->beginTransaction();

try {

    // Kullanıcıyı soft delete yap
    $stmt = $pdo->prepare("
        UPDATE users 
        SET status = 'deleted', deleted_at = NOW() 
        WHERE id = ? AND status = 'active'
    ");
    $stmt->execute([$_SESSION['user_id']]);

    // Kullanıcının yorumlarını yayından kaldır
    $stmt = $pdo->prepare("
        UPDATE reviews 
        SET status = 0 
        WHERE user_id = ?
    ");
    $stmt->execute([$_SESSION['user_id']]);

    $pdo->commit();

    session_destroy();

    echo json_encode(["status" => "success"]);
    exit;

} catch (Exception $e) {

    $pdo->rollBack();

    http_response_code(500);
    echo json_encode(["status" => "error"]);
    exit;
}