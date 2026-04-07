<?php
require_once('/home/puandeks.com/backend/config.php');
require_once('/home/puandeks.com/backend/helpers/mailer.php');

echo "Running review invite cron...\n";

/* ======================================================
   INITIAL MAILS
====================================================== */

$stmt = $pdo->prepare("
    SELECT ri.*, c.name AS company_name, c.reminder_enabled
    FROM review_invites ri
    INNER JOIN companies c ON c.id = ri.company_id
    WHERE (
        (ri.status = 'scheduled' AND ri.invite_type = 'initial')
        OR (ri.status = 'failed' AND ri.failed_attempts < 3 AND ri.invite_type = 'initial')
    )
    AND ri.scheduled_at <= NOW()
    LIMIT 50
");

$stmt->execute();
$initialInvites = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($initialInvites as $invite) {

    echo "Processing INITIAL invite ID: {$invite['id']}\n";

    $reviewLink = "https://puandeks.com/review-invite.php?token=" . $invite['secure_token'];

    $mailSent = sendReviewInviteMail(
        $invite['customer_email'],
        $invite['company_name'],
        $reviewLink
    );

    $attemptNumber = $invite['failed_attempts'] + 1;

    if ($mailSent) {

        $update = $pdo->prepare("
            UPDATE review_invites
            SET status = 'sent',
                sent_at = NOW(),
                failed_attempts = 0,
                fail_reason = NULL
            WHERE id = ?
        ");
        $update->execute([$invite['id']]);

        // LOG SUCCESS
        $pdo->prepare("
            INSERT INTO review_invite_logs 
            (review_invite_id, attempt_number, status, response_message, created_at)
            VALUES (?, ?, 'success', NULL, NOW())
        ")->execute([$invite['id'], $attemptNumber]);

        echo "Initial mail sent.\n";

    } else {

        $pdo->prepare("
            UPDATE review_invites
            SET status = 'failed',
                failed_attempts = failed_attempts + 1,
                last_attempt_at = NOW(),
                fail_reason = 'SMTP send failed'
            WHERE id = ?
        ")->execute([$invite['id']]);

        // LOG FAILURE
        $pdo->prepare("
            INSERT INTO review_invite_logs 
            (review_invite_id, attempt_number, status, response_message, created_at)
            VALUES (?, ?, 'failed', 'SMTP send failed', NOW())
        ")->execute([$invite['id'], $attemptNumber]);

        echo "Initial mail failed.\n";
    }
}

/* ======================================================
  REMINDER MAILS (TEK)
====================================================== */

$stmt = $pdo->prepare("
    SELECT ri.*, c.name AS company_name
    FROM review_invites ri
    INNER JOIN companies c ON c.id = ri.company_id
    WHERE ri.invite_type = 'initial'
      AND ri.status = 'sent'
      AND ri.used_at IS NULL
      AND ri.reminder_scheduled_at IS NOT NULL
      AND ri.reminder_scheduled_at <= NOW()
      AND ri.reminder_sent_at IS NULL
    LIMIT 50
");

$stmt->execute();
$reminderInvites = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($reminderInvites as $invite) {

    echo "Processing REMINDER for invite ID: {$invite['id']}\n";

    $reviewLink = "https://puandeks.com/review-invite.php?token=" . $invite['secure_token'];

    $mailSent = sendReviewInviteMail(
        $invite['customer_email'],
        $invite['company_name'],
        $reviewLink
    );

    if ($mailSent) {

        $pdo->prepare("
            UPDATE review_invites
            SET reminder_sent_at = NOW()
            WHERE id = ?
        ")->execute([$invite['id']]);

        // LOG SUCCESS
        $pdo->prepare("
            INSERT INTO review_invite_logs 
            (review_invite_id, attempt_number, status, response_message, created_at)
            VALUES (?, 1, 'success', NULL, NOW())
        ")->execute([$invite['id']]);

        echo "Reminder sent.\n";

    } else {

        // LOG FAILURE
        $pdo->prepare("
            INSERT INTO review_invite_logs 
            (review_invite_id, attempt_number, status, response_message, created_at)
            VALUES (?, 1, 'failed', 'SMTP send failed', NOW())
        ")->execute([$invite['id']]);

        echo "Reminder failed.\n";
    }
}

echo "Done.\n";