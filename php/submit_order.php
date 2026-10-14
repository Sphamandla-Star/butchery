<?php
/**
 * Receives an order request from the Products page, validates it,
 * recalculates prices on the server, and saves it to the database.
 */

declare(strict_types=1);

require_once __DIR__ . '/db.php';

// Only accept form submissions
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    renderPage('Not allowed', '<p>Please place your order from the <a href="../products.html">Products page</a>.</p>');
    exit;
}

// ---------- Read and validate customer details ----------
$name           = cleanText($_POST['name'] ?? '', 100);
$phone          = cleanText($_POST['phone'] ?? '', 30);
$email          = cleanText($_POST['email'] ?? '', 150);
$collectionType = cleanText($_POST['collection'] ?? '', 20);
$notes          = cleanText($_POST['notes'] ?? '', 500);
$cartJson       = $_POST['cart_data'] ?? '';

$errors = [];

if (mb_strlen($name) < 2) {
    $errors[] = 'Please enter your full name.';
}

if (!preg_match('/^[0-9+\s()\-]{7,30}$/', $phone)) {
    $errors[] = 'Please enter a valid phone number.';
}

if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Please enter a valid email address or leave it blank.';
}

if (!in_array($collectionType, ['collection', 'delivery'], true)) {
    $errors[] = 'Please choose collection or delivery.';
}

// ---------- Read and validate cart items ----------
$cartItems = json_decode((string) $cartJson, true);

if (!is_array($cartItems) || count($cartItems) === 0) {
    $errors[] = 'Your cart is empty. Please add products before submitting.';
} elseif (count($cartItems) > 50) {
    $errors[] = 'Too many items in one order. Please split it into smaller orders.';
}

if (!empty($errors)) {
    http_response_code(400);
    $list = '<ul>';
    foreach ($errors as $error) {
        $list .= '<li>' . e($error) . '</li>';
    }
    $list .= '</ul>';
    renderPage('Please check your order', $list . '<p><a href="javascript:history.back()">Go back and fix the form</a></p>');
    exit;
}

try {
    $db = getDb();

    // Look up each product's price in the database. Never trust prices sent from the browser.
    $productStmt = $db->prepare('SELECT id, name, price_per_kg FROM products WHERE id = ? AND is_active = 1');

    $validItems = [];
    $orderTotal = 0.0;

    foreach ($cartItems as $item) {
        $productId = cleanText($item['id'] ?? '', 50);
        $grams     = filter_var($item['grams'] ?? null, FILTER_VALIDATE_INT);
        $qty       = filter_var($item['qty'] ?? null, FILTER_VALIDATE_INT);

        // Weight must be a multiple of 100g, between 100g and 10kg
        if ($grams === false || $grams < 100 || $grams > 10000 || $grams % 100 !== 0) {
            fail('One of the weights in your cart is not valid. Please update your cart and try again.');
        }

        // Quantity must be between 1 and 20
        if ($qty === false || $qty < 1 || $qty > 20) {
            fail('One of the quantities in your cart is not valid. Please update your cart and try again.');
        }

        $productStmt->execute([$productId]);
        $product = $productStmt->fetch();

        if ($product === false) {
            fail('One of the products in your cart is no longer available. Please refresh the Products page and try again.');
        }

        $pricePerKg = (float) $product['price_per_kg'];
        $lineTotal  = round($pricePerKg * $grams / 1000 * $qty, 2);
        $orderTotal += $lineTotal;

        $validItems[] = [
            'product_id'   => $product['id'],
            'product_name' => $product['name'],
            'grams'        => $grams,
            'qty'          => $qty,
            'price_per_kg' => $pricePerKg,
            'line_total'   => $lineTotal,
        ];
    }

    $orderTotal = round($orderTotal, 2);

    // ---------- Save order and items together ----------
    $db->beginTransaction();

    $orderRef = 'BTC' . date('ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));

    $orderStmt = $db->prepare(
        'INSERT INTO orders (order_ref, customer_name, phone, email, collection_type, notes, total)
         VALUES (?, ?, ?, ?, ?, ?, ?)'
    );
    $orderStmt->execute([
        $orderRef,
        $name,
        $phone,
        $email !== '' ? $email : null,
        $collectionType,
        $notes !== '' ? $notes : null,
        $orderTotal,
    ]);

    $orderId = (int) $db->lastInsertId();

    $itemStmt = $db->prepare(
        'INSERT INTO order_items (order_id, product_id, product_name, grams, qty, price_per_kg, line_total)
         VALUES (?, ?, ?, ?, ?, ?, ?)'
    );

    foreach ($validItems as $item) {
        $itemStmt->execute([
            $orderId,
            $item['product_id'],
            $item['product_name'],
            $item['grams'],
            $item['qty'],
            $item['price_per_kg'],
            $item['line_total'],
        ]);
    }

    $db->commit();

} catch (Throwable $error) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    error_log('Order save failed: ' . $error->getMessage());
    http_response_code(500);
    renderPage('Something went wrong', '<p>Sorry, we could not save your order. Please try again or contact us by phone or WhatsApp.</p>');
    exit;
}

