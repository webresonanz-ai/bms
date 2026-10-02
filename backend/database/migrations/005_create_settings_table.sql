-- =============================================================
-- Migration: 005 — Create settings table (site-wide key/value store)
-- Batavia Madrigal Singers
-- Run once against your MySQL database:
--   mysql -u root -p batavia_madrigal < 005_create_settings_table.sql
-- =============================================================

USE batavia_madrigal;

CREATE TABLE IF NOT EXISTS settings (
    `key`        VARCHAR(191)  NOT NULL,
    `value`      TEXT          NULL,
    created_at   DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at   DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP
                                       ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (`key`)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- Default: no hero background (NULL = gradient only)
INSERT IGNORE INTO settings (`key`, `value`) VALUES
('hero_background', NULL);
