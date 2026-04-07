<?php
session_start();
require_once('/home/puandeks.com/backend/config.php');
header('Content-Type: application/json');

if (!isset($_SESSION['company_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Oturum bulunamadı'
    ]);
    exit;
}

$company_id = (int) $_SESSION['company_id'];

$address   = trim($_POST['address'] ?? '');
$cityName  = trim($_POST['city_name'] ?? '');
$latitude  = isset($_POST['latitude'])  && $_POST['latitude'] !== ''  ? (float)$_POST['latitude']  : null;
$longitude = isset($_POST['longitude']) && $_POST['longitude'] !== '' ? (float)$_POST['longitude'] : null;

try {

    $hasAddressUpdate  = ($cityName !== '' && $address !== '');
    $hasLocationUpdate = ($latitude !== null && $longitude !== null);

    if (!$hasAddressUpdate && !$hasLocationUpdate) {
        echo json_encode([
            'success' => false,
            'message' => 'Güncellenecek veri bulunamadı'
        ]);
        exit;
    }

    /* ==============================
       ADRES GÜNCELLEME
    ============================== */
    if ($hasAddressUpdate) {

        $stmtCity = $pdo->prepare("
            SELECT id
            FROM cities
            WHERE country_code = 'TR'
              AND LOWER(name) = LOWER(?)
            LIMIT 1
        ");
        $stmtCity->execute([$cityName]);
        $city = $stmtCity->fetch(PDO::FETCH_ASSOC);

        if (!$city) {
            echo json_encode([
                'success' => false,
                'message' => 'Şehir adını doğru giriniz'
            ]);
            exit;
        }

        $city_id = (int)$city['id'];

        $stmt = $pdo->prepare("
            UPDATE companies
            SET address = ?, city_id = ?
            WHERE id = ?
        ");
        $stmt->execute([$address, $city_id, $company_id]);
    }

    /* ==============================
       PIN (LAT / LNG) GÜNCELLEME
    ============================== */
    if ($hasLocationUpdate) {

        $stmt = $pdo->prepare("
            UPDATE companies
            SET latitude = ?, longitude = ?
            WHERE id = ?
        ");
        $stmt->execute([$latitude, $longitude, $company_id]);
    }

    echo json_encode([
        'success' => true
    ]);

} catch (Throwable $e) {

    echo json_encode([
        'success' => false,
        'message' => 'Güncelleme sırasında hata oluştu'
    ]);
}