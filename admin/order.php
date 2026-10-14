<?php
/**
 * Shows one order in full and lets the admin change its status.
 */

declare(strict_types=1);

require_once __DIR__ . '/auth.php';
requireLogin();

$db = getDb();
$orderId = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);

if ($orderId === false || $orderId === null) {
    http_response_code(400);
    exit('Invalid order.');
}

// ---------- Handle status update ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    $newStatus = $_POST['status'] ?? '';
    if (in_array($newStatus, ORDER_STATUSES, true)) {
        $update = $db->prepare('UPDATE orders SET status = ? WHERE id = ?');
        $update->execute([$newStatus, $orderId]);
    }

    header('Location: order.php?id=' . $orderId . '&updated=1');
    exit;
}

// ---------- Load order and items ----------
$stmt = $db->prepare('SELECT * FROM orders WHERE id = ?');
$stmt->execute([$orderId]);
$order = $stmt->fetch();

if ($order === false) {
    http_response_code(404);
    exit('Order not found.');
}

$itemStmt = $db->prepare('SELECT * FROM order_items WHERE order_id = ? ORDER BY id');
$itemStmt->execute([$orderId]);
$items = $itemStmt->fetchAll();

renderHeader('Order ' . $order['order_ref']);
?>

<div class="no-print">
    <p><a href="index.php">&larr; Back to dashboard</a></p>
</div>

<?php if (isset($_GET['updated'])): ?>
    <p class="notice success no-print">Order status updated.</p>
<?php endif; ?>

<section class="card">
    <div class="card-header">
        <h1>Order <?= e($order['order_ref']) ?></h1>
        <button type="button" class="btn btn-small no-print" onclick="window.print()">Print</button>
    </div>

    <p><?= statusBadge($order['status']) ?> &nbsp; Placed <?= e(formatDate($order['created_at'])) ?></p>

    <div class="details-grid">
        <div>
            <h3>Customer</h3>
            <p>
                <?= e($order['customer_name']) ?><br>
                <a href="tel:<?= e($order['phone']) ?>"><?= e($order['phone']) ?></a><br>
                <?php if (!empty($order['email'])): ?>
                    <a href="mailto:<?= e($order['email']) ?>"><?= e($order['email']) ?></a>
                <?php endif; ?>
            </p>
        </div>
        <div>
            <h3>Collection or Delivery</h3>
            <p><?= e(ucfirst($order['collection_type'])) ?></p>
            <?php if (!empty($order['notes'])): ?>
                <h3>Special Instructions</h3>
                <p><?= nl2br(e($order['notes'])) ?></p>
            <?php endif; ?>
        </div>
    </div>

    <h3>Items</h3>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Weight</th>
                    <th>Qty</th>
                    <th>Price per kg</th>
                    <th>Line total</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($items as $item): ?>
                <?php $weight = $item['grams'] >= 1000 ? ($item['grams'] / 1000) . ' kg' : $item['grams'] . ' g'; ?>
                <tr>
                    <td><?= e($item['product_name']) ?></td>
                    <td><?= e($weight) ?></td>
                    <td><?= (int) $item['qty'] ?></td>
                    <td>R <?= number_format((float) $item['price_per_kg'], 2) ?></td>
                    <td>R <?= number_format((float) $item['line_total'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4">Total</th>
                    <th>R <?= number_format((float) $order['total'], 2) ?></th>
                </tr>
            </tfoot>
        </table>
    </div>

    <form method="POST" class="status-form no-print">
        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
        <label>Update status
            <select name="status">
                <?php foreach (ORDER_STATUSES as $status): ?>
                    <option value="<?= e($status) ?>" <?= $order['status'] === $status ? 'selected' : '' ?>>
                        <?= e(ucfirst($status)) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <button type="submit" class="btn">Save Status</button>
    </form>
</section>

<?php renderFooter(); ?>
