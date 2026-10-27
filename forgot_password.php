<?php
require_once 'config/database.php';
require_once 'config/helpers.php';
if (isLoggedIn()) redirect('index.php');
$message = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        // Always show the same response to avoid revealing registered accounts.
        $stmt = $pdo->prepare('SELECT id, name FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user) {
            $token = bin2hex(random_bytes(32));
            $tokenHash = hash('sha256', $token);
            $pdo->prepare('DELETE FROM password_resets WHERE user_id = ?')->execute([$user['id']]);
            $pdo->prepare('INSERT INTO password_resets (user_id, token_hash, expires_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 1 HOUR))')->execute([$user['id'], $tokenHash]);
            $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
            $base = ($https ? 'https://' : 'http://') . ($_SERVER['HTTP_HOST'] ?? 'localhost') . rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? '/'), '/\\');
            $resetUrl = $base . '/reset_password.php?token=' . urlencode($token);
            $subject = 'Reset your MobiXa password';
            $body = "Hello {$user['name']},\n\nWe received a request to reset your MobiXa password. Use this link within one hour:\n\n{$resetUrl}\n\nIf you did not request this, you can ignore this email.";
            $headers = "From: no-reply@mobixa.local\r\n" . "Content-Type: text/plain; charset=UTF-8\r\n";
            // PHP mail() requires a working mail server configuration (SMTP on Windows/XAMPP).
            if (!@mail($email, $subject, $body, $headers)) {
                // Do not expose the reset token in the browser; configure SMTP to deliver the email.
                error_log('MobiXa password reset email could not be sent. Configure SMTP/mail() in PHP.');
            }
        }
        $message = 'If an account exists for that email, a password reset link will be sent. Please check your inbox and spam folder.';
    }
}
$pageTitle = 'Forgot Password - MobiXa';
include 'includes/header.php';
?>
<section class="section"><div class="form-card">
    <h1>Forgot Password?</h1>
    <p>Enter the email address linked to your MobiXa account. We will email you a secure reset link that expires in one hour.</p>
    <?php if ($message): ?><div class="alert success"><?= e($message) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
    <form method="post">
        <div class="form-group"><label for="email">Email</label><input id="email" class="form-control" type="email" name="email" required autocomplete="email"></div>
        <button class="btn" type="submit">Send Reset Link</button>
    </form>
    <p><a href="login.php">Back to login</a></p>
</div></section>
<?php include 'includes/footer.php'; ?>
