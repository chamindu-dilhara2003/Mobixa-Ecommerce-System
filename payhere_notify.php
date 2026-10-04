<?php
require_once 'config/database.php';

$payhere = require 'config/payhere.php';
$merchantId = (string)($_POST['merchant_id'] ?? '');
$orderId = (int)($_POST['order_id'] ?? 0);
$amount = (string)($_POST['payhere_amount'] ?? '');
$currency = (string)($_POST['payhere_currency'] ?? '');
$statusCode = (string)($_POST['status_code'] ?? '');
$signature = (string)($_POST['md5sig'] ?? '');
$paymentId = trim((string)($_POST['payment_id'] ?? ''));

$expectedSignature = strtoupper(md5(
    $merchantId . $orderId . $amount . $currency . $statusCode . strtoupper(md5($payhere['merchant_secret']))
));

if ($merchantId !== (string)$payhere['merchant_id'] || !hash_equals($expectedSignature, $signature)) {
    http_response_code(400);
    exit('Invalid notification');
}

$pdo->beginTransaction();
try {
    $orderQuery = $pdo->prepare('SELECT id, payment_status FROM orders WHERE id=? AND payment_method="payhere" FOR UPDATE');
    $orderQuery->execute([$orderId]);
    $order = $orderQuery->fetch();
    if (!$order) {
        $pdo->rollBack();
        exit('Unknown order');
    }

    if ($statusCode === '2' && $order['payment_status'] === 'Pending') {
        $update = $pdo->prepare('UPDATE orders SET payment_status="Paid", status="Processing", payhere_payment_id=? WHERE id=?');
        $update->execute([$paymentId, $orderId]);
    } elseif (in_array($statusCode, ['-1', '-2', '-3'], true) && $order['payment_status'] === 'Pending') {
        // Release reserved stock once when a payment fails, is cancelled, or expires.
        $items = $pdo->prepare('SELECT product_id, quantity FROM order_items WHERE order_id=?');
        $items->execute([$orderId]);
        $restore = $pdo->prepare('UPDATE products SET stock=stock+? WHERE id=?');
        foreach ($items->fetchAll() as $item) {
            $restore->execute([(int)$item['quantity'], (int)$item['product_id']]);
        }
        $update = $pdo->prepare('UPDATE orders SET payment_status="Failed", status="Cancelled", payhere_payment_id=? WHERE id=?');
        $update->execute([$paymentId, $orderId]);
    }
    $pdo->commit();
    echo 'OK';
} catch (Throwable $exception) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    http_response_code(500);
    echo 'Notification could not be processed';
}
