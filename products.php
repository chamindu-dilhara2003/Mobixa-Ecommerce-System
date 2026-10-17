<?php
require_once 'config/database.php'; require_once 'config/helpers.php';
$pageTitle='Products - MobiXa';
$q=trim($_GET['q']??''); $cat=(int)($_GET['category']??0);
$sortOptions=[
    'newest'     => ['label'=>'Newest',              'sql'=>'p.id DESC'],
    'name_asc'   => ['label'=>'Name (A-Z)',           'sql'=>'p.name ASC'],
    'name_desc'  => ['label'=>'Name (Z-A)',           'sql'=>'p.name DESC'],
    'price_asc'  => ['label'=>'Price: Low to High',   'sql'=>'p.price ASC'],
    'price_desc' => ['label'=>'Price: High to Low',   'sql'=>'p.price DESC'],
];
$sort=$_GET['sort']??'newest';
if(!array_key_exists($sort,$sortOptions)) $sort='newest';
$categories=$pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
$sql="SELECT p.*,c.name category_name FROM products p LEFT JOIN categories c ON c.id=p.category_id WHERE p.status=1"; $params=[];
if($q!==''){ $sql.=" AND (p.name LIKE ? OR p.description LIKE ?)"; $params[]="%$q%"; $params[]="%$q%"; }
if($cat){$sql.=" AND p.category_id=?";$params[]=$cat;}
$sql.=" ORDER BY ".$sortOptions[$sort]['sql']; $st=$pdo->prepare($sql);$st->execute($params);$products=$st->fetchAll();
include 'includes/header.php';
?>
<section class="section"><div class="container"><h1>Our Products</h1>
<form class="searchbar" method="get"><input class="form-control" name="q" value="<?= e($q) ?>" placeholder="Search accessories..."><button class="btn">Search</button></form>
<div class="category-list"><a class="<?= !$cat?'active':'' ?>" href="products.php">All</a><?php foreach($categories as $c): ?>
    <a class="<?= $cat==$c['id']?'active':'' ?>" href="products.php?category=<?= $c['id'] ?>"><?= e($c['name']) ?></a><?php endforeach; ?></div>
<div class="toolbar">
    <p class="muted result-count"><?= count($products) ?> product<?= count($products)==1?'':'s' ?> found</p>
    <form class="sort-bar" method="get" id="sort-form">
        <?php if($q!==''): ?><input type="hidden" name="q" value="<?= e($q) ?>"><?php endif; ?>
        <?php if($cat): ?><input type="hidden" name="category" value="<?= $cat ?>"><?php endif; ?>
        <label for="sort">Sort by</label>
        <select name="sort" id="sort" onchange="document.getElementById('sort-form').submit()">
            <?php foreach($sortOptions as $key=>$opt): ?>
            <option value="<?= e($key) ?>" <?= $sort===$key?'selected':'' ?>><?= e($opt['label']) ?></option>
            <?php endforeach; ?>
        </select>
    </form>
</div>
<div class="product-grid"><?php foreach($products as $p): ?><article class="product-card"><a href="product.php?id=<?= $p['id'] ?>">
    <img loading="lazy" src="<?= e($p['image'] ?: 'assets/images/placeholder.svg') ?>"></a><div class="product-info"><p class="muted small"><?= e($p['category_name']??'Accessories') ?></p><h3><?= e($p['name']) ?></h3>
    <p class="price"><?= money($p['price']) ?></p><p class="muted"><?= e(mb_strimwidth($p['description'],0,80,'...')) ?></p>
    <div class="product-actions"><a class="btn" href="product.php?id=<?= $p['id'] ?>">View Product</a><?php if ((int)$p['stock'] > 0): ?><form method="post" action="cart.php"><input type="hidden" name="action" value="add"><input type="hidden" name="product_id" value="<?= $p['id'] ?>"><input type="hidden" name="quantity" value="1"><button class="btn secondary cart-icon-btn" type="submit" aria-label="Add <?= e($p['name']) ?> to cart" title="Add to cart">🛒</button></form><?php endif; ?></div></div></article><?php endforeach; ?></div>
<?php if(!$products): ?><div class="empty">No products found.</div><?php endif; ?></div></section>
<?php include 'includes/footer.php'; ?>
