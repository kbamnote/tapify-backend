-- ====================================================
-- TAPIFY - WhatsApp Store Themes Migration
-- Adds template-independent branding/behaviour columns so the
-- new webStore templates (store_template_9..16) can render fully
-- dynamically while switching remains a single-column change.
-- All columns are additive + nullable/defaulted => zero data change,
-- fully backward-compatible with the existing 8 templates.
-- Run once via run_migration.php (or phpMyAdmin).
-- ====================================================

-- Helper: only add columns if they do not already exist.
-- (MySQL has no "ADD COLUMN IF NOT EXISTS" before 8.0.29, so guard in PHP;
--  if running manually and a column exists, ignore the duplicate-column error.)

ALTER TABLE `whatsapp_stores`
  ADD COLUMN `accent_color`     VARCHAR(20)  DEFAULT NULL AFTER `secondary_color`,
  ADD COLUMN `text_color`       VARCHAR(20)  DEFAULT NULL AFTER `accent_color`,
  ADD COLUMN `font_family`      VARCHAR(80)  DEFAULT NULL AFTER `text_color`,
  ADD COLUMN `theme_mode`       ENUM('light','dark','auto') NOT NULL DEFAULT 'light' AFTER `font_family`,
  ADD COLUMN `enable_translate` TINYINT(1)   NOT NULL DEFAULT 1 AFTER `theme_mode`,
  ADD COLUMN `enable_pwa`       TINYINT(1)   NOT NULL DEFAULT 0 AFTER `enable_translate`,
  ADD COLUMN `seo_title`        VARCHAR(200) DEFAULT NULL AFTER `enable_pwa`,
  ADD COLUMN `seo_description`  TEXT         DEFAULT NULL AFTER `seo_title`;

-- Speed up the "Date Posted" client filter / featured ordering on big stores.
-- (Ignore "Duplicate key name" if it already exists.)
ALTER TABLE `whatsapp_store_products`
  ADD INDEX `idx_store_created` (`store_id`, `created_at`);

-- Done.
