-- ====================================================
-- TAPIFY DATABASE SCHEMA
-- Run this in phpMyAdmin to create all tables
-- ====================================================

-- Use this database (Hostinger me create karna hoga)
-- CREATE DATABASE tapify_db;
-- USE tapify_db;

SET FOREIGN_KEY_CHECKS=0;

-- ====================================================
-- TABLE: users (Login users / vCard owners)
-- ====================================================
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(150) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `avatar` VARCHAR(255) DEFAULT NULL,
  `role` ENUM('admin','user') NOT NULL DEFAULT 'user',
  `email_verified` TINYINT(1) NOT NULL DEFAULT 0,
  `email_verify_token` VARCHAR(100) DEFAULT NULL,
  `password_reset_token` VARCHAR(100) DEFAULT NULL,
  `password_reset_expires` DATETIME DEFAULT NULL,
  `last_login` DATETIME DEFAULT NULL,
  `status` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_email` (`email`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================================
-- TABLE: subscriptions (User plans)
-- ====================================================
DROP TABLE IF EXISTS `subscriptions`;
CREATE TABLE `subscriptions` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) UNSIGNED NOT NULL,
  `plan_name` VARCHAR(100) NOT NULL DEFAULT 'Free Plan',
  `vcards_limit` INT(11) NOT NULL DEFAULT 1,
  `stores_limit` INT(11) NOT NULL DEFAULT 0,
  `price` DECIMAL(10,2) NOT NULL DEFAULT 0,
  `subscribed_date` DATE NOT NULL,
  `expiry_date` DATE NOT NULL,
  `status` ENUM('active','expired','cancelled') NOT NULL DEFAULT 'active',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  CONSTRAINT `fk_sub_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================================
