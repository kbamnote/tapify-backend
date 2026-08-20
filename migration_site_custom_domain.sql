-- TAPIFY — let a builder site answer on the customer's own domain.
--
-- HOW THE ROUTING ACTUALLY WORKS, because it is not obvious from this column:
-- Railway caps custom domains per service (currently 2, both used by
-- app.tapify.co.in and the *.tapify.co.in wildcard), so customer domains are
-- NOT added to Railway. Instead Cloudflare sits in front and fetches the page
-- from the site's normal subdomain:
--
--   www.galaxycardecor.com -> Cloudflare -> galaxycardecor.tapify.co.in -> Railway
--
-- Railway therefore never sees the custom domain and the cap never applies.
--
-- WHAT THIS COLUMN IS FOR. Because Cloudflare rewrites the Host on the way
-- through, PHP sees the tapify subdomain and would emit a canonical tag, an OG
-- url and a share/QR link pointing at tapify.co.in — so Google would index the
-- subdomain and the custom domain would be pointless. Cloudflare passes the
-- original host in X-Forwarded-Host, and the renderer prefers it — but ONLY
-- when it matches the domain recorded here. Without that check anyone could
-- forge the header against the public subdomain and poison the canonical tags.
--
-- Safe to re-run.

SET @add_domain := (
  SELECT IF(COUNT(*) = 0,
    'ALTER TABLE sites ADD COLUMN domain VARCHAR(190) NULL DEFAULT NULL',
    'SELECT "sites.domain already exists"')
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'sites' AND COLUMN_NAME = 'domain'
);
PREPARE s FROM @add_domain; EXECUTE s; DEALLOCATE PREPARE s;

SET @add_verified := (
  SELECT IF(COUNT(*) = 0,
    'ALTER TABLE sites ADD COLUMN domain_verified_at TIMESTAMP NULL DEFAULT NULL',
    'SELECT "sites.domain_verified_at already exists"')
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'sites' AND COLUMN_NAME = 'domain_verified_at'
);
PREPARE s FROM @add_verified; EXECUTE s; DEALLOCATE PREPARE s;

-- One domain can only belong to one site. UNIQUE tolerates many NULLs in MySQL,
-- so sites without a custom domain are unaffected.
SET @add_idx := (
  SELECT IF(COUNT(*) = 0,
    'ALTER TABLE sites ADD UNIQUE KEY uk_domain (domain)',
    'SELECT "uk_domain already exists"')
  FROM information_schema.STATISTICS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'sites' AND INDEX_NAME = 'uk_domain'
);
PREPARE s FROM @add_idx; EXECUTE s; DEALLOCATE PREPARE s;
