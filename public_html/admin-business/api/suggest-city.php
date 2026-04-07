<?php
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json; charset=utf-8');

$q = isset($_GET['q']) ? trim($_GET['q']) : '';

try {

  if ($q === 'all' || $q === '') {

    $stmt = $pdo->prepare("
      SELECT id, name
      FROM cities
      WHERE country_code = 'TR'
      ORDER BY name ASC
    ");
    $stmt->execute();

  } else {

    $stmt = $pdo->prepare("
      SELECT id, name
      FROM cities
      WHERE country_code = 'TR'
        AND name LIKE ?
      ORDER BY name ASC
    ");
    $stmt->execute([$q . '%']);
  }

  $cities = $stmt->fetchAll(PDO::FETCH_ASSOC);

  echo json_encode([
    'success' => true,
    'results' => $cities
  ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {

  echo json_encode([
    'success' => false,
    'message' => 'Şehirler alınamadı'
  ]);
}