// ---------- Confirmation page ----------
$itemsHtml = '';
foreach ($validItems as $item) {
    $weightLabel = $item['grams'] >= 1000 ? ($item['grams'] / 1000) . ' kg' : $item['grams'] . ' g';
    $itemsHtml .= '<tr>'
        . '<td>' . e($item['product_name']) . '</td>'
        . '<td>' . e($weightLabel) . '</td>'
        . '<td>' . (int) $item['qty'] . '</td>'
        . '<td>R ' . number_format($item['line_total'], 2) . '</td>'
        . '</tr>';
}

$content = '
    <p>Thank you, <strong>' . e($name) . '</strong>. We have received your order request.</p>
    <p>Your order reference is <strong>' . e($orderRef) . '</strong>. Please quote it when you contact us.</p>
    <table class="summary">
        <thead><tr><th>Product</th><th>Weight</th><th>Qty</th><th>Price</th></tr></thead>
        <tbody>' . $itemsHtml . '</tbody>
        <tfoot><tr><th colspan="3">Estimated total</th><th>R ' . number_format($orderTotal, 2) . '</th></tr></tfoot>
    </table>
    <p class="note">This is a request. We will confirm the final price and availability with you by phone or WhatsApp.</p>
    <p><a class="button" href="../index.html">Back to Home</a></p>
    <script>
        // Clear the saved cart now that the order has been placed
        localStorage.removeItem("butcheryCart");
    </script>';

renderPage('Order Received', $content);

/**
 * Stops processing and shows an error message to the customer.
 */
function fail(string $message): void
{
    http_response_code(400);
    renderPage('Order not saved', '<p>' . e($message) . '</p><p><a href="../products.html">Return to Products</a></p>');
    exit;
}

/**
 * Outputs a simple styled page. Used for confirmations and errors.
 */
function renderPage(string $title, string $content): void
{
    header('Content-Type: text/html; charset=utf-8');
    echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . e($title) . ' | Ubuntu-Butchery</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .result-box { max-width: 720px; margin: 60px auto; background: #fff; padding: 36px; border-radius: 10px; box-shadow: 0 4px 14px rgba(0,0,0,0.08); }
        .summary { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .summary th, .summary td { padding: 10px; border-bottom: 1px solid #e6dfd3; text-align: left; }
        .button { display: inline-block; background: #8b1e1e; color: #fff; padding: 12px 24px; border-radius: 6px; }
        .button:hover { background: #6a1515; color: #fff; }
        .note { color: #6b6b6b; font-size: 0.9rem; }
    </style>
</head>
<body style="background:#faf7f2;">
    <div class="result-box">
        <h1>' . e($title) . '</h1>
        ' . $content . '
    </div>
</body>
</html>';
}
