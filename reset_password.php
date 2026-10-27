<?php
require_once 'config/database.php';
require_once 'config/helpers.php';
if (isLoggedIn()) redirect('index.php');
$token = trim($_GET['token'] ?? $_POST['token'] ?? '');
$error = '';
$success = false;
$reset = null;
if (preg_match('/^[a-f0-9]{64}$/', $token)) {
    $stmt = $pdo->prepare('SELECT id, user_id FROM password_resets WHERE token_hash = ? AND expires_at > NOW() LIMIT 1');
    $stmt->execute([hash('sha256', $token)]);
    $reset = $stmt->fetch();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    if (!$reset) {
        $error = 'This reset link is invalid or has expired. Please request a new one.';
    } elseif (strlen($password) < 8) {
        $error = 'Your new password must contain at least 8 characters.';
    } elseif ($password !== $confirm) {
        $error = 'The passwords do not match.';
    } else {
        $pdo->beginTransaction();
        try {
            $pdo->prepare('UPDATE users SET password = ? WHERE id = ?')->execute([password_hash($password, PASSWORD_DEFAULT), $reset['user_id']]);
            $pdo->prepare('DELETE FROM password_resets WHERE user_id = ?')->execute([$reset['user_id']]);
            $pdo->commit();
            $success = true;
        } catch (Throwable $e) {
            $pdo->rollBack();
            $error = 'Unable to reset your password right now. Please try again.';
        }
    }
}
$pageTitle = 'Reset Password - MobiXa';
include 'includes/header.php';
?>
<section class="section"><div class="form-card">
    <h1>Reset Password</h1>
    <?php if ($success): ?>
        <div class="alert success">Your password has been reset successfully. You can now log in with your new password.</div>
        <a class="btn" href="login.php">Go to Login</a>
    <?php elseif (!$reset): ?>
        <div class="alert error">This reset link is invalid or has expired. Please request a new one.</div>
        <p><a href="forgot_password.php">Request another reset link</a></p>
    <?php else: ?>
        <?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
        <p>Choose a new password (at least 8 characters).</p>
        <form method="post">
            <input type="hidden" name="token" value="<?= e($token) ?>">
            <div class="form-group"><label for="password">New Password</label><input id="password" class="form-control" type="password" name="password" minlength="8" required autocomplete="new-password"></div>
            <div class="form-group"><label for="confirm_password">Confirm New Password</label><input id="confirm_password" class="form-control" type="password" name="confirm_password" minlength="8" required autocomplete="new-password"></div>
            <button class="btn" type="submit">Update Password</button>
        </form>
    <?php endif; ?>
</div></section>
<?php include 'includes/footer.php'; ?>
