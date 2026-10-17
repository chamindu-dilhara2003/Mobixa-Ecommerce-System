<?php
require_once 'config/database.php';
require_once 'config/helpers.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('orders.php');
$orderId = (int)($_POST['order_id'] ?? 0);
if ($orderId <= 0) { flash('error','Invalid order.'); redirect('orders.php'); }

try {
    $pdo->beginTransaction();
    $q=$pdo->prepare('SELECT id,status,payment_status,payment_method FROM orders WHERE id=? AND user_id=? FOR UPDATE');
    $q->execute([$orderId,$_SESSION['user_id']]);
    $order=$q->fetch();
    if(!$order){ throw new Exception('Order not found.'); }
    if($order['status']!=='Pending'){ throw new Exception('Only pending orders can be cancelled.'); }

    $items=$pdo->prepare('SELECT product_id,quantity FROM order_items WHERE order_id=?');
    $items->execute([$orderId]);
    $restore=$pdo->prepare('UPDATE products SET stock=stock+? WHERE id=?');
    foreach($items->fetchAll() as $item){ $restore->execute([(int)$item['quantity'],(int)$item['product_id']]); }

    $newPaymentStatus = $order['payment_method']==='payhere' ? 'Failed' : $order['payment_status'];
    $u=$pdo->prepare('UPDATE orders SET status="Cancelled", payment_status=? WHERE id=? AND status="Pending"');
    $u->execute([$newPaymentStatus,$orderId]);
    if($u->rowCount()!==1) throw new Exception('Order could not be cancelled.');
    $pdo->commit();
    flash('success','Order #'.$orderId.' was cancelled successfully.');
} catch(Throwable $e) {
    if($pdo->inTransaction()) $pdo->rollBack();
    flash('error',$e->getMessage());
}
redirect('orders.php');
