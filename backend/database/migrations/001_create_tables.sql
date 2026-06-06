-- Users table migration
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `role` varchar(100) NOT NULL DEFAULT 'Member',
  `avatar` varchar(500) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `specialization` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `join_date` date DEFAULT NULL,
  `achievements` json DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Members table migration
CREATE TABLE IF NOT EXISTS `members` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `role` varchar(100) NOT NULL DEFAULT 'Member',
  `section` varchar(100) NOT NULL,
  `join_date` date NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `performances` int(11) NOT NULL DEFAULT 0,
  `avatar` varchar(500) DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Events table migration
CREATE TABLE IF NOT EXISTS `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `location` varchar(255) NOT NULL,
  `type` enum('concert','rehearsal','performance') NOT NULL,
  `status` enum('upcoming','completed','cancelled') NOT NULL DEFAULT 'upcoming',
  `participants` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed initial user
INSERT INTO `users` (`name`, `email`, `password`, `role`, `avatar`, `phone`, `location`, `specialization`, `bio`, `join_date`, `achievements`) VALUES
('Alexandra Williams', 'alexandra.w@bms.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Music Director', 'https://i.pravatar.cc/150?img=5', '+1 234-567-8900', 'New York, USA', 'Choral Conducting', 'Passionate music director with 15 years of experience in choral conducting.', '2023-01-15', '[\"International Choir Competition Gold Medalist 2025\",\"Best Conductor Award 2024\",\"Published \"Modern Choral Techniques\" 2023\"]');

-- Seed initial members
INSERT INTO `members` (`name`, `email`, `phone`, `role`, `section`, `join_date`, `status`, `performances`, `avatar`) VALUES
('Sarah Johnson', 'sarah.j@bms.com', '+1 234-567-8901', 'Lead Soprano', 'Soprano', '2024-01-15', 'active', 25, 'https://i.pravatar.cc/150?img=1'),
('Michael Chen', 'michael.c@bms.com', '+1 234-567-8902', 'Tenor Section Leader', 'Tenor', '2023-09-20', 'active', 30, 'https://i.pravatar.cc/150?img=2'),
('Emily Davis', 'emily.d@bms.com', '+1 234-567-8903', 'Alto', 'Alto', '2024-03-10', 'active', 18, 'https://i.pravatar.cc/150?img=3');

-- Seed initial events
INSERT INTO `events` (`title`, `date`, `time`, `location`, `type`, `status`, `participants`) VALUES
('Annual Concert 2026', '2026-06-15', '19:00:00', 'Grand Symphony Hall', 'concert', 'upcoming', 45),
('Rehearsal Session', '2026-06-10', '18:00:00', 'Studio A', 'rehearsal', 'upcoming', 30),
('Community Performance', '2026-06-20', '16:00:00', 'City Park Amphitheater', 'performance', 'upcoming', 25);