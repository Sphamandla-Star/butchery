<?php
/**
 * Admin dashboard: recent orders (with status filter) and contact messages.
 */

declare(strict_types=1);

require_once __DIR__ . '/auth.php';
requireLogin();

$db = getDb();

// Status filter from the URL, e.g. index.php?status=new
$filter = $_GET['status'] ?? 'all';

if (in_array($filter, ORDER_STATUSES, true)) {
    $stmt = $db->prepare(
        'SELECT id, order_ref, customer_name, phone, collection_type, total, status, created_at
         FROM orders WHERE status = ? ORDER BY created_at DESC LIMIT 100'
    );
    $stmt->execute([$filter]);
} else {
    $filter = 'all';
    $stmt = $db->query(
        'SELECT id, order_ref, customer_name, phone, collection_type, total, status, created_at
         FROM orders ORDER BY created_at DESC LIMIT 100'
    );
}
$orders = $stmt->fetchAll();

$messages = $db->query(
    'SELECT id, name, subject, created_at, is_read FROM messages ORDER BY created_at DESC LIMIT 50'
)->fetchAll();

$newOrders   = (int) $db->query("SELECT COUNT(*) FROM orders WHERE status = 'new'")->fetchColumn();
$unreadCount = (int) $db->query('SELECT COUNT(*) FROM messages WHERE is_read = 0')->fetchColumn();

$subjectLabels = [
    'general'   => 'General enquiry',
    'order'     => 'Bulk or special order',
    'catering'  => 'Catering or events',
    'feedback'  => 'Feedback',
];

renderHeader('Dashboard');
?>

<h1>Dashboard</h1>

<div class="stats">
    <div class="stat"><span><?= $newOrders ?></span>New orders</div>
    <div class="stat"><span><?= $unreadCount ?></span>Unread messages</div>
</div>

<section class="card">
    <div class="card-header">
        <h2>Orders</h2>
        <div class="filters">
            <?php
            $filterLinks = array_merge(['all'], ORDER_STATUSES);
            foreach ($filterLinks as $status):
            ?>
                <a href="?status=<?= e($status) ?>"
                   class="filter-link <?= $filter === $status ? 'active' : '' ?>">
                    <?= e(ucfirst($status)) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <?php if (empty($orders)): ?>
        <p class="empty">No orders found.</p>
    <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>Customer</th>
                        <th>Phone</th>
                        <th>Type</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><a href="order.php?id=<?= (int) $order['id'] ?>"><?= e($order['order_ref']) ?></a></td>
                        <td><?= e($order['customer_name']) ?></td>
                        <td><?= e($order['phone']) ?></td>
                        <td><?= e(ucfirst($order['collection_type'])) ?></td>
                        <td>R <?= number_format((float) $order['total'], 2) ?></td>
                        <td><?= statusBadge($order['status']) ?></td>
                        <td><?= e(formatDate($order['created_at'])) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<section class="card">
    <div class="card-header">
        <h2>Contact Messages</h2>
    </div>

    <?php if (empty($messages)): ?>
        <p class="empty">No messages yet.</p>
    <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>From</th>
                        <th>Subject</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($messages as $msg): ?>
                    <tr class="<?= $msg['is_read'] ? '' : 'unread' ?>">
                        <td><a href="message.php?id=<?= (int) $msg['id'] ?>"><?= e($msg['name']) ?></a></td>
                        <td><?= e($subjectLabels[$msg['subject']] ?? $msg['subject']) ?></td>
                        <td><?= e(formatDate($msg['created_at'])) ?></td>
                        <td><?= $msg['is_read'] ? 'Read' : '<strong>Unread</strong>' ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<?php renderFooter(); ?>
