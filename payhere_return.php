<?php
require_once 'config/database.php'; require_once 'config/helpers.php'; requireLogin();
$orderId = (int)($_GET['order_id'] ?? 0);
$st = $pdo->prepare('SELECT id, payment_status FROM orders WHERE id=? AND user_id=? AND payment_method="payhere"');
$st->execute([$orderId, $_SESSION['user_id']]); $order = $st->fetch(); if (!$order) redirect('orders.php');
$pageTitle = 'PayHere Payment - MobiXa'; include 'includes/header.php';
?><section class="section"><div class="container"><div class="form-card payment-redirect-card"><h1>Payment received</h1><p><?= $order['payment_status'] === 'Paid' ? 'Your PayHere payment has been confirmed. Thank you for your order!' : 'We are waiting for PayHere to confirm your payment. This can take a moment.' ?></p><a class="btn" href="orders.php">View My Orders</a></div></div></section><?php include 'includes/footer.php'; ?>
