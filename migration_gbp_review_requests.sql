-- TAPIFY Google Business Profile — log of review requests sent to customers.
--
-- One row per customer asked for a review. The log is the point of the feature,
-- not bookkeeping: it is what stops the same customer being asked twice, and it
-- is the only record that exists at all, because the message is sent from the
-- owner's own WhatsApp or SMS app and never touches our servers.
--
-- We deliberately do NOT store whether the customer went on to leave a review.
-- Google does not tell us who wrote a review, and guessing by matching names
-- would be wrong often enough to be worse than useless.
--
-- Safe to re-run.

CREATE TABLE IF NOT EXISTS google_review_requests (
  id            BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id       INT(11) UNSIGNED NOT NULL,
  location_id   VARCHAR(120) DEFAULT NULL,
  customer_name VARCHAR(160) DEFAULT NULL,
  phone         VARCHAR(32) NOT NULL,          -- digits only, country code included
  channel       VARCHAR(20) NOT NULL DEFAULT 'whatsapp',   -- whatsapp | sms | copy
  created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  -- The list is always "this user's, newest first".
  KEY idx_user_time (user_id, created_at),
  -- "have I already asked this person?" — the lookup that prevents a repeat ask.
  KEY idx_user_phone (user_id, phone)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
