-- Run this once only when updating an existing MobiXa database and automatic
-- schema updates are not permitted for the configured database account.
ALTER TABLE orders MODIFY payment_method ENUM('cod','card','payhere') NOT NULL DEFAULT 'cod';
ALTER TABLE orders ADD COLUMN payment_status VARCHAR(20) NOT NULL DEFAULT 'Pending' AFTER payment_method;
ALTER TABLE orders ADD COLUMN payhere_payment_id VARCHAR(100) NULL AFTER payment_status;
