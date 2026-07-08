-- ====================================================
-- SOCIAL MEDIA PUBLISHING — connections, posts, targets, oauth states
-- Safe to run multiple times (CREATE TABLE IF NOT EXISTS). No backticks.
-- ====================================================

-- One row per authorized account (a FB Page, an IG business account, a LinkedIn
-- profile/org). A single Meta OAuth can create several rows (one per Page + IG).
CREATE TABLE IF NOT EXISTS social_connections (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id INT(11) UNSIGNED NOT NULL,
  platform VARCHAR(30) NOT NULL,               -- facebook | instagram | linkedin
  account_id VARCHAR(191) NOT NULL,            -- page id / ig user id / linkedin urn
  account_name VARCHAR(255) DEFAULT NULL,
  account_avatar TEXT DEFAULT NULL,
  access_token TEXT DEFAULT NULL,              -- page token / user token
  refresh_token TEXT DEFAULT NULL,
  token_expiry DATETIME DEFAULT NULL,
  scope VARCHAR(500) DEFAULT NULL,
  extra_json TEXT DEFAULT NULL,                -- platform extras (ig_user_id, page_id, urn...)
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  connected_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uk_user_platform_account (user_id, platform, account_id),
  KEY idx_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- A composed post (one caption + media set), possibly targeting several accounts.
CREATE TABLE IF NOT EXISTS social_posts (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id INT(11) UNSIGNED NOT NULL,
  caption LONGTEXT DEFAULT NULL,
  media_json TEXT DEFAULT NULL,                -- [{type:image|video,url:...}]
  status VARCHAR(20) NOT NULL DEFAULT 'draft', -- draft|scheduled|publishing|published|partial|failed
  scheduled_at DATETIME DEFAULT NULL,          -- null = post now
  published_at DATETIME DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_user_created (user_id, created_at),
  KEY idx_scheduled (status, scheduled_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Per-account delivery result for a post (one row per target account).
CREATE TABLE IF NOT EXISTS social_post_targets (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  post_id BIGINT(20) UNSIGNED NOT NULL,
  connection_id BIGINT(20) UNSIGNED NOT NULL,
  platform VARCHAR(30) NOT NULL,
  status VARCHAR(20) NOT NULL DEFAULT 'pending', -- pending|published|failed
  remote_post_id VARCHAR(255) DEFAULT NULL,
  remote_url VARCHAR(500) DEFAULT NULL,
  error TEXT DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_post (post_id),
  KEY idx_conn (connection_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Short-lived CSRF/state bridge for the browser OAuth flow (carries platform).
CREATE TABLE IF NOT EXISTS social_oauth_states (
  state CHAR(64) NOT NULL,
  user_id INT(11) UNSIGNED NOT NULL,
  platform VARCHAR(30) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (state),
  KEY idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
