-- =============================================================
-- Migration: 004 — Add image + date columns to gallery_items
-- Batavia Madrigal Singers
-- Run once against your MySQL database:
--   mysql -u root -p batavia_madrigal < 004_add_image_to_gallery.sql
-- =============================================================

USE batavia_madrigal;

-- Photo file path (relative, e.g. uploads/gallery/abc123.webp) or remote URL
SET @col_exists := (
  SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'gallery_items' AND COLUMN_NAME = 'image_url'
);
SET @ddl := IF(@col_exists = 0,
  'ALTER TABLE gallery_items ADD COLUMN image_url VARCHAR(512) NULL AFTER icon',
  'SELECT 1');
PREPARE stmt FROM @ddl; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Photo date (shown on public gallery + admin)
SET @col_exists := (
  SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'gallery_items' AND COLUMN_NAME = 'photo_date'
);
SET @ddl := IF(@col_exists = 0,
  'ALTER TABLE gallery_items ADD COLUMN photo_date DATE NULL AFTER image_url',
  'SELECT 1');
PREPARE stmt FROM @ddl; EXECUTE stmt; DEALLOCATE PREPARE stmt;
