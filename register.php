<?php
require_once 'config/database.php'; require_once 'config/helpers.php';
if(isLoggedIn()) redirect('index.php');$errors=[];
if($_SERVER['REQUEST_METHOD']==='POST'){$name=trim($_POST['name']);$email=trim($_POST['email']);$phone=trim($_POST['phone']);$password=$_POST['password'];
if(!$name||!filter_var($email,FILTER_VALIDATE_EMAIL)||strlen($password)<6)$errors[]='Enter valid details. Password must be at least 6 characters.';
$st=$pdo->prepare("SELECT id FROM users WHERE email=?");$st->execute([$email]);if($st->fetch())$errors[]='Email already registered.';
if(!$errors){$st=$pdo->prepare("INSERT INTO users(name,email,phone,password) VALUES(?,?,?,?)");$st->execute([$name,$email,$phone,password_hash($password,PASSWORD_DEFAULT)]);
flash('success','Registration successful. Please login.');redirect('login.php');}}
$pageTitle='Register - MobiXa';include 'includes/header.php';
?><section class="section"><div class="form-card"><h1>Create Account</h1>
<?php foreach($errors as $e):?><div class="alert error"><?=e($e)?></div><?php endforeach;?><form method="post">
    <div class="form-group"><label>Name</label><input class="form-control" name="name" required></div><div class="form-group"><label>Email</label><input class="form-control" type="email" name="email" required></div>
    <div class="form-group"><label>Phone</label><input class="form-control" name="phone"></div><div class="form-group"><label>Password</label><input class="form-control" type="password" name="password" required></div>
     <button class="btn">Register</button></form></div></section><?php include 'includes/footer.php';?>