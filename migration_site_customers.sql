-- ---------------------------------------------------------------------------
-- Customer accounts for a published website-builder site (optional login/signup
-- for e-commerce sites). Scoped per site — an account on one site is unrelated
-- to any other site or to the Tapify dashboard user.
--
-- Additive only. Safe to run more than once.
-- ---------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS site_customers (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  site_id       INT NOT NULL,
  name          VARCHAR(150) NOT NULL,
  email         VARCHAR(190) NOT NULL,
  phone         VARCHAR(30)  DEFAULT NULL,
  password_hash VARCHAR(255) NOT NULL,
  token         VARCHAR(64)  DEFAULT NULL,
  created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uk_site_email (site_id, email),
  KEY idx_token (token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
