<?php require_once '../config/database.php';require_once '../config/helpers.php';requireAdmin();
$errors=[];
if($_SERVER['REQUEST_METHOD']==='POST'){
    $name=trim($_POST['name']);
    $email=trim($_POST['email']);
    $password=trim($_POST['password']??'');
    $confirm=trim($_POST['confirm_password']??'');

    if(!$name) $errors[]='Name is required.';
    if(!$email || !filter_var($email,FILTER_VALIDATE_EMAIL)) $errors[]='A valid email is required.';
    if(strlen($password)<6) $errors[]='Password must be at least 6 characters.';
    if($password!==$confirm) $errors[]='Passwords do not match.';

    if(!$errors){
        $chk=$pdo->prepare("SELECT id FROM admins WHERE email=?");
        $chk->execute([$email]);
        if($chk->fetch()) $errors[]='An admin with that email already exists.';
    }

    if(!$errors){
        $hash=password_hash($password,PASSWORD_DEFAULT);
        $st=$pdo->prepare("INSERT INTO admins(name,email,password) VALUES(?,?,?)");
        $st->execute([$name,$email,$hash]);
        flash('success','Admin added successfully.');
        redirect('admins.php');
    }
}
$pageTitle='Add Admin';include '../includes/admin_header.php';?>
<div class="admin-actions"><h1>Add Admin</h1><a class="btn secondary" href="admins.php">Back</a></div>
<?php foreach($errors as $er):?><div class="alert error"><?=e($er)?></div><?php endforeach;?>
<div class="form-card" style="margin:0;max-width:none"><form method="post">
    <div class="form-group"><label>Name</label>
        <input class="form-control" name="name" value="<?=e($_POST['name']??'')?>" required></div>
    <div class="form-group"><label>Email</label>
        <input class="form-control" type="email" name="email" value="<?=e($_POST['email']??'')?>" required></div>
    <div class="form-group"><label>Password</label>
        <input class="form-control" type="password" name="password" required minlength="6"></div>
    <div class="form-group"><label>Confirm Password</label>
        <input class="form-control" type="password" name="confirm_password" required minlength="6"></div>
    <button class="btn">Add Admin</button>
</form></div>
<?php include '../includes/admin_footer.php';?>
