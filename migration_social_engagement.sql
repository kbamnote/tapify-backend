-- TAPIFY Social Publishing — post engagement cache.
--
-- Engagement (likes / comments / shares) is read back from the platform for
-- posts published through Tapify and shown to the customer in the app.
-- Cached on the target row because a customer with 30 published posts would
-- otherwise cost 30 Graph calls on every screen open, which burns the app's
-- rate limit for no benefit — the counts move slowly.
--
-- Safe to re-run: both columns are added only if missing.

SET @add_metrics := (
  SELECT IF(
    COUNT(*) = 0,
    'ALTER TABLE social_post_targets ADD COLUMN metrics_json TEXT NULL AFTER remote_url',
    'SELECT "social_post_targets.metrics_json already exists"'
  )
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME   = 'social_post_targets'
    AND COLUMN_NAME  = 'metrics_json'
);
PREPARE s FROM @add_metrics; EXECUTE s; DEALLOCATE PREPARE s;

SET @add_fetched := (
  SELECT IF(
    COUNT(*) = 0,
    'ALTER TABLE social_post_targets ADD COLUMN metrics_fetched_at DATETIME NULL AFTER metrics_json',
    'SELECT "social_post_targets.metrics_fetched_at already exists"'
  )
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME   = 'social_post_targets'
    AND COLUMN_NAME  = 'metrics_fetched_at'
);
PREPARE s FROM @add_fetched; EXECUTE s; DEALLOCATE PREPARE s;

-- Picking the stalest rows to refresh is the hot query; NULL sorts first, which
-- is what we want (never-fetched rows go to the front of the queue).
SET @add_idx := (
  SELECT IF(
    COUNT(*) = 0,
    'CREATE INDEX idx_spt_metrics_fetched ON social_post_targets (status, metrics_fetched_at)',
    'SELECT "idx_spt_metrics_fetched already exists"'
  )
  FROM information_schema.STATISTICS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME   = 'social_post_targets'
    AND INDEX_NAME   = 'idx_spt_metrics_fetched'
);
PREPARE s FROM @add_idx; EXECUTE s; DEALLOCATE PREPARE s;
