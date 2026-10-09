CREATE DATABASE IF NOT EXISTS mobixa CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE mobixa;

CREATE TABLE admins (
 id INT AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(100) NOT NULL,
 email VARCHAR(150) NOT NULL UNIQUE,
 password VARCHAR(255) NOT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE users (
 id INT AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(100) NOT NULL,
 email VARCHAR(150) NOT NULL UNIQUE,
 phone VARCHAR(30),
 profile_image VARCHAR(255) NULL,
 password VARCHAR(255) NOT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categories (
 id INT AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE products (
 id INT AUTO_INCREMENT PRIMARY KEY,
 category_id INT NULL,
 name VARCHAR(180) NOT NULL,
 price DECIMAL(10,2) NOT NULL DEFAULT 0,
 stock INT NOT NULL DEFAULT 0,
 description TEXT,
 image VARCHAR(255),
 status TINYINT(1) NOT NULL DEFAULT 1,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(category_id) REFERENCES categories(id) ON DELETE SET NULL
);

CREATE TABLE orders (
 id INT AUTO_INCREMENT PRIMARY KEY,
 user_id INT NOT NULL,
 total_amount DECIMAL(10,2) NOT NULL,
 address TEXT NOT NULL,
 city VARCHAR(100) NOT NULL,
 phone VARCHAR(30) NOT NULL,
 payment_method ENUM('cod','card','payhere') NOT NULL DEFAULT 'cod',
 payment_status VARCHAR(20) NOT NULL DEFAULT 'Pending',
 payhere_payment_id VARCHAR(100) NULL,
 status ENUM('Pending','Processing','Shipped','Delivered','Cancelled') NOT NULL DEFAULT 'Pending',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE order_items (
 id INT AUTO_INCREMENT PRIMARY KEY,
 order_id INT NOT NULL,
 product_id INT NOT NULL,
 quantity INT NOT NULL,
 price DECIMAL(10,2) NOT NULL,
 FOREIGN KEY(order_id) REFERENCES orders(id) ON DELETE CASCADE,
 FOREIGN KEY(product_id) REFERENCES products(id) ON DELETE RESTRICT
);

INSERT INTO categories(name) VALUES
('Phone Cases'),('Chargers'),('Cables'),('Earbuds'),('Power Banks'),('Screen Protectors'),('Phone Holders');

INSERT INTO admins(name,email,password) VALUES
('MobiXa Admin','admin@mobixa.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC7r8K1hB0p3yF8Vh7uK');

INSERT INTO products(category_id,name,price,stock,description,image,status) VALUES
(1,'Premium Silicone Phone Case',1800,25,'Durable silicone case with a comfortable grip and everyday protection.','',1),
(2,'20W Fast Charger',3500,20,'Compact USB-C fast charger for compatible devices.','',1),
(3,'USB-C Fast Charging Cable',1500,40,'Strong braided cable for charging and data transfer.','',1),
(4,'Wireless Bluetooth Earbuds',6500,15,'Compact wireless earbuds with charging case.','',1);
-- Demo admin password is: password

-- ---------------------------------------------------------------------------
-- Migration: run this ONLY if your `mobixa` database already existed before
-- the payment_method column was added to `orders` (skip it on a fresh install,
-- since CREATE TABLE above already includes the column).
-- ---------------------------------------------------------------------------
-- ALTER TABLE orders ADD COLUMN payment_method ENUM('cod','card') NOT NULL DEFAULT 'cod' AFTER phone;

-- Existing installations: run database/profile_migration.sql once to add
-- the customer profile photo column.
