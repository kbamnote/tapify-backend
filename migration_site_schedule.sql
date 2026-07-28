-- ---------------------------------------------------------------------------
-- Weekly availability for builder sites.
--
-- Mirrors vcard_weekly_schedule, but keyed to a site instead of a vCard. One
-- row per open range, so a day can hold several — a lunch break is simply a
-- gap between two rows rather than a field:
--
--   (site 7, Monday, 10:00, 13:00)
--   (site 7, Monday, 16:00, 19:00)
--
-- A day with NO rows means closed for appointments that day.
--
-- Deliberately separate from the site document's business.hours: opening hours
-- and bookable hours are not the same thing (a clinic open 9-9 may run OPD only
-- 10-1), and business.hours allows a single open/close pair per day, which
-- cannot express a break.
-- ---------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS site_schedule (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  site_id      INT NOT NULL,
  day_of_week  TINYINT NOT NULL,          -- 0=Sun .. 6=Sat, matching PHP date('w')
  start_time   TIME NOT NULL,
  end_time     TIME NOT NULL,
  created_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_site_day (site_id, day_of_week)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Per-site booking rules. Kept in its own table so a site with no row simply
-- falls back to the defaults below rather than needing a backfill.
CREATE TABLE IF NOT EXISTS site_schedule_settings (
  site_id        INT NOT NULL PRIMARY KEY,
  slot_minutes   SMALLINT NOT NULL DEFAULT 30,   -- length of each bookable slot
  lead_minutes   SMALLINT NOT NULL DEFAULT 120,  -- min notice; blocks "in 5 minutes" bookings
  horizon_days   SMALLINT NOT NULL DEFAULT 60,   -- how far ahead bookings are allowed
  capacity       SMALLINT NOT NULL DEFAULT 1,    -- bookings accepted per slot (chairs / doctors / tables)
  updated_at     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- If you already created site_schedule_settings from an earlier copy of this
-- file, the CREATE above is skipped and you need the column added explicitly.
-- MySQL has no "ADD COLUMN IF NOT EXISTS", so run this and ignore a
-- "Duplicate column name 'capacity'" error — that just means it is already there.
-- ALTER TABLE site_schedule_settings ADD COLUMN capacity SMALLINT NOT NULL DEFAULT 1 AFTER horizon_days;
