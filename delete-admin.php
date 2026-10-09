<?php require_once '../config/database.php';require_once '../config/helpers.php';requireAdmin();
$id=(int)($_GET['id']??0);

// Never allow an admin to delete their own account (would lock them out
// mid-session) or delete the last remaining admin in the system.
if($id===(int)$_SESSION['admin_id']){
    flash('error',"You can't delete your own account while logged in.");
    redirect('admins.php');
}

$count=(int)$pdo->query("SELECT COUNT(*) FROM admins")->fetchColumn();
if($count<=1){
    flash('error','At least one admin account must remain.');
    redirect('admins.php');
}

$st=$pdo->prepare("SELECT id FROM admins WHERE id=?");
$st->execute([$id]);
if($st->fetch()){
    $del=$pdo->prepare("DELETE FROM admins WHERE id=?");
    $del->execute([$id]);
    flash('success','Admin removed.');
}else{
    flash('error','Admin not found.');
}
redirect('admins.php');
?>
