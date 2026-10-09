<?php
require_once '../config/database.php';require_once '../config/helpers.php';requireAdmin();$categories=$pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();$errors=[];
if($_SERVER['REQUEST_METHOD']==='POST'){$name=trim($_POST['name']);$cat=(int)$_POST['category_id'];
$price=(float)$_POST['price'];$stock=(int)$_POST['stock'];$desc=trim($_POST['description']);$status=isset($_POST['status'])?1:0;$image='';
if(isset($_FILES['image'])&&$_FILES['image']['error']===UPLOAD_ERR_OK){$ext=strtolower(pathinfo($_FILES['image']['name'],PATHINFO_EXTENSION));
$allowed=['jpg','jpeg','png','webp'];if(!in_array($ext,$allowed))$errors[]='Only JPG, PNG and WEBP images are allowed.';
else{$nameFile=uniqid('product_',true).'.'.$ext;$dir=__DIR__.'/../assets/images/products';if(!is_dir($dir))mkdir($dir,0777,true);move_uploaded_file($_FILES['image']['tmp_name'],$dir.'/'.$nameFile);
$image='assets/images/products/'.$nameFile;}}
if(!$name||$price<0||$stock<0)$errors[]='Please enter valid product details.';
if(!$errors){$st=$pdo->prepare("INSERT INTO products(name,category_id,price,stock,description,image,status) VALUES(?,?,?,?,?,?,?)");
$st->execute([$name,$cat,$price,$stock,$desc,$image,$status]);flash('success','Product added successfully.');redirect('products.php');}}
$pageTitle='Add Product';include '../includes/admin_header.php';?><div class="admin-actions"><h1>Add Product</h1><a class="btn secondary" href="products.php">Back</a></div>
<?php foreach($errors as $er):?><div class="alert error"><?=e($er)?></div><?php endforeach;?><div class="form-card" style="margin:0;max-width:none">
    <form method="post" enctype="multipart/form-data"><div class="form-group"><label>Product Name</label><input class="form-control" name="name" required></div><div class="form-group"><label>Category</label>
    <select class="form-control" name="category_id" required><?php foreach($categories as $c):?><option value="<?=$c['id']?>"><?=e($c['name'])?></option><?php endforeach;?></select></div><div class="form-group"><label>Price</label>
        <input class="form-control" type="number" step="0.01" name="price" required></div><div class="form-group"><label>Stock</label><input class="form-control" type="number" name="stock" min="0" required></div>
        <div class="form-group"><label>Description</label><textarea class="form-control" name="description" rows="6"></textarea></div><div class="form-group"><label>Product Image</label><input class="form-control" type="file" name="image" accept=".jpg,.jpeg,.png,.webp"></div>
        <label class="checkbox"><input type="checkbox" name="status" checked> Active product</label><br><button class="btn">Add Product</button></form></div><?php include '../includes/admin_footer.php';?>