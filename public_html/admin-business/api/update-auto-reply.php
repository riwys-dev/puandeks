<?php
session_start();
header("Content-Type: application/json; charset=UTF-8");

require_once('/home/puandeks.com/backend/config.php');

if (!isset($_SESSION['company_id'])) {
    echo json_encode(["status" => "error", "message" => "Oturum bulunamadı."]);
    exit;
}

$company_id = (int)$_SESSION['company_id'];

$data = json_decode(file_get_contents("php://input"), true);
if (!is_array($data)) {
    echo json_encode(["status" => "error", "message" => "Geçersiz veri."]);
    exit;
}

$enabled  = !empty($data['enabled']) ? 1 : 0;
$messages = $data['messages'] ?? [];

/*
  KURAL:
  - enabled = 0  → auto_reply_enabled = 0, auto_reply_message = NULL
  - enabled = 1  → 1–5 TAM DOLU değilse KAYIT YOK
*/

if ($enabled === 1) {
    if (
        !is_array($messages) ||
        count($messages) !== 5
    ) {
        echo json_encode([
            "status"  => "error",
            "message" => "Otomatik yanıt için 1–5 tüm alanlar dolu olmalı."
        ]);
        exit;
    }

    // Boş kontrolü
    foreach (['1','2','3','4','5'] as $k) {
        if (!isset($messages[$k]) || trim($messages[$k]) === '') {
            echo json_encode([
                "status"  => "error",
                "message" => "Otomatik yanıt için 1–5 tüm alanlar dolu olmalı."
            ]);
            exit;
        }
    }

    $json = json_encode($messages, JSON_UNESCAPED_UNICODE);

    try {
        $stmt = $pdo->prepare("
            UPDATE companies
            SET auto_reply_enabled = 1,
                auto_reply_message = ?
            WHERE id = ?
        ");
        $stmt->execute([$json, $company_id]);

        echo json_encode(["status" => "success"]);
        exit;

    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => "DB hatası"]);
        exit;
    }
}

/* enabled = 0 → HARD OFF */
try {
    $stmt = $pdo->prepare("
        UPDATE companies
        SET auto_reply_enabled = 0
        WHERE id = ?
    ");
    $stmt->execute([$company_id]);

    echo json_encode(["status" => "success"]);
    exit;

} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "DB hatası"]);
    exit;
}

