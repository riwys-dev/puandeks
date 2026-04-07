<?php
session_start();
header('Content-Type: application/json');

// Oturum kontrolü
if (!isset($_SESSION['company_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Oturum bulunamadı.']);
    exit;
}

require_once('/home/puandeks.com/backend/config.php');

$company_id = $_SESSION['company_id'];

try {
    // Toplam onaylı yorum
    $stmt1 = $conn->prepare("SELECT COUNT(*) FROM reviews WHERE company_id = :company_id AND status = 'approved'");
    $stmt1->execute(['company_id' => $company_id]);
    $total_reviews = (int)$stmt1->fetchColumn();

    // Yanıtlanmış yorum
    $stmt2 = $conn->prepare("SELECT COUNT(*) FROM reviews WHERE company_id = :company_id AND status = 'approved' AND company_reply IS NOT NULL");
    $stmt2->execute(['company_id' => $company_id]);
    $replied_reviews = (int)$stmt2->fetchColumn();

    // Ortalama puan
    $stmt3 = $conn->prepare("SELECT AVG(score) FROM reviews WHERE company_id = :company_id AND status = 'approved'");
    $stmt3->execute(['company_id' => $company_id]);
    $average_score = round((float)$stmt3->fetchColumn(), 1);

    echo json_encode([
        'status' => 'success',
        'total_reviews' => $total_reviews,
        'replied_reviews' => $replied_reviews,
        'average_score' => $average_score
    ]);
    exit;

} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Veritabanı hatası.']);
    exit;
}
