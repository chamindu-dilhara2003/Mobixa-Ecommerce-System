<?php
require_once 'config/database.php'; require_once 'config/helpers.php';
$pageTitle='MobiXa - Mobile Accessories';
$products=$pdo->query("SELECT * FROM products WHERE status=1 ORDER BY id DESC LIMIT 8")->fetchAll();
$categories=$pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
include 'includes/header.php';
?>
<section class="hero">

    <div class="hero-content">

        <span class="hero-badge">
            PREMIUM MOBILE ACCESSORIES
        </span>

        <h1>
            Upgrade Your
            <span>Mobile Experience</span>
        </h1>

        <p>
            Discover premium quality mobile accessories
            designed for your everyday life.
        </p>

        <a href="products.php" class="hero-btn">
            Shop Now →
        </a>

    </div>

    <div class="hero-visual">

        <div class="glow"></div>

        <img src="assets/images/hero-products.png"
             alt="MobiXa Mobile Accessories"
             class="hero-product">

    </div>

</section>

<section class="announcement-bar" aria-label="Store benefits">
    <div class="announcement-viewport">
        <div class="announcement-track">
            <div class="announcement-group">
                <span>✦ Premium mobile accessories</span><span>•</span>
                <span>Fast islandwide delivery</span><span>•</span>
                <span>Secure checkout</span><span>•</span>
                <span>New arrivals every week</span><span>•</span>
            </div>
            <div class="announcement-group" aria-hidden="true">
                <span>✦ Premium mobile accessories</span><span>•</span>
                <span>Fast islandwide delivery</span><span>•</span>
                <span>Secure checkout</span><span>•</span>
                <span>New arrivals every week</span><span>•</span>
            </div>
        </div>
    </div>
</section>

<section class="section"><div class="container"><div class="section-title"><h2>Categories</h2>
<a href="products.php">View all</a></div><div class="category-list">
    <?php foreach($categories as $c): ?><a href="products.php?category=<?= $c['id'] ?>"><?= e($c['name']) ?></a><?php endforeach; ?></div></div></section>
    <section class="section"><div class="container"><div class="section-title"><h2>Latest Products</h2><a href="products.php">View all</a></div><div class="product-grid">
    <?php foreach($products as $p): ?><article class="product-card"><a href="product.php?id=<?= $p['id'] ?>">
    <img loading="lazy" src="<?= e($p['image'] ?: 'assets/images/placeholder.svg') ?>" alt=""></a><div class="product-info"><h3><?= e($p['name']) ?></h3><p class="price"><?= money($p['price']) ?></p>
    <div class="product-actions"><a class="btn" href="product.php?id=<?= $p['id'] ?>">View</a><?php if ((int)$p['stock'] > 0): ?><form method="post" action="cart.php"><input type="hidden" name="action" value="add"><input type="hidden" name="product_id" value="<?= $p['id'] ?>"><input type="hidden" name="quantity" value="1"><button class="btn secondary cart-icon-btn" type="submit" aria-label="Add <?= e($p['name']) ?> to cart" title="Add to cart">🛒</button></form><?php endif; ?></div></div></article><?php endforeach; ?></div></div></section>
    <?php include 'includes/footer.php'; ?>
