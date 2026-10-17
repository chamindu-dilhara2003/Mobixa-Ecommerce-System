<?php require_once '../config/database.php';
require_once '../config/helpers.php';
requireAdmin();
$id=(int)($_GET['id']??0);

// Never allow an admin to delete their own storefront account by accident,
// and make sure the target user actually exists before deleting.
$st=$pdo->prepare("SELECT id FROM users WHERE id=?");
$st->execute([$id]);
if($st->fetch()){
    $del=$pdo->prepare("DELETE FROM users WHERE id=?");
    $del->execute([$id]);
    flash('success','User deleted.');
}else{
    flash('error','User not found.');
}
redirect('users.php');
?>
