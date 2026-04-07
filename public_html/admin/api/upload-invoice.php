<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once('/home/puandeks.com/backend/config.php');

header('Content-Type: application/json');

/* ADMIN kontrol */
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Yetkisiz']);
    exit;
}

/* POST kontrol */
if (
    empty($_POST['company_id']) ||
    empty($_POST['invoice_number']) ||
    empty($_POST['issue_date']) ||
    empty($_FILES['invoice_file'])
) {
    echo json_encode(['success' => false, 'message' => 'Eksik veri']);
    exit;
}

$company_id     = (int) $_POST['company_id'];
$invoice_number = trim($_POST['invoice_number']);
$issue_date     = $_POST['issue_date'];
$file           = $_FILES['invoice_file'];

/* Dosya kontrol */
if ($file['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'Dosya yüklenemedi', 'error' => $file['error']]);
    exit;
}

/* Sadece PDF */
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if ($ext !== 'pdf') {
    echo json_encode(['success' => false, 'message' => 'Sadece PDF yüklenebilir']);
    exit;
}

/* Upload path */
$uploadDir = '/home/puandeks.com/public_html/uploads/invoices/';

if (!is_dir($uploadDir)) {
    if (!mkdir($uploadDir, 0755, true)) {
        echo json_encode(['success' => false, 'message' => 'Klasör oluşturulamadı']);
        exit;
    }
}

if (!is_writable($uploadDir)) {
    echo json_encode(['success' => false, 'message' => 'Klasör yazılabilir değil']);
    exit;
}

/* Dosya adı */
$fileName = 'invoice_' . $company_id . '_' . time() . '.pdf';
$filePath = $uploadDir . $fileName;

/* Move */
if (!move_uploaded_file($file['tmp_name'], $filePath)) {
    echo json_encode([
        'success' => false,
        'message' => 'Dosya kaydedilemedi',
        'tmp' => $file['tmp_name'],
        'path' => $filePath
    ]);
    exit;
}

/* DB path */
$dbPath = '/uploads/invoices/' . $fileName;

/* INSERT */
try {

    $insert = $pdo->prepare("
        INSERT INTO company_invoices 
        (company_id, invoice_number, issue_date, file_path, payment_date, amount, due_date)
        VALUES (?, ?, ?, ?, NOW(), ?, ?)
    ");

        $insert->execute([
        $company_id,
        $invoice_number,
        $issue_date,
        $dbPath,
        0,
        $issue_date
    ]);
    
    echo json_encode(['success' => true]);
    exit;

} catch (PDOException $e) {

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
    exit;
}