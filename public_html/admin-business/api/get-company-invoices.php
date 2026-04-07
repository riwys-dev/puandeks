<?php
session_start();
require_once('/home/puandeks.com/backend/config.php');

header('Content-Type: application/json');

/* LOGIN kontrol */
if (!isset($_SESSION['company_id'])) {
    echo json_encode(['success' => false, 'message' => 'Yetkisiz']);
    exit;
}

$company_id = $_SESSION['company_id'];

$stmt = $pdo->prepare("
    SELECT 
        invoice_number,
        issue_date,
        file_path,
        payment_date
    FROM company_invoices
    WHERE company_id = ?
    ORDER BY id DESC
");

$stmt->execute([$company_id]);
$invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* PDF link ekle */
foreach ($invoices as &$inv) {
    $inv['pdf_url'] = $inv['file_path'] 
        ? 'https://puandeks.com' . $inv['file_path'] 
        : null;
}

echo json_encode([
    'success' => true,
    'data' => $invoices
]);