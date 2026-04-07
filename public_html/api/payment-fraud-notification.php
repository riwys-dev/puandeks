<?php
require_once('/home/puandeks.com/backend/config.php');

// Allow only POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

$expectedSecret = 'CHANGE_THIS_TO_STRONG_RANDOM_SECRET';
// Get incoming secret header
$incomingSecret = $_SERVER['HTTP_X_PUANDEKS_SECRET'] ?? '';

// Validate secret
if ($incomingSecret !== $expectedSecret) {
    http_response_code(403);
    exit('Forbidden');
}

// Get raw POST payload
$input = file_get_contents('php://input');

// Log file path
$logFile = '/home/puandeks.com/backend/logs/fraud-log.txt';

// Write payload to log for debugging
file_put_contents($logFile, date('Y-m-d H:i:s') . "\n" . $input . "\n\n", FILE_APPEND);

// Return 200 OK response
http_response_code(200);
echo "OK";