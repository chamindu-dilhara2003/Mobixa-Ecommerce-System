<?php
require_once 'config/database.php';
require_once 'config/helpers.php';
requireLogin();

if (empty($_SESSION['cart'])) redirect('cart.php');
$payhere = require 'config/payhere.php';
$ids = array_keys($_SESSION['cart']);
$placeholders = implode(',', array_fill(0, count($ids), '?'));
$st = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders) AND status=1");
$st->execute($ids);
$products = $st->fetchAll();
if (count($products) !== count($ids)) { flash('error', 'One or more products are no longer available. Please review your cart.'); redirect('cart.php'); }
$total = 0;
foreach ($products as $product) $total += $product['price'] * $_SESSION['cart'][$product['id']];

$errors = [];
$payherePayload = null;
$paymentMethod = $_POST['payment_method'] ?? 'cod';
if (!in_array($paymentMethod, ['cod', 'payhere'], true)) $paymentMethod = 'cod';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address = trim($_POST['address'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    if (!$address || !$city || !$phone) $errors[] = 'Please fill all delivery details.';
    $baseUrl = rtrim((string)($payhere['base_url'] ?? ''), '/');
    $payhereConfigured = !empty($payhere['merchant_id']) && !empty($payhere['merchant_secret'])
        && !str_starts_with((string)$payhere['merchant_id'], 'YOUR_')
        && !str_starts_with((string)$payhere['merchant_secret'], 'YOUR_') && $baseUrl !== '';
    if ($paymentMethod === 'payhere' && !$payhereConfigured) $errors[] = 'PayHere is not configured yet. Add your Merchant ID, Merchant Secret, and site URL in config/payhere.php.';

    if (!$errors) {
        try {
            $pdo->beginTransaction();
            $pdo->prepare('INSERT INTO orders(user_id,total_amount,address,city,phone,payment_method,payment_status,status) VALUES(?,?,?,?,?,?,?,?)')
                ->execute([$_SESSION['user_id'], $total, $address, $city, $phone, $paymentMethod, 'Pending', 'Pending']);
            $orderId = (int)$pdo->lastInsertId();
            $insertItem = $pdo->prepare('INSERT INTO order_items(order_id,product_id,quantity,price) VALUES(?,?,?,?)');
            $reduceStock = $pdo->prepare('UPDATE products SET stock=stock-? WHERE id=? AND stock>=?');
            foreach ($products as $product) {
                $quantity = (int)$_SESSION['cart'][$product['id']];
                $reduceStock->execute([$quantity, $product['id'], $quantity]);
                if ($reduceStock->rowCount() === 0) throw new Exception('Stock changed. Please update your cart.');
                $insertItem->execute([$orderId, $product['id'], $quantity, $product['price']]);
            }
            $pdo->commit();
            $_SESSION['cart'] = [];
            if ($paymentMethod === 'cod') { flash('success', 'Order placed successfully. Order #' . $orderId); redirect('orders.php'); }

            $customerQuery = $pdo->prepare('SELECT name, email FROM users WHERE id=?');
            $customerQuery->execute([$_SESSION['user_id']]);
            $customer = $customerQuery->fetch() ?: ['name' => 'Customer', 'email' => 'customer@example.com'];
            $nameParts = preg_split('/\s+/', trim($customer['name']), 2);
            $amount = number_format($total, 2, '.', '');
            $currency = 'LKR';
            $hash = strtoupper(md5($payhere['merchant_id'] . $orderId . $amount . $currency . strtoupper(md5($payhere['merchant_secret']))));
            $payherePayload = [
                'merchant_id' => $payhere['merchant_id'], 'return_url' => $baseUrl . '/payhere_return.php?order_id=' . $orderId,
                'cancel_url' => $baseUrl . '/payhere_cancel.php?order_id=' . $orderId, 'notify_url' => $baseUrl . '/payhere_notify.php',
                'order_id' => $orderId, 'items' => 'MobiXa order #' . $orderId, 'currency' => $currency, 'amount' => $amount,
                'first_name' => $nameParts[0] ?: 'Customer', 'last_name' => $nameParts[1] ?? '', 'email' => $customer['email'],
                'phone' => $phone, 'address' => $address, 'city' => $city, 'country' => 'Sri Lanka', 'hash' => $hash,
            ];
        } catch (Throwable $exception) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $errors[] = $exception->getMessage();
        }
    }
}
$pageTitle = 'Checkout - MobiXa'; include 'includes/header.php';
?>
<?php if ($payherePayload): ?>
<section class="section"><div class="container"><div class="form-card payment-redirect-card"><h1>Redirecting to PayHere</h1><p>Please wait while we open PayHere’s secure payment page. Do not refresh this page.</p>
    <form id="payhere-form" method="post" action="<?= e($payhere['sandbox'] ? 'https://sandbox.payhere.lk/pay/checkout' : 'https://www.payhere.lk/pay/checkout') ?>">
        <?php foreach ($payherePayload as $key => $value): ?><input type="hidden" name="<?= e($key) ?>" value="<?= e($value) ?>"><?php endforeach; ?>
        <button class="btn" type="submit">Continue to PayHere</button>
    </form>
</div></div></section><script>document.getElementById('payhere-form').submit();</script>
<?php else: ?>
<section class="section"><div class="container"><div class="form-card"><h1>Checkout</h1><p><strong>Total: <?= money($total) ?></strong></p>
    <?php foreach ($errors as $error): ?><div class="alert error"><?= e($error) ?></div><?php endforeach; ?>
    <form method="post" id="checkout-form">
        <div class="form-group"><label>Delivery Address</label><textarea class="form-control" name="address" rows="3" required><?= e($_POST['address'] ?? '') ?></textarea></div>
        <div class="form-group"><label>City</label><input class="form-control" name="city" value="<?= e($_POST['city'] ?? '') ?>" required></div>
        <div class="form-group"><label>Phone</label><input class="form-control" name="phone" value="<?= e($_POST['phone'] ?? '') ?>" required></div>
        <div class="form-group"><label>Payment Method</label><div class="payment-options">
            <label class="payment-option <?= $paymentMethod === 'cod' ? 'active' : '' ?>"><input type="radio" name="payment_method" value="cod" <?= $paymentMethod === 'cod' ? 'checked' : '' ?>><span class="payment-icon">💵</span><span class="payment-text"><strong>Cash on Delivery</strong><small>Pay when your order arrives</small></span></label>
            <label class="payment-option <?= $paymentMethod === 'payhere' ? 'active' : '' ?>"><input type="radio" name="payment_method" value="payhere" <?= $paymentMethod === 'payhere' ? 'checked' : '' ?>><span class="payment-icon">💳</span><span class="payment-text"><strong>PayHere</strong><small>Pay securely using cards, bank transfer, or wallets</small></span></label>
        </div></div><button class="btn success" type="submit">Place Order</button>
    </form>
</div></div></section>
<?php endif; ?><script src="assets/js/checkout-live.js"></script><?php include 'includes/footer.php'; ?>