-- TABLE: vcards (Main vCard records)
-- ====================================================
DROP TABLE IF EXISTS `vcards`;
CREATE TABLE `vcards` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) UNSIGNED NOT NULL,
  `url_alias` VARCHAR(100) NOT NULL UNIQUE,
  `vcard_name` VARCHAR(150) NOT NULL,
  `occupation` VARCHAR(150) DEFAULT NULL,
  `description` TEXT DEFAULT NULL,
  `cover_type` ENUM('image','color','gradient','video') DEFAULT 'image',
  `cover_image` VARCHAR(255) DEFAULT NULL,
  `cover_color` VARCHAR(20) DEFAULT NULL,
  `profile_image` VARCHAR(255) DEFAULT NULL,
  `favicon_image` VARCHAR(255) DEFAULT NULL,
  `template_id` VARCHAR(50) DEFAULT 'vcard1',

  -- Personal Details
  `first_name` VARCHAR(100) DEFAULT NULL,
  `last_name` VARCHAR(100) DEFAULT NULL,
  `email` VARCHAR(150) DEFAULT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `phone_country_code` VARCHAR(10) DEFAULT '+91',
  `alternate_email` VARCHAR(150) DEFAULT NULL,
  `alternate_phone` VARCHAR(20) DEFAULT NULL,
  `location` TEXT DEFAULT NULL,
  `location_url` VARCHAR(500) DEFAULT NULL,
  `location_type` ENUM('link','map','address') DEFAULT 'link',
  `dob` DATE DEFAULT NULL,
  `company` VARCHAR(200) DEFAULT NULL,
  `job_title` VARCHAR(150) DEFAULT NULL,
  `made_by` VARCHAR(150) DEFAULT NULL,
  `made_by_url` VARCHAR(500) DEFAULT NULL,
  `default_language` VARCHAR(10) DEFAULT 'en',

  -- Other Configurations
  `display_inquiry_form` TINYINT(1) DEFAULT 1,
  `display_qr_section` TINYINT(1) DEFAULT 1,
  `display_download_qr` TINYINT(1) DEFAULT 1,
  `display_add_contact` TINYINT(1) DEFAULT 1,
  `display_whatsapp_share` TINYINT(1) DEFAULT 1,
  `display_language_selector` TINYINT(1) DEFAULT 1,
  `hide_sticky_bar` TINYINT(1) DEFAULT 0,
  `qr_download_size` INT(11) DEFAULT 200,

  -- Dynamic vCard colors
  `primary_color` VARCHAR(20) DEFAULT '#8338ec',
  `secondary_color` VARCHAR(20) DEFAULT '#a855f7',
  `bg_color` VARCHAR(20) DEFAULT '#1a2035',
  `cards_bg_color` VARCHAR(20) DEFAULT '#243055',
  `button_text_color` VARCHAR(20) DEFAULT '#ffffff',
  `label_text_color` VARCHAR(20) DEFAULT '#FFD700',
  `description_text_color` VARCHAR(20) DEFAULT '#cccccc',
  `social_icon_color` VARCHAR(20) DEFAULT '#ffffff',
  `button_style` INT(11) DEFAULT 1,
  `sticky_position` ENUM('left','right') DEFAULT 'left',

  -- QR Code customization
  `qr_color` VARCHAR(20) DEFAULT '#000000',
  `qr_bg_color` VARCHAR(20) DEFAULT '#ffffff',
  `qr_style` ENUM('square','rounded','dots','classy') DEFAULT 'square',
  `qr_eye_style` ENUM('square','rounded','leaf','circle') DEFAULT 'square',
  `qr_use_config` TINYINT(1) DEFAULT 0,

  -- Banner
  `banner_title` VARCHAR(200) DEFAULT NULL,
  `banner_url` VARCHAR(500) DEFAULT NULL,
  `banner_description` TEXT DEFAULT NULL,
  `banner_button_text` VARCHAR(50) DEFAULT NULL,
  `banner_show` TINYINT(1) DEFAULT 0,

  -- Advanced
  `password` VARCHAR(255) DEFAULT NULL,
  `custom_css` TEXT DEFAULT NULL,
  `custom_js` TEXT DEFAULT NULL,
  `remove_branding` TINYINT(1) DEFAULT 0,
  `font_family` VARCHAR(50) DEFAULT 'default',
  `font_size` INT(11) DEFAULT NULL,

  -- SEO
  `seo_site_title` VARCHAR(200) DEFAULT NULL,
  `seo_home_title` VARCHAR(200) DEFAULT NULL,
  `seo_meta_keyword` TEXT DEFAULT NULL,
  `seo_meta_description` TEXT DEFAULT NULL,
  `google_analytics` TEXT DEFAULT NULL,

  -- Privacy & Terms
  `privacy_policy` TEXT DEFAULT NULL,
  `terms_conditions` TEXT DEFAULT NULL,

  -- Manage Sections (visibility)
  `show_contact` TINYINT(1) DEFAULT 1,
  `show_services` TINYINT(1) DEFAULT 1,
  `show_galleries` TINYINT(1) DEFAULT 1,
  `show_products` TINYINT(1) DEFAULT 1,
  `show_testimonials` TINYINT(1) DEFAULT 1,
  `show_blogs` TINYINT(1) DEFAULT 1,
  `show_business_hours` TINYINT(1) DEFAULT 1,
  `show_appointments` TINYINT(1) DEFAULT 1,
  `show_map` TINYINT(1) DEFAULT 1,
  `show_banner` TINYINT(1) DEFAULT 0,
  `show_instagram` TINYINT(1) DEFAULT 1,
  `show_iframes` TINYINT(1) DEFAULT 0,
  `show_newsletter` TINYINT(1) DEFAULT 1,

  -- Stats
  `view_count` INT(11) NOT NULL DEFAULT 0,
  `status` TINYINT(1) NOT NULL DEFAULT 1,

  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_url_alias` (`url_alias`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_status` (`status`),
  CONSTRAINT `fk_vcard_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================================
-- TABLE: vcard_business_hours
-- ====================================================
DROP TABLE IF EXISTS `vcard_business_hours`;
CREATE TABLE `vcard_business_hours` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `vcard_id` INT(11) UNSIGNED NOT NULL,
  `day_name` ENUM('MONDAY','TUESDAY','WEDNESDAY','THURSDAY','FRIDAY','SATURDAY','SUNDAY') NOT NULL,
  `is_open` TINYINT(1) NOT NULL DEFAULT 1,
  `open_time` VARCHAR(20) DEFAULT '10:00 AM',
  `close_time` VARCHAR(20) DEFAULT '06:00 PM',
  PRIMARY KEY (`id`),
  KEY `idx_vcard_id` (`vcard_id`),
  CONSTRAINT `fk_bh_vcard` FOREIGN KEY (`vcard_id`) REFERENCES `vcards`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================================
-- TABLE: vcard_services
-- ====================================================
DROP TABLE IF EXISTS `vcard_services`;
CREATE TABLE `vcard_services` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `vcard_id` INT(11) UNSIGNED NOT NULL,
  `name` VARCHAR(200) NOT NULL,
  `service_url` VARCHAR(500) DEFAULT NULL,
  `icon` VARCHAR(255) DEFAULT NULL,
  `display_order` INT(11) DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_vcard_id` (`vcard_id`),
  CONSTRAINT `fk_svc_vcard` FOREIGN KEY (`vcard_id`) REFERENCES `vcards`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================================
