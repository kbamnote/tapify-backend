-- ---------------------------------------------------------------------------
-- Appointments booked from a published website-builder site.
--
-- Kept in its own table (NOT vcard_appointments) because those rows are keyed
-- to a vcard_id and the dashboard scopes them through the vcards table. The
-- Appointments API merges both sources for display.
--
-- Additive only. Safe to run more than once.
-- ---------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS site_appointments (
  id               INT AUTO_INCREMENT PRIMARY KEY,
  site_id          INT NOT NULL,
  customer_name    VARCHAR(150) NOT NULL,
  customer_email   VARCHAR(150) DEFAULT NULL,
  customer_phone   VARCHAR(30)  NOT NULL,
  service_name     VARCHAR(255) DEFAULT NULL,
  appointment_date DATE NOT NULL,
  appointment_time TIME NOT NULL,
  customer_notes   TEXT DEFAULT NULL,
  admin_notes      TEXT DEFAULT NULL,
  status           ENUM('pending','confirmed','completed','cancelled','no_show') DEFAULT 'pending',
  is_read          TINYINT(1) DEFAULT 0,
  ip_address       VARCHAR(45) NOT NULL DEFAULT '',
  created_at       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY idx_site_date (site_id, appointment_date),
  KEY idx_site_status (site_id, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
