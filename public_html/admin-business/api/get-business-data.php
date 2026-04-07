<?php

require_once('/home/puandeks.com/backend/config.php');
header('Content-Type: application/json');

session_start();

// Oturumdan giriş yapan işletme ID'si alınır
if (!isset($_SESSION['company_id'])) {
    echo json_encode(['success' => false, 'message' => 'Oturum bulunamadı']);
    exit;
}

$company_id = $_SESSION['company_id'];

$stmt = $pdo->prepare("SELECT * FROM companies WHERE id = ?");
$stmt->execute([$company_id]);
$company = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$company) {
    echo json_encode(['success' => false, 'message' => 'İşletme bulunamadı']);
    exit;
}

// Belgeleri ayrı döndür
$documents = [
    'vergi_levhasi' => $company['doc_tax'],
    'faaliyet_belgesi' => $company['doc_activity'],
    'sicil_gazetesi' => $company['doc_registry']
];

echo json_encode([
    'success' => true,
    'company' => [
        'id' => $company['id'],
        'name' => $company['name'],
        'owner_name' => $company['owner_name'],
        'domain' => $company['domain'],
        'rating' => $company['rating'],
        'reviews' => $company['reviews'],
        'created_at' => $company['created_at'],
        'category_id' => $company['category_id'],
        'verified' => $company['verified'],
        'about' => $company['about'],
        'address' => $company['address'],
        'email' => $company['email'],
        'phone' => $company['phone'],
        'title' => $company['title'],
        'website' => $company['website'],
        'phone_prefix' => $company['phone_prefix'],
        'postal_code' => $company['postal_code'],
        'city' => $company['city'],
        'district' => $company['district'],
        'country' => $company['country'],
        'annual_revenue' => $company['annual_revenue'],
        'currency' => $company['currency'],
        'latitude' => $company['latitude'],
        'longitude' => $company['longitude'],
        'logo' => $company['logo'],
        'linkedin_url' => $company['linkedin_url'],
        'facebook_url' => $company['facebook_url'],
        'instagram_url' => $company['instagram_url'],
        'x_url' => $company['x_url'],
        'youtube_url' => $company['youtube_url'],
        'email_offers' => $company['email_offers'],
        'agreement' => $company['agreement']
    ],
    'documents' => $documents
]);