-- TABLE: vcard_products
-- ====================================================
DROP TABLE IF EXISTS `vcard_products`;
CREATE TABLE `vcard_products` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `vcard_id` INT(11) UNSIGNED NOT NULL,
  `name` VARCHAR(200) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `currency` VARCHAR(10) DEFAULT 'INR',
  `price` DECIMAL(10,2) DEFAULT NULL,
  `product_url` VARCHAR(500) DEFAULT NULL,
  `image` VARCHAR(255) DEFAULT NULL,
  `display_order` INT(11) DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_vcard_id` (`vcard_id`),
  CONSTRAINT `fk_prd_vcard` FOREIGN KEY (`vcard_id`) REFERENCES `vcards`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================================
-- TABLE: vcard_social_links
-- ====================================================
DROP TABLE IF EXISTS `vcard_social_links`;
CREATE TABLE `vcard_social_links` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `vcard_id` INT(11) UNSIGNED NOT NULL,
  `platform` VARCHAR(50) NOT NULL,
  `url` VARCHAR(500) NOT NULL,
  `display_order` INT(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_vcard_id` (`vcard_id`),
  CONSTRAINT `fk_soc_vcard` FOREIGN KEY (`vcard_id`) REFERENCES `vcards`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================================
-- TABLE: vcard_inquiries
-- ====================================================
DROP TABLE IF EXISTS `vcard_inquiries`;
CREATE TABLE `vcard_inquiries` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `vcard_id` INT(11) UNSIGNED NOT NULL,
  `name` VARCHAR(150) NOT NULL,
  `email` VARCHAR(150) DEFAULT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `message` TEXT DEFAULT NULL,
  `attachment` VARCHAR(255) DEFAULT NULL,
  `is_read` TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_vcard_id` (`vcard_id`),
  CONSTRAINT `fk_inq_vcard` FOREIGN KEY (`vcard_id`) REFERENCES `vcards`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================================
-- TABLE: sessions (User sessions)
-- ====================================================
DROP TABLE IF EXISTS `user_sessions`;
CREATE TABLE `user_sessions` (
  `id` VARCHAR(64) NOT NULL,
  `user_id` INT(11) UNSIGNED NOT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `user_agent` TEXT DEFAULT NULL,
  `last_activity` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  CONSTRAINT `fk_sess_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================================
-- INSERT: Demo Admin User
-- Password is: admin123 (bcrypt hashed)
-- ====================================================
INSERT INTO `users` (`name`, `email`, `password`, `phone`, `role`, `email_verified`, `status`)
VALUES (
  'Tapify World',
  'admin@tapify.com',
  '$2y$10$zr7c/as47bPGiD6eHnxFbOSqQKkTmHLC.4fzOcg0pQMNpNVc8zuYi',
  '7841840840',
  'admin',
  1,
  1
);

-- Insert demo subscription
INSERT INTO `subscriptions` (`user_id`, `plan_name`, `vcards_limit`, `stores_limit`, `price`, `subscribed_date`, `expiry_date`, `status`)
VALUES (1, 'Premium Plan', 100, 10, 14999.00, CURDATE(), '2030-03-01', 'active');

-- Insert demo vCard
INSERT INTO `vcards` (
  `user_id`, `url_alias`, `vcard_name`, `occupation`, `description`,
  `first_name`, `last_name`, `email`, `phone`, `company`,
  `template_id`, `view_count`, `status`
) VALUES (
  1, 'tapify', 'Tapify World', 'NFC Smart Cards',
  '<p>Tapify is an innovative and registered brand of MrPrint World Pvt. Ltd.</p>',
  'Tapify', 'World', 'info.tapify1@gmail.com', '7841840840',
  'MrPrint World Pvt. Ltd.', 'vcard22', 128, 1
);

-- Insert default business hours for the demo vCard
INSERT INTO `vcard_business_hours` (`vcard_id`, `day_name`, `is_open`, `open_time`, `close_time`) VALUES
(1, 'MONDAY', 1, '10:30 AM', '07:30 PM'),
(1, 'TUESDAY', 1, '10:30 AM', '07:30 PM'),
(1, 'WEDNESDAY', 1, '10:30 AM', '07:30 PM'),
(1, 'THURSDAY', 1, '10:30 AM', '07:30 PM'),
(1, 'FRIDAY', 1, '10:30 AM', '07:30 PM'),
(1, 'SATURDAY', 1, '10:30 AM', '07:30 PM'),
(1, 'SUNDAY', 0, '00:00 AM', '00:00 AM');

SET FOREIGN_KEY_CHECKS=1;

-- ====================================================
-- DONE! Database setup complete
-- Login: admin@tapify.com / admin123
-- ====================================================
