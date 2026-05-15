-- ====================================================
-- TAPIFY PHASE 3C - DATABASE MIGRATION
-- Run this in phpMyAdmin to add new tables
-- ====================================================

-- Galleries
DROP TABLE IF EXISTS `vcard_galleries`;
CREATE TABLE `vcard_galleries` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `vcard_id` INT(11) UNSIGNED NOT NULL,
  `name` VARCHAR(200) NOT NULL,
  `display_order` INT(11) DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_vcard_id` (`vcard_id`),
  CONSTRAINT `fk_gal_vcard` FOREIGN KEY (`vcard_id`) REFERENCES `vcards`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Gallery Images (each gallery has many images)
DROP TABLE IF EXISTS `vcard_gallery_images`;
CREATE TABLE `vcard_gallery_images` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `gallery_id` INT(11) UNSIGNED NOT NULL,
  `image_url` VARCHAR(255) NOT NULL,
  `display_order` INT(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_gallery_id` (`gallery_id`),
  CONSTRAINT `fk_galimg_gallery` FOREIGN KEY (`gallery_id`) REFERENCES `vcard_galleries`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Blogs
DROP TABLE IF EXISTS `vcard_blogs`;
CREATE TABLE `vcard_blogs` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `vcard_id` INT(11) UNSIGNED NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `content` TEXT DEFAULT NULL,
  `image` VARCHAR(255) DEFAULT NULL,
  `published_date` DATE DEFAULT NULL,
  `display_order` INT(11) DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_vcard_id` (`vcard_id`),
  CONSTRAINT `fk_blog_vcard` FOREIGN KEY (`vcard_id`) REFERENCES `vcards`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Testimonials
DROP TABLE IF EXISTS `vcard_testimonials`;
CREATE TABLE `vcard_testimonials` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `vcard_id` INT(11) UNSIGNED NOT NULL,
  `name` VARCHAR(150) NOT NULL,
  `company` VARCHAR(200) DEFAULT NULL,
  `designation` VARCHAR(150) DEFAULT NULL,
  `message` TEXT DEFAULT NULL,
  `rating` TINYINT(1) DEFAULT 5,
  `image` VARCHAR(255) DEFAULT NULL,
  `display_order` INT(11) DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_vcard_id` (`vcard_id`),
  CONSTRAINT `fk_test_vcard` FOREIGN KEY (`vcard_id`) REFERENCES `vcards`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Custom Links (custom buttons)
DROP TABLE IF EXISTS `vcard_custom_links`;
CREATE TABLE `vcard_custom_links` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `vcard_id` INT(11) UNSIGNED NOT NULL,
  `label` VARCHAR(200) NOT NULL,
  `url` VARCHAR(500) NOT NULL,
  `icon` VARCHAR(100) DEFAULT 'fa-link',
  `display_order` INT(11) DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_vcard_id` (`vcard_id`),
  CONSTRAINT `fk_link_vcard` FOREIGN KEY (`vcard_id`) REFERENCES `vcards`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Done!
-- Now you can use these tables for galleries, blogs, testimonials, custom links
