<?php
declare(strict_types=1);
header('Content-Type: application/json');
header('Cache-Control: no-store');

require_once('/home/puandeks.com/backend/config.php');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Only POST']);
        exit;
    }

    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);

    if (!is_array($data)) {
        echo json_encode(['success' => false, 'message' => 'Invalid JSON']);
        exit;
    }

    $visitor_id = isset($data['visitor_id']) ? trim((string)$data['visitor_id']) : '';
    $company_id = isset($data['company_id']) ? (int)$data['company_id'] : 0;

    // Basit doğrulamalar
    if ($visitor_id === '' || !preg_match('/^[A-Za-z0-9_-]{8,64}$/', $visitor_id)) {
        echo json_encode(['success' => false, 'message' => 'Geçersiz visitor_id']);
        exit;
    }
    if ($company_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Geçersiz company_id']);
        exit;
    }

    // Şirket doğrula
    $chk = $conn->prepare("SELECT 1 FROM companies WHERE id = ? LIMIT 1");
    $chk->execute([$company_id]);
    if (!$chk->fetchColumn()) {
        echo json_encode(['success' => false, 'message' => 'Şirket bulunamadı']);
        exit;
    }

    // Var mı kontrol et → insert/update ayrımı (debug için action bilgisi)
    $sel = $conn->prepare("SELECT 1 FROM visitor_activity WHERE visitor_id = ? AND company_id = ? LIMIT 1");
    $sel->execute([$visitor_id, $company_id]);
    $exists = (bool)$sel->fetchColumn();

    if ($exists) {
        $upd = $conn->prepare("UPDATE visitor_activity SET viewed_at = NOW() WHERE visitor_id = ? AND company_id = ?");
        $upd->execute([$visitor_id, $company_id]);
        $action = 'update';
        $affected = $upd->rowCount();
        $insert_id = null;
    } else {
        $ins = $conn->prepare("INSERT INTO visitor_activity (visitor_id, company_id, viewed_at) VALUES (?, ?, NOW())");
        $ins->execute([$visitor_id, $company_id]);
        $action = 'insert';
        $affected = $ins->rowCount();
        $insert_id = $conn->lastInsertId();
    }

    // Ziyaretçi toplam kaydı (debug için)
    $tot = $conn->prepare("SELECT COUNT(*) FROM visitor_activity WHERE visitor_id = ?");
    $tot->execute([$visitor_id]);
    $total_for_visitor = (int)$tot->fetchColumn();

    echo json_encode([
        'success' => true,
        'action' => $action,
        'affected' => $affected,
        'insert_id' => $insert_id,
        'visitor_id' => $visitor_id,
        'company_id' => $company_id,
        'total_for_visitor' => $total_for_visitor,
        'message' => 'OK'
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error', 'error' => $e->getMessage()]);
}
