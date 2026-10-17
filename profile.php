<?php
require_once 'config/database.php';
require_once 'config/helpers.php';
requireLogin();

$st = $pdo->prepare('SELECT id, name, email, phone, password, profile_image FROM users WHERE id=?');
$st->execute([$_SESSION['user_id']]);
$user = $st->fetch();
if (!$user) {
    unset($_SESSION['user_id'], $_SESSION['user_name']);
    flash('error', 'Your account could not be found. Please sign in again.');
    redirect('login.php');
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $profileImage = $user['profile_image'];

    if ($name === '') $errors[] = 'Your name is required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid email address.';

    $emailCheck = $pdo->prepare('SELECT id FROM users WHERE email=? AND id<>?');
    $emailCheck->execute([$email, $user['id']]);
    if ($emailCheck->fetch()) $errors[] = 'That email address is already in use.';

    if ($newPassword !== '' || $confirmPassword !== '') {
        if (!password_verify($currentPassword, $user['password'])) $errors[] = 'Your current password is incorrect.';
        if (strlen($newPassword) < 6) $errors[] = 'Your new password must be at least 6 characters.';
        if ($newPassword !== $confirmPassword) $errors[] = 'The new passwords do not match.';
    }

    $upload = $_FILES['profile_image'] ?? null;
    if ($upload && $upload['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($upload['error'] !== UPLOAD_ERR_OK || $upload['size'] > 2 * 1024 * 1024) {
            $errors[] = 'Choose an image smaller than 2 MB.';
        } else {
            $image = @getimagesize($upload['tmp_name']);
            $extensions = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp'];
            if (!$image || !isset($extensions[$image[2]])) {
                $errors[] = 'Profile photos must be JPG, PNG, or WebP images.';
            } else {
                $directory = __DIR__ . '/assets/images/profiles';
                if (!is_dir($directory) && !mkdir($directory, 0755, true)) {
                    $errors[] = 'The profile image folder could not be created.';
                } else {
                    $filename = 'profile_' . bin2hex(random_bytes(16)) . '.' . $extensions[$image[2]];
                    if (!move_uploaded_file($upload['tmp_name'], $directory . '/' . $filename)) {
                        $errors[] = 'Your profile photo could not be uploaded.';
                    } else {
                        $profileImage = 'assets/images/profiles/' . $filename;
                    }
                }
            }
        }
    }

    if (!$errors) {
        if ($newPassword !== '') {
            $update = $pdo->prepare('UPDATE users SET name=?, email=?, phone=?, profile_image=?, password=? WHERE id=?');
            $update->execute([$name, $email, $phone, $profileImage, password_hash($newPassword, PASSWORD_DEFAULT), $user['id']]);
        } else {
            $update = $pdo->prepare('UPDATE users SET name=?, email=?, phone=?, profile_image=? WHERE id=?');
            $update->execute([$name, $email, $phone, $profileImage, $user['id']]);
        }
        $_SESSION['user_name'] = $name;
        flash('success', 'Your profile has been updated.');
        redirect('profile.php');
    }
    $user = array_merge($user, ['name' => $name, 'email' => $email, 'phone' => $phone, 'profile_image' => $profileImage]);
}

$pageTitle = 'My Profile - MobiXa';
include 'includes/header.php';
?>
<section class="section"><div class="container">
    <div class="section-title"><h1>My Profile</h1><a class="btn secondary" href="orders.php">My Orders</a></div>
    <?php if ($message = flash('success')): ?><div class="alert success"><?= e($message) ?></div><?php endif; ?>
    <?php foreach ($errors as $error): ?><div class="alert error"><?= e($error) ?></div><?php endforeach; ?>
    <div class="profile-layout">
        <aside class="profile-summary">
            <?php if ($user['profile_image']): ?><img class="profile-avatar" src="<?= e($user['profile_image']) ?>" alt="Profile photo of <?= e($user['name']) ?>">
            <?php else: ?><div class="profile-avatar profile-avatar--placeholder" aria-hidden="true"><?= e(strtoupper(mb_substr($user['name'], 0, 1))) ?></div><?php endif; ?>
            <h2><?= e($user['name']) ?></h2><p class="muted"><?= e($user['email']) ?></p>
            <p class="small muted">Upload a JPG, PNG, or WebP image up to 2 MB.</p>
        </aside>
        <div class="form-card profile-form"><h2>Account details</h2>
            <form method="post" enctype="multipart/form-data">
                <div class="form-group"><label for="name">Name</label><input class="form-control" id="name" name="name" value="<?= e($user['name']) ?>" required></div>
                <div class="form-group"><label for="email">Email</label><input class="form-control" id="email" type="email" name="email" value="<?= e($user['email']) ?>" required></div>
                <div class="form-group"><label for="phone">Phone</label><input class="form-control" id="phone" name="phone" value="<?= e($user['phone']) ?>"></div>
                <div class="form-group"><label for="profile_image">Profile photo</label><input class="form-control" id="profile_image" type="file" name="profile_image" accept="image/jpeg,image/png,image/webp"></div>
                <hr class="form-divider"><h2>Change password <span class="muted small">(optional)</span></h2>
                <div class="form-group"><label for="current_password">Current password</label><input class="form-control" id="current_password" type="password" name="current_password" autocomplete="current-password"></div>
                <div class="form-group"><label for="new_password">New password</label><input class="form-control" id="new_password" type="password" name="new_password" minlength="6" autocomplete="new-password"></div>
                <div class="form-group"><label for="confirm_password">Confirm new password</label><input class="form-control" id="confirm_password" type="password" name="confirm_password" minlength="6" autocomplete="new-password"></div>
                <button class="btn" type="submit">Save Profile</button>
            </form>
        </div>
    </div>
</div></section>
<?php include 'includes/footer.php'; ?>
