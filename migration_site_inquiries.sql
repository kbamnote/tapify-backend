-- ---------------------------------------------------------------------------
-- Enquiries sent from a published website-builder site's Contact section.
--
-- Separate from vcard_inquiries because these key to a site, not a vCard, and
-- are shown on their own "Website Inquiries" dashboard page.
--
-- Additive only. Safe to run more than once.
-- ---------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS site_inquiries (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  site_id    INT NOT NULL,
  name       VARCHAR(150) NOT NULL,
  email      VARCHAR(190) NOT NULL DEFAULT '',
  phone      VARCHAR(40)  NOT NULL DEFAULT '',
  subject    VARCHAR(200) NOT NULL DEFAULT '',
  message    TEXT NOT NULL,
  page_url   VARCHAR(600) NOT NULL DEFAULT '',
  is_read    TINYINT(1) NOT NULL DEFAULT 0,
  ip_address VARCHAR(45) NOT NULL DEFAULT '',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_site_created (site_id, created_at),
  KEY idx_site_read (site_id, is_read)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
