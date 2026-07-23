-- ---------------------------------------------------------------------------
-- Website-builder orders + product reviews.
--
-- These belong to the BUILDER (tapify-sites) and are independent of the vCard /
-- mini-site tables.
--
-- No foreign key to sites(id): the column types differ on the hosted database
-- and MySQL rejects the constraint. Ownership is enforced in code instead --
-- order-submit.php, orders.php, reviews.php and review-submit.php all scope
-- every query by site_id. The trade-off is that deleting a site leaves its
-- orders and reviews behind as orphan rows.
--
-- Additive only: creates two new tables and touches nothing that already
-- exists. Safe to run more than once (CREATE TABLE IF NOT EXISTS).
-- ---------------------------------------------------------------------------

-- Orders placed from a published builder site's product page or its cart.
CREATE TABLE IF NOT EXISTS site_orders (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  site_id        INT NOT NULL,
  item_title     VARCHAR(200) NOT NULL DEFAULT '',
  item_slug      VARCHAR(120) NOT NULL DEFAULT '',
  price          VARCHAR(40)  NOT NULL DEFAULT '',
  mrp            VARCHAR(40)  NOT NULL DEFAULT '',
  -- the customisable choice, e.g. option_label "Size" / option_value "M"
  option_label   VARCHAR(60)  NOT NULL DEFAULT '',
  option_value   VARCHAR(120) NOT NULL DEFAULT '',
  quantity       INT NOT NULL DEFAULT 1,
  customer_name  VARCHAR(120) NOT NULL,
  customer_phone VARCHAR(40)  NOT NULL,
  customer_email VARCHAR(190) NOT NULL DEFAULT '',
  note           TEXT NULL,
  status         ENUM('new','confirmed','completed','cancelled') NOT NULL DEFAULT 'new',
  ip_address     VARCHAR(45) NOT NULL DEFAULT '',
  created_at     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY idx_site_created (site_id, created_at),
  KEY idx_site_status  (site_id, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Customer reviews shown on the product/service detail page.
CREATE TABLE IF NOT EXISTS site_reviews (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  site_id    INT NOT NULL,
  item_slug  VARCHAR(120) NOT NULL DEFAULT '',
  name       VARCHAR(120) NOT NULL,
  rating     TINYINT NOT NULL DEFAULT 5,
  comment    TEXT NOT NULL,
  -- reviews appear immediately; flip to 0 to hide one from the site
  approved   TINYINT(1) NOT NULL DEFAULT 1,
  ip_address VARCHAR(45) NOT NULL DEFAULT '',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_site_item (site_id, item_slug, approved)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
