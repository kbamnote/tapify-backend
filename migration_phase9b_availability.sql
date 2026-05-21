-- ====================================================
-- TAPIFY PHASE 9B - Appointments Availability Migration
-- Run this in phpMyAdmin
-- ====================================================

DROP TABLE IF EXISTS `vcard_appointment_slots`;
CREATE TABLE `vcard_appointment_slots` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `vcard_id` INT(11) UNSIGNED NOT NULL,
  `available_date` DATE NOT NULL,
  `time_slot` VARCHAR(50) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_vcard_id` (`vcard_id`),
  KEY `idx_available_date` (`available_date`),
  CONSTRAINT `fk_slot_vcard` FOREIGN KEY (`vcard_id`) REFERENCES `vcards`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `uk_vcard_date_time` (`vcard_id`, `available_date`, `time_slot`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Done!
