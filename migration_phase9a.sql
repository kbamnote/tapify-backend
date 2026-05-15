-- ====================================================
-- TAPIFY PHASE 9A - Appointments Database Migration
-- Run this in phpMyAdmin
-- ====================================================

DROP TABLE IF EXISTS `vcard_appointments`;
CREATE TABLE `vcard_appointments` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `vcard_id` INT(11) UNSIGNED NOT NULL,

  -- Customer Details
  `customer_name` VARCHAR(150) NOT NULL,
  `customer_email` VARCHAR(150) DEFAULT NULL,
  `customer_phone` VARCHAR(20) NOT NULL,

  -- Appointment Details
  `service_name` VARCHAR(255) DEFAULT NULL,
  `appointment_date` DATE NOT NULL,
  `appointment_time` TIME NOT NULL,
  `duration_minutes` INT(11) DEFAULT 30,

  -- Notes
  `customer_notes` TEXT DEFAULT NULL,
  `admin_notes` TEXT DEFAULT NULL,

  -- Status workflow
  `status` ENUM('pending','confirmed','completed','cancelled','no_show') DEFAULT 'pending',
  `is_read` TINYINT(1) DEFAULT 0,

  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (`id`),
  KEY `idx_vcard_id` (`vcard_id`),
  KEY `idx_appointment_date` (`appointment_date`),
  KEY `idx_status` (`status`),
  CONSTRAINT `fk_appt_vcard` FOREIGN KEY (`vcard_id`) REFERENCES `vcards`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Done!
