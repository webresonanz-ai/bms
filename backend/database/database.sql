-- =============================================================
-- Batavia Madrigal Singers — Full database setup (single file)
-- Consolidated from:
--   001_create_users_table.sql
--   002_create_content_tables.sql
--   003_add_google_auth_to_users.sql
--
-- Fresh install:
--   mysql -u root -p < backend/database/database.sql
-- =============================================================

CREATE DATABASE IF NOT EXISTS batavia_madrigal
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE batavia_madrigal;

-- ── Users (auth: password + Google OAuth) ──────────────────────
CREATE TABLE IF NOT EXISTS users (
    id             INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    name           VARCHAR(100)    NOT NULL,
    email          VARCHAR(191)    NOT NULL,
    password_hash  VARCHAR(255)    NULL,
    role           ENUM('member', 'admin') NOT NULL DEFAULT 'member',
    google_id      VARCHAR(255)    NULL,
    avatar_url     VARCHAR(512)    NULL,
    created_at     DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at     DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP
                                           ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    UNIQUE KEY uq_users_email (email),
    UNIQUE KEY uq_users_google_id (google_id)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- ── Members ────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS members (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `nickname` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `stage_name` varchar(100) DEFAULT NULL,
  `birth_place` varchar(100) DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `domicile` varchar(150) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `year_join` varchar(10) DEFAULT NULL,
  `field_of_work` varchar(100) DEFAULT NULL,
  `role` enum('Sopran','Alto','Tenor','Bass') DEFAULT NULL,
  `section` varchar(100) DEFAULT NULL,
  `join_date` date DEFAULT NULL,
  `status` enum('active','passive') NOT NULL DEFAULT 'active',
  `performances` int(11) NOT NULL DEFAULT 0,
  `avatar_url` varchar(255) NOT NULL DEFAULT 'https://voca-land.sgp1.cdn.digitaloceanspaces.com/0/1757684222527/9465e2e8.jpg',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Events ─────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS events (
    id          INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    title       VARCHAR(200)    NOT NULL,
    date        DATE            NOT NULL,
    time        TIME            NOT NULL,
    venue       VARCHAR(200)    NOT NULL,
    city        VARCHAR(100)    NOT NULL,
    description TEXT            NOT NULL,
    status      ENUM('upcoming','past') NOT NULL DEFAULT 'upcoming',
    created_at  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP
                                         ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Gallery items ──────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS gallery_items (
    id          INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    title       VARCHAR(200)    NOT NULL,
    category    VARCHAR(100)    NOT NULL,
    icon        VARCHAR(60)     NOT NULL DEFAULT 'bi-image',
    sort_order  INT             NOT NULL DEFAULT 0,
    created_at  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP
                                         ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Seed members ───────────────────────────────────────────────
INSERT IGNORE INTO members (id, name, nickname, email, stage_name, birth_place, birth_date, domicile, phone, year_join, field_of_work, role, section, join_date, status, performances, avatar_url, created_at, updated_at) VALUES
(1, 'Aria Wijaya', 'Aria', 'aria@batavia.or.id', 'Aria', 'Jakarta', '2002-05-15', 'Jakarta', '081234567890', '2015', 'Performance', 'Sopran', 'Soprano', '2015-01-15', 'active', 15, 'https://voca-land.sgp1.cdn.digitaloceanspaces.com/0/1757684222527/9465e2e8.jpg', current_timestamp(), current_timestamp()),
(2, 'Bunga Lestari', 'Bunga', 'bunga@batavia.or.id', 'Bunga', 'Bandung', '2001-08-22', 'Bandung', '081234567891', '2016', 'Performance', 'Sopran', 'Soprano', '2016-03-01', 'active', 12, 'https://voca-land.sgp1.cdn.digitaloceanspaces.com/0/1757684222527/9465e2e8.jpg', current_timestamp(), current_timestamp()),
(3, 'Citra Dewi', 'Citra', 'citra@batavia.or.id', 'Citra', 'Semarang', '1999-11-30', 'Semarang', '081234567892', '2018', 'Performance', 'Sopran', 'Soprano', '2018-06-10', 'active', 8, 'https://voca-land.sgp1.cdn.digitaloceanspaces.com/0/1757684222527/9465e2e8.jpg', current_timestamp(), current_timestamp()),
(4, 'Damar Pratama', 'Damar', 'damar@batavia.or.id', 'Damar', 'Yogyakarta', '1998-03-10', 'Yogyakarta', '081234567893', '2014', 'Performance', 'Alto', 'Alto', '2014-09-20', 'active', 20, 'https://voca-land.sgp1.cdn.digitaloceanspaces.com/0/1757684222527/9465e2e8.jpg', current_timestamp(), current_timestamp()),
(5, 'Eka Sari', 'Eka', 'eka@batavia.or.id', 'Eka', 'Surabaya', '2000-07-25', 'Surabaya', '081234567894', '2017', 'Performance', 'Alto', 'Alto', '2017-02-14', 'active', 18, 'https://voca-land.sgp1.cdn.digitaloceanspaces.com/0/1757684222527/9465e2e8.jpg', current_timestamp(), current_timestamp()),
(6, 'Fajar Nugroho', 'Fajar', 'fajar@batavia.or.id', 'Fajar', 'Medan', '1997-12-05', 'Medan', '081234567895', '2013', 'Performance', 'Tenor', 'Tenor', '2013-10-05', 'active', 25, 'https://voca-land.sgp1.cdn.digitaloceanspaces.com/0/1757684222527/9465e2e8.jpg', current_timestamp(), current_timestamp()),
(7, 'Gita Permata', 'Gita', 'gita@batavia.or.id', 'Gita', 'Bandung', '1999-04-18', 'Bandung', '081234567896', '2019', 'Performance', 'Tenor', 'Tenor', '2019-05-22', 'active', 10, 'https://voca-land.sgp1.cdn.digitaloceanspaces.com/0/1757684222527/9465e2e8.jpg', current_timestamp(), current_timestamp()),
(8, 'Hadi Santoso', 'Hadi', 'hadi@batavia.or.id', 'Hadi', 'Jakarta', '1996-06-14', 'Jakarta', '081234567897', '2012', 'Performance', 'Bass', 'Bass', '2012-08-30', 'active', 30, 'https://voca-land.sgp1.cdn.digitaloceanspaces.com/0/1757684222527/9465e2e8.jpg', current_timestamp(), current_timestamp()),
(9, 'Indah Cahaya', 'Indah', 'indah@batavia.or.id', 'Indah', 'Jakarta', '2001-01-12', 'Jakarta', '081234567898', '2020', 'Performance', 'Bass', 'Bass', '2020-11-18', 'active', 5, 'https://voca-land.sgp1.cdn.digitaloceanspaces.com/0/1757684222527/9465e2e8.jpg', current_timestamp(), current_timestamp()),
(10, 'Joko Widodo', 'Joko', 'joko@batavia.or.id', 'Joko', 'Solo', '1995-09-28', 'Solo', '081234567899', '2011', 'Performance', 'Bass', 'Bass', '2011-07-12', 'active', 22, 'https://voca-land.sgp1.cdn.digitaloceanspaces.com/0/1757684222527/9465e2e8.jpg', current_timestamp(), current_timestamp()),
(11, 'Kartika Sari', 'Kartika', 'kartika@batavia.or.id', 'Kartika', 'Jakarta', '2002-02-14', 'Jakarta', '081234567900', '2021', 'Performance', 'Sopran', 'Soprano', '2021-03-08', 'active', 3, 'https://voca-land.sgp1.cdn.digitaloceanspaces.com/0/1757684222527/9465e2e8.jpg', current_timestamp(), current_timestamp()),
(12, 'Larasati Putri', 'Laras', 'laras@batavia.or.id', 'Laras', 'Yogyakarta', '2000-09-05', 'Yogyakarta', '081234567901', '2019', 'Performance', 'Alto', 'Alto', '2019-12-01', 'active', 14, 'https://voca-land.sgp1.cdn.digitaloceanspaces.com/0/1757684222527/9465e2e8.jpg', current_timestamp(), current_timestamp());

-- ── Seed events ────────────────────────────────────────────────
INSERT INTO events (title, date, time, venue, city, description, status) VALUES
('Harmony of the Archipelago', '2026-11-21', '19:30:00', 'Aula Simfonia Jakarta',  'Jakarta', 'A journey through Indonesian folk songs reimagined for chamber choir.', 'upcoming'),
('Sacred Voices: Requiem',     '2026-12-13', '18:00:00', 'Katedral Jakarta',        'Jakarta', 'Featuring Fauré\'s Requiem and works by contemporary composers.',        'upcoming'),
('Christmas with Batavia',     '2024-12-20', '20:00:00', 'Balai Kartini',           'Jakarta', 'An evening of carols and holiday favorites for the whole family.',        'past'),
('Bach Motets Marathon',       '2024-10-05', '16:00:00', 'Goethe Institut',         'Jakarta', 'Complete performance of J.S. Bach\'s six motets.',                        'past');

-- ── Seed gallery items ─────────────────────────────────────────
INSERT INTO gallery_items (title, category, icon, sort_order) VALUES
('Concert Hall',    'Performance',        'bi-music-note-beamed', 1),
('Ensemble',        'Group',              'bi-people',            2),
('Solo Moments',    'Performance',        'bi-mic',               3),
('World Tour',      'Tour',               'bi-globe',             4),
('Backstage',       'Behind the Scenes',  'bi-heart',             5),
('Award Night',     'Milestone',          'bi-star',              6),
('Rehearsal',       'Behind the Scenes',  'bi-camera',            7),
('Interview',       'Media',              'bi-chat-quote',        8),
('Recording',       'Studio',             'bi-music-note-list',   9);
