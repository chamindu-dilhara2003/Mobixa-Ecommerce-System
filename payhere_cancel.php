<?php
require_once 'config/database.php';
require_once 'config/helpers.php';
requireLogin();

$orderId=(int)($_GET['order_id'] ?? 0);
if($orderId>0){
    try{
        $pdo->beginTransaction();
        $q=$pdo->prepare('SELECT id,status,payment_status FROM orders WHERE id=? AND user_id=? AND payment_method="payhere" FOR UPDATE');
        $q->execute([$orderId,$_SESSION['user_id']]);
        $order=$q->fetch();
        if($order && $order['status']!=='Cancelled' && $order['payment_status']==='Pending'){
            $items=$pdo->prepare('SELECT product_id,quantity FROM order_items WHERE order_id=?');
            $items->execute([$orderId]);
            $restore=$pdo->prepare('UPDATE products SET stock=stock+? WHERE id=?');
            foreach($items->fetchAll() as $item){ $restore->execute([(int)$item['quantity'],(int)$item['product_id']]); }
            $u=$pdo->prepare('UPDATE orders SET payment_status="Failed",status="Cancelled" WHERE id=?');
            $u->execute([$orderId]);
        }
        $pdo->commit();
    }catch(Throwable $e){ if($pdo->inTransaction()) $pdo->rollBack(); }
}
$pageTitle='Payment Cancelled - MobiXa'; include 'includes/header.php';
?>
<section class="section"><div class="container"><div class="form-card payment-redirect-card"><div class="payment-status-icon">×</div><h1>Payment cancelled</h1><p>Your PayHere payment was cancelled and the order has been marked as <strong>Cancelled</strong>. Reserved stock has been released.</p><a class="btn" href="orders.php">View My Orders</a> <a class="btn secondary" href="products.php">Continue Shopping</a></div></div></section><?php include 'includes/footer.php';?>
