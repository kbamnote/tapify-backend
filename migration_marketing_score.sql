-- TAPIFY — let the score history hold BOTH kinds of score.
--
-- google_business_scores was written for the profile (completeness) score. The
-- marketing score now replaces it in the app, and it needs the same history:
-- "66, up from 21" is the single most motivating line on the screen, and a
-- score with no history is just a statistic.
--
-- Storing both in one table needs a discriminator, otherwise the delta compares
-- a marketing score against yesterday's profile score and reports nonsense.
--
-- Safe to re-run.

SET @add_kind := (
  SELECT IF(COUNT(*) = 0,
    'ALTER TABLE google_business_scores ADD COLUMN kind VARCHAR(20) NOT NULL DEFAULT ''profile''',
    'SELECT "kind already exists"')
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'google_business_scores'
    AND COLUMN_NAME = 'kind'
);
PREPARE s FROM @add_kind; EXECUTE s; DEALLOCATE PREPARE s;

-- The lookup is always "latest score of THIS kind for THIS user".
SET @add_idx := (
  SELECT IF(COUNT(*) = 0,
    'ALTER TABLE google_business_scores ADD INDEX idx_user_kind_time (user_id, kind, created_at)',
    'SELECT "idx_user_kind_time already exists"')
  FROM information_schema.STATISTICS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'google_business_scores'
    AND INDEX_NAME = 'idx_user_kind_time'
);
PREPARE s FROM @add_idx; EXECUTE s; DEALLOCATE PREPARE s;
