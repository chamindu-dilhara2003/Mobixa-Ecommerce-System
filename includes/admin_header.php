<?php require_once __DIR__ . '/../config/helpers.php'; ?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($pageTitle ?? 'MobiXa Admin') ?></title>
<script>
  try {
    const savedTheme = localStorage.getItem('mobixa-theme');
    document.documentElement.dataset.theme = savedTheme ||
      (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
  } catch (error) {
    document.documentElement.dataset.theme = 'light';
  }
</script>
<link rel="stylesheet" href="../assets/css/style.css?v=20261006">
<link rel="stylesheet" href="../assets/css/admin.css?v=20261006">
</head>
<body class="admin-body">
<header class="admin-top">
  <div class="container nav">
    <a class="logo" href="index.php">Mobi<span>Xa</span> Admin</a>
    <nav class="nav-links">
      <a href="index.php">Dashboard</a>
      <a href="products.php">Products</a>
      <a href="orders.php">Orders</a>
      <a href="users.php">Users</a>
      <a href="admins.php">Admins</a>
      <a href="account.php">My Account</a>
      <a href="logout.php">Logout</a>
      <button class="theme-toggle" type="button" aria-label="Switch to dark mode" title="Switch to dark mode">☾</button>
    </nav>
  </div>
</header>
<main class="container admin-main">
