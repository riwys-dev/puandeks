<?php
require_once('/home/puandeks.com/backend/config.php');
header('Content-Type: application/json');

$statusMap = [
    'aktif'        => 'active',
    'süre_doldu'   => 'expired',
    'dondurulmuş'  => 'frozen'
];

$rawStatus = $_GET['status'] ?? null;
$autoRenew = $_GET['auto_renew'] ?? null;
$search    = trim($_GET['search'] ?? '');

$where = ['c.status = "approved"', 'cs.package_id IS NOT NULL']; 
$params = [];

/* STATUS FILTER  */
if ($rawStatus && isset($statusMap[strtolower($rawStatus)])) {
    $where[] = 'cs.status = :status';
    $params['status'] = $statusMap[strtolower($rawStatus)];
}

/* AUTO RENEW FILTER  */
if ($autoRenew === 'true') {
    $where[] = 'cs.auto_renew = 1';
}
if ($autoRenew === 'false') {
    $where[] = 'cs.auto_renew = 0';
}

/* SEARCH  */
if ($search !== '') {
    $where[] = 'c.name LIKE :search';
    $params['search'] = '%' . $search . '%';
}

$whereSql = implode(' AND ', $where);

/* LIMIT  */
$limitSql = ($search === '') ? 'LIMIT 10' : '';

try {
    $stmt = $pdo->prepare("
        SELECT 
            c.id,
            c.name,
            c.email,

            cs.package_type,
            cs.start_date AS subscription_start,
            cs.end_date AS subscription_end,
            cs.status AS subscription_status,
            cs.auto_renew,

            p.price_monthly AS payment_amount,

            ci.invoice_number AS invoice_no,
            ci.file_path

        FROM companies c

        LEFT JOIN company_subscriptions cs 
            ON cs.company_id = c.id

        LEFT JOIN packages p 
            ON p.id = cs.package_id

        LEFT JOIN company_invoices ci 
        ON ci.id = (
            SELECT id 
            FROM company_invoices 
            WHERE company_id = c.id 
            ORDER BY id DESC 
            LIMIT 1
        )

        WHERE $whereSql

        ORDER BY c.id DESC
        $limitSql
    ");

    $stmt->execute($params);
    $companies = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($companies as &$row) {

    if (!empty($row['file_path'])) {
        $row['pdf_url'] = 'https://puandeks.com' . $row['file_path'];
    } else {
        $row['pdf_url'] = null;
    }

}

    echo json_encode([
        'companies' => $companies
    ]);

} catch (Exception $e) {
    echo json_encode([
        'error' => $e->getMessage()
    ]);
}