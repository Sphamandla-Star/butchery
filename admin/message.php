<?php
/**
 * Shows one contact message and marks it as read.
 */

declare(strict_types=1);

require_once __DIR__ . '/auth.php';
requireLogin();

$db = getDb();
$messageId = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);

if ($messageId === false || $messageId === null) {
    http_response_code(400);
    exit('Invalid message.');
}

$stmt = $db->prepare('SELECT * FROM messages WHERE id = ?');
$stmt->execute([$messageId]);
$msg = $stmt->fetch();

if ($msg === false) {
    http_response_code(404);
    exit('Message not found.');
}

// Mark as read once it has been opened
if (!$msg['is_read']) {
    $db->prepare('UPDATE messages SET is_read = 1 WHERE id = ?')->execute([$messageId]);
}

$subjectLabels = [
    'general'  => 'General enquiry',
    'order'    => 'Bulk or special order',
    'catering' => 'Catering or events',
    'feedback' => 'Feedback',
];

renderHeader('Message from ' . $msg['name']);
?>

<div class="no-print">
    <p><a href="index.php">&larr; Back to dashboard</a></p>
</div>

<section class="card">
    <h1>Message from <?= e($msg['name']) ?></h1>
    <p class="meta">
        <?= e($subjectLabels[$msg['subject']] ?? $msg['subject']) ?> &middot;
        <?= e(formatDate($msg['created_at'])) ?>
    </p>

    <div class="details-grid">
        <div>
            <h3>Email</h3>
            <p><a href="mailto:<?= e($msg['email']) ?>"><?= e($msg['email']) ?></a></p>
        </div>
        <div>
            <h3>Phone</h3>
            <p><?= !empty($msg['phone']) ? '<a href="tel:' . e($msg['phone']) . '">' . e($msg['phone']) . '</a>' : 'Not given' ?></p>
        </div>
    </div>

    <h3>Message</h3>
    <p class="message-body"><?= nl2br(e($msg['message'])) ?></p>

    <p class="no-print">
        <a class="btn" href="mailto:<?= e($msg['email']) ?>?subject=<?= rawurlencode('Re: ' . ($subjectLabels[$msg['subject']] ?? 'Your enquiry')) ?>">Reply by Email</a>
    </p>
</section>

<?php renderFooter(); ?>
