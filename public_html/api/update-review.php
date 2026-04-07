<?php
session_start();
require_once('/home/puandeks.com/backend/config.php');
header('Content-Type: application/json');

// Session check
if (!isset($_SESSION['user_id'])) {
  echo json_encode(['status' => 'error', 'message' => 'Session not found.']);
  exit;
}

// Inputs
$reviewId = $_POST['id'] ?? null;
$title    = trim($_POST['title'] ?? '');
$content  = trim($_POST['content'] ?? '');
$rating   = isset($_POST['rating']) ? (int)$_POST['rating'] : 3;

// Basic validation
if (!$reviewId || !$title || !$content || $rating < 1 || $rating > 5) {
  echo json_encode(['status' => 'error', 'message' => 'Missing or invalid data.']);
  exit;
}

try {

  // Fetch review
  $stmt = $conn->prepare("SELECT user_id, created_at, edit_count FROM reviews WHERE id = ? LIMIT 1");
  $stmt->execute([$reviewId]);
  $review = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$review) {
    echo json_encode(['status' => 'error', 'message' => 'Review not found.']);
    exit;
  }

  // Ownership check
  if ($review['user_id'] != $_SESSION['user_id']) {
    echo json_encode(['status' => 'error', 'message' => 'You cannot edit this review.']);
    exit;
  }

  // EDIT LIMIT: only 1 time allowed
  if ((int)$review['edit_count'] >= 1) {
    echo json_encode(['status' => 'error', 'message' => 'You can edit a review only once.']);
    exit;
  }

  // 90-DAY LIMIT
  $createdTime = strtotime($review['created_at']);
  $ninetyDays  = strtotime('+90 days', $createdTime);

  if (time() > $ninetyDays) {
    echo json_encode(['status' => 'error', 'message' => 'The editing period (90 days) has expired.']);
    exit;
  }

  // Update review
  $stmt = $conn->prepare("
    UPDATE reviews 
    SET 
        title = ?, 
        comment = ?, 
        rating = ?, 
        updated_at = NOW(),
        edited_at = NOW(),
        edit_count = edit_count + 1
    WHERE id = ? AND user_id = ?
  ");

  $stmt->execute([
      $title,
      $content,
      $rating,
      $reviewId,
      $_SESSION['user_id']
  ]);

  echo json_encode(['status' => 'success']);
  exit;

} catch (Exception $e) {
  echo json_encode(['status' => 'error', 'message' => 'Server error: ' . $e->getMessage()]);
  exit;
}
