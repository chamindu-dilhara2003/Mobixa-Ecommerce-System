<?php
require_once 'config/helpers.php'; requireLogin(); $pageTitle = 'Payment Cancelled - MobiXa'; include 'includes/header.php';
?><section class="section"><div class="container"><div class="form-card payment-redirect-card"><h1>Payment cancelled</h1><p>Your PayHere payment was cancelled.
     You can return to your orders or start a new checkout.</p>
<a class="btn" href="orders.php">View My Orders</a>
 <a class="btn secondary" href="products.php">Continue Shopping</a></div></div></section>
<?php include 'includes/footer.php'; ?>
