-- =======================================================
-- V-NEWS CMS - CƠ SỞ DỮ LIỆU MYSQL / MARIADB
-- Dùng để Import vào phpMyAdmin trên Hosting / VPS có Tên Miền
-- =======================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- 1. Bảng Users (Người dùng & Quản trị viên)
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `full_name` VARCHAR(100) NOT NULL,
  `role` ENUM('admin', 'editor', 'author') DEFAULT 'editor',
  `avatar` VARCHAR(255) DEFAULT NULL,
  `bio` TEXT DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Bảng Categories (Chuyên mục bài viết)
CREATE TABLE IF NOT EXISTS `categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(100) NOT NULL UNIQUE,
  `description` TEXT DEFAULT NULL,
  `color` VARCHAR(20) DEFAULT 'indigo',
  `sort_order` INT DEFAULT 0,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Bảng Articles (Bài viết / Tin tức)
CREATE TABLE IF NOT EXISTS `articles` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `excerpt` TEXT DEFAULT NULL,
  `content` LONGTEXT NOT NULL,
  `thumbnail` VARCHAR(255) DEFAULT NULL,
  `category_id` INT NOT NULL,
  `author_id` INT NOT NULL,
  `status` ENUM('published', 'draft', 'archived') DEFAULT 'published',
  `is_featured` TINYINT(1) DEFAULT 0,
  `is_breaking` TINYINT(1) DEFAULT 0,
  `views_count` INT DEFAULT 0,
  `tags` VARCHAR(255) DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`author_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Bảng Comments (Bình luận bạn đọc)
CREATE TABLE IF NOT EXISTS `comments` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `article_id` INT NOT NULL,
  `author_name` VARCHAR(100) NOT NULL,
  `author_email` VARCHAR(100) NOT NULL,
  `content` TEXT NOT NULL,
  `status` ENUM('approved', 'pending', 'spam') DEFAULT 'approved',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`article_id`) REFERENCES `articles`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Bảng Settings (Cài đặt hệ thống & Tên miền)
CREATE TABLE IF NOT EXISTS `settings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `setting_key` VARCHAR(100) NOT NULL UNIQUE,
  `setting_value` TEXT DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dữ liệu mặc định cho Settings
INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
('site_name', 'V-NEWS 24/7'),
('site_tagline', 'Thời sự - Công nghệ - Kinh doanh - Đời sống số'),
('site_description', 'Kênh thông tin tin tức, blog đa phương tiện cập nhật nhanh nhất, chuẩn xác nhất và chuyên sâu.'),
('site_keywords', 'tin tức, công nghệ, kinh doanh, thời sự, blog php, cms, tin nhanh 24h'),
('primary_domain', 'https://vnews247.vn'),
('contact_email', 'contact@vnews247.vn'),
('contact_hotline', '1900 6868'),
('facebook_url', 'https://facebook.com/vnews247'),
('youtube_url', 'https://youtube.com/@vnews247'),
('footer_copyright', '© 2026 V-NEWS CMS. Bảo lưu mọi quyền.')
ON DUPLICATE KEY UPDATE `setting_value` = VALUES(`setting_value`);

-- Mật khẩu mặc định: admin123 (mã hóa BCRYPT)
INSERT INTO `users` (`id`, `username`, `email`, `password`, `full_name`, `role`, `bio`) VALUES
(1, 'admin', 'admin@vnews.vn', '$2y$10$AUQFYHYbpw03zeKpblU30.4pk8OsMaSQda4AArothx8x8vVOaK0VC', 'Tổng Biên Tập', 'admin', 'Nhà báo công nghệ & Trưởng ban biên tập V-NEWS')
ON DUPLICATE KEY UPDATE `password` = VALUES(`password`);

SET FOREIGN_KEY_CHECKS = 1;
