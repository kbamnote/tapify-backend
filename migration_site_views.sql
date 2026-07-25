-- ---------------------------------------------------------------------------
-- Website view tracking for builder sites.
--
-- One row per site per day (a compact daily counter), so the dashboard can show
-- total views + a last-7-days trend without storing a row per hit.
--   INSERT ... ON DUPLICATE KEY UPDATE views = views + 1
--
-- Additive only. Safe to run more than once.
-- ---------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS site_views (
  site_id    INT NOT NULL,
  view_date  DATE NOT NULL,
  views      INT NOT NULL DEFAULT 0,
  PRIMARY KEY (site_id, view_date),
  KEY idx_view_date (view_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
