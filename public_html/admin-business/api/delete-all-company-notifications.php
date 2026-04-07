<?php
session_start();
require_once('/home/puandeks.com/backend/config.php');

$company_id = $_SESSION['company_id'] ?? null;

if (!$company_id) {
    exit('ERR');
}

try {
    $stmt = $pdo->prepare("DELETE FROM company_notifications WHERE company_id = ?");
    $stmt->execute([$company_id]);

    echo 'OK';
} catch (PDOException $e) {
    echo 'ERR';
}