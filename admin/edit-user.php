<?php
require_once '../config/database.php';require_once '../config/helpers.php';requireAdmin();
$id=(int)($_GET['id']??0);
$st=$pdo->prepare("SELECT * FROM users WHERE id=?");$st->execute([$id]);$u=$st->fetch();if(!$u)die('User not found');
$errors=[];
if($_SERVER['REQUEST_METHOD']==='POST'){
    $name=trim($_POST['name']);
    $email=trim($_POST['email']);
    $phone=trim($_POST['phone']);
    $newPassword=trim($_POST['password']??'');

    if(!$name) $errors[]='Name is required.';
    if(!$email || !filter_var($email,FILTER_VALIDATE_EMAIL)) $errors[]='A valid email is required.';

    if(!$errors){
        // Make sure another user hasn't already taken this email.
        $chk=$pdo->prepare("SELECT id FROM users WHERE email=? AND id<>?");
        $chk->execute([$email,$id]);
        if($chk->fetch()) $errors[]='Another user already uses that email.';
    }

    if(!$errors){
        if($newPassword!==''){
            $hash=password_hash($newPassword,PASSWORD_DEFAULT);
            $st=$pdo->prepare("UPDATE users SET name=?,email=?,phone=?,password=? WHERE id=?");
            $st->execute([$name,$email,$phone,$hash,$id]);
        }else{
            $st=$pdo->prepare("UPDATE users SET name=?,email=?,phone=? WHERE id=?");
            $st->execute([$name,$email,$phone,$id]);
        }
        flash('success','User profile updated.');
        redirect('users.php');
    }
    // Keep the submitted values on screen if validation failed.
    $u['name']=$name;$u['email']=$email;$u['phone']=$phone;
}
$pageTitle='Edit User';include '../includes/admin_header.php';?>
<div class="admin-actions"><h1>Edit User</h1><a class="btn secondary" href="users.php">Back</a></div>
<?php foreach($errors as $er):?><div class="alert error"><?=e($er)?></div><?php endforeach;?>
<div class="form-card" style="margin:0;max-width:none"><form method="post">
    <div class="form-group"><label>Name</label>
        <input class="form-control" name="name" value="<?=e($u['name'])?>" required></div>
    <div class="form-group"><label>Email</label>
        <input class="form-control" type="email" name="email" value="<?=e($u['email'])?>" required></div>
    <div class="form-group"><label>Phone</label>
        <input class="form-control" name="phone" value="<?=e($u['phone'])?>"></div>
    <div class="form-group"><label>New Password <span class="muted small">(leave blank to keep current password)</span></label>
        <input class="form-control" type="password" name="password" placeholder="••••••••" autocomplete="new-password"></div>
    <button class="btn">Save Changes</button>
</form></div>
<?php include '../includes/admin_footer.php';?>
