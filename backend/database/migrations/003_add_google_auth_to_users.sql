-- =============================================================
-- Migration: 003 — Add Google auth columns to users table
-- Batavia Madrigal Singers
-- Run once against your MySQL database:
--   mysql -u root -p batavia_madrigal < 003_add_google_auth_to_users.sql
-- =============================================================

USE batavia_madrigal;

-- Allow password-less accounts (Google sign-in users have NULL hash)
ALTER TABLE users
  MODIFY COLUMN password_hash VARCHAR(255) NULL;

-- Google identity columns (safe to re-run: each ADD is guarded)
SET @col_exists := (
  SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'google_id'
);
SET @ddl := IF(@col_exists = 0,
  'ALTER TABLE users ADD COLUMN google_id VARCHAR(255) NULL AFTER role',
  'SELECT 1');
PREPARE stmt FROM @ddl; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists := (
  SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'avatar_url'
);
SET @ddl := IF(@col_exists = 0,
  'ALTER TABLE users ADD COLUMN avatar_url VARCHAR(512) NULL AFTER google_id',
  'SELECT 1');
PREPARE stmt FROM @ddl; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Unique index on google_id (only one account per Google identity)
SET @idx_exists := (
  SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND INDEX_NAME = 'uq_users_google_id'
);
SET @ddl := IF(@idx_exists = 0,
  'ALTER TABLE users ADD UNIQUE KEY uq_users_google_id (google_id)',
  'SELECT 1');
PREPARE stmt FROM @ddl; EXECUTE stmt; DEALLOCATE PREPARE stmt;
