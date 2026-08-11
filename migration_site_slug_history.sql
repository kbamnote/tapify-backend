-- TAPIFY Website Builder — slug rename history (permanent redirects).
--
-- Renaming a site changes its public address instantly, which would 404 every
-- QR code, business card and shared link already in the wild. Every old slug is
-- recorded here so index.php can 301 it to the site's CURRENT address.
--
-- We store site_id, NOT the replacement slug: after foo -> bar -> baz, both
-- "foo" and "bar" resolve to the site and then to its current slug in a single
-- hop. Storing the next slug instead would build a redirect chain that grows
-- with every rename.
--
-- Safe to re-run.

CREATE TABLE IF NOT EXISTS `site_slug_history` (
  `id`         BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `site_id`    BIGINT(20) UNSIGNED NOT NULL,
  `old_slug`   VARCHAR(120) NOT NULL,
  `changed_by` BIGINT(20) UNSIGNED DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  -- One row per retired slug globally: an old address can only ever point at
  -- one site, otherwise a redirect would be ambiguous.
  UNIQUE KEY `uk_old_slug` (`old_slug`),
  KEY `idx_site` (`site_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
