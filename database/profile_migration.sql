-- Run this once only when updating an existing MobiXa database.
ALTER TABLE users ADD COLUMN profile_image VARCHAR(255) NULL AFTER phone;
