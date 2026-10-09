<?php
require_once '../config/database.php';
require_once '../config/helpers.php';

if (isAdmin()) redirect('index.php');

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $st = $pdo->prepare("SELECT * FROM admins WHERE email=?");
    $st->execute([$email]);
    $a = $st->fetch();

    if ($a && password_verify($password, $a['password'])) {
        $_SESSION['admin_id'] = $a['id'];
        $_SESSION['admin_name'] = $a['name'];
        redirect('index.php');
    }

    $error = 'Invalid admin login.';
}

$pageTitle = 'Admin Login';
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($pageTitle) ?></title>
<script>
  try {
    const savedTheme = localStorage.getItem('mobixa-theme');
    document.documentElement.dataset.theme = savedTheme ||
      (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
  } catch (error) {
    document.documentElement.dataset.theme = 'light';
  }
</script>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body><div class="page-loading" aria-hidden="true"><div class="loader"></div></div>

<section class="auth-page auth-page--admin">

    <div class="auth-visual">
        <div class="auth-grid"></div>
        <div class="auth-glow"></div>
        <div class="auth-lock">🔒</div>

        <div class="auth-visual-text">
            <a class="logo" href="../index.php">Mobi<span>Xa</span></a>
            <span class="auth-badge">RESTRICTED ACCESS</span>
            <h2>MobiXa Admin Console</h2>
            <p>Manage products, orders and customers from one place.</p>
        </div>
    </div>

    <div class="auth-form-wrap">
        <div class="form-card">

            <h1>Admin Login</h1>

            <?php if ($error): ?>
                <div class="alert error"><?= e($error) ?></div>
            <?php endif; ?>

            <form method="post">
                <div class="form-group">
                    <label>Email</label>
                    <input class="form-control" type="email" name="email" required autofocus>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input class="form-control" type="password" name="password" required>
                </div>
                <button class="btn">Login</button>
            </form>

            <p><a class="back-link" href="../index.php">← Back to store</a></p>

        </div>
    </div>

</section>

<script src="../assets/js/script.js"></script>
</body>
</html>
