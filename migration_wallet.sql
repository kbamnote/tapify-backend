-- ====================================================
-- WALLET & PAYMENTS — points wallet + immutable ledger
-- Safe to run multiple times (CREATE TABLE IF NOT EXISTS). No backticks.
-- ====================================================

-- One wallet per user. balance_points is the source of truth; every change is
-- also written to wallet_transactions (append-only ledger).
CREATE TABLE IF NOT EXISTS wallets (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id INT(11) UNSIGNED NOT NULL,
  balance_points BIGINT(20) NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uk_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Append-only ledger. Never UPDATE/DELETE rows — corrections are new rows.
CREATE TABLE IF NOT EXISTS wallet_transactions (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id INT(11) UNSIGNED NOT NULL,
  direction VARCHAR(10) NOT NULL,               -- credit | debit
  points BIGINT(20) NOT NULL,                   -- always positive magnitude
  balance_after BIGINT(20) NOT NULL,            -- wallet balance after this txn
  category VARCHAR(30) NOT NULL,                -- topup | ad_spend | commission | refund | adjustment
  reference VARCHAR(191) DEFAULT NULL,          -- razorpay_payment_id / campaign id / etc.
  description VARCHAR(255) DEFAULT NULL,
  meta_json TEXT DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_user_created (user_id, created_at),
  KEY idx_reference (reference)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Razorpay orders created for top-ups (bridges the checkout to wallet credit).
CREATE TABLE IF NOT EXISTS wallet_topups (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id INT(11) UNSIGNED NOT NULL,
  provider VARCHAR(20) NOT NULL DEFAULT 'razorpay',
  order_id VARCHAR(191) DEFAULT NULL,           -- provider order id
  payment_id VARCHAR(191) DEFAULT NULL,         -- provider payment id (on success)
  amount_inr DECIMAL(10,2) NOT NULL,
  points BIGINT(20) NOT NULL,
  status VARCHAR(20) NOT NULL DEFAULT 'created', -- created | paid | failed
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uk_order (order_id),
  KEY idx_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
