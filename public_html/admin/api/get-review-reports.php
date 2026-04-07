<?php
session_start();
require_once('/home/puandeks.com/backend/config.php');

header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false]);
    exit;
}

try {

    $stmt = $conn->prepare("
        SELECT 
            rr.id,
            rr.reason,
            rr.created_at,

            r.id AS review_id,
            u.name AS review_user_name,
            c.name AS company_name,

            ru.name AS reported_by_name

        FROM review_reports rr
        JOIN reviews r ON rr.review_id = r.id
        JOIN users u ON r.user_id = u.id
        JOIN companies c ON r.company_id = c.id
        JOIN users ru ON rr.reported_by_id = ru.id

        WHERE rr.reported_by_role = 'user'
        ORDER BY rr.created_at DESC
    ");

    $stmt->execute();
    $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => $reports
    ]);

} catch (Exception $e) {

    echo json_encode([
        'success' => false
    ]);
}
