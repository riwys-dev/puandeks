<?php
require_once('/home/puandeks.com/backend/config.php');
header('Content-Type: application/json');

$id     = $_POST['id'] ?? null;
$status = $_POST['status'] ?? null;

/**
 * Basic input validation
 */
if (!$id || !is_numeric($id) || !in_array($status, ['1', '2'], true)) {
    echo json_encode(['success' => false, 'message' => 'Geçersiz veri']);
    exit;
}

try {

    /**
     * Update review status
     * 1 = approved
     * 2 = rejected
     */
    $stmt = $pdo->prepare("
        UPDATE reviews 
        SET status = :status 
        WHERE id = :id
    ");
    $stmt->execute([
        'status' => (int)$status,
        'id'     => (int)$id
    ]);

    /**
     * If review is approved, check auto-reply rules
     */
    if ((int)$status === 1) {

        /**
         * Fetch review and company auto-reply configuration
         */
        $stmt = $pdo->prepare("
            SELECT 
                r.id,
                r.company_id,
                c.auto_reply_enabled,
                c.auto_reply_message
            FROM reviews r
            JOIN companies c ON c.id = r.company_id
            WHERE r.id = ?
        ");
        $stmt->execute([(int)$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        /**
         * Apply auto reply only when:
         * - auto reply is enabled
         * - message exists
         * - review does not already have a reply
         */
        if (
            $row &&
            (int)$row['auto_reply_enabled'] === 1 &&
            trim($row['auto_reply_message']) !== ''
        ) {
            $stmt = $pdo->prepare("
                UPDATE reviews 
                SET reply = :reply,
                    updated_at = NOW()
                WHERE id = :id
                  AND (reply IS NULL OR reply = '')
            ");
            $stmt->execute([
                'reply' => $row['auto_reply_message'],
                'id'    => (int)$id
            ]);
        }
    }

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'DB hatası']);
}
