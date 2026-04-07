<?php
require_once('/home/puandeks.com/backend/config.php');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  echo json_encode(["status" => "error", "message" => "Geçersiz istek."]);
  exit;
}

$id        = intval($_POST['id']);
$title     = trim($_POST['title']);
$color     = trim($_POST['color']);
$size      = trim($_POST['size']);
$theme     = trim($_POST['theme']);
$language  = trim($_POST['language']);
$status    = trim($_POST['status']);
$type      = trim($_POST['type']);

if (!$id || !$title || !$color || !$size || !$theme || !$language || !$type) {
  echo json_encode(["status" => "error", "message" => "Eksik alanlar var."]);
  exit;
}

$stmt = $conn->prepare("UPDATE widgets SET title=?, color=?, size=?, theme=?, language=?, status=?, type=? WHERE id=?");
$stmt->bind_param("sssssssi", $title, $color, $size, $theme, $language, $status, $type, $id);

if ($stmt->execute()) {
  echo json_encode(["status" => "success"]);
} else {
  echo json_encode(["status" => "error", "message" => "Güncelleme başarısız."]);
}

$stmt->close();
$conn->close();
