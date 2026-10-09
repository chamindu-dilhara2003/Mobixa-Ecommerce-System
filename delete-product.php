<?php require_once '../config/database.php';
require_once '../config/helpers.php';
requireAdmin();$id=(int)($_GET['id']??0);$st=$pdo->prepare("DELETE FROM products WHERE id=?");
$st->execute([$id]);flash('success','Product deleted.');redirect('products.php');?>