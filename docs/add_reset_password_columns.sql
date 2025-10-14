-- SQL untuk menambahkan kolom reset password ke tabel users
-- Jalankan query ini di phpMyAdmin atau MySQL client Anda

ALTER TABLE users
ADD COLUMN reset_token VARCHAR(100) NULL DEFAULT NULL,
ADD COLUMN reset_token_expiry DATETIME NULL DEFAULT NULL;

-- Verifikasi struktur tabel
DESCRIBE users;
