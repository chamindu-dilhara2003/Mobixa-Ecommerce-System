<?php
require_once __DIR__ . '/../config/helpers.php';
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($pageTitle ?? 'MobiXa') ?></title>
<script>
  // Apply the saved preference before the page is painted. This key is shared
  // by the storefront and the admin area, so the setting follows users sitewide.
  try {
    const savedTheme = localStorage.getItem('mobixa-theme');
    document.documentElement.dataset.theme = savedTheme ||
      (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
  } catch (error) {
    document.documentElement.dataset.theme = 'light';
  }
</script>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body><div class="page-loading" aria-hidden="true"><div class="loader"></div></div>
<header class="site-header">
  <div class="container nav">
    <a class="logo" href="index.php">Mobi<span>Xa</span></a>
    <button class="menu-toggle" onclick="document.querySelector('.nav-links').classList.toggle('show')">☰</button>
    <nav class="nav-links">
      <a href="index.php">Home</a>
      <a href="products.php">Products</a>
      <a href="cart.php">Cart <b class="badge"><?= cartCount() ?></b></a>
      <?php if (isLoggedIn()): ?>
        <a href="profile.php">My Profile</a>
        <a href="orders.php">My Orders</a>
        <a href="logout.php">Logout</a>
      <?php else: ?>
        <a href="login.php">Login</a>
        <a class="nav-button" href="register.php">Register</a>
      <?php endif; ?>
      <button class="theme-toggle" type="button" aria-label="Switch to dark mode" title="Switch to dark mode">☾</button>
    </nav>
  </div>
</header>
<main>
