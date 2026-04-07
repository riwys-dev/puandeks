<?php
session_start();
require_once('/home/puandeks.com/backend/config.php');

header('Content-Type: application/json');

if (!isset($_SESSION['company_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$companyId = $_SESSION['company_id'];

/* ===== INPUTS ===== */
$inviteEnabled        = isset($data['invite_enabled']) ? (int)$data['invite_enabled'] : 1;
$inviteDelayDays      = isset($data['invite_delay_days']) ? (int)$data['invite_delay_days'] : 0;
$inviteFrequencyDays  = isset($data['invite_frequency_days']) ? (int)$data['invite_frequency_days'] : 0;
$reminderEnabled      = isset($data['reminder_enabled']) ? (int)$data['reminder_enabled'] : 0;
$reminderDelayDays    = isset($data['reminder_delay_days']) ? (int)$data['reminder_delay_days'] : 0;

/* ===== VALIDATION ===== */
if ($inviteDelayDays < 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid delay']);
    exit;
}

if ($inviteFrequencyDays < 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid frequency']);
    exit;
}

if ($reminderEnabled && $reminderDelayDays < 1) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid reminder delay']);
    exit;
}

/* ===== UPDATE ===== */
$stmt = $pdo->prepare("
    UPDATE companies SET
        invite_enabled = ?,
        invite_delay_days = ?,
        invite_frequency_days = ?,
        reminder_enabled = ?,
        reminder_delay_days = ?
    WHERE id = ?
");

$ok = $stmt->execute([
    $inviteEnabled,
    $inviteDelayDays,
    $inviteFrequencyDays,
    $reminderEnabled,
    $reminderDelayDays,
    $companyId
]);

if ($ok) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'DB update failed']);
}