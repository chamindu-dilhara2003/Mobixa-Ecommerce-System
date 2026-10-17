<?php
require_once 'config/database.php';
require_once 'config/helpers.php';
requireLogin();

$st=$pdo->prepare("SELECT * FROM orders WHERE user_id=? ORDER BY id DESC");
$st->execute([$_SESSION['user_id']]);
$orders=$st->fetchAll();
$pageTitle='My Orders - MobiXa';
include 'includes/header.php';
?>
<section class="section">
  <div class="container">
    <div class="section-title"><div><p class="eyebrow">ACCOUNT</p><h1>My Orders</h1><p class="muted">Track your orders and cancel pending orders before processing starts.</p></div><a class="btn secondary" href="products.php">Continue Shopping</a></div>
    <?php if($m=flash('success')):?><div class="alert success"><?=e($m)?></div><?php endif;?>
    <?php if($m=flash('error')):?><div class="alert error"><?=e($m)?></div><?php endif;?>
    <?php if(!$orders):?>
      <div class="empty"><h3>No orders yet</h3><p class="muted">Your completed orders will appear here.</p><a class="btn" href="products.php">Start Shopping</a></div>
    <?php else:?>
      <div class="table-wrap"><table class="table orders-table"><thead><tr><th>Order</th><th>Date</th><th>Total</th><th>Payment</th><th>Status</th><th>Action</th></tr></thead><tbody>
      <?php foreach($orders as $o):?>
        <tr>
          <td><strong>#<?=e($o['id'])?></strong></td>
          <td><?=e(date('d M Y, h:i A', strtotime($o['created_at'])))?></td>
          <td><strong><?=money($o['total_amount'])?></strong></td>
          <td><?=e($o['payment_method']==='payhere'?'PayHere':($o['payment_method']==='card'?'Card':'Cash on Delivery'))?><br><span class="small muted"><?=e($o['payment_status'])?></span></td>
          <td><span class="status-badge status-<?=e(strtolower($o['status']))?>"><?=e($o['status'])?></span></td>
          <td>
            <?php if($o['status']==='Pending'):?>
              <form method="post" action="cancel_order.php" class="inline" onsubmit="return confirm('Cancel order #<?=e($o['id'])?>? This action cannot be undone.');">
                <input type="hidden" name="order_id" value="<?=e($o['id'])?>">
                <button class="btn danger small" type="submit">Cancel Order</button>
              </form>
            <?php elseif($o['status']==='Cancelled'):?>
              <span class="small muted">Cancelled</span>
            <?php else:?>
              <span class="small muted">In progress</span>
            <?php endif;?>
          </td>
        </tr>
      <?php endforeach;?>
      </tbody></table></div>
    <?php endif;?>
  </div>
</section>
<?php include 'includes/footer.php';?>
