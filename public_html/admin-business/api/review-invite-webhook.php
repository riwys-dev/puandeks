<?php
require_once('/home/puandeks.com/backend/config.php');

header('Content-Type: application/json');

// Allow only POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

// Read raw JSON body
$rawBody = file_get_contents('php://input');
$input = json_decode($rawBody, true);

if (!$input) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid JSON']);
    exit;
}

// Required fields
$company_id     = $input['company_id'] ?? null;
$order_id       = $input['order_id'] ?? null;
$customer_email = $input['customer_email'] ?? null;
$order_date     = $input['order_date'] ?? null;
$signature = $_SERVER['HTTP_X_PUANDEKS_SIGNATURE'] ?? null;

if (!$company_id || !$order_id || !$customer_email || !$order_date || !$signature) {
    http_response_code(422);
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
    exit;
}

// Fetch webhook secret
$stmtSecret = $pdo->prepare("SELECT webhook_secret FROM companies WHERE id = ?");
$stmtSecret->execute([$company_id]);
$company = $stmtSecret->fetch(PDO::FETCH_ASSOC);

if (!$company || !$company['webhook_secret']) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Invalid company or webhook secret']);
    exit;
}

$secret = $company['webhook_secret'];

// Verify HMAC using raw body
$generatedSignature = hash_hmac('sha256', $rawBody, $secret);

if (!hash_equals($generatedSignature, $signature)) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Invalid signature']);
    exit;
}

// Duplicate protection (company_id + order_id must be unique)
$stmtCheck = $pdo->prepare("
    SELECT id FROM review_invites 
    WHERE company_id = ? AND order_id = ?
    LIMIT 1
");
$stmtCheck->execute([$company_id, $order_id]);

if ($stmtCheck->fetch()) {
    http_response_code(409);
    echo json_encode(['status' => 'error', 'message' => 'Duplicate order_id']);
    exit;
}

// Get company invite delay
$stmtDelay = $pdo->prepare("SELECT invite_delay_days, invite_enabled FROM companies WHERE id = ?");
$stmtDelay->execute([$company_id]);
$companySettings = $stmtDelay->fetch(PDO::FETCH_ASSOC);

if (!$companySettings || !$companySettings['invite_enabled']) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Invites disabled for this company']);
    exit;
}

$delayDays = (int)$companySettings['invite_delay_days'];
$scheduled_at = date('Y-m-d H:i:s', strtotime($order_date . " +{$delayDays} days"));

// Frequency control
if ($companySettings['invite_frequency_days'] > 0) {

    $stmtFreq = $pdo->prepare("
        SELECT ri.sent_at 
        FROM review_invites ri
        INNER JOIN companies c ON c.id = ri.company_id
        WHERE ri.company_id = ?
        AND ri.customer_email = ?
        AND ri.invite_type = 'initial'
        AND ri.created_at >= DATE_SUB(NOW(), INTERVAL c.invite_frequency_days DAY)
        LIMIT 1
    ");
    $stmtFreq->execute([$company_id, $customer_email]);

    if ($stmtFreq->fetch()) {
        http_response_code(429);
        echo json_encode(['status' => 'error', 'message' => 'Frequency limit reached']);
        exit;
    }
}


// Generate secure token
$secureToken = bin2hex(random_bytes(32));

// Get reminder settings
$reminderEnabled = (int)$companySettings['reminder_enabled'];
$reminderDelay   = (int)$companySettings['reminder_delay_days'];

$reminderScheduledAt = null;

if ($reminderEnabled && $reminderDelay > 0) {
    $reminderScheduledAt = date(
        'Y-m-d H:i:s',
        strtotime($scheduled_at . " +{$reminderDelay} days")
    );
}

$stmt = $pdo->prepare("
    INSERT INTO review_invites 
    (
        company_id,
        order_id,
        customer_email,
        status,
        invite_type,
        scheduled_at,
        reminder_scheduled_at,
        secure_token,
        created_at
    )
    VALUES (?, ?, ?, 'scheduled', 'initial', ?, ?, ?, NOW())
");

$stmt->execute([
    $company_id,
    $order_id,
    $customer_email,
    $scheduled_at,
    $reminderScheduledAt,
    $secureToken
]);

echo json_encode([
    'status' => 'success',
    'scheduled_at' => $scheduled_at
]);