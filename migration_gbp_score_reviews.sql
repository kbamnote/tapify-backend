-- TAPIFY Google Business Profile — profile score history + review auto-reply.
--
-- Two additions:
--   1. google_business_scores — one row per score computation, so the app can
--      show "54 → 78 this month" rather than only a bare number. A score with
--      no history is a statistic; a score with history is progress, and that is
--      the whole reason a customer opens the screen again.
--   2. auto_reply columns on the connection — opt-in, off by default, with a
--      star threshold so a business is never auto-replying to a 1-star review
--      in its own name without reading it first.
--
-- Safe to re-run.

CREATE TABLE IF NOT EXISTS google_business_scores (
  id          BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id     INT(11) UNSIGNED NOT NULL,
  location_id VARCHAR(120) DEFAULT NULL,
  score       TINYINT UNSIGNED NOT NULL,
  breakdown   TEXT DEFAULT NULL,          -- JSON snapshot of the itemised result
  created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_user_time (user_id, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Auto-reply settings live on the connection, so disconnecting clears them too.
SET @add_ar := (
  SELECT IF(COUNT(*) = 0,
    'ALTER TABLE google_business_connections ADD COLUMN auto_reply_enabled TINYINT(1) NOT NULL DEFAULT 0',
    'SELECT "auto_reply_enabled already exists"')
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'google_business_connections'
    AND COLUMN_NAME = 'auto_reply_enabled'
);
PREPARE s FROM @add_ar; EXECUTE s; DEALLOCATE PREPARE s;

-- Only reviews at or above this rating are answered automatically. Default 4:
-- praise gets a warm reply instantly, complaints wait for a human.
SET @add_min := (
  SELECT IF(COUNT(*) = 0,
    'ALTER TABLE google_business_connections ADD COLUMN auto_reply_min_stars TINYINT UNSIGNED NOT NULL DEFAULT 4',
    'SELECT "auto_reply_min_stars already exists"')
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'google_business_connections'
    AND COLUMN_NAME = 'auto_reply_min_stars'
);
PREPARE s FROM @add_min; EXECUTE s; DEALLOCATE PREPARE s;

-- Which reviews we have already answered automatically, so a cron re-run never
-- posts a second reply to the same review.
CREATE TABLE IF NOT EXISTS google_review_replies (
  id         BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id    INT(11) UNSIGNED NOT NULL,
  review_id  VARCHAR(255) NOT NULL,
  star_rating TINYINT UNSIGNED DEFAULT NULL,
  reply_text TEXT DEFAULT NULL,
  source     VARCHAR(20) NOT NULL DEFAULT 'auto',   -- auto | manual
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uk_review (review_id),
  KEY idx_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
