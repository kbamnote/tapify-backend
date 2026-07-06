-- ====================================================
-- GOOGLE BUSINESS PROFILE — connection + OAuth state tables
-- Safe to run multiple times (CREATE TABLE IF NOT EXISTS).
-- No backticks (some SQL consoles reject them).
-- ====================================================

-- One connection per user. Holds OAuth tokens + the selected GBP location.
CREATE TABLE IF NOT EXISTS google_business_connections (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id INT(11) UNSIGNED NOT NULL,
  google_account_id VARCHAR(120) DEFAULT NULL,   -- e.g. accounts/123
  account_name VARCHAR(255) DEFAULT NULL,
  location_id VARCHAR(120) DEFAULT NULL,          -- e.g. locations/456 (selected)
  location_title VARCHAR(255) DEFAULT NULL,
  access_token TEXT DEFAULT NULL,
  refresh_token TEXT DEFAULT NULL,
  token_expiry DATETIME DEFAULT NULL,
  scope VARCHAR(255) DEFAULT NULL,
  connected_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uk_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Short-lived CSRF/state store bridging the app session to the browser OAuth
-- flow (the system browser does not share the app's PHP session cookie).
CREATE TABLE IF NOT EXISTS google_oauth_states (
  state CHAR(64) NOT NULL,
  user_id INT(11) UNSIGNED NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (state),
  KEY idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
