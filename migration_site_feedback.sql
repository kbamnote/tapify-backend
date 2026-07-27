-- ---------------------------------------------------------------------------
-- Feedback submitted from a published builder site's Feedback section.
-- A lightweight satisfaction form: name + optional email + 1-5 rating + message.
-- Separate from site_inquiries (contact) and site_reviews (per-product).
--
-- Additive only. Safe to run more than once.
-- ---------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS site_feedback (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  site_id    INT NOT NULL,
  name       VARCHAR(150) NOT NULL DEFAULT '',
  email      VARCHAR(190) NOT NULL DEFAULT '',
  rating     TINYINT NOT NULL DEFAULT 5,
  message    TEXT NOT NULL,
  page_url   VARCHAR(600) NOT NULL DEFAULT '',
  is_read    TINYINT(1) NOT NULL DEFAULT 0,
  ip_address VARCHAR(45) NOT NULL DEFAULT '',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_site_created (site_id, created_at),
  KEY idx_site_read (site_id, is_read)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
