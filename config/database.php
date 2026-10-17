<?php
$host = '127.0.0.1';
$db   = 'mobixa';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Database connection failed. Please check config/database.php and make sure MySQL is running.");
}

// Existing installations may have been created before customer profile photos
// were introduced. Add the backwards-compatible column on first load. If the
// database account cannot alter tables, the supplied profile_migration.sql can
// still be run by an administrator without preventing the rest of the site
// from connecting.
try {
    $usersTable = $pdo->query("SHOW TABLES LIKE 'users'")->fetchColumn();
    if ($usersTable) {
        $profileImageColumn = $pdo->query("SHOW COLUMNS FROM users LIKE 'profile_image'")->fetch();
        if (!$profileImageColumn) {
            $pdo->exec("ALTER TABLE users ADD COLUMN profile_image VARCHAR(255) NULL AFTER phone");
        }
    }

    $ordersTable = $pdo->query("SHOW TABLES LIKE 'orders'")->fetchColumn();
    if ($ordersTable) {
        $paymentMethodColumn = $pdo->query("SHOW COLUMNS FROM orders LIKE 'payment_method'")->fetch();
        if ($paymentMethodColumn && !str_contains($paymentMethodColumn['Type'], "'payhere'")) {
            $pdo->exec("ALTER TABLE orders MODIFY payment_method ENUM('cod','card','payhere') NOT NULL DEFAULT 'cod'");
        }
        $paymentStatusColumn = $pdo->query("SHOW COLUMNS FROM orders LIKE 'payment_status'")->fetch();
        if (!$paymentStatusColumn) {
            $pdo->exec("ALTER TABLE orders ADD COLUMN payment_status VARCHAR(20) NOT NULL DEFAULT 'Pending' AFTER payment_method");
        }
        $paymentIdColumn = $pdo->query("SHOW COLUMNS FROM orders LIKE 'payhere_payment_id'")->fetch();
        if (!$paymentIdColumn) {
            $pdo->exec("ALTER TABLE orders ADD COLUMN payhere_payment_id VARCHAR(100) NULL AFTER payment_status");
        }
    }
} catch (PDOException $e) {
    // Leave legacy databases usable; the profile page will work after the
    // included migration has been applied if ALTER permission is restricted.
}
?>
