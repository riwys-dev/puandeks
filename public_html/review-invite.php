<?php
require_once('/home/puandeks.com/backend/config.php');

$token = $_GET['token'] ?? null;

if (!$token) {
    exit("Invalid link.");
}

// Fetch invite by token
$stmt = $pdo->prepare("
    SELECT ri.*, c.name AS company_name
    FROM review_invites ri
    INNER JOIN companies c ON c.id = ri.company_id
    WHERE ri.secure_token = ?
    LIMIT 1
");
$stmt->execute([$token]);
$invite = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$invite) {
    exit("Invalid or expired link.");
}

// Expire control (7 days from sent_at)
if (!$invite['sent_at'] || strtotime($invite['sent_at']) < strtotime('-7 days')) {
    exit("This review link has expired.");
}

// Scheduled date control
if ($invite['scheduled_at'] && strtotime($invite['scheduled_at']) > time()) {
    exit("This review link is not active yet.");
}

// Prevent reuse
if ($invite['status'] !== 'sent') {
    exit("This link is not valid anymore.");
}

// Mark as used immediately
$update = $pdo->prepare("
    UPDATE review_invites
    SET status = 'used',
        used_at = NOW()
    WHERE id = ?
    AND status = 'sent'
    AND used_at IS NULL
");
$update->execute([$invite['id']]);

if ($update->rowCount() === 0) {
    exit("This link has already been used or expired.");
}

// Redirect to review form
header("Location: /add-review.php?company=" . $invite['company_id']);
exit;