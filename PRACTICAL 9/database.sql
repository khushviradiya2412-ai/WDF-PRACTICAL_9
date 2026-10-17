-- =========================================
-- StudentHub Practical 9 Database
-- DB: studenthub_p9
-- XAMPP MySQL (root, empty password)
-- Import: phpMyAdmin > Create DB > Import this file
-- =========================================

CREATE DATABASE IF NOT EXISTS `studenthub_p9`
  CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

USE `studenthub_p9`;

-- ---------- users table ----------
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(20) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `role` ENUM('admin','student') NOT NULL DEFAULT 'student',
  `status` ENUM('active','inactive') NOT NULL DEFAULT 'active',
  `last_login` DATETIME NULL DEFAULT NULL,
  `remember_token_hash` VARCHAR(255) NULL DEFAULT NULL,
  `remember_expires` DATETIME NULL DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_users_username` (`username`),
  UNIQUE KEY `uq_users_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------- students table ----------
CREATE TABLE IF NOT EXISTS `students` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `full_name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(15) NOT NULL,
  `student_id` VARCHAR(20) NOT NULL,
  `course` VARCHAR(50) NOT NULL,
  `year` INT NOT NULL,
  `gender` VARCHAR(10) NOT NULL,
  `status` VARCHAR(10) NOT NULL DEFAULT 'active',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_students_email` (`email`),
  UNIQUE KEY `uq_students_student_id` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------- events table ----------
CREATE TABLE IF NOT EXISTS `events` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(150) NOT NULL,
  `description` TEXT,
  `category` VARCHAR(50) NOT NULL DEFAULT 'Technical',
  `event_date` DATE NOT NULL,
  `venue` VARCHAR(150) NOT NULL,
  `status` ENUM('open','closed') NOT NULL DEFAULT 'open',
  `poster` VARCHAR(255) NULL DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------- registrations table ----------
CREATE TABLE IF NOT EXISTS `registrations` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_id` INT UNSIGNED NOT NULL,
  `event_id` INT UNSIGNED NOT NULL,
  `registered_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_reg_student_event` (`student_id`, `event_id`),
  CONSTRAINT `fk_reg_student` FOREIGN KEY (`student_id`)
    REFERENCES `students` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_reg_event` FOREIGN KEY (`event_id`)
    REFERENCES `events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------- audit_logs table ----------
CREATE TABLE IF NOT EXISTS `audit_logs` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NULL DEFAULT NULL,
  `action` VARCHAR(100) NOT NULL,
  `details` VARCHAR(255) NULL DEFAULT NULL,
  `ip_address` VARCHAR(45) NULL DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_audit_user` FOREIGN KEY (`user_id`)
    REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------- Seed users ----------
-- admin / Admin@123 and student / Student@123 (hashes given in task spec)
INSERT INTO `users` (`username`, `email`, `password_hash`, `role`, `status`) VALUES
('admin', 'admin@studenthub.local', '$2b$10$IxL73cChhqbhMu8GgUBcQOw97GGxgivtR9dx0qAs6W4vsJ110Bg0e', 'admin', 'active'),
('student', 'student@studenthub.local', '$2b$10$g57ksW2ZDcxFPn9wV7GqbenmxjZ3MSXTRmo29G6vs03cq7Levmtyy', 'student', 'active');

-- ---------- Seed students (3) ----------
INSERT INTO `students` (`full_name`, `email`, `phone`, `student_id`, `course`, `year`, `gender`, `status`) VALUES
('Aarav Patel', 'aarav@studenthub.local', '9876543210', 'STU001', 'B.Tech CSE', 2, 'Male', 'active'),
('Diya Shah', 'diya@studenthub.local', '9876543211', 'STU002', 'B.Tech IT', 1, 'Female', 'active'),
('Kabir Rao', 'kabir@studenthub.local', '9876543212', 'STU003', 'BCA', 3, 'Male', 'active');

-- ---------- Seed events (2) ----------
INSERT INTO `events` (`title`, `description`, `category`, `event_date`, `venue`, `status`, `poster`) VALUES
('Tech Fest 2026', 'Annual technical festival with coding contests.', 'Technical', '2026-11-20', 'Seminar Hall A', 'open', NULL),
('Cultural Night', 'Music, dance and drama performances.', 'Cultural', '2026-12-05', 'Main Ground', 'open', NULL);
