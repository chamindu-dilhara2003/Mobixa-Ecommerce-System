<?php require_once '../config/database.php';require_once '../config/helpers.php';
requireAdmin();$pageTitle='Manage Products';$products=$pdo->query("SELECT p.*,c.name category_name FROM products p LEFT JOIN categories c ON c.id=p.category_id ORDER BY p.id DESC")->fetchAll();
include '../includes/admin_header.php';?><div class="admin-actions"><h1>Products</h1><a class="btn" href="add-product.php">+ Add Product</a></div><div class="table-wrap">
    <table class="table"><tr><th>Image</th><th>Name</th><th>Category</th><th>Price</th><th>Stock</th><th>Status</th><th>Actions</th></tr>
    <?php foreach($products as $p):?><tr><td><img loading="lazy" class="product-thumb" src="<?=e($p['image']?:'../assets/images/placeholder.svg')?>"></td><td><?=e($p['name'])?></td>
        <td><?=e($p['category_name']??'-')?></td><td><?=money($p['price'])?></td><td><?=$p['stock']?></td><td><?=$p['status']?'Active':'Hidden'?></td>
        <td><a class="btn small" href="edit-product.php?id=<?=$p['id']?>">Edit</a> <a class="btn danger small confirm-delete" href="delete-product.php?id=<?=$p['id']?>">Delete</a></td></tr>
        <?php endforeach;?></table></div><?php include '../includes/admin_footer.php';?>