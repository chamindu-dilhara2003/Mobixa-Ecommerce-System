<?php
if (session_status() === PHP_SESSION_NONE) session_start();

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function redirect($url) {
    header("Location: $url");
    exit;
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['admin_id']);
}

function requireLogin() {
    if (!isLoggedIn()) redirect('login.php');
}

function requireAdmin() {
    if (!isAdmin()) redirect('login.php');
}

function cartCount() {
    return array_sum($_SESSION['cart'] ?? []);
}

function money($value) {
    return 'Rs. ' . number_format((float)$value, 2);
}

function flash($key, $message = null) {
    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
        return;
    }
    $msg = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);
    return $msg;
}
?>