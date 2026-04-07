<?php
require_once('/home/puandeks.com/backend/config.php');
header('Content-Type: application/json');

try {

    $pdo->query("DELETE FROM admin_notifications");

    echo json_encode([
        'status' => 'success'
    ]);

} catch (Exception $e) {

    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}