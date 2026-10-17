<?php
require_once 'config/database.php'; require_once 'config/helpers.php';
$id=(int)($_GET['id']??0);$st=$pdo->prepare("SELECT p.*,c.name category_name FROM products p LEFT JOIN categories c ON c.id=p.category_id WHERE p.id=? AND p.status=1");$st->execute([$id]);$p=$st->fetch();if(!$p) die('Product not found');
$pageTitle=$p['name'].' - MobiXa'; include 'includes/header.php';
?>
<section class="section"><div class="container"><div class="detail-grid"><div>
    <img loading="lazy" class="detail-image" src="<?= e($p['image'] ?: 'assets/images/placeholder.svg') ?>"></div><div><p class="muted"><?= e($p['category_name']??'Accessories') ?></p><h1><?= e($p['name']) ?></h1><p class="price"><?= money($p['price']) ?></p>
    <p><?= nl2br(e($p['description'])) ?></p><p><strong>Stock:</strong> <?= (int)$p['stock'] ?></p>
<?php if($p['stock']>0): ?><form method="post" action="cart.php">
    <input type="hidden" name="action" value="add"><input type="hidden" name="product_id" value="<?= $p['id'] ?>"><label>Quantity</label><input class="qty" type="number" name="quantity" value="1" min="1" max="<?= $p['stock'] ?>">
    <button class="btn" type="submit">Add to Cart</button></form><?php else: ?><p class="alert error">Out of stock</p><?php endif; ?></div></div></div></section>
<?php include 'includes/footer.php'; ?>