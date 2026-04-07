<?php
require_once('/home/puandeks.com/backend/config.php');

// -----------------------------------------------------
// RATE LIMIT (IP per 10 , 10 sec)
// -----------------------------------------------------
session_start();
$ip = $_SERVER['REMOTE_ADDR'];
$now = time();

if (!isset($_SESSION['rate_limit'])) {
    $_SESSION['rate_limit'] = [];
}

if (!isset($_SESSION['rate_limit'][$ip])) {
    $_SESSION['rate_limit'][$ip] = [];
}

$_SESSION['rate_limit'][$ip] = array_filter(
    $_SESSION['rate_limit'][$ip],
    function ($timestamp) use ($now) {
        return ($timestamp > $now - 10);
    }
);

if (count($_SESSION['rate_limit'][$ip]) >= 10) {
    http_response_code(429);
    echo json_encode([
        "error" => true,
        "message" => "Too many requests. Please slow down."
    ]);
    exit;
}

$_SESSION['rate_limit'][$ip][] = $now;


// -----------------------------------------------------
// DOMAIN VALIDATION
// -----------------------------------------------------
$domain = $_GET['domain'] ?? '';
$domain = strtolower(trim($domain));

if (!preg_match('/^[a-z0-9.-]+\.[a-z]{2,}$/', $domain)) {
    echo json_encode([
        "exists" => false,
        "id" => null,
        "error" => "Invalid domain format"
    ]);
    exit;
}


// -----------------------------------------------------
// DATABASE QUERY  (YENİ MANTIK: domain OR website)
// -----------------------------------------------------
try {
    $stmt = $conn->prepare("
        SELECT id, slug 
        FROM companies 
        WHERE domain = :domain
            OR website LIKE :website
            OR slug = :domain
        LIMIT 1
    ");

        $stmt->execute([
        ':domain'  => $domain,
        ':website' => '%' . $domain . '%'
    ]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        echo json_encode([
            "exists" => true,
            "id" => (int)$row['id'],
            "slug" => $row['slug']
        ]);
    } else {
        echo json_encode([
            "exists" => false,
            "id" => null
        ]);
    }

} catch (Exception $e) {
    echo json_encode([
        "error" => true,
        "message" => "Database error"
    ]);
}
