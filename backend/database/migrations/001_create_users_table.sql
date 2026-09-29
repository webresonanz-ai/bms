-- =============================================================
-- Migration: 001 — Create users table
-- Batavia Madrigal Singers
-- Run once against your MySQL database:
--   mysql -u root -p batavia_madrigal < 001_create_users_table.sql
-- =============================================================

CREATE DATABASE IF NOT EXISTS batavia_madrigal
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE batavia_madrigal;

CREATE TABLE IF NOT EXISTS users (
    id             INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    name           VARCHAR(100)    NOT NULL,
    email          VARCHAR(191)    NOT NULL,
    password_hash  VARCHAR(255)    NOT NULL,
    role           ENUM('member', 'admin') NOT NULL DEFAULT 'member',
    created_at     DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at     DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP
                                           ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    UNIQUE KEY uq_users_email (email)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;
