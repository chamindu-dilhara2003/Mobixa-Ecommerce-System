<?php
require_once '../config/database.php';
require_once '../config/helpers.php';
requireAdmin();

if($_SERVER['REQUEST_METHOD']==='POST'){
    $id=(int)($_POST['id']??0); $status=$_POST['status']??'';
    $allowed=['Pending','Processing','Shipped','Delivered','Cancelled'];
    if($id>0 && in_array($status,$allowed,true)){
        try{
            $pdo->beginTransaction();
            $q=$pdo->prepare('SELECT status,payment_status FROM orders WHERE id=? FOR UPDATE'); $q->execute([$id]); $old=$q->fetch();
            if(!$old) throw new Exception('Order not found.');
            if($old['status']!==$status){
                if($status==='Cancelled' && $old['status']!=='Cancelled'){
                    $items=$pdo->prepare('SELECT product_id,quantity FROM order_items WHERE order_id=?'); $items->execute([$id]);
                    $restore=$pdo->prepare('UPDATE products SET stock=stock+? WHERE id=?');
                    foreach($items->fetchAll() as $item){$restore->execute([(int)$item['quantity'],(int)$item['product_id']]);}
                    $paymentStatus = $old['payment_status']==='Paid' ? 'Refund Required' : ($old['payment_status']==='Pending' ? 'Failed' : $old['payment_status']);
                    $u=$pdo->prepare('UPDATE orders SET status="Cancelled",payment_status=? WHERE id=?'); $u->execute([$paymentStatus,$id]);
                } else {
                    $u=$pdo->prepare('UPDATE orders SET status=? WHERE id=?'); $u->execute([$status,$id]);
                }
            }
            $pdo->commit(); flash('success','Order #'.$id.' updated successfully.');
        }catch(Throwable $e){if($pdo->inTransaction())$pdo->rollBack();flash('error',$e->getMessage());}
    }
    redirect('orders.php');
}

$orders=$pdo->query("SELECT o.*,u.name user_name,u.email FROM orders o JOIN users u ON u.id=o.user_id ORDER BY o.id DESC")->fetchAll();
$pageTitle='Manage Orders'; include '../includes/admin_header.php';
?>
<div class="admin-page-heading"><div><p class="eyebrow">STORE MANAGEMENT</p><h1>Orders</h1><p class="muted">Review payments, update fulfilment status and manage customer orders.</p></div><a class="btn secondary" href="index.php">← Dashboard</a></div>
<?php if($m=flash('success')):?><div class="alert success"><?=e($m)?></div><?php endif;?>
<?php if($m=flash('error')):?><div class="alert error"><?=e($m)?></div><?php endif;?>
<div class="table-wrap admin-table-wrap"><table class="table admin-orders-table"><thead><tr><th>Order</th><th>Customer</th><th>Total</th><th>Payment</th><th>Delivery</th><th>Status</th><th>Update</th></tr></thead><tbody>
<?php foreach($orders as $o):?>
<tr><td><strong>#<?=e($o['id'])?></strong><br><span class="small muted"><?=e(date('d M Y, h:i A',strtotime($o['created_at'])))?></span></td><td><?=e($o['user_name'])?><br><span class="small muted"><?=e($o['email'])?></span></td><td><strong><?=money($o['total_amount'])?></strong></td><td><span class="payment-method-chip"><?=e(strtoupper($o['payment_method']))?></span><br><span class="small muted"><?=e($o['payment_status'])?></span></td><td><?=e($o['address'])?>, <?=e($o['city'])?><br><span class="small muted"><?=e($o['phone'])?></span></td><td><span class="status-badge status-<?=e(strtolower($o['status']))?>"><?=e($o['status'])?></span></td><td><form method="post" class="admin-status-form"><input type="hidden" name="id" value="<?=e($o['id'])?>"><select class="form-control" name="status"><option <?= $o['status']==='Pending'?'selected':''?>>Pending</option><option <?= $o['status']==='Processing'?'selected':''?>>Processing</option><option <?= $o['status']==='Shipped'?'selected':''?>>Shipped</option><option <?= $o['status']==='Delivered'?'selected':''?>>Delivered</option><option <?= $o['status']==='Cancelled'?'selected':''?>>Cancelled</option></select><button class="btn small">Update</button></form></td></tr>
<?php endforeach;?>
</tbody></table></div>
<?php include '../includes/admin_footer.php';?>
