-- ====================================================
-- TAPIFY PHASE 7A - WhatsApp Stores Database Migration
-- Run this in phpMyAdmin
-- ====================================================

-- Stores table - main store config
DROP TABLE IF EXISTS `whatsapp_stores`;
CREATE TABLE `whatsapp_stores` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) UNSIGNED NOT NULL,
  `url_alias` VARCHAR(100) NOT NULL UNIQUE,
  `store_name` VARCHAR(200) NOT NULL,
  `template_id` VARCHAR(50) DEFAULT 'whatsapp_store_default',

  -- Owner Details
  `owner_name` VARCHAR(150) DEFAULT NULL,
  `whatsapp_number` VARCHAR(20) NOT NULL,
  `email` VARCHAR(150) DEFAULT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,

  -- Address
  `address` TEXT DEFAULT NULL,
  `location` VARCHAR(255) DEFAULT NULL,
  `location_url` VARCHAR(500) DEFAULT NULL,

  -- Branding
  `logo_image` VARCHAR(255) DEFAULT NULL,
  `cover_image` VARCHAR(255) DEFAULT NULL,
  `favicon_image` VARCHAR(255) DEFAULT NULL,

  -- Description
  `tagline` VARCHAR(255) DEFAULT NULL,
  `description` TEXT DEFAULT NULL,

  -- Currency / Settings
  `currency` VARCHAR(10) DEFAULT 'INR',
  `currency_symbol` VARCHAR(5) DEFAULT '₹',

  -- Order Settings
  `min_order_amount` DECIMAL(10,2) DEFAULT 0,
  `delivery_charge` DECIMAL(10,2) DEFAULT 0,
  `cod_available` TINYINT(1) DEFAULT 1,

  -- Display Settings
  `show_search` TINYINT(1) DEFAULT 1,
  `show_categories` TINYINT(1) DEFAULT 1,
  `show_featured` TINYINT(1) DEFAULT 1,

  -- WhatsApp Order Format
  `order_message_template` TEXT DEFAULT NULL,

  -- Theme Colors
  `primary_color` VARCHAR(20) DEFAULT '#25D366',
  `secondary_color` VARCHAR(20) DEFAULT '#128C7E',

  -- Stats
  `view_count` INT(11) UNSIGNED DEFAULT 0,
  `order_count` INT(11) UNSIGNED DEFAULT 0,

  `status` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_url_alias` (`url_alias`),
  CONSTRAINT `fk_store_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Categories
DROP TABLE IF EXISTS `whatsapp_store_categories`;
CREATE TABLE `whatsapp_store_categories` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `store_id` INT(11) UNSIGNED NOT NULL,
  `name` VARCHAR(150) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `image` VARCHAR(255) DEFAULT NULL,
  `display_order` INT(11) DEFAULT 0,
  `status` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_store_id` (`store_id`),
  CONSTRAINT `fk_cat_store` FOREIGN KEY (`store_id`) REFERENCES `whatsapp_stores`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Products
DROP TABLE IF EXISTS `whatsapp_store_products`;
CREATE TABLE `whatsapp_store_products` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `store_id` INT(11) UNSIGNED NOT NULL,
  `category_id` INT(11) UNSIGNED DEFAULT NULL,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `price` DECIMAL(10,2) DEFAULT 0,
  `discount_price` DECIMAL(10,2) DEFAULT NULL,
  `sku` VARCHAR(100) DEFAULT NULL,
  `image` VARCHAR(255) DEFAULT NULL,
  `gallery_images` TEXT DEFAULT NULL,
  `is_featured` TINYINT(1) DEFAULT 0,
  `in_stock` TINYINT(1) DEFAULT 1,
  `display_order` INT(11) DEFAULT 0,
  `status` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_store_id` (`store_id`),
  KEY `idx_category_id` (`category_id`),
  CONSTRAINT `fk_prod_store` FOREIGN KEY (`store_id`) REFERENCES `whatsapp_stores`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_prod_cat` FOREIGN KEY (`category_id`) REFERENCES `whatsapp_store_categories`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Orders (Phase 7C)
DROP TABLE IF EXISTS `whatsapp_store_orders`;
CREATE TABLE `whatsapp_store_orders` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `store_id` INT(11) UNSIGNED NOT NULL,
  `customer_name` VARCHAR(150) NOT NULL,
  `customer_phone` VARCHAR(20) NOT NULL,
  `customer_email` VARCHAR(150) DEFAULT NULL,
  `customer_address` TEXT DEFAULT NULL,
  `items` TEXT NOT NULL,
  `subtotal` DECIMAL(10,2) DEFAULT 0,
  `delivery_charge` DECIMAL(10,2) DEFAULT 0,
  `total_amount` DECIMAL(10,2) NOT NULL,
  `payment_method` VARCHAR(50) DEFAULT 'COD',
  `notes` TEXT DEFAULT NULL,
  `status` ENUM('pending','confirmed','processing','shipped','delivered','cancelled') DEFAULT 'pending',
  `is_read` TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_store_id` (`store_id`),
  KEY `idx_status` (`status`),
  CONSTRAINT `fk_ord_store` FOREIGN KEY (`store_id`) REFERENCES `whatsapp_stores`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Done!
