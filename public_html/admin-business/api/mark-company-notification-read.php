<?php
session_start();
require_once('/home/puandeks.com/backend/config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $company_id = $_SESSION['company_id'] ?? null;

    if (!$id || !$company_id) {
        http_response_code(400);
        echo 'Geçersiz veri';
        exit;
    }

    try {
        $stmt = $pdo->prepare("UPDATE company_notifications SET is_read = 1 WHERE id = ? AND company_id = ?");
        $stmt->execute([$id, $company_id]);

        if ($stmt->rowCount() > 0) {
            echo 'OK';
        } else {
            http_response_code(403);
            echo 'Yetkisiz işlem';
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo 'Hata: ' . $e->getMessage();
    }
} else {
    http_response_code(405);
    echo 'Geçersiz istek';
}
