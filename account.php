<?php
require_once '../config/database.php';
require_once '../config/helpers.php';
requireAdmin();

$st = $pdo->prepare('SELECT id, name, email, password FROM admins WHERE id=?');
$st->execute([$_SESSION['admin_id']]);
$admin = $st->fetch();
if (!$admin) {
    unset($_SESSION['admin_id'], $_SESSION['admin_name']);
    redirect('login.php');
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    if (!password_verify($currentPassword, $admin['password'])) $errors[] = 'Your current password is incorrect.';
    if (strlen($newPassword) < 6) $errors[] = 'Your new password must be at least 6 characters.';
    if ($newPassword !== $confirmPassword) $errors[] = 'The new passwords do not match.';
    if (!$errors) {
        $update = $pdo->prepare('UPDATE admins SET password=? WHERE id=?');
        $update->execute([password_hash($newPassword, PASSWORD_DEFAULT), $admin['id']]);
        flash('success', 'Your admin password has been changed.');
        redirect('account.php');
    }
}

$pageTitle = 'My Account - MobiXa Admin';
include '../includes/admin_header.php';
?>
<div class="admin-actions"><h1>My Account</h1><a class="btn secondary" href="index.php">Dashboard</a></div>
<?php if ($message = flash('success')): ?><div class="alert success"><?= e($message) ?></div><?php endif; ?>
<?php foreach ($errors as $error): ?><div class="alert error"><?= e($error) ?></div><?php endforeach; ?>
<div class="form-card admin-account-card"><h2><?= e($admin['name']) ?></h2><p class="muted"><?= e($admin['email']) ?></p><hr class="form-divider">
    <form method="post">
        <div class="form-group"><label for="current_password">Current password</label><input class="form-control" id="current_password" type="password" name="current_password" required autocomplete="current-password"></div>
        <div class="form-group"><label for="new_password">New password</label><input class="form-control" id="new_password" type="password" name="new_password" required minlength="6" autocomplete="new-password"></div>
        <div class="form-group"><label for="confirm_password">Confirm new password</label><input class="form-control" id="confirm_password" type="password" name="confirm_password" required minlength="6" autocomplete="new-password"></div>
        <button class="btn" type="submit">Change Password</button>
    </form>
</div>
<?php include '../includes/admin_footer.php'; ?>
