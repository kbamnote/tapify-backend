-- ====================================================
-- AI GROWTH CENTER — cache + history tables
-- Safe to run multiple times (CREATE TABLE IF NOT EXISTS).
-- ====================================================

-- ----------------------------------------------------
-- ai_history: every generation attempt (success or error).
-- Powers the per-card "History" view and the "Save" action.
-- ----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ai_history` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) UNSIGNED NOT NULL,
  `feature` VARCHAR(60) NOT NULL,               -- e.g. business-description, keywords
  `input_json` LONGTEXT DEFAULT NULL,           -- normalised request input
  `output_json` LONGTEXT DEFAULT NULL,          -- parsed AI result (null on error)
  `provider` VARCHAR(40) DEFAULT NULL,          -- gemini | openai | claude | openrouter
  `model` VARCHAR(80) DEFAULT NULL,
  `status` VARCHAR(20) NOT NULL DEFAULT 'success', -- success | error
  `error` TEXT DEFAULT NULL,
  `is_saved` TINYINT(1) NOT NULL DEFAULT 0,     -- user bookmarked this result
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_feature` (`user_id`, `feature`, `created_at`),
  KEY `idx_user_saved` (`user_id`, `is_saved`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------
-- ai_cache: last successful result per (user, feature, input hash).
-- If the input has not changed we return the cached result instead of
-- calling the provider again. "Regenerate" bypasses + overwrites this row.
-- ----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ai_cache` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) UNSIGNED NOT NULL,
  `feature` VARCHAR(60) NOT NULL,
  `input_hash` CHAR(64) NOT NULL,               -- sha256(feature|normalised-input)
  `input_json` LONGTEXT DEFAULT NULL,
  `output_json` LONGTEXT DEFAULT NULL,
  `provider` VARCHAR(40) DEFAULT NULL,
  `model` VARCHAR(80) DEFAULT NULL,
  `history_id` BIGINT(20) UNSIGNED DEFAULT NULL, -- links the cached result to its history row
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_user_feature_hash` (`user_id`, `feature`, `input_hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
