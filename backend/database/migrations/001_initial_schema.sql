-- ============================================================
-- Migration: Initial Schema - db_job_seeker
-- Created: 2025-07-13
-- ============================================================

CREATE DATABASE IF NOT EXISTS `db_job_seeker`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_0900_ai_ci;

USE `db_job_seeker`;

-- ── users ────────────────────────────────────────────────────
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id`                 int          NOT NULL AUTO_INCREMENT,
  `username`           varchar(50)  NOT NULL,
  `email`              varchar(100) NOT NULL,
  `password`           varchar(255) NOT NULL,
  `role`               enum('member','company','admin') NOT NULL DEFAULT 'member',
  `is_verified`        tinyint(1)   DEFAULT '0',
  `verification_token` varchar(255) DEFAULT NULL,
  `created_at`         timestamp    NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`         timestamp    NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `users` VALUES
  (1,'admin',   'admin@example.com',   '240be518fabd2724ddb6f04eeb1da5967448d7e831c08c8fa822809f74c720a9',   'company', 1, NULL, '2025-07-13 12:07:05', '2025-07-13 12:07:05'),
  (2,'member1', 'member1@example.com', 'ef92b778bafe771e89245b89ecbc08a44a4e166c06659911881f383d4473e94f','member',  1, NULL, '2025-07-13 12:07:05', '2025-07-13 12:07:05'),
  (3,'company1','company1@example.com','c78a05c319409893d5f309a6042ef3a7beec3c2be3c20db1adfa678d4924c53d', 'company', 1, NULL, '2025-07-13 12:07:05', '2025-07-13 12:07:05');

-- ── company_profiles ─────────────────────────────────────────
DROP TABLE IF EXISTS `company_profiles`;
CREATE TABLE `company_profiles` (
  `id`           int          NOT NULL AUTO_INCREMENT,
  `user_id`      int          NOT NULL,
  `company_name` varchar(100) DEFAULT NULL,
  `description`  text,
  `website`      varchar(255) DEFAULT NULL,
  `phone`        varchar(20)  DEFAULT NULL,
  `address`      text,
  `logo`         varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `company_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `company_profiles` VALUES
  (1, 1, 'Admin Company',          'Admin company for testing',               NULL,                    NULL,          NULL,            NULL),
  (2, 3, 'Tech Solutions',         'Leading technology company',              NULL,                    NULL,          NULL,            NULL);

-- ── member_profiles ──────────────────────────────────────────
DROP TABLE IF EXISTS `member_profiles`;
CREATE TABLE `member_profiles` (
  `id`            int          NOT NULL AUTO_INCREMENT,
  `user_id`       int          NOT NULL,
  `full_name`     varchar(100) DEFAULT NULL,
  `phone`         varchar(20)  DEFAULT NULL,
  `address`       text,
  `profile_photo` varchar(255) DEFAULT NULL,
  `cv_file`       varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `member_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `member_profiles` VALUES
  (1, 2, 'John Doe',    '081234567890', 'Jakarta, Indonesia',               NULL, NULL),
  (2, 4, 'BE Engineer', '081987654321', 'Engineering Division, CyberArmy',  NULL, NULL);

-- ── jobs ─────────────────────────────────────────────────────
DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs` (
  `id`           int             NOT NULL AUTO_INCREMENT,
  `company_id`   int             NOT NULL,
  `title`        varchar(255)    DEFAULT NULL,
  `description`  text,
  `requirements` text,
  `salary_min`   decimal(10,2)   DEFAULT NULL,
  `salary_max`   decimal(10,2)   DEFAULT NULL,
  `location`     varchar(100)    DEFAULT NULL,
  `job_type`     enum('full-time','part-time','contract','internship') DEFAULT NULL,
  `status`       enum('active','closed') DEFAULT 'active',
  `created_at`   timestamp       NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`   timestamp       NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `company_id` (`company_id`),
  CONSTRAINT `jobs_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `jobs` VALUES
  (1, 1, 'Senior PHP Developer',  'We are looking for an experienced PHP developer', 'PHP, MySQL, JavaScript', 8000000.00, 12000000.00, 'Jakarta', 'full-time', 'active', '2025-07-13 12:07:05', '2025-07-13 12:07:05'),
  (2, 3, 'Frontend Developer',    'Join our frontend team',                           'HTML, CSS, JavaScript, React', 6000000.00, 9000000.00, 'Bandung', 'full-time', 'active', '2025-07-13 12:07:05', '2025-07-13 12:07:05');

-- ── job_applications ─────────────────────────────────────────
DROP TABLE IF EXISTS `job_applications`;
CREATE TABLE `job_applications` (
  `id`           int    NOT NULL AUTO_INCREMENT,
  `job_id`       int    NOT NULL,
  `user_id`      int    NOT NULL,
  `cover_letter` text,
  `status`       enum('pending','reviewed','accepted','rejected') DEFAULT 'pending',
  `applied_at`   timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `job_id`  (`job_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `job_applications_ibfk_1` FOREIGN KEY (`job_id`)  REFERENCES `jobs`  (`id`) ON DELETE CASCADE,
  CONSTRAINT `job_applications_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ── education ─────────────────────────────────────────────────
DROP TABLE IF EXISTS `education`;
CREATE TABLE `education` (
  `id`             int          NOT NULL AUTO_INCREMENT,
  `user_id`        int          NOT NULL,
  `institution`    varchar(100) DEFAULT NULL,
  `degree`         varchar(100) DEFAULT NULL,
  `field_of_study` varchar(100) DEFAULT NULL,
  `start_date`     date         DEFAULT NULL,
  `end_date`       date         DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `education_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ── skills ───────────────────────────────────────────────────
DROP TABLE IF EXISTS `skills`;
CREATE TABLE `skills` (
  `id`         int          NOT NULL AUTO_INCREMENT,
  `user_id`    int          NOT NULL,
  `skill_name` varchar(100) DEFAULT NULL,
  `level`      enum('beginner','intermediate','advanced','expert') DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `skills_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


-- ── seed job_applications ──────────────────────────────────
INSERT INTO `job_applications` VALUES
  (1, 1, 4, 'Saya mengajukan diri untuk posisi Senior PHP Developer. Pengalaman backend saya siap berkontribusi pada sistem.', 'pending', '2026-11-20 10:00:00'),
  (2, 2, 2, 'Halo, saya John Doe ingin mengajukan lamaran untuk posisi Frontend Developer. Saya memiliki keahlian dalam Vue.js dan arsitektur komponen modern.', 'reviewed', '2026-11-21 11:30:00');

-- ── seed skills ─────────────────────────────────────────────
INSERT INTO `skills` VALUES
  (1, 2, 'Vue.js', 'expert'),
  (2, 2, 'TypeScript', 'advanced'),
  (3, 2, 'Bootstrap & Tailwind', 'intermediate'),
  (4, 4, 'Go / Golang', 'expert'),
  (5, 4, 'PHP 8 / Symfony', 'expert'),
  (6, 4, 'MySQL Optimization', 'advanced');

-- ── seed education ──────────────────────────────────────────
INSERT INTO `education` VALUES
  (1, 2, 'Universitas Indonesia', 'Sarjana Komputer (S1)', 'Ilmu Komputer', '2019-08-01', '2023-07-15'),
  (2, 4, 'Institut Teknologi Bandung', 'Sarjana Teknik (S1)', 'Teknik Informatika', '2018-08-01', '2022-06-30');
