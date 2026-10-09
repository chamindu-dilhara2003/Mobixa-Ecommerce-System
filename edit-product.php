<?php
require_once '../config/database.php';require_once '../config/helpers.php';requireAdmin();$id=(int)($_GET['id']??0);
$st=$pdo->prepare("SELECT * FROM products WHERE id=?");$st->execute([$id]);$p=$st->fetch();if(!$p)die('Product not found');
$categories=$pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();$errors=[];
if($_SERVER['REQUEST_METHOD']==='POST'){$name=trim($_POST['name']);$cat=(int)$_POST['category_id'];$price=(float)$_POST['price'];$stock=(int)$_POST['stock'];$desc=trim($_POST['description']);
$status=isset($_POST['status'])?1:0;$image=$p['image'];
if(isset($_FILES['image'])&&$_FILES['image']['error']===UPLOAD_ERR_OK){$ext=strtolower(pathinfo($_FILES['image']['name'],PATHINFO_EXTENSION));
if(!in_array($ext,['jpg','jpeg','png','webp']))$errors[]='Invalid image type.';else{$file=uniqid('product_',true).'.'.$ext;$dir=__DIR__.'/../assets/images/products';
if(!is_dir($dir))mkdir($dir,0777,true);move_uploaded_file($_FILES['image']['tmp_name'],$dir.'/'.$file);$image='assets/images/products/'.$file;}}
if(!$name||$price<0||$stock<0)$errors[]='Invalid product details.';if(!$errors){$st=$pdo->prepare("UPDATE products SET name=?,category_id=?,price=?,stock=?,description=?,image=?,status=? WHERE id=?");
$st->execute([$name,$cat,$price,$stock,$desc,$image,$status,$id]);flash('success','Product updated successfully.');redirect('products.php');}}
$pageTitle='Edit Product';include '../includes/admin_header.php';?><div class="admin-actions"><h1>Edit Product</h1><a class="btn secondary" href="products.php">Back</a></div>
<?php foreach($errors as $er):?><div class="alert error"><?=e($er)?></div><?php endforeach;?><div class="form-card" style="margin:0;max-width:none"><form method="post" enctype="multipart/form-data"><div class="form-group"><label>Product Name</label>
    <input class="form-control" name="name" value="<?=e($p['name'])?>" required></div><div class="form-group"><label>Category</label><select class="form-control" name="category_id">
        <?php foreach($categories as $c):?><option value="<?=$c['id']?>" <?=$p['category_id']==$c['id']?'selected':''?>><?=e($c['name'])?></option><?php endforeach;?></select></div><div class="form-group"><label>Price</label>
            <input class="form-control" type="number" step="0.01" name="price" value="<?=$p['price']?>" required></div><div class="form-group"><label>Stock</label><input class="form-control" type="number" name="stock" value="<?=$p['stock']?>" min="0" required></div>
            <div class="form-group"><label>Description</label><textarea class="form-control" name="description" rows="6"><?=e($p['description'])?></textarea></div><div class="form-group"><label>Replace Image</label>
            <input class="form-control" type="file" name="image" accept=".jpg,.jpeg,.png,.webp"></div><?php if($p['image']):?><p><img class="product-thumb" src="../<?=e($p['image'])?>"></p><?php endif;?><label class="checkbox">
            <input type="checkbox" name="status" <?=$p['status']?'checked':''?>> Active product</label><br><button class="btn">Save Changes</button></form></div><?php include '../includes/admin_footer.php';?>