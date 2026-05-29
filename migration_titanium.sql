-- =====================================================
-- Migration: Titanium Members
-- =====================================================

CREATE TABLE titanium_members (
  id               INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  user_id          INT UNSIGNED    NOT NULL,
  card_holder_name VARCHAR(100)    DEFAULT NULL,
  card_number      VARCHAR(19)     DEFAULT NULL,
  expiry_date      VARCHAR(7)      DEFAULT NULL,
  is_active        TINYINT(1)      NOT NULL DEFAULT 1,
  created_at       TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at       TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY unique_user (user_id),
  CONSTRAINT fk_titanium_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
