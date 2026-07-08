-- ====================================================
-- META ADS — local mirror of boosts run through Tapify's ad account
-- Safe to run multiple times. No backticks.
-- ====================================================
CREATE TABLE IF NOT EXISTS ad_campaigns (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id INT(11) UNSIGNED NOT NULL,
  connection_id BIGINT(20) UNSIGNED DEFAULT NULL,   -- the customer's social_connections row (their Page)
  page_id VARCHAR(191) DEFAULT NULL,
  object_story_id VARCHAR(191) DEFAULT NULL,        -- pageid_postid being boosted
  campaign_id VARCHAR(191) DEFAULT NULL,            -- Meta campaign id
  adset_id VARCHAR(191) DEFAULT NULL,
  ad_id VARCHAR(191) DEFAULT NULL,
  name VARCHAR(255) DEFAULT NULL,
  budget_inr DECIMAL(10,2) NOT NULL,
  budget_points BIGINT(20) NOT NULL,
  commission_points BIGINT(20) NOT NULL,
  duration_days INT(11) NOT NULL DEFAULT 1,
  targeting_json TEXT DEFAULT NULL,
  status VARCHAR(20) NOT NULL DEFAULT 'active',      -- active | paused | failed | completed
  error TEXT DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_user_created (user_id, created_at),
  KEY idx_campaign (campaign_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
