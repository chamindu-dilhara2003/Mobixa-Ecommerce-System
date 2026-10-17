<?php
require_once 'config/database.php'; require_once 'config/helpers.php';
if(isLoggedIn()) redirect('index.php');$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){$email=trim($_POST['email']);$password=$_POST['password'];
$st=$pdo->prepare("SELECT * FROM users WHERE email=?");$st->execute([$email]);$u=$st->fetch();if($u&&password_verify($password,$u['password'])){$_SESSION['user_id']=$u['id'];
$_SESSION['user_name']=$u['name'];redirect('index.php');}$error='Invalid email or password.';}
$pageTitle='Login - MobiXa';include 'includes/header.php';
?><section class="auth-page auth-page--customer">

    <div class="auth-visual">
        <div class="auth-glow"></div>
        <div class="auth-float f1">📱</div>
        <div class="auth-float f2">🎧</div>
        <div class="auth-float f3">🔌</div>
        <div class="auth-float f4">🔋</div>

        <div class="auth-visual-text">
            <span class="auth-badge">WELCOME BACK</span>
            <h2>Your favourite mobile accessories, one login away.</h2>
            <p>Track orders, save your details and check out faster every time.</p>
        </div>
    </div>

    <div class="auth-form-wrap">
        <div class="form-card">
            <h1>Customer Login</h1>
            <?php if($m=flash('success')):?>
            <div class="alert success"><?=e($m)?></div><?php endif;?><?php if($error):?><div class="alert error"><?=e($error)?></div>
                <?php endif;?><form method="post"><div class="form-group"><label>Email</label><input class="form-control" type="email" name="email" required></div>
                <div class="form-group"><label>Password</label><input class="form-control" type="password" name="password" required></div><button class="btn">Login</button></form><p>Don't have an account? <a href="register.php">Register</a></p>
        </div>
    </div>

</section>
    <?php include 'includes/footer.php';?>