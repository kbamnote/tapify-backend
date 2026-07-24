-- ============================================================================
-- "Sign in with Google" for builder-site customers.
--
-- Extends site_customers so a customer can exist without a password (Google
-- accounts), records which provider they used, and carries a short-lived
-- one-time "handoff code" used to hand the session token from the fixed OAuth
-- callback (app.tapify.co.in) back to the customer's own subdomain.
--
-- Safe to re-run: each ADD/ MODIFY is guarded by hand — run the block once.
-- On MySQL 8 without IF NOT EXISTS for columns, skip any line that errors with
-- "Duplicate column name".
-- ============================================================================

-- Passwords are only set for email/password accounts now.
ALTER TABLE site_customers MODIFY password_hash VARCHAR(255) DEFAULT NULL;

-- How the account was created, and the Google subject id (stable per user).
ALTER TABLE site_customers ADD COLUMN provider VARCHAR(20) NOT NULL DEFAULT 'email' AFTER phone;
ALTER TABLE site_customers ADD COLUMN google_id VARCHAR(40) DEFAULT NULL AFTER provider;

-- One-time handoff code (+ expiry) minted by the OAuth callback and redeemed
-- once by the customer's browser on the site subdomain.
ALTER TABLE site_customers ADD COLUMN handoff_code VARCHAR(64) DEFAULT NULL AFTER token;
ALTER TABLE site_customers ADD COLUMN handoff_expires DATETIME DEFAULT NULL AFTER handoff_code;

ALTER TABLE site_customers ADD INDEX idx_handoff (handoff_code);
