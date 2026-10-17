<?php
require_once '../config/database.php'; require_once '../config/helpers.php'; requireAdmin();
$pageTitle='Dashboard';
$products=(int)$pdo->query("SELECT COUNT(*) c FROM products")->fetch()['c'];
$users=(int)$pdo->query("SELECT COUNT(*) c FROM users")->fetch()['c'];
$orders=(int)$pdo->query("SELECT COUNT(*) c FROM orders")->fetch()['c'];
$pending=(int)$pdo->query("SELECT COUNT(*) c FROM orders WHERE status='Pending'")->fetch()['c'];
$processing=(int)$pdo->query("SELECT COUNT(*) c FROM orders WHERE status='Processing'")->fetch()['c'];
$paid=(int)$pdo->query("SELECT COUNT(*) c FROM orders WHERE payment_status='Paid'")->fetch()['c'];
$lowStock=(int)$pdo->query("SELECT COUNT(*) c FROM products WHERE status=1 AND stock<=5")->fetch()['c'];
$sales=$pdo->query("SELECT COALESCE(SUM(total_amount),0) c FROM orders WHERE status<>'Cancelled' AND (payment_method='cod' OR payment_status='Paid')")->fetch()['c'];
$todaySales=$pdo->query("SELECT COALESCE(SUM(total_amount),0) c FROM orders WHERE DATE(created_at)=CURDATE() AND status<>'Cancelled' AND (payment_method='cod' OR payment_status='Paid')")->fetch()['c'];
$recent=$pdo->query("SELECT o.id,o.total_amount,o.status,o.payment_status,o.payment_method,o.created_at,u.name user_name FROM orders o JOIN users u ON u.id=o.user_id ORDER BY o.id DESC LIMIT 6")->fetchAll();
$topProducts=$pdo->query("SELECT p.name,SUM(oi.quantity) qty FROM order_items oi JOIN products p ON p.id=oi.product_id JOIN orders o ON o.id=oi.order_id WHERE o.status<>'Cancelled' GROUP BY oi.product_id,p.name ORDER BY qty DESC LIMIT 5")->fetchAll();
include '../includes/admin_header.php';
?>
<div class="dashboard-hero"><div><p class="eyebrow">MOBIXA CONTROL CENTER</p><h1>Good to see you, <?=e($_SESSION['admin_name'])?>.</h1><p>Monitor your store, orders, payments and inventory from one place.</p></div><div class="dashboard-hero-actions"><a class="btn" href="add-product.php">+ Add Product</a><a class="btn secondary" href="orders.php">View Orders</a></div></div>

<div class="dashboard-stats">
  <div class="dashboard-stat"><span class="dashboard-stat-icon">▦</span><div><p>Total Orders</p><h3><?=number_format($orders)?></h3><small><?=number_format($pending)?> pending</small></div></div>
  <div class="dashboard-stat"><span class="dashboard-stat-icon">◉</span><div><p>Total Sales</p><h3><?=money($sales)?></h3><small><?=money($todaySales)?> today</small></div></div>
  <div class="dashboard-stat"><span class="dashboard-stat-icon">♙</span><div><p>Customers</p><h3><?=number_format($users)?></h3><small><?=number_format($paid)?> paid orders</small></div></div>
  <div class="dashboard-stat"><span class="dashboard-stat-icon">▤</span><div><p>Products</p><h3><?=number_format($products)?></h3><small class="<?= $lowStock?'text-warning':'' ?>"><?=number_format($lowStock)?> low stock</small></div></div>
</div>

<div class="dashboard-grid">
  <section class="dashboard-panel dashboard-panel-wide"><div class="panel-heading"><div><h2>Recent Orders</h2><p>Latest activity from your customers.</p></div><a href="orders.php">View all →</a></div>
    <?php if(!$recent):?><div class="dashboard-empty">No orders yet.</div><?php else:?><div class="recent-orders">
      <?php foreach($recent as $o):?><div class="recent-order-row"><div class="order-avatar">#<?=e($o['id'])?></div><div class="order-main"><strong><?=e($o['user_name'])?></strong><span>#<?=e($o['id'])?> · <?=e(date('d M, h:i A',strtotime($o['created_at'])))?></span></div><div class="order-payment"><strong><?=money($o['total_amount'])?></strong><span><?=e(strtoupper($o['payment_method']))?> · <?=e($o['payment_status'])?></span></div><span class="status-badge status-<?=e(strtolower($o['status']))?>"><?=e($o['status'])?></span></div><?php endforeach;?>
    </div><?php endif;?>
  </section>

  <section class="dashboard-panel"><div class="panel-heading"><div><h2>Order Pipeline</h2><p>Current fulfilment workload.</p></div></div><div class="pipeline"><div><span>Pending</span><strong><?=$pending?></strong></div><div><span>Processing</span><strong><?=$processing?></strong></div><div><span>Paid</span><strong><?=$paid?></strong></div></div></section>

  <section class="dashboard-panel"><div class="panel-heading"><div><h2>Top Products</h2><p>Best-selling items by quantity.</p></div></div><?php if(!$topProducts):?><div class="dashboard-empty">No sales data yet.</div><?php else:?><div class="top-products"><?php foreach($topProducts as $i=>$p):?><div class="top-product"><span class="rank">0<?=($i+1)?></span><span><?=e($p['name'])?></span><strong><?=number_format($p['qty'])?></strong></div><?php endforeach;?></div><?php endif;?></section>
</div>
<?php include '../includes/admin_footer.php';?>
