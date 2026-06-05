-- Migration: category-based services
-- A service CATEGORY (name only, no image) that contains multiple service
-- ITEMS, each with an image + name. Mirrors the galleries model.
-- Safe to run once on the production database.

CREATE TABLE IF NOT EXISTS `vcard_service_categories` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `vcard_id` INT(11) UNSIGNED NOT NULL,
  `name` VARCHAR(200) NOT NULL,
  `display_order` INT(11) DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_vcard_id` (`vcard_id`),
  CONSTRAINT `fk_svccat_vcard` FOREIGN KEY (`vcard_id`) REFERENCES `vcards`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `vcard_service_items` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_id` INT(11) UNSIGNED NOT NULL,
  `name` VARCHAR(200) NOT NULL,
  `image` VARCHAR(255) DEFAULT NULL,
  `display_order` INT(11) DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_category_id` (`category_id`),
  CONSTRAINT `fk_svcitem_cat` FOREIGN KEY (`category_id`) REFERENCES `vcard_service_categories`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
