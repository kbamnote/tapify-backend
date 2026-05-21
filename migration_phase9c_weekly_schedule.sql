-- ====================================================
-- TAPIFY PHASE 9C - Appointments Weekly Schedule Migration
-- Run this in phpMyAdmin
-- ====================================================

-- Drop the old table if it exists
DROP TABLE IF EXISTS `vcard_appointment_slots`;

-- Create the new weekly schedule table
CREATE TABLE `vcard_weekly_schedule` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `vcard_id` INT(11) UNSIGNED NOT NULL,
  `day_of_week` TINYINT(1) NOT NULL COMMENT '0=Sunday, 1=Monday, ..., 6=Saturday',
  `start_time` TIME NOT NULL,
  `end_time` TIME NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_vcard_id` (`vcard_id`),
  KEY `idx_day` (`day_of_week`),
  CONSTRAINT `fk_weekly_vcard` FOREIGN KEY (`vcard_id`) REFERENCES `vcards`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Done!
