-- ====================================================
-- NO-CODE WEBSITE BUILDER — core tables
--
-- Brand-new system. Does NOT touch vcards / whatsapp_stores / dynamic_qrs
-- or any existing table. Safe to run multiple times (CREATE TABLE IF NOT EXISTS).
--
-- Model: a `site` owns an ordered history of `site_versions`. Each version holds
-- ONE JSON document (the whole website: theme + pages + sections). The site row
-- just points at which version is the working draft and which one is published.
--   Publish  = copy draft doc into a new version, set published_version_id.
--   Rollback = point published_version_id at an older version.
-- No HTML/CSS is ever stored — only structured JSON.
-- ====================================================

-- ----------------------------------------------------
-- sites: one row per website. `slug` is the public URL key
-- (kept in its own namespace; it is NOT a vcards.url_alias).
-- ----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sites` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) UNSIGNED NOT NULL,             -- owner (existing Tapify users.id)
  `slug` VARCHAR(120) NOT NULL,                    -- public URL key, DNS-label safe
  `name` VARCHAR(160) NOT NULL,                    -- internal/display name
  `industry` VARCHAR(60) DEFAULT NULL,             -- coaching | gym | restaurant | ...
  `status` VARCHAR(20) NOT NULL DEFAULT 'draft',   -- draft | published | disabled
  `draft_version_id` BIGINT(20) UNSIGNED DEFAULT NULL,     -- -> site_versions.id
  `published_version_id` BIGINT(20) UNSIGNED DEFAULT NULL, -- -> site_versions.id (NULL = never published)
  `published_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_slug` (`slug`),
  KEY `idx_user` (`user_id`, `updated_at`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------
-- site_versions: immutable snapshots of the site JSON document.
-- `rev` increments per site and is what the editors send back for
-- optimistic locking (reject a save based on a stale rev).
-- ----------------------------------------------------
CREATE TABLE IF NOT EXISTS `site_versions` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `site_id` BIGINT(20) UNSIGNED NOT NULL,
  `rev` INT(11) UNSIGNED NOT NULL DEFAULT 1,       -- per-site revision counter
  `doc` LONGTEXT NOT NULL,                         -- the Site Document (JSON)
  `schema_version` INT(11) UNSIGNED NOT NULL DEFAULT 1,
  `kind` VARCHAR(20) NOT NULL DEFAULT 'draft',     -- draft | published
  `label` VARCHAR(120) DEFAULT NULL,               -- optional human label ("before redesign")
  `author_user_id` INT(11) UNSIGNED DEFAULT NULL,  -- who saved it
  `source` VARCHAR(20) NOT NULL DEFAULT 'web',     -- web | app | ai | system
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_site_rev` (`site_id`, `rev`),
  KEY `idx_site_created` (`site_id`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------
-- media_assets: reusable media library (upload once, use anywhere).
-- Documents reference assets as "media:<id>" — never a raw URL — so a file
-- can be replaced/moved/CDN-swapped without rewriting any site JSON.
-- ----------------------------------------------------
CREATE TABLE IF NOT EXISTS `media_assets` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) UNSIGNED NOT NULL,
  `site_id` BIGINT(20) UNSIGNED DEFAULT NULL,      -- NULL = account-level, reusable across sites
  `kind` VARCHAR(20) NOT NULL DEFAULT 'image',     -- image | video | pdf | icon | doc
  `path` VARCHAR(500) NOT NULL,                    -- storage path or absolute URL
  `mime` VARCHAR(100) DEFAULT NULL,
  `bytes` BIGINT(20) UNSIGNED DEFAULT NULL,
  `width` INT(11) UNSIGNED DEFAULT NULL,           -- for images/video (prevents layout shift)
  `height` INT(11) UNSIGNED DEFAULT NULL,
  `alt` VARCHAR(255) DEFAULT NULL,                 -- accessibility / SEO
  `title` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_kind` (`user_id`, `kind`, `created_at`),
  KEY `idx_site` (`site_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------
-- form_submissions: leads from builder-created forms.
-- `form_id` matches a form declared inside the site document (doc.forms[].id).
-- ----------------------------------------------------
CREATE TABLE IF NOT EXISTS `form_submissions` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `site_id` BIGINT(20) UNSIGNED NOT NULL,
  `form_id` VARCHAR(60) NOT NULL,                  -- doc.forms[].id
  `data` LONGTEXT NOT NULL,                        -- submitted field values (JSON)
  `page_slug` VARCHAR(160) DEFAULT NULL,           -- where it was submitted from
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `user_agent` VARCHAR(255) DEFAULT NULL,
  `is_read` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_site_created` (`site_id`, `created_at`),
  KEY `idx_site_unread` (`site_id`, `is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
