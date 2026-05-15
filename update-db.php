<?php
require_once __DIR__ . '/config/database.php';

try {
    $pdo = getDB();
    
    $sql = "CREATE TABLE IF NOT EXISTS `appointments` (
      `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `user_id` INT(11) UNSIGNED NOT NULL,
      `vcard_id` INT(11) UNSIGNED NOT NULL,
      `name` VARCHAR(150) NOT NULL,
      `email` VARCHAR(150) NOT NULL,
      `phone` VARCHAR(20) NOT NULL,
      `date` DATETIME NOT NULL,
      `status` ENUM('pending','confirmed','completed','cancelled') DEFAULT 'pending',
      `message` TEXT DEFAULT NULL,
      `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      KEY `idx_user_id` (`user_id`),
      KEY `idx_vcard_id` (`vcard_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

    $pdo->exec($sql);
    echo "✅ Appointments table created successfully.";
} catch (Exception $e) {
    die("❌ Error: " . $e->getMessage());
}
