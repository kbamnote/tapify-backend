<?php
/**
 * SiteRenderer — renders a published builder site (the JSON document produced by
 * the website builder) to a self-contained HTML page, served from Railway at
 * <slug>.tapify.co.in exactly like a vCard.
 *
 * Why PHP and not the Next.js app:
 *   - the same wildcard *.tapify.co.in already points here, so every published
 *     site is instantly live with NO per-site DNS or Vercel work;
 *   - fully server-rendered HTML is the best case for SEO;
 *   - it lives beside the vCards, so there is one hosting story, not two.
 *
 * The Next.js app remains the editor (the interactive builder UI). Both draw the
 * SAME document — the section manifests are the shared contract — so this is a
 * faithful port of src/sections/* and src/lib/theme.ts.
 */

require_once __DIR__ . '/../lib/SiteRepo.php';

class SiteRenderer
{
    /** @var string current site slug (for the contact form's hidden field) */
    private static $slug = '';

    /**
     * @var string The customer's own domain for this site, if they have one and
     *   this request genuinely arrived through it. Empty otherwise.
     */
    private static $customDomain = '';

    /**
     * The hostname to put in canonical tags, OG urls, share links and the QR.
     *
     * Custom domains do NOT go on Railway — its per-service domain cap is full
     * (app.tapify.co.in plus the *.tapify.co.in wildcard). Cloudflare sits in
     * front instead and fetches the page from the site's ordinary subdomain:
     *
     *   www.galaxycardecor.com -> Cloudflare -> galaxycardecor.tapify.co.in
     *
     * That rewrite is invisible to PHP, so without this helper every canonical
     * tag, share link and QR code would point at the tapify subdomain — Google
     * would index that, and the custom domain would be decorative.
     *
     * Cloudflare passes the real hostname in X-Forwarded-Host. That header is
     * attacker-controlled on the public subdomain, so it is honoured ONLY when
     * it matches the domain recorded against this site; anything else falls
     * back to the real Host.
     */
    private static function publicHost(): string
    {
        if (self::$customDomain !== '') return self::$customDomain;
        return $_SERVER['HTTP_HOST'] ?? (self::$slug . '.' . PUBLIC_BASE_DOMAIN);
    }

    /**
     * The hostname the VISITOR typed, as forwarded by Cloudflare.
     *
     * X-Forwarded-Host alone is not enough: Railway's own edge sets that header
     * from the Host it receives — which the Worker has already rewritten to the
     * tapify subdomain — so PHP would either see the subdomain or a comma list
     * and never recognise the customer's domain. The Worker therefore also sends
     * X-Tapify-Host, which nothing between it and PHP touches. Both are still
     * verified against the site's recorded domain by the caller, so an outsider
     * forging either header on the public subdomain gains nothing.
     */
    private static function forwardedHost(): string
    {
        $h = (string)($_SERVER['HTTP_X_TAPIFY_HOST'] ?? '');
        if (trim($h) === '') $h = (string)($_SERVER['HTTP_X_FORWARDED_HOST'] ?? '');
        // A chain of proxies appends rather than replaces; the first entry is the
        // one closest to the visitor.
        if (strpos($h, ',') !== false) $h = explode(',', $h)[0];
        $h = strtolower(trim($h));
        if (($p = strpos($h, ':')) !== false) $h = substr($h, 0, $p);
        return $h;
    }

    /** Normalise a hostname for comparison: lowercase, no port, no leading www. */
    private static function normHost(string $h): string
    {
        $h = strtolower(trim($h));
        if (($p = strpos($h, ':')) !== false) $h = substr($h, 0, $p);
        return preg_replace('/^www\./', '', $h);
    }

    /* ------------------------------------------------------------- entry */

    /** Fast check used by the router: is this subdomain a PUBLISHED builder site? */
    public static function hasPublishedSite(string $slug): bool
    {
        $slug = strtolower(trim($slug));
        if ($slug === '') return false;
        try {
            $stmt = getDB()->prepare(
                "SELECT 1 FROM sites
                 WHERE slug = ? AND status = 'published' AND published_version_id IS NOT NULL
                 LIMIT 1"
            );
            $stmt->execute([$slug]);
            return (bool)$stmt->fetchColumn();
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Render the page at $path for site $slug. Echoes the page and returns true,
     * or 404s (still returns true — the host belongs to this site either way).
     */
    public static function renderBySlug(string $slug, string $path = '/'): bool
    {
        // This is a customer-facing page: never leak PHP notices/warnings into the
        // HTML. They are still written to the error log for debugging.
        @ini_set('display_errors', '0');
        error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);

        self::$slug = strtolower(trim($slug));

        $site = SiteRepo::findBySlug(self::$slug);
        if (!$site || ($site['status'] ?? '') === 'disabled') { self::notFound(); return true; }

        // Did this request come in through the customer's own domain? Only
        // believe X-Forwarded-Host when it matches what is recorded for THIS
        // site — the header is forgeable by anyone hitting the public subdomain,
        // and an unchecked value would let a stranger rewrite our canonical tags.
        self::$customDomain = '';
        $seen    = self::forwardedHost();
        $claimed = self::normHost($seen);
        $owned   = self::normHost((string)($site['domain'] ?? ''));
        if ($claimed !== '' && $owned !== '' && $claimed === $owned) {
            // Keep whatever form the visitor actually used (with or without www)
            // so the canonical matches the address in their address bar.
            self::$customDomain = $seen;
        }

        // Once a custom domain is VERIFIED, the tapify subdomain becomes a
        // duplicate of it, which splits the site's search ranking across two
        // addresses. Send direct visitors to the real one.
        //
        // THE TRAP: Cloudflare reaches us BY requesting the subdomain, so an
        // unconditional redirect here would bounce Cloudflare straight back to
        // the customer's domain and loop forever. A request that arrived through
        // the proxy is exactly the one that set $customDomain — so only redirect
        // when it is EMPTY, i.e. somebody typed the subdomain directly.
        if (self::$customDomain === ''
            && !empty($site['domain'])
            && !empty($site['domain_verified_at'])) {
            $target = 'https://' . $site['domain'] . ($path === '' ? '/' : $path);
            header('Location: ' . $target, true, 301);
            return true;
        }

        $published = SiteRepo::getPublished($site);
        $doc = is_array($published) ? ($published['doc'] ?? null) : null;
        if (!is_array($doc) || empty($doc['pages'])) { self::notFound(); return true; }

        // Count this visit for the site's view analytics (best-effort, never blocks).
        self::trackView($site, $path);
        // …and add the tap-tracking snippet to whatever page is rendered below,
        // so Call / WhatsApp / directions taps on the customer's website are
        // counted the same way as on their card.
        self::injectTaps((int)$site['id']);

        $norm = ($path === '' || $path === '/') ? '/' : rtrim($path, '/');

        // Legal pages: /privacy and /terms. Content lives in the footer section.
        if ($norm === '/privacy' || $norm === '/terms') {
            $kind = $norm === '/privacy' ? 'privacy' : 'terms';
            [$title, $body] = self::legalContent($doc, $kind);
            if (trim($body) !== '') {
                header('Content-Type: text/html; charset=utf-8');
                echo self::renderLegal($doc, $title, $body, $norm);
                return true;
            }
            self::notFound();
            return true;
        }

        // Blog post detail: /post/<slug>. Posts live inside blog sections (not in
        // doc.pages), so this renders a single post with the site's header/footer.
        if (preg_match('#^/post/([a-z0-9][a-z0-9-]*)$#', $norm, $pm)) {
            $posts = self::allPosts($doc);
            if (isset($posts[$pm[1]])) {
                header('Content-Type: text/html; charset=utf-8');
                echo self::renderPostDetail($doc, $posts[$pm[1]]);
                return true;
            }
            self::notFound();
            return true;
        }

        // Product/service detail: /service/<slug> — photo gallery + description.
        if (preg_match('#^/service/([a-z0-9][a-z0-9-]*)$#', $norm, $sm)) {
            $services = self::allServices($doc);
            if (isset($services[$sm[1]])) {
                header('Content-Type: text/html; charset=utf-8');
                echo self::renderServiceDetail($doc, $services[$sm[1]]);
                return true;
            }
            self::notFound();
            return true;
        }

        // Product detail: /product/<slug> — same idea as /service/, but the item
        // can carry nested Colour/Size-style variant attributes.
        if (preg_match('#^/product/([a-z0-9][a-z0-9-]*)$#', $norm, $prm)) {
            $products = self::allProducts($doc);
            if (isset($products[$prm[1]])) {
                header('Content-Type: text/html; charset=utf-8');
                echo self::renderProductDetail($doc, $products[$prm[1]]);
                return true;
            }
            self::notFound();
            return true;
        }

        $page = null;
        foreach ($doc['pages'] as $p) {
            if (($p['slug'] ?? '') === $norm) { $page = $p; break; }
        }
        // Built-in cart page. Looked up after doc.pages so a site that builds its
        // own /cart keeps it.
        if (!$page && $norm === '/cart') {
            header('Content-Type: text/html; charset=utf-8');
            echo self::renderCartPage($doc);
            return true;
        }
        // Built-in login / signup page. This is where the header's Login/Signup
        // button points; after doc.pages so a site that builds its own /account
        // keeps it.
        if (!$page && $norm === '/account') {
            header('Content-Type: text/html; charset=utf-8');
            echo self::renderAccountPage($doc);
            return true;
        }
        // Built-in "My Orders" page — full order history with reorder / cancel /
        // review. Requires a signed-in customer (the page redirects to /account).
        if (!$page && $norm === '/orders') {
            header('Content-Type: text/html; charset=utf-8');
            echo self::renderOrdersPage($doc);
            return true;
        }
        // Built-in product search — where the header's search box submits.
        if (!$page && $norm === '/search') {
            header('Content-Type: text/html; charset=utf-8');
            echo self::renderSearchPage($doc, is_string($_GET['q'] ?? null) ? $_GET['q'] : '');
            return true;
        }

        if (!$page || ($page['visible'] ?? true) === false) { self::notFound(); return true; }

        header('Content-Type: text/html; charset=utf-8');
        echo self::renderDocument($doc, $page);
        return true;
    }

    /** Bump today's view counter for a site. Skips bots; never throws. */
    private static function trackView(array $site, string $path = ''): void
    {
        $siteId = (int)($site['id'] ?? 0);
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
        // Don't count obvious crawlers / link-preview fetchers.
        if ($siteId <= 0 || $ua === '' || preg_match('/bot|crawl|spider|slurp|bingpreview|facebookexternalhit|whatsapp|telegram|preview|monitor|curl|wget|python-requests/i', $ua)) {
            return;
        }
        try {
            getDB()->prepare(
                "INSERT INTO site_views (site_id, view_date, views) VALUES (?, CURDATE(), 1)
                 ON DUPLICATE KEY UPDATE views = views + 1"
            )->execute([$siteId]);
        } catch (Throwable $e) {
            // Table not migrated yet, or any DB hiccup — a view count must never
            // break the customer's page.
        }

        // The same visit, with which page, how they got there and whether they
        // had been before — for the Customer Manager dashboard.
        try {
            require_once __DIR__ . '/../../includes/engagement/Engagement.php';
            Engagement::record((int)($site['user_id'] ?? 0), 'site', $siteId, 'view', [
                'label' => $path === '' ? '/' : $path,
            ]);
        } catch (Throwable $e) {
            // Never at the cost of the page.
        }
    }

    /**
     * Buffers the rest of the response and puts the tap-tracking snippet before
     * </body>. Done here, once, rather than in each of the built-in pages.
     */
    private static function injectTaps(int $siteId): void
    {
        try {
            require_once __DIR__ . '/../../includes/engagement/tap-snippet.php';
            $snippet = tapify_tap_snippet('site', $siteId);
            if ($snippet === '') {
                return;
            }
            ob_start(static function ($html) use ($snippet) {
                if (stripos($html, '<body') === false) {
                    return $html; // not an HTML page (sitemap, robots, a redirect)
                }
                $pos = strripos($html, '</body>');
                return $pos === false
                    ? $html . $snippet
                    : substr($html, 0, $pos) . $snippet . substr($html, $pos);
            });
        } catch (Throwable $e) {
            // Tracking is never worth a broken page.
        }
    }

    private static function notFound(): void
    {
        http_response_code(404);
        header('Content-Type: text/html; charset=utf-8');
        echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Not found</title>'
           . '<meta name="viewport" content="width=device-width, initial-scale=1">'
           . '<style>body{font-family:system-ui,sans-serif;text-align:center;padding:60px 20px;color:#334155}h1{font-size:3rem;margin:0}</style>'
           . '</head><body><h1>404</h1><p>This page could not be found.</p></body></html>';
    }

    /* ------------------------------------------------------------- page */

    private static function renderDocument(array $doc, array $page): string
    {
        $vars  = self::themeVars($doc['theme'] ?? []);
        $fonts = self::googleFonts($doc['theme'] ?? []);
        $head  = self::head($doc, $page, $fonts);

        $sections = '';
        // If this page has no header, prepend one from another page so the
        // site chrome (logo, nav) is consistent on every page.
        if (!self::pageHasType($page, 'header')) {
            $sections .= self::chromeSection($doc, 'header');
        }
        foreach (($page['sections'] ?? []) as $s) {
            if (($s['visible'] ?? true) === false) continue;
            if (!empty($s['style']['hidden'])) continue;
            $sections .= self::section($s, $doc);
        }
        // If this page has no footer, append one from another page so every
        // page always shows a footer (new pages seeded without one).
        if (!self::pageHasType($page, 'footer')) {
            $sections .= self::chromeSection($doc, 'footer');
        }

        // The theme tokens go in a <style> block, NOT an inline style attribute:
        // font values contain double quotes ("DM Sans") which would otherwise
        // truncate a double-quoted style="" and drop every var after them
        // (fonts, spacing, radius). In a stylesheet the quotes are valid CSS.
        return "<!DOCTYPE html><html lang=\"" . self::esc($doc['site']['locale'] ?? 'en') . "\"><head>"
             . $head
             . "<style>.tf-site{" . $vars . "}" . self::baseCss() . "</style>"
             . "</head><body>"
             . "<main class=\"tf-site\">" . $sections . "</main>"
             . self::mobileBar($doc)
             . self::carouselScript()
             . self::cartScript()
             . self::animScript()
             . self::countScript()
             . "</body></html>";
    }

    private static function head(array $doc, array $page, ?string $fonts): string
    {
        $site = $doc['site'] ?? [];
        $seo  = $page['seo'] ?? [];
        $name = $site['name'] ?? 'Website';
        $title = trim((string)($seo['title'] ?? '')) !== ''
            ? $seo['title']
            : ($page['title'] ?? $name) . ' | ' . $name;

        $host = self::publicHost();
        $canonical = $seo['canonical'] ?? ('https://' . $host . ($page['slug'] === '/' ? '' : $page['slug']));
        $og = self::media($seo['ogImage'] ?? null);
        // Fallback: use the favicon as a social share image when no specific
        // OG image is set — better than no image at all on WhatsApp / Facebook.
        $favicon = self::media($site['favicon'] ?? null);
        if (!$og) $og = $favicon;

        $h  = '<meta charset="utf-8">';
        $h .= '<meta name="viewport" content="width=device-width, initial-scale=1">';
        $h .= '<title>' . self::esc($title) . '</title>';
        if (!empty($seo['description'])) $h .= '<meta name="description" content="' . self::esc($seo['description']) . '">';
        if (!empty($seo['keywords']) && is_array($seo['keywords'])) $h .= '<meta name="keywords" content="' . self::esc(implode(', ', $seo['keywords'])) . '">';
        $h .= '<meta name="robots" content="' . self::esc($seo['robots'] ?? 'index,follow') . '">';
        // Which site this page belongs to. set-domain.php reads this when
        // verifying a custom domain: fetching the customer's domain and finding
        // THIS slug is the only proof the DNS and the Cloudflare route really
        // reach us, rather than a parked page or somebody else's server.
        $h .= '<meta name="tapify-site" content="' . self::esc(self::$slug) . '">';
        $h .= '<link rel="canonical" href="' . self::esc($canonical) . '">';
        if ($favicon) $h .= '<link rel="icon" href="' . self::esc($favicon) . '">';
        $h .= '<link rel="manifest" href="/manifest.json">';

        // Open Graph / Twitter
        $h .= '<meta property="og:title" content="' . self::esc($title) . '">';
        if (!empty($seo['description'])) $h .= '<meta property="og:description" content="' . self::esc($seo['description']) . '">';
        $h .= '<meta property="og:type" content="website">';
        $h .= '<meta property="og:site_name" content="' . self::esc($name) . '">';
        $h .= '<meta property="og:url" content="' . self::esc($canonical) . '">';
        if ($og) {
            $h .= '<meta property="og:image" content="' . self::esc($og) . '">';
            $h .= '<meta property="og:image:alt" content="' . self::esc($title) . '">';
            $h .= '<meta name="twitter:card" content="summary_large_image">';
            $h .= '<meta name="twitter:image" content="' . self::esc($og) . '">';
        } else {
            $h .= '<meta name="twitter:card" content="summary">';
        }

        $h .= self::jsonLd($doc, $page);

        if ($fonts) $h .= '<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link rel="stylesheet" href="' . self::esc($fonts) . '">';
        return $h;
    }

    /* --------------------------------------------------------- theme */

    /* ------------------------------------------------- structured data */

    /**
     * LocalBusiness + WebSite structured data (JSON-LD), on every page.
     *
     * Everything here is already in `doc.business`, which the customer edits in
     * one place — this states it in the form search engines actually read. The
     * point is the NAP triple: this name, at this address, on this telephone
     * number, published on the business's own domain. That is the strongest
     * signal a site can give that a phone number belongs to a place, and it is
     * what ties the site to the right listing when the two disagree.
     *
     * Nothing is invented: every field is emitted only when the customer has
     * filled it in, so a half-filled business block produces a smaller node
     * rather than a wrong one.
     */
    private static function jsonLd(array $doc, array $page): string
    {
        $site = $doc['site'] ?? [];
        $biz  = $doc['business'] ?? [];
        $name = trim((string)($site['name'] ?? ''));
        if ($name === '') return '';

        $home    = 'https://' . self::publicHost();
        $locale  = (string)($site['locale'] ?? 'en-IN');
        $country = preg_match('/-([A-Z]{2})$/i', $locale, $m) ? strtoupper($m[1]) : 'IN';

        $types = self::schemaTypes((string)($site['industry'] ?? ''));
        $node  = [
            '@type' => count($types) === 1 ? $types[0] : $types,
            '@id'   => $home . '/#business',
            'name'  => $name,
            'url'   => $home,
        ];

        $phone = self::e164((string)($biz['phone'] ?? ''), $country);
        if ($phone !== '') $node['telephone'] = $phone;
        if (trim((string)($biz['email'] ?? '')) !== '') $node['email'] = trim((string)$biz['email']);

        $addr = self::postalAddress((string)($biz['address'] ?? ''), $country);
        if ($addr) $node['address'] = $addr;

        $map = trim((string)($biz['mapUrl'] ?? ''));
        if ($map !== '') $node['hasMap'] = $map;
        $geo = self::geoFromMapUrl($map);
        if ($geo) $node['geo'] = $geo;

        // The home page's own description doubles as the business description.
        foreach (($doc['pages'] ?? []) as $pg) {
            if (($pg['slug'] ?? '') === '/' && trim((string)($pg['seo']['description'] ?? '')) !== '') {
                $node['description'] = trim((string)$pg['seo']['description']);
                break;
            }
        }

        $logo = self::absUrl(self::siteLogo($doc), $home);
        $img  = $logo !== '' ? $logo : self::absUrl((string)(self::media($site['favicon'] ?? null) ?? ''), $home);
        if ($logo !== '') $node['logo'] = $logo;
        if ($img !== '')  $node['image'] = $img;

        $sameAs = [];
        foreach (($biz['social'] ?? []) as $url) {
            $url = trim((string)$url);
            if (preg_match('#^https?://#i', $url)) $sameAs[] = $url;
        }
        if ($sameAs) $node['sameAs'] = array_values(array_unique($sameAs));

        $hours = self::openingHours($biz);
        if ($hours) $node['openingHoursSpecification'] = $hours;

        // WhatsApp, when it is a different number from the one above.
        $wa = self::e164((string)($biz['whatsapp'] ?? ''), $country);
        if ($wa !== '' && $wa !== $phone) {
            $node['contactPoint'] = [
                '@type'       => 'ContactPoint',
                'contactType' => 'customer support',
                'telephone'   => $wa,
            ];
        }

        // A LocalBusiness node with neither a phone number nor an address claims
        // nothing — and the whole point here is the claim. When the customer has
        // not filled in the Business tab, say only what is true: the site exists.
        $hasNap = isset($node['telephone']) || isset($node['address']);

        $web = [
            '@type' => 'WebSite',
            '@id'   => $home . '/#website',
            'url'   => $home,
            'name'  => $name,
        ];
        if ($hasNap) $web['publisher'] = ['@id' => $home . '/#business'];
        // Sitelinks search box — only claimed when the site really has search.
        if (self::hasSearch($doc)) {
            $web['potentialAction'] = [
                '@type'       => 'SearchAction',
                'target'      => ['@type' => 'EntryPoint', 'urlTemplate' => $home . '/search?q={search_term_string}'],
                'query-input' => 'required name=search_term_string',
            ];
        }

        $json = json_encode(
            ['@context' => 'https://schema.org', '@graph' => $hasNap ? [$node, $web] : [$web]],
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_INVALID_UTF8_SUBSTITUTE
        );
        return $json === false ? '' : '<script type="application/ld+json">' . $json . '</script>';
    }

    /** True when any header has the product search box switched on. */
    private static function hasSearch(array $doc): bool
    {
        foreach (($doc['pages'] ?? []) as $pg) {
            foreach (($pg['sections'] ?? []) as $s) {
                if (($s['type'] ?? '') === 'header' && !empty($s['props']['showSearch'])) return true;
            }
        }
        return false;
    }

    /**
     * schema.org type(s) for an industry.
     *
     * Every entry is a LocalBusiness subtype, or is paired with LocalBusiness,
     * so the node always says "a real place you can phone and visit" — which is
     * the claim that matters here. Unknown industries fall back to the generic
     * LocalBusiness rather than guessing something more specific and wrong.
     */
    private static function schemaTypes(string $industry): array
    {
        $map = [
            'jewellery' => ['JewelryStore'], 'jewelry' => ['JewelryStore'],
            'hospital' => ['MedicalClinic'], 'clinic' => ['MedicalClinic'],
            'dental' => ['Dentist'], 'dentist' => ['Dentist'], 'doctor' => ['Physician'],
            'restaurant' => ['Restaurant'], 'cafe' => ['CafeOrCoffeeShop'], 'bakery' => ['Bakery'],
            'salon' => ['BeautySalon'], 'spa' => ['DaySpa'],
            'gym' => ['ExerciseGym'], 'fitness' => ['ExerciseGym'],
            'optical' => ['Optician'],
            'automotive' => ['AutomotiveBusiness'],
            'solar' => ['HomeAndConstructionBusiness'], 'architecture' => ['HomeAndConstructionBusiness'],
            'real-estate' => ['RealEstateAgent'],
            'travel' => ['TravelAgency'],
            'finance' => ['FinancialService'],
            'pet-shop' => ['PetStore'],
            'boutique' => ['ClothingStore'], 'clothing' => ['ClothingStore'],
            'flower-decoration' => ['Florist'], 'florist' => ['Florist'],
            'grocery' => ['GroceryStore'], 'store' => ['Store'], 'shop' => ['Store'],
            'coaching' => ['LocalBusiness', 'EducationalOrganization'],
            'music-band' => ['LocalBusiness', 'MusicGroup'],
            'portfolio' => ['ProfessionalService'], 'sales-portfolio' => ['ProfessionalService'],
        ];
        return $map[strtolower(trim($industry))] ?? ['LocalBusiness'];
    }

    /** A relative /path becomes absolute; anything already absolute is left alone. */
    private static function absUrl(string $url, string $home): string
    {
        if ($url === '') return '';
        if (preg_match('#^https?://#i', $url)) return $url;
        return $home . '/' . ltrim($url, '/');
    }

    /**
     * A phone number in the form search engines prefer (+<country><number>).
     *
     * The customer types it however they like — "086686 81115", "+91 86686
     * 81115" — and the displayed number on the page is untouched; this is only
     * for the structured data, where one canonical form is the whole point.
     */
    private static function e164(string $raw, string $country): string
    {
        $raw = trim($raw);
        if ($raw === '') return '';
        $digits = preg_replace('/\D+/', '', $raw);
        if ($digits === '') return '';
        if (strncmp($raw, '+', 1) === 0) return '+' . $digits;
        if ($country === 'IN') {
            $digits = ltrim($digits, '0');
            if (strlen($digits) === 10) return '+91' . $digits;
            if (strlen($digits) === 12 && strncmp($digits, '91', 2) === 0) return '+' . $digits;
        }
        return $raw;                       // leave anything unusual exactly as typed
    }

    /**
     * Split a one-line address into a PostalAddress.
     *
     * Deliberately conservative: it only lifts out a 6-digit PIN code and a
     * state it recognises, then treats the last remaining comma-separated piece
     * as the town. Whatever it cannot place stays in streetAddress, so a
     * misparse loses nothing — the full address is still there.
     */
    private static function postalAddress(string $raw, string $country): ?array
    {
        $raw = trim((string)preg_replace('/\s+/u', ' ', $raw));
        if ($raw === '') return null;

        $parts = array_values(array_filter(array_map('trim', explode(',', $raw)), fn($p) => $p !== ''));
        $states = ['Maharashtra', 'Madhya Pradesh', 'Gujarat', 'Karnataka', 'Telangana', 'Andhra Pradesh',
                   'Tamil Nadu', 'Kerala', 'Goa', 'Rajasthan', 'Punjab', 'Haryana', 'Delhi', 'Uttar Pradesh',
                   'Uttarakhand', 'Bihar', 'Jharkhand', 'West Bengal', 'Odisha', 'Chhattisgarh', 'Assam',
                   'Himachal Pradesh', 'Jammu and Kashmir', 'Chandigarh', 'Puducherry'];

        // A trailing "India" is the country, not the town — without this it became
        // addressLocality and the real city was buried in streetAddress.
        $countries = ['india' => 'IN', 'bharat' => 'IN'];
        foreach ($parts as $i => $p) {
            $key = strtolower(trim($p, " .,"));
            if (isset($countries[$key])) { $country = $countries[$key]; unset($parts[$i]); }
        }
        $parts = array_values($parts);

        $pin = '';
        foreach ($parts as $i => $p) {
            if ($pin === '' && preg_match('/\b(\d{6})\b/', $p, $m)) {
                $pin = $m[1];
                $parts[$i] = trim(str_replace($m[1], '', $p), " ,-");
            }
        }
        $region = '';
        foreach ($parts as $i => $p) {
            foreach ($states as $st) {
                if ($p !== '' && strcasecmp($p, $st) === 0) { $region = $st; unset($parts[$i]); break 2; }
            }
        }
        $parts = array_values(array_filter(array_map('trim', $parts), fn($p) => $p !== ''));

        $locality = $parts ? array_pop($parts) : '';
        $street   = implode(', ', $parts);

        $addr = ['@type' => 'PostalAddress'];
        if ($street !== '')   $addr['streetAddress'] = $street;
        if ($locality !== '') $addr['addressLocality'] = $locality;
        if ($region !== '')   $addr['addressRegion'] = $region;
        if ($pin !== '')      $addr['postalCode'] = $pin;
        // A one-line address with no commas ("Shop 4, Main Road") is a street, so
        // demote it — but only when nothing else was recognised. With a state or a
        // PIN alongside it, the leftover piece is the town ("Nagpur, Maharashtra").
        if (!isset($addr['streetAddress']) && isset($addr['addressLocality'])
            && !isset($addr['addressRegion']) && !isset($addr['postalCode'])) {
            $addr['streetAddress'] = $addr['addressLocality'];
            unset($addr['addressLocality']);
        }
        if (count($addr) === 1) return null;
        $addr['addressCountry'] = $country;
        return $addr;
    }

    /** Coordinates out of a Google Maps link, when it carries any. */
    private static function geoFromMapUrl(string $url): ?array
    {
        if ($url === '') return null;
        $u = rawurldecode($url);
        foreach (['/[?&](?:q|ll|sll|center|daddr)=(-?\d{1,3}\.\d+)\s*,\s*(-?\d{1,3}\.\d+)/',
                  '/@(-?\d{1,3}\.\d+),(-?\d{1,3}\.\d+)/',
                  '/!3d(-?\d{1,3}\.\d+)!4d(-?\d{1,3}\.\d+)/'] as $re) {
            if (preg_match($re, $u, $m)) {
                $lat = (float)$m[1]; $lng = (float)$m[2];
                if (abs($lat) <= 90 && abs($lng) <= 180 && ($lat !== 0.0 || $lng !== 0.0)) {
                    return ['@type' => 'GeoCoordinates', 'latitude' => $lat, 'longitude' => $lng];
                }
            }
        }
        return null;   // a short maps.app.goo.gl link has no coordinates in it
    }

    /** doc.business.hours -> openingHoursSpecification. */
    private static function openingHours(array $biz): array
    {
        $days = ['mon' => 'Monday', 'tue' => 'Tuesday', 'wed' => 'Wednesday', 'thu' => 'Thursday',
                 'fri' => 'Friday', 'sat' => 'Saturday', 'sun' => 'Sunday'];
        $out = [];
        foreach (($biz['hours'] ?? []) as $h) {
            if (!is_array($h)) continue;
            $day = $days[strtolower(trim((string)($h['day'] ?? '')))] ?? null;
            if ($day === null) continue;
            $dow = 'https://schema.org/' . $day;
            // Google reads opens == closes == 00:00 as "closed all day".
            if (!empty($h['closed'])) {
                $out[] = ['@type' => 'OpeningHoursSpecification', 'dayOfWeek' => $dow, 'opens' => '00:00', 'closes' => '00:00'];
                continue;
            }
            $o = self::time24((string)($h['open'] ?? ''));
            $c = self::time24((string)($h['close'] ?? ''));
            if ($o === null || $c === null) continue;
            $out[] = ['@type' => 'OpeningHoursSpecification', 'dayOfWeek' => $dow, 'opens' => $o, 'closes' => $c];
        }
        return $out;
    }

    /** "11:00 AM" / "8 PM" / "18:30" -> "11:00" / "20:00" / "18:30". */
    private static function time24(string $t): ?string
    {
        $t = trim($t);
        if ($t === '' || !preg_match('/^(\d{1,2})(?::(\d{2}))?\s*(am|pm)?\.?$/i', $t, $m)) return null;
        $h   = (int)$m[1];
        $min = $m[2] ?? '00';
        $ap  = strtolower($m[3] ?? '');
        if ($ap === 'pm' && $h < 12) $h += 12;
        if ($ap === 'am' && $h === 12) $h = 0;
        if ($h > 23 || (int)$min > 59) return null;
        return sprintf('%02d:%s', $h, $min);
    }

    private static function themeVars(array $t): string
    {
        $fallback = [
            'primary' => '#2563EB', 'secondary' => '#1D4ED8', 'accent' => '#F7941D',
            'bg' => '#FFFFFF', 'surface' => '#F5F7FB', 'text' => '#111827',
            'muted' => '#6B7280', 'border' => '#E5E7EB',
        ];
        $c = $fallback;
        foreach (($t['color'] ?? []) as $k => $v) {
            if (is_string($v) && trim($v) !== '') $c[$k] = $v;
        }

        $radiusMap    = ['none'=>'0px','sm'=>'6px','md'=>'12px','lg'=>'18px','xl'=>'26px','pill'=>'999px'];
        $spacingMap   = ['compact'=>'0.75','comfortable'=>'1','spacious'=>'1.35'];
        $containerMap = ['narrow'=>'900px','normal'=>'1200px','wide'=>'1440px','full'=>'100%'];

        $radius    = $radiusMap[$t['radius'] ?? 'md'] ?? '12px';
        $spacing   = $spacingMap[$t['spacing'] ?? 'comfortable'] ?? '1';
        $container = $containerMap[$t['container'] ?? 'normal'] ?? '1200px';

        $head = !empty($t['font']['heading']) ? '"' . $t['font']['heading'] . '", system-ui, sans-serif' : 'system-ui, sans-serif';
        $body = !empty($t['font']['body']) ? '"' . $t['font']['body'] . '", system-ui, sans-serif' : 'system-ui, sans-serif';

        $vars = [
            '--color-primary'      => $c['primary'],
            '--color-primary-dark' => self::shade($c['primary'], -0.18),
            '--color-primary-fg'   => self::readableOn($c['primary']),
            '--color-secondary'    => $c['secondary'],
            '--color-accent'       => $c['accent'],
            '--color-accent-fg'    => self::readableOn($c['accent']),
            '--color-bg'           => $c['bg'],
            '--color-surface'      => $c['surface'],
            '--color-text'         => $c['text'],
            '--color-muted'        => $c['muted'],
            '--color-border'       => $c['border'],
            '--font-heading'       => $head,
            '--font-body'          => $body,
            '--radius'             => $radius,
            '--space-scale'        => $spacing,
            '--container'          => $container,
        ];
        $out = '';
        foreach ($vars as $k => $v) $out .= $k . ':' . $v . ';';
        return $out;
    }

    private static function googleFonts(array $t): ?string
    {
        $fams = [];
        foreach ([$t['font']['heading'] ?? null, $t['font']['body'] ?? null] as $f) {
            $f = trim((string)$f);
            if ($f !== '' && stripos($f, 'system') !== 0) $fams[$f] = true;
        }
        if (!$fams) return null;
        $q = [];
        foreach (array_keys($fams) as $f) {
            $q[] = 'family=' . str_replace('%20', '+', rawurlencode($f)) . ':wght@400;500;600;700';
        }
        return 'https://fonts.googleapis.com/css2?' . implode('&', $q) . '&display=swap';
    }

    /** Darken a hex colour (matches theme.ts shade()). */
    private static function shade(string $hex, float $amt = -0.15): string
    {
        if (!preg_match('/^#([0-9a-f]{3}|[0-9a-f]{6})$/i', trim($hex), $m)) return $hex;
        $h = $m[1];
        if (strlen($h) === 3) $h = $h[0].$h[0].$h[1].$h[1].$h[2].$h[2];
        $num = hexdec($h);
        $r = max(0, min(255, (int)round((($num >> 16) & 255) * (1 + $amt))));
        $g = max(0, min(255, (int)round((($num >> 8) & 255) * (1 + $amt))));
        $b = max(0, min(255, (int)round(($num & 255) * (1 + $amt))));
        return sprintf('#%06x', ($r << 16) | ($g << 8) | $b);
    }

    /** Readable text colour (#111827 or #FFFFFF) for a background (matches theme.ts). */
    private static function readableOn(string $hex): string
    {
        if (!preg_match('/^#([0-9a-f]{3}|[0-9a-f]{6})$/i', trim($hex), $m)) return '#FFFFFF';
        $h = $m[1];
        if (strlen($h) === 3) $h = $h[0].$h[0].$h[1].$h[1].$h[2].$h[2];
        $num = hexdec($h);
        $r = ($num >> 16) & 255; $g = ($num >> 8) & 255; $b = $num & 255;
        $lum = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;
        return $lum > 0.6 ? '#111827' : '#FFFFFF';
    }

    /* --------------------------------------------------------- helpers */

    private static function esc($v): string
    {
        return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
    }

    /** Resolve "media:<id>" | URL | /path to a servable src. */
    private static function media($ref): ?string
    {
        if (!is_string($ref) || $ref === '') return null;
        if (preg_match('#^(https?://|/)#i', $ref)) return $ref;
        if (preg_match('/^media:(\d+)$/', $ref, $m)) {
            $base = defined('SITE_URL') ? SITE_URL : 'https://app.tapify.co.in';
            return $base . '/api/sites/media.php?id=' . $m[1];
        }
        return null;
    }

    private static function isDarkBg(array $s): bool
    {
        $bg = $s['style']['bg'] ?? 'default';
        return in_array($bg, ['primary', 'dark', 'image'], true);
    }

    /** The site's logo, from the first header or footer that carries one. */
    private static function siteLogo(array $doc): string
    {
        foreach (($doc['pages'] ?? []) as $pg) {
            foreach (($pg['sections'] ?? []) as $s) {
                if (in_array($s['type'] ?? '', ['header', 'footer'], true) && !empty($s['props']['logo'])) {
                    return self::media($s['props']['logo']) ?? '';
                }
            }
        }
        return '';
    }

    /** Background css [background, color] for a section bg token. */
    private static function bgCss(string $bg, ?string $img): array
    {
        switch ($bg) {
            case 'surface': return ['background:var(--color-surface);', 'color:var(--color-text);'];
            case 'primary': return ['background:var(--color-primary);', 'color:var(--color-primary-fg);'];
            case 'dark':    return ['background:#0F172A;', 'color:#F8FAFC;'];
            case 'image':   return ['', 'color:#fff;'];
            case 'none':    return ['', ''];
            default:        return ['background:var(--color-bg);', 'color:var(--color-text);'];
        }
    }

    /** object-fit/position for the "Image fit" option (avoids cropping faces). */
    /**
     * How a photo sits inside its frame — the section's visual crop.
     *
     * Stored either as a legacy string ("cover"/"top"/"contain") from before the
     * cropper existed, or as {fit,x,y,zoom} written by the builder's crop tool.
     * Zoom needs a transform, which paints outside the element box, so the paint
     * is clipped back to that box (following $radius) to keep it off the layout.
     */
    private static function imgFit($fit, string $radius = '0'): string
    {
        if (!is_array($fit)) {
            if ($fit === 'contain') return 'object-fit:contain;object-position:center;';
            if ($fit === 'top')     return 'object-fit:cover;object-position:top;';
            return 'object-fit:cover;object-position:center;';
        }
        if (($fit['fit'] ?? 'cover') === 'contain') {
            return 'object-fit:contain;object-position:center;';
        }
        $x = round(min(100, max(0, (float)($fit['x'] ?? 50))), 2);
        $y = round(min(100, max(0, (float)($fit['y'] ?? 50))), 2);
        $z = round(min(4, max(1, (float)($fit['zoom'] ?? 1))), 3);
        $css = 'object-fit:cover;object-position:' . $x . '% ' . $y . '%;';
        if ($z > 1.001) {
            $css .= 'transform:scale(' . $z . ');transform-origin:' . $x . '% ' . $y . '%;'
                  . 'clip-path:inset(0' . ($radius !== '0' ? ' round ' . $radius : '') . ');';
        }
        return $css;
    }

    /**
     * The same visual crop as imgFit(), for a photo painted as a CSS background.
     *
     * The hero is the one section that paints its picture as a background layer
     * rather than an <img>, so it never got the cropper the others have — its
     * photo was permanently `cover` + centred. That is invisible with a 16:9
     * video (which matches the hero box, so `cover` crops nothing) but obvious
     * with a phone photo: a 4:3 shot loses 25% of its height and a portrait one
     * over half, always off the top and bottom, so whatever was at the top of
     * the frame disappears under the header.
     *
     * background-size has no "cover, then zoom", so zoom is applied as a
     * transform on the layer instead. The section is overflow:hidden, so the
     * scaled paint is clipped back to the section box.
     */
    private static function bgFit($fit): string
    {
        if (!is_array($fit)) {
            if ($fit === 'contain') return 'background-size:contain;background-repeat:no-repeat;background-position:center;';
            if ($fit === 'top')     return 'background-size:cover;background-position:center top;';
            return '';                       // the .tf-bgimg default: cover + centre
        }
        if (($fit['fit'] ?? 'cover') === 'contain') {
            return 'background-size:contain;background-repeat:no-repeat;background-position:center;';
        }
        $x = round(min(100, max(0, (float)($fit['x'] ?? 50))), 2);
        $y = round(min(100, max(0, (float)($fit['y'] ?? 50))), 2);
        $z = round(min(4, max(1, (float)($fit['zoom'] ?? 1))), 3);
        $css = 'background-size:cover;background-position:' . $x . '% ' . $y . '%;';
        if ($z > 1.001) {
            $css .= 'transform:scale(' . $z . ');transform-origin:' . $x . '% ' . $y . '%;';
        }
        return $css;
    }

    /**
     * The shape of a card's image box.
     *
     * Cards used to be a fixed `height:176px`, which letterboxes anything tall —
     * and clothing photography is almost always portrait, so a garment shot
     * lost its top and bottom and showed a band of midriff. This lets the
     * section choose a shape that suits its pictures.
     *
     * 'auto' sets no box at all: the image keeps its own proportions and
     * nothing is ever cropped, at the cost of cards ending at different heights.
     */
    private static function ratioCss($r, string $fallback = 'height:176px;'): string
    {
        switch ((string)$r) {
            case 'auto':      return 'height:auto;';
            case 'square':    return 'aspect-ratio:1/1;height:auto;';
            case 'portrait':  return 'aspect-ratio:3/4;height:auto;';
            case 'tall':      return 'aspect-ratio:2/3;height:auto;';
            case 'wide':      return 'aspect-ratio:3/2;height:auto;';
            case 'landscape': return 'aspect-ratio:16/9;height:auto;';
            default:          return $fallback;
        }
    }

    /** The shared catalogue, keyed by product id. */
    private static function catalog(array $doc): array
    {
        static $memo = null, $forDoc = null;
        $key = spl_object_hash((object)['p' => $doc['catalog']['products'] ?? []]);
        if ($memo !== null && $forDoc === $key) return $memo;
        $out = [];
        foreach (($doc['catalog']['products'] ?? []) as $it) {
            if (is_array($it) && !empty($it['id'])) $out[(string)$it['id']] = $it;
        }
        $memo = $out; $forDoc = $key;
        return $out;
    }

    /**
     * The items a section should render.
     *
     * `itemRefs` points into the shared catalogue, so a product edited once
     * changes everywhere it appears — the home page row and its category page
     * are the same product, not two copies that drift apart.
     *
     * `items` still works and takes precedence when both are present, so every
     * document written before the catalogue existed renders unchanged. A ref
     * that does not resolve is dropped rather than rendered blank; the
     * validator rejects those at save time, so it only happens to a document
     * edited by hand.
     */
    private static function resolveItems(array $p, array $doc): array
    {
        $inline = (array)($p['items'] ?? []);
        $refs   = (array)($p['itemRefs'] ?? []);
        if (!$refs) return $inline;

        $cat = self::catalog($doc);
        $out = [];
        foreach ($refs as $ref) {
            if (is_string($ref) && isset($cat[$ref])) $out[] = $cat[$ref];
        }
        // Inline items sit after referenced ones, so a section can pull the
        // catalogue in and still add a one-off piece of its own.
        return array_merge($out, $inline);
    }

    /** A colour we are willing to drop straight into a style attribute. */
    private static function isColor($v): bool
    {
        return is_string($v) && preg_match('/^#[0-9a-f]{3,8}$/i', $v) === 1;
    }

    /** SectionShell: padding / bg / align / radius + bg-image + overlay + container. */
    /** @param string $backdrop full-bleed layer behind the content (e.g. a hero video) */
    /**
     * $extraClass (not an inline style) is how a section opts into layout the
     * stylesheet can still override at a breakpoint — inline styles beat media
     * queries, so anything that must respond to screen size has to be a class.
     */
    private static function shell(array $s, string $inner, string $extraClass = '', string $backdrop = ''): string
    {
        $style = $s['style'] ?? [];
        $padMap = ['none'=>0,'sm'=>28,'md'=>48,'lg'=>72,'xl'=>104];
        $pad = $padMap[$style['paddingY'] ?? 'lg'] ?? 72;

        $bg = $style['bg'] ?? 'default';
        $bgImg = ($bg === 'image') ? self::media($style['bgMedia'] ?? null) : null;
        if ($bg === 'image' && !$bgImg) $bg = 'primary';      // never invisible
        $overlay = isset($style['overlay']) ? (float)$style['overlay'] : ($bgImg ? 0.5 : 0);

        [$bgc, $color] = self::bgCss($bg, $bgImg);
        $align = in_array($style['align'] ?? '', ['center','right'], true) ? $style['align'] : 'left';
        $radius = !empty($style['radius']) ? ('border-radius:' . (['none'=>'0','sm'=>'6px','md'=>'12px','lg'=>'18px','xl'=>'26px'][$style['radius']] ?? '0') . ';') : '';

        // Per-section font colours. Emitted as custom properties so every heading
        // and paragraph inside the section picks them up, including the ones that
        // set their own colour inline.
        $vars = '';
        if (self::isColor($style['headingColor'] ?? null)) $vars .= '--tf-heading:' . $style['headingColor'] . ';';
        if (self::isColor($style['textColor'] ?? null))    $vars .= '--tf-text:' . $style['textColor'] . ';';

        // Padding rides on a custom property rather than a literal inline padding
        // so the stylesheet can scale it down on small screens (an inline
        // padding-top could never be overridden by a media query).
        $secStyle = '--tf-pad:' . $pad . 'px;'
                  . 'text-align:' . $align . ';' . $bgc . $color . $radius . $vars;

        $bgLayer = '';
        if ($bgImg) {
            $bgLayer = '<div aria-hidden="true" class="tf-bgimg" style="background-image:url(' . self::esc($bgImg) . ');'
                     . self::bgFit($style['bgFit'] ?? null) . '"></div>'
                     . '<div aria-hidden="true" class="tf-overlay" style="background:rgba(2,6,23,' . $overlay . ')"></div>';
        }

        $anim = (!empty($style['animation']) && $style['animation'] !== 'none')
            ? ' data-anim="' . self::esc($style['animation']) . '"' : '';

        return '<section id="' . self::esc($s['id'] ?? '') . '" class="tf-section tf-al-' . $align . ($extraClass ? ' ' . $extraClass : '') . '" style="' . $secStyle . '"' . $anim . '>'
             . $bgLayer
             . $backdrop
             . '<div class="tf-container tf-rel">' . $inner . '</div>'
             . '</section>';
    }

    private static function sectionHeader(?string $label, ?string $heading, ?string $sub, bool $light = false): string
    {
        if (!$label && !$heading && !$sub) return '';
        $h = '<div class="tf-head">';
        if ($label)   $h .= '<p class="tf-eyebrow"' . ($light ? ' style="color:var(--tf-text,rgba(255,255,255,.8))"' : '') . '>' . self::esc($label) . '</p>';
        if ($heading) $h .= '<h2 class="tf-h2">' . self::esc($heading) . '</h2>';
        if ($sub)     $h .= '<p class="tf-sub"' . ($light ? ' style="color:var(--tf-text,rgba(255,255,255,.85))"' : '') . '>' . self::esc($sub) . '</p>';
        return $h . '</div>';
    }

    /** CtaButton port. */
    private static function btn(?array $link, bool $onDark = false, string $fallback = 'primary'): string
    {
        if (empty($link['text']) || empty($link['href'])) return '';
        $variant = $link['style'] ?? $fallback ?: 'primary';
        switch ($variant) {
            case 'secondary': $st = 'background:var(--color-accent);color:var(--color-accent-fg);border-radius:var(--radius);'; break;
            case 'ghost': $st = $onDark
                ? 'background:rgba(255,255,255,.14);color:#fff;border:1px solid rgba(255,255,255,.45);border-radius:var(--radius);'
                : 'background:transparent;color:var(--color-text);border:1px solid var(--color-border);border-radius:var(--radius);'; break;
            case 'link': $st = 'padding:0;color:' . ($onDark ? '#fff' : 'var(--color-primary)') . ';text-decoration:underline;background:none;'; break;
            default: $st = $onDark
                ? 'background:var(--color-accent);color:var(--color-accent-fg);border-radius:var(--radius);'
                : 'background:var(--color-primary);color:var(--color-primary-fg);border-radius:var(--radius);';
        }
        $cls = $variant === 'link' ? 'tf-btn tf-btn-link' : 'tf-btn';
        $tgt = !empty($link['newTab']) ? ' target="_blank" rel="noopener noreferrer"' : '';
        return '<a class="' . $cls . '" href="' . self::esc($link['href']) . '"' . $tgt . ' style="' . $st . '">' . self::esc($link['text']) . '</a>';
    }

    private static function gridClass(string $variant): string
    {
        if ($variant === 'cards-2' || $variant === 'list') return 'tf-grid tf-c2';
        if ($variant === 'cards-4') return 'tf-grid tf-c4';
        return 'tf-grid tf-c3';
    }

    /** A real carousel — arrows, dots, autoplay, swipe (wired by the page script). */
    private static function carousel(array $slidesHtml, int $autoplay = 4000): string
    {
        if (!$slidesHtml) return '';
        $slides = '';
        foreach ($slidesHtml as $sl) $slides .= '<div class="tf-cslide">' . $sl . '</div>';
        $multi = count($slidesHtml) > 1;
        $arrows = $multi
            ? '<button type="button" class="tf-cprev" aria-label="Previous">&#8249;</button>'
              . '<button type="button" class="tf-cnext" aria-label="Next">&#8250;</button>'
            : '';
        $dots = $multi ? '<div class="tf-cdots"></div>' : '';
        return '<div class="tf-carousel" data-autoplay="' . (int)$autoplay . '">'
             . '<div class="tf-cviewport"><div class="tf-ctrack">' . $slides . '</div>' . $arrows . '</div>'
             . $dots
             . '</div>';
    }

    /** A marquee — cards scroll continuously in a loop. Pure CSS, pauses on hover. */
    private static function marquee(array $slidesHtml, int $secsPerSlide = 3): string
    {
        if (!$slidesHtml) return '';
        $one = '';
        foreach ($slidesHtml as $sl) $one .= '<div class="tf-mqslide">' . $sl . '</div>';
        $dur = max(10, count($slidesHtml) * $secsPerSlide);
        // The set is duplicated so translateX(-50%) loops seamlessly.
        return '<div class="tf-marquee">'
             . '<div class="tf-mqtrack" style="animation-duration:' . $dur . 's">' . $one . $one . '</div>'
             . '</div>';
    }

    /**
     * A thin strip of text scrolling sideways — offers, delivery notices, a
     * safety warning. Reads well directly under the hero.
     *
     * Same technique as marquee() above: the message set is duplicated so
     * translateX(-50%) loops with no visible seam. No JavaScript, and it stops
     * moving entirely for anyone who has asked for reduced motion — a strip of
     * text sliding past forever is exactly what that setting is for.
     */
    private static function secTicker(array $s, array $doc): string
    {
        $p = $s['props'] ?? [];
        $items = array_values(array_filter(
            (array)($p['items'] ?? []),
            fn($it) => trim((string)($it['text'] ?? '')) !== ''
        ));
        if (!$items) return '';

        $variant = $s['variant'] ?? 'dark';
        $sep = trim((string)($p['separator'] ?? '')) ?: '•';
        // Duration scales with how much text there is, so a long strip does not
        // race past and a short one does not crawl.
        $chars = 0;
        foreach ($items as $it) $chars += mb_strlen((string)$it['text']) + 6;
        $perChar = ['slow' => 0.28, 'normal' => 0.18, 'fast' => 0.11];
        $rate = $perChar[$p['speed'] ?? 'normal'] ?? 0.18;
        $dur = max(14, (int)round($chars * $rate));

        $one = '';
        foreach ($items as $it) {
            $ico = trim((string)($it['icon'] ?? ''));
            $txt = self::esc($it['text']);
            // An item may link somewhere — "WhatsApp us", "Book now". External
            // links open in a new tab; a /path stays on the site.
            $href = trim((string)($it['href'] ?? ''));
            if ($href !== '') {
                $ext = preg_match('#^(https?:)?//#i', $href);
                $txt = '<a class="tf-tklink" href="' . self::esc($href) . '"'
                     . ($ext ? ' target="_blank" rel="noopener noreferrer"' : '') . '>' . $txt . '</a>';
            }
            $one .= '<span class="tf-tkitem">'
                  . ($ico !== '' ? '<span class="tf-tkicon">' . self::esc($ico) . '</span>' : '')
                  . $txt
                  . '<span class="tf-tksep" aria-hidden="true">' . self::esc($sep) . '</span>'
                  . '</span>';
        }

        $bg = $variant === 'accent' ? 'var(--color-accent)'
            : ($variant === 'light' ? 'var(--color-surface)' : 'var(--color-primary)');
        $fg = $variant === 'light' ? 'var(--color-text)' : self::readableOn(
            $variant === 'accent' ? ($doc['theme']['color']['accent'] ?? '#000000')
                                  : ($doc['theme']['color']['primary'] ?? '#000000')
        );
        // A bar in a colour the theme does not have — a green booking strip on a
        // plum site — without repainting every accent on the page to get it.
        $custom = trim((string)($p['bgColor'] ?? ''));
        if (preg_match('/^#[0-9a-fA-F]{6}$/', $custom)) {
            $bg = $custom;
            $fg = self::readableOn($custom);
        }
        // Static: a fixed announcement bar ("Book your appointment now · WhatsApp")
        // rather than a scrolling strip. One set, no duplicate, no animation —
        // and nothing for a screen reader to hear twice.
        if (($p['speed'] ?? 'normal') === 'static') {
            return '<section class="tf-ticker tf-tkstatic" style="background:' . $bg . ';color:' . $fg . '">'
                 . '<div class="tf-tkset">' . $one . '</div></section>';
        }

        $rev = ($p['direction'] ?? 'left') === 'right' ? ' tf-tkrev' : '';
        $hov = ($p['pauseOnHover'] ?? true) !== false ? ' tf-tkpause' : '';

        // aria-hidden on the duplicate: a screen reader should hear the notices
        // once, not twice.
        return '<section class="tf-ticker' . $hov . '" style="background:' . $bg . ';color:' . $fg . '">'
             . '<div class="tf-tktrack' . $rev . '" style="animation-duration:' . $dur . 's">'
             . '<div class="tf-tkset">' . $one . '</div>'
             . '<div class="tf-tkset" aria-hidden="true">' . $one . '</div>'
             . '</div></section>';
    }

    /* --------------------------------------------------------- sections */

    private static function section(array $s, array $doc): string
    {
        switch ($s['type'] ?? '') {
            case 'ticker':       return self::secTicker($s, $doc);
            case 'header':       return self::secHeader($s, $doc);
            case 'hero':         return self::secHero($s, $doc);
            case 'about':        return self::secAbout($s, $doc);
            case 'services':     return self::secServices($s, $doc);
            case 'products':     return self::secProducts($s, $doc);
            case 'gallery':      return self::secGallery($s, $doc);
            case 'stats':        return self::secStats($s, $doc);
            case 'team':         return self::secTeam($s, $doc);
            case 'testimonials': return self::secTestimonials($s, $doc);
            case 'faq':          return self::secFaq($s, $doc);
            case 'cta':          return self::secCta($s, $doc);
            case 'contact':      return self::secContact($s, $doc);
            case 'footer':       return self::secFooter($s, $doc);
            case 'hours':        return self::secHours($s, $doc);
            case 'blog':         return self::secBlog($s, $doc);
            case 'appointment':  return self::secAppointment($s, $doc);
            case 'feedback':     return self::secFeedback($s, $doc);
            case 'embed':        return self::secEmbed($s, $doc);
            case 'share':        return self::secShare($s, $doc);
            case 'account':      return self::secAccount($s, $doc);
            case 'slideshow':    return self::secSlideshow($s, $doc);
            case 'categories':   return self::secCategories($s, $doc);
            case 'banners':      return self::secBanners($s, $doc);
            case 'features':     return self::secFeatures($s, $doc);
            case 'videos':       return self::secVideos($s, $doc);
            case 'calculators':  return self::secCalculators($s, $doc);
            case 'pillars':      return self::secPillars($s, $doc);
            case 'quiz':         return self::secQuiz($s, $doc);
            case 'social':       return self::secSocial($s, $doc);
            case 'steps':        return self::secSteps($s, $doc);
            default:             return '';
        }
    }

    private static function secHeader(array $s, array $doc): string
    {
        $p = $s['props'] ?? [];
        $variant = $s['variant'] ?? 'left';
        $dark = self::isDarkBg($s);
        $logo = self::media($p['logo'] ?? null);
        $logoPx = ['small'=>28,'medium'=>36,'large'=>48,'extra-large'=>64][$p['logoSize'] ?? 'medium'] ?? 36;
        $brandInner = $logo
            ? '<img src="' . self::esc($logo) . '" alt="' . self::esc($doc['site']['name'] ?? '') . '" style="height:' . $logoPx . 'px;width:auto;object-fit:contain;display:block">'
            : '<span style="font-size:18px;font-weight:700;font-family:var(--font-heading)">' . self::esc($doc['site']['name'] ?? '') . '</span>';
        // The logo always links back to the home page.
        $brand = '<a href="/" aria-label="' . self::esc($doc['site']['name'] ?? '') . ' home" style="display:inline-flex;align-items:center;color:inherit;text-decoration:none">' . $brandInner . '</a>';

        // Auto-menu from visible pages when no custom links.
        $custom = array_values(array_filter($p['links'] ?? [], fn($l) => !empty($l['text']) && !empty($l['href'])));
        $items = $custom;
        if (!$items) {
            foreach (($doc['pages'] ?? []) as $pg) {
                if (($pg['visible'] ?? true) === false) continue;
                $items[] = ['text' => $pg['title'] ?? '', 'href' => $pg['slug'] ?? '/'];
            }
        }
        /**
         * Nav links, with an optional dropdown under any of them.
         *
         * A shop with sixteen categories cannot fit them in a bar capped at
         * eight links, so a link may carry `children`. The parent stays a real
         * link — it still navigates on tap, which matters on a phone where
         * there is no hover — and the children appear beneath it: on hover or
         * keyboard focus on a desktop, and always expanded inside the burger
         * menu, where a hover-only dropdown would be unreachable.
         */
        $navLinks = function (bool $mobile) use ($items) {
            $out = '';
            foreach ($items as $l) {
                $kids = array_values(array_filter(
                    (array)($l['children'] ?? []),
                    fn($c) => trim((string)($c['text'] ?? '')) !== '' && trim((string)($c['href'] ?? '')) !== ''
                ));
                $a = '<a href="' . self::esc($l['href'] ?? '#') . '">' . self::esc($l['text'] ?? '') . '</a>';
                if (!$kids) { $out .= $a; continue; }

                $sub = '';
                foreach ($kids as $c) {
                    $sub .= '<a href="' . self::esc($c['href']) . '">' . self::esc($c['text']) . '</a>';
                }
                if ($mobile) {
                    // Tap to open, and closed until then — twelve categories
                    // listed under every parent turns the burger into a wall of
                    // links you have to scroll past to reach Contact.
                    //
                    // <details> rather than a checkbox hack or JS: it is a real
                    // disclosure widget, so it is keyboard- and screen-reader-
                    // operable for free, and it degrades to "open" if CSS fails.
                    //
                    // The parent becomes the toggle, NOT a link. A <summary> that
                    // also navigates is a coin-flip on touch — you cannot tell
                    // whether a tap will open the list or leave the page. The
                    // parent's own destination is kept as the first row instead.
                    $out .= '<details class="tf-mdrop"><summary>'
                          . self::esc($l['text'] ?? '')
                          . '<span class="tf-mdrop-caret" aria-hidden="true">&#9662;</span></summary>'
                          . '<div class="tf-mdrop-list">'
                          . '<a href="' . self::esc($l['href'] ?? '#') . '">All ' . self::esc($l['text'] ?? '') . '</a>'
                          . $sub . '</div></details>';
                } else {
                    $out .= '<div class="tf-drop">'
                          . '<a href="' . self::esc($l['href'] ?? '#') . '" aria-haspopup="true">'
                          . self::esc($l['text'] ?? '')
                          . '<span class="tf-drop-caret" aria-hidden="true">&#9662;</span></a>'
                          . '<div class="tf-drop-menu">' . $sub . '</div></div>';
                }
            }
            return $out;
        };
        $links  = $navLinks(false);
        $mlinks = $navLinks(true);

        $cta = self::btn($p['cta'] ?? null, $dark);
        $toggleId = 'nav-' . self::esc($s['id'] ?? 'h');

        [$bgc, $color] = self::bgCss($s['style']['bg'] ?? 'default', null);
        $sticky = ($p['sticky'] ?? true) !== false;

        // Optional cart icon button.
        $cartIcon = '';
        if (!empty($p['showCart'])) {
            $cartIcon = '<a href="' . self::esc($p['cartHref'] ?? '/cart') . '" aria-label="Cart" style="position:relative;display:inline-flex;align-items:center;justify-content:center;padding:8px;border-radius:6px;color:inherit;text-decoration:none;border:1px solid rgba(120,120,120,.28)">'
                  . '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>'
                  // Filled in by cartScript() and hidden while the cart is empty.
                  . '<span data-tf-cart-count style="display:none;position:absolute;top:-7px;right:-7px;min-width:19px;height:19px;padding:0 5px;'
                  . 'border-radius:999px;background:var(--color-primary);color:var(--color-primary-fg);font-size:11px;font-weight:700;line-height:19px;text-align:center">0</span>'
                  . '</a>';
        }

        // Commerce group: when the account system is on, the cart is gated behind
        // login — a "Login / Signup" button shows when signed out, and the cart +
        // account icon appear once signed in (toggled by the auth script from the
        // customer token). When accounts are off, the cart shows ungated.
        if (!empty($p['showAccount'])) {
            $accHref     = self::esc($p['accountHref'] ?? '/account');
            $accountIcon = '<a href="' . $accHref . '" aria-label="My account" style="display:inline-flex;align-items:center;justify-content:center;padding:8px;border-radius:6px;color:inherit;text-decoration:none;border:1px solid rgba(120,120,120,.28)">'
                . '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg></a>';
            $loginBtn    = '<a href="' . $accHref . '" style="display:inline-flex;align-items:center;gap:6px;padding:9px 16px;border-radius:var(--radius);font-size:14px;font-weight:600;color:var(--color-primary-fg);background:var(--color-primary);text-decoration:none">'
                . '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg> Login / Signup</a>';
            $cart = '<span data-tf-auth="out" style="display:inline-flex">' . $loginBtn . '</span>'
                  . '<span data-tf-auth="in" style="display:none;align-items:center;gap:12px">' . $accountIcon . $cartIcon . '</span>';
        } else {
            $cart = $cartIcon;
        }
        $burger    = '<label for="' . $toggleId . '" class="tf-burger" aria-label="Menu">&#9776;</label>';
        $navDesk   = '<div class="tf-nav tf-nav-desktop">' . $links . '</div>';
        $ctaEl     = $cta ?: '';

        // Product search (optional). A real form submitting to the built-in /search
        // page, so it works with JavaScript off. The full box needs the "left"
        // layout, which moves the menu onto a row of its own to make room — the
        // online-store arrangement. In the other layouts, and on every phone, it
        // is an icon that opens /search; the burger menu carries a box too.
        $searchBox = $searchIcon = $searchM = '';
        if (!empty($p['showSearch'])) {
            $ph  = self::esc(trim((string)($p['searchPlaceholder'] ?? '')) ?: 'Search products');
            $ico = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"></circle><path d="m20 20-3.5-3.5"></path></svg>';
            $field = '<input type="search" name="q" placeholder="' . $ph . '" aria-label="' . $ph . '"><button type="submit" aria-label="Search">' . $ico . '</button>';
            $searchBox  = $variant === 'left' ? '<form class="tf-hsearch" action="/search" method="get" role="search">' . $field . '</form>' : '';
            $searchM    = '<form class="tf-msearch" action="/search" method="get" role="search">' . $field . '</form>';
            $searchIcon = '<a class="tf-hsearch-ic' . ($variant === 'left' ? '' : ' tf-always') . '" href="/search" aria-label="Search">' . $ico . '</a>';
        }

        if ($variant === 'nav-center') {
            // Logo left · menu centered · button/cart right.
            $bar = '<div class="tf-header-bar" style="justify-content:space-between">'
                 . '<div style="flex-shrink:0">' . $brand . '</div>'
                 . '<div class="tf-nav tf-nav-desktop" style="flex:1;justify-content:center">' . $links . '</div>'
                 . '<div style="display:flex;align-items:center;gap:12px">' . $ctaEl . $cart . $searchIcon . $burger . '</div>'
                 . '</div>';
        } elseif ($variant === 'center') {
            // Logo centered on top · menu centered below.
            $bar = '<div class="tf-header-bar" style="flex-direction:column;gap:8px">'
                 . '<div class="tf-center-top">' . $brand
                 .   '<div class="tf-mobile-only" style="align-items:center;gap:12px">' . $searchIcon . $cart . $burger . '</div>'
                 . '</div>'
                 . '<div class="tf-nav tf-nav-desktop" style="width:100%;justify-content:center">' . $links . $ctaEl . $cart . $searchIcon . '</div>'
                 . '</div>';
        } elseif ($variant === 'split') {
            // Menu left · logo centered · button/cart right.
            $bar = '<div class="tf-header-bar" style="justify-content:space-between;position:relative">'
                 . '<div style="display:flex;align-items:center;gap:16px">' . $navDesk . $burger . '</div>'
                 . '<div class="tf-brand-center">' . $brand . '</div>'
                 . '<div style="display:flex;align-items:center;gap:12px">' . $ctaEl . $cart . $searchIcon . '</div>'
                 . '</div>';
        } elseif ($searchBox !== '') {
            // "left" with search: logo · search box · button/cart, menu row below.
            $bar = '<div class="tf-header-bar">' . $brand . $searchBox
                 . '<div style="display:flex;align-items:center;gap:14px">' . $ctaEl . $cart . $searchIcon . $burger . '</div>'
                 . '</div>'
                 . '<div class="tf-hnav-row">' . $navDesk . '</div>';
        } else {
            // "left" (default): logo left · menu + button right.
            $bar = '<div class="tf-header-bar">' . $brand
                 . '<div style="display:flex;align-items:center;gap:20px">' . $navDesk . $ctaEl . $cart . $burger . '</div>'
                 . '</div>';
        }

        $mnav = '<nav class="tf-nav tf-mnav">' . $searchM . $mlinks . ($cta ? '<div style="margin-top:8px">' . $cta . '</div>' : '') . '</nav>';

        // Show the cart/account vs the login button based on the customer token.
        $authJs = !empty($p['showAccount'])
            ? '<script>(function(){var t;try{t=localStorage.getItem("tf_customer_' . self::esc(self::$slug) . '");}catch(e){}var yes=!!t;'
              . 'document.querySelectorAll("[data-tf-auth=in]").forEach(function(el){el.style.display=yes?"inline-flex":"none";});'
              . 'document.querySelectorAll("[data-tf-auth=out]").forEach(function(el){el.style.display=yes?"none":"inline-flex";});})();</script>'
            : '';

        return '<header class="tf-header" style="' . $bgc . $color . ($sticky ? '' : 'position:static;') . '">'
             . '<input type="checkbox" id="' . $toggleId . '" class="tf-navtoggle" hidden>'
             . '<div class="tf-container">' . $bar . '</div>'
             . '<div class="tf-container">' . $mnav . '</div>'
             . '</header>' . $authJs;
    }

    private static function secHero(array $s, array $doc): string
    {
        $p = $s['props'] ?? [];
        $variant = $s['variant'] ?? 'centered-bg';
        $img = self::media($p['image'] ?? null);
        $biz = $doc['business'] ?? [];

        // A video (uploaded file, or a pasted .mp4 / YouTube / Vimeo link) wins
        // over the image. It plays muted + looped as the hero backdrop.
        $upl = self::media($p['video'] ?? null);
        $lnk = trim((string)($p['videoUrl'] ?? ''));
        $embed = null;
        if (!$upl && $lnk !== '') {
            if (preg_match('#(?:youtube\.com/(?:watch\?v=|embed/|shorts/)|youtu\.be/)([\w-]{11})#i', $lnk, $m)) {
                $embed = 'https://www.youtube.com/embed/' . $m[1] . '?autoplay=1&mute=1&loop=1&playlist=' . $m[1] . '&controls=0&playsinline=1';
            } elseif (preg_match('#vimeo\.com/(\d+)#i', $lnk, $m)) {
                $embed = 'https://player.vimeo.com/video/' . $m[1] . '?autoplay=1&muted=1&loop=1&background=1';
            }
        }
        $fileVid = $upl ?: (($lnk !== '' && !$embed) ? $lnk : null);
        $hasVid = (bool)($fileVid || $embed);
        if ($hasVid && $variant !== 'split') {
            // The backdrop is dark, so force light-on-dark treatment.
            $s['style'] = array_merge($s['style'] ?? [], ['bg' => 'dark']);
        }

        // Does anything get written OVER the picture? This decides the treatment
        // for the uncropped hero below, and it has to be settled HERE because
        // $onDark is what styles the buttons a few lines down.
        $heroCopy = trim((string)($p['heading'] ?? '')) !== ''
                 || trim((string)($p['sub'] ?? '')) !== ''
                 || trim((string)($p['badge'] ?? '')) !== ''
                 || !empty($p['ctaPrimary']['text']) || !empty($p['ctaSecondary']['text'])
                 || (!empty($p['showWhatsapp']) && !empty($biz['whatsapp']))
                 || (!empty($p['showCall']) && !empty($biz['phone']));
        $hr0 = (string)($p['imageRatio'] ?? '');
        // A hero with no copy at all IS the picture — there is nothing the crop
        // could be protecting, so cropping it is never what was wanted. Left on
        // the default shape, such a hero shows the whole picture. An explicit
        // choice is still obeyed: someone who picked Square meant Square.
        $autoShape = ($variant === 'centered-bg' && !$hasVid && $img
                      && ($hr0 === 'auto' || (!$heroCopy && ($hr0 === '' || $hr0 === 'default'))));
        if ($autoShape && $heroCopy) {
            // Copy sits on the photo, so it needs the same light-on-dark
            // treatment the background path gets.
            $s['style'] = array_merge($s['style'] ?? [], ['bg' => 'dark']);
        }

        $onDark = self::isDarkBg($s);
        // A centered-bg hero paints its Image as the background under a dark
        // overlay, but that is only decided further down — too late for the
        // buttons, which were styled for a LIGHT page: a ghost button came out as
        // dark text on the darkened photo, i.e. invisible.
        if ($variant === 'centered-bg' && !$hasVid && $img && !$autoShape) $onDark = true;
        // Full-viewport hero, like a landing page. A CLASS, not an inline style,
        // so the mobile breakpoint can shorten it (see .tf-full in baseCss).
        $fh = !empty($p['fullHeight']) ? 'tf-full' : '';

        $body = '';
        if (!empty($p['badge'])) $body .= '<span class="tf-badge">' . self::esc($p['badge']) . '</span>';
        // No heading, no <h1>. A picture-only hero (a ready-made banner that already
        // carries the name) used to ship an EMPTY h1 — a page whose only top-level
        // heading is blank, which screen readers announce and search engines read.
        if (trim((string)($p['heading'] ?? '')) !== '') {
            // A sentence-length headline at 60px wraps to five or six lines and pushes
            // the buttons below the fold, so long headlines step down a size.
            $len = mb_strlen((string)$p['heading']);
            $size = $len > 90 ? ' tf-h1-xl' : ($len > 50 ? ' tf-h1-l' : '');
            $body .= '<h1 class="tf-h1' . $size . '">' . self::esc($p['heading']) . '</h1>';
        }
        if (!empty($p['sub'])) $body .= '<p class="tf-lead">' . self::esc($p['sub']) . '</p>';

        $btns = self::btn($p['ctaPrimary'] ?? null, $onDark) . self::btn($p['ctaSecondary'] ?? null, $onDark, 'ghost');
        if (!empty($p['showWhatsapp']) && !empty($biz['whatsapp'])) $btns .= self::btn(['text'=>'WhatsApp','href'=>'https://wa.me/' . preg_replace('/\D/', '', $biz['whatsapp']),'newTab'=>true,'style'=>'ghost'], $onDark);
        if (!empty($p['showCall']) && !empty($biz['phone'])) $btns .= self::btn(['text'=>'Call now','href'=>'tel:' . $biz['phone'],'style'=>'ghost'], $onDark);
        $body .= '<div class="tf-btns">' . $btns . '</div>';

        if ($variant === 'split' && ($hasVid || $img)) {
            if ($hasVid) {
                $media = '<div style="width:100%;aspect-ratio:16/9;overflow:hidden;border-radius:var(--radius)">'
                       . ($fileVid
                          ? '<video src="' . self::esc($fileVid) . '" autoplay muted loop playsinline controls style="width:100%;height:100%;object-fit:cover"></video>'
                          : '<iframe src="' . self::esc($embed) . '" allow="autoplay; encrypted-media; picture-in-picture" style="width:100%;height:100%;border:0"></iframe>')
                       . '</div>';
            } else {
                $media = '<img src="' . self::esc($img) . '" alt="' . self::esc($p['heading'] ?? '') . '" style="width:100%;border-radius:var(--radius);max-height:460px;' . self::imgFit($p['imageFit'] ?? null, 'var(--radius)') . '">';
            }
            $inner = '<div class="tf-two" style="text-align:left"><div>' . $body . '</div>' . $media . '</div>';
            return self::shell($s, $inner, $fh);
        }

        // Video backdrop wins; otherwise the Image field IS the background.
        $backdrop = '';
        if ($hasVid) {
            $ov = isset($s['style']['overlay']) ? (float)$s['style']['overlay'] : 0.55;
            if ($fileVid) {
                // A real <video> can object-fit:cover its box at any size — already
                // responsive on mobile, short section or not.
                $media = '<video src="' . self::esc($fileVid) . '" autoplay muted loop playsinline style="width:100%;height:100%;object-fit:cover"></video>';
            } else {
                // <iframe> content isn't a replaced element, so object-fit doesn't
                // work on it. This measures the ACTUAL backdrop box (any height —
                // not just a full-viewport hero) and sizes the embed in pixels to
                // cover it, keyed to 16:9. Fixes the old width:177.78vh hack, which
                // sized off the viewport instead of the section and broke badly on
                // phones (short/non-fullHeight heroes, dynamic browser-chrome vh).
                // Counter, not just the section id: hero allows 2 per page and a
                // hand-authored doc may leave ids blank, which would collide.
                static $hvSeq = 0;
                $hid = 'hv' . substr(md5(($s['id'] ?? '') . 'v'), 0, 8) . (++$hvSeq);
                $media = '<iframe id="' . $hid . '" src="' . self::esc($embed) . '" allow="autoplay; encrypted-media; picture-in-picture" style="position:absolute;left:50%;top:50%;transform:translate(-50%,-50%);border:0"></iframe>'
                    . '<script>(function(){var f=document.getElementById(' . json_encode($hid) . ');if(!f)return;var w=f.parentElement;'
                    . 'function fit(){var ww=w.clientWidth,wh=w.clientHeight,ar=16/9,fw,fh;if(ww/wh>ar){fw=ww*1.1;fh=fw/ar;}else{fh=wh*1.1;fw=fh*ar;}f.style.width=fw+"px";f.style.height=fh+"px";}'
                    . 'fit();window.addEventListener("resize",fit);if(window.ResizeObserver)new ResizeObserver(fit).observe(w);})();</script>';
            }
            $backdrop = '<div aria-hidden="true" style="position:absolute;inset:0;overflow:hidden">'
                      . $media
                      . '<div style="position:absolute;inset:0;background:rgba(2,6,23,' . $ov . ')"></div></div>';
        } elseif ($variant === 'centered-bg' && !empty($p['image'])) {
            // A PORTRAIT hero photo is the common case for clothing, and it is
            // the worst fit for a full-height hero on a phone: background-size
            // cover crops whatever does not match the box, so a tall photo in a
            // tall-but-narrower box loses its sides and the garment goes with
            // them. `imageRatio` lets the hero take the picture's own shape on
            // small screens instead of a fixed slice of the viewport.
            $hr = $hr0;

            if ($autoShape) {
                // "Show the whole picture." Every other shape still crops —
                // they only change WHAT gets cropped — because a CSS background
                // can cover its box or letterbox inside it, and it cannot make
                // the box match a picture whose size the server does not know.
                //
                // So `auto` is not a background at all: the photo is a real
                // <img> in the flow, and the section is a one-cell GRID with the
                // copy stacked in the same cell. The row is as tall as the
                // taller of the two, which means the picture is never cropped at
                // any width AND the copy can never overflow it. This is the one
                // shape a client can safely pick for a ready-made banner or
                // poster with text baked into it.
                // A bare poster is shown clean — tinting it would dull artwork
                // nobody is reading text off. A hero that does carry a headline
                // keeps the dark scrim, or the copy lands on whatever colour the
                // photo happens to be underneath it.
                $ov = $heroCopy ? (isset($s['style']['overlay']) ? (float)$s['style']['overlay'] : 0.45) : 0.0;

                $backdrop = '<img class="tf-hero-img" src="' . self::esc($img) . '" alt="' . self::esc($p['heading'] ?? '') . '">'
                          . ($ov > 0 ? '<div aria-hidden="true" class="tf-hero-scrim" style="background:rgba(2,6,23,' . $ov . ')"></div>' : '');
                $fh = trim($fh . ' tf-hero-auto');
            } else {
                $s['style'] = array_merge($s['style'] ?? [], [
                    'bg'      => 'image',
                    'bgMedia' => $p['image'],
                    'bgFit'   => $p['imageFit'] ?? null,
                    'overlay' => $s['style']['overlay'] ?? 0.55,
                ]);
                if ($hr !== '' && $hr !== 'default') {
                    $fh = trim($fh . ' tf-hero-r-' . preg_replace('/[^a-z]/', '', $hr));
                }
            }
        }
        return self::shell($s, '<div class="tf-hero-wrap">' . $body . '</div>', $fh, $backdrop);
    }

    private static function secAbout(array $s, array $doc): string
    {
        $p = $s['props'] ?? [];
        $variant = $s['variant'] ?? 'image-left';
        $img = self::media($p['image'] ?? null);
        $hasImage = $variant !== 'text-only' && $img;

        $text = '<div>' . self::sectionHeader($p['label'] ?? null, $p['heading'] ?? null, null);
        if (!empty($p['body'])) $text .= '<p class="tf-body" style="white-space:pre-line;color:var(--tf-text,var(--color-muted))">' . self::esc($p['body']) . '</p>';
        if (!empty($p['showPillars']) && !empty($p['pillars'])) {
            $text .= '<div class="tf-grid tf-c2" style="margin-top:32px">';
            foreach ($p['pillars'] as $pl) {
                $text .= '<div class="tf-card" style="padding:20px"><h3 style="font-family:var(--font-heading);font-size:16px;font-weight:600">' . self::esc($pl['title'] ?? '') . '</h3>'
                       . (!empty($pl['text']) ? '<p style="margin-top:8px;font-size:14px;color:var(--tf-text,var(--color-muted))">' . self::esc($pl['text']) . '</p>' : '') . '</div>';
            }
            $text .= '</div>';
        }
        $text .= '</div>';

        if (!$hasImage) return self::shell($s, '<div style="max-width:768px;margin:0 auto">' . $text . '</div>');

        $image = '<img src="' . self::esc($img) . '" alt="' . self::esc($p['heading'] ?? 'About') . '" loading="lazy" style="width:100%;border-radius:var(--radius);max-height:460px;' . self::imgFit($p['imageFit'] ?? null, 'var(--radius)') . '">';
        $cols = $variant === 'image-left' ? ($image . $text) : ($text . $image);
        return self::shell($s, '<div class="tf-two" style="text-align:left">' . $cols . '</div>');
    }

    private static function secServices(array $s, array $doc): string
    {
        $p = $s['props'] ?? [];
        $items = self::resolveItems($p, $doc);
        if (!$items) return '';
        // WhatsApp-order mode: no detail pages, the card links straight to a chat.
        $waOrder = self::isWhatsappOrder($p);
        $biz     = $doc['business'] ?? [];
        $onDark  = self::isDarkBg($s);
        $variant = $s['variant'] ?? 'cards-3';
        $showImages = $variant !== 'list';

        $cards = [];
        foreach ($items as $it) {
            $img = self::media($it['image'] ?? null);
            // An item with a full description has its own product page. The photo
            // and the title link straight to it — a separate "View details" button
            // is one extra thing to notice for something the card already implies.
            $href = (!$waOrder && self::hasDetailPage($it))
                ? '/service/' . self::itemSlug($it, 'service')
                : '';
            $open = $href !== '' ? '<a href="' . self::esc($href) . '" style="color:inherit;text-decoration:none;display:block">' : '';
            $shut = $href !== '' ? '</a>' : '';

            $c = '<div class="tf-card">';
            if ($showImages && $img) {
                $c .= $open . '<img src="' . self::esc($img) . '" alt="' . self::esc($it['title'] ?? '') . '" loading="lazy" style="width:100%;' . self::ratioCss($p['imageRatio'] ?? null) . self::imgFit($p['imageFit'] ?? null) . '">' . $shut;
            }
            $c .= '<div style="padding:20px">';
            $c .= $open . '<h3 style="font-family:var(--font-heading);font-size:18px;font-weight:600">' . self::esc($it['title'] ?? '') . '</h3>' . $shut;
            if (!empty($it['meta'])) $c .= '<p style="margin-top:4px;font-size:12px;font-weight:600;color:var(--color-accent)">' . self::esc($it['meta']) . '</p>';
            if (!empty($it['price'])) $c .= '<p style="margin-top:6px;font-size:16px;font-weight:700;color:var(--color-primary)">' . self::esc($it['price']) . '</p>';
            if (!empty($it['desc'])) $c .= '<p style="margin-top:8px;font-size:14px;color:var(--tf-text,var(--color-muted))">' . self::esc($it['desc']) . '</p>';
            // Items without a description have no page to open, so they keep
            // whatever custom button the customer configured.
            if ($waOrder) {
                $c .= self::waOrderButton($it, $biz, $p, $onDark);
            }
            if (!$waOrder && $href === '' && !empty($it['cta']['text'])) {
                $l = $it['cta']; $l['style'] = $l['style'] ?? 'link'; $c .= '<div style="margin-top:16px">' . self::btn($l) . '</div>';
            }
            $c .= '</div></div>';
            $cards[] = $c;
        }
        $body = $variant === 'marquee' ? self::marquee($cards)
            : ($variant === 'carousel' ? self::carousel($cards)
            : '<div class="' . self::gridClass($variant) . '" style="text-align:left">' . implode('', $cards) . '</div>');
        $inner = self::sectionHeader($p['label'] ?? null, $p['heading'] ?? null, $p['sub'] ?? null) . $body;
        return self::shell($s, $inner);
    }

    /**
     * Products grid — same card layout as Services, but each item can carry
     * nested variant attributes (Colour, Size, ...) resolved on its own detail
     * page at /product/<slug>. The card itself only needs the starting price.
     */
    private static function secProducts(array $s, array $doc): string
    {
        $p = $s['props'] ?? [];
        $items = self::resolveItems($p, $doc);
        if (!$items) return '';
        // WhatsApp-order mode: no detail pages, the card links straight to a chat.
        $waOrder = self::isWhatsappOrder($p);
        $biz     = $doc['business'] ?? [];
        $onDark  = self::isDarkBg($s);
        $variant = $s['variant'] ?? 'cards-3';
        $showImages = $variant !== 'list';
        $shop = ($p['cardStyle'] ?? 'default') === 'shop';

        $cards = [];
        foreach ($items as $it) {
            $img = self::media($it['image'] ?? null);
            $href = (!$waOrder && self::hasDetailPage($it))
                ? '/product/' . self::itemSlug($it, 'product')
                : '';
            $open = $href !== '' ? '<a href="' . self::esc($href) . '" style="color:inherit;text-decoration:none;display:block">' : '';
            $shut = $href !== '' ? '</a>' : '';
            if ($shop) {
                $cards[] = self::shopCard($it, $href, $p, $biz, $waOrder);
                continue;
            }

            $c = '<div class="tf-card">';
            if ($showImages && $img) {
                $c .= $open . '<img src="' . self::esc($img) . '" alt="' . self::esc($it['title'] ?? '') . '" loading="lazy" style="width:100%;' . self::ratioCss($p['imageRatio'] ?? null) . self::imgFit($p['imageFit'] ?? null) . '">' . $shut;
            }
            $c .= '<div style="padding:20px">';
            $c .= $open . '<h3 style="font-family:var(--font-heading);font-size:18px;font-weight:600">' . self::esc($it['title'] ?? '') . '</h3>' . $shut;
            if (!empty($it['meta'])) $c .= '<p style="margin-top:4px;font-size:12px;font-weight:600;color:var(--color-accent)">' . self::esc($it['meta']) . '</p>';
            if (!empty($it['price']) || !empty($it['mrp'])) {
                [$sell, $mrp, $off] = self::priceBits($it);
                $c .= '<p style="margin-top:6px;display:flex;align-items:baseline;flex-wrap:wrap;gap:8px">'
                    . ($sell !== '' ? '<span style="font-size:16px;font-weight:700;color:var(--color-primary)">' . self::esc($sell) . '</span>' : '')
                    . ($mrp !== '' ? '<s style="font-size:13px;color:var(--tf-text,var(--color-muted))">' . self::esc($mrp) . '</s>' : '')
                    . ($off !== '' ? '<span style="font-size:12px;font-weight:700;color:#16a34a">' . $off . '</span>' : '')
                    . '</p>';
            }
            if (!empty($it['desc'])) $c .= '<p style="margin-top:8px;font-size:14px;color:var(--tf-text,var(--color-muted))">' . self::esc($it['desc']) . '</p>';
            if ($waOrder) {
                $c .= self::waOrderButton($it, $biz, $p, $onDark);
            }
            if (!$waOrder && $href === '' && !empty($it['cta']['text'])) {
                $l = $it['cta']; $l['style'] = $l['style'] ?? 'link'; $c .= '<div style="margin-top:16px">' . self::btn($l) . '</div>';
            }
            $c .= '</div></div>';
            $cards[] = $c;
        }
        if ($shop) {
            $ratio = ['square' => '1/1', 'tall' => '2/3', 'wide' => '3/2', 'landscape' => '16/9'][$p['imageRatio'] ?? ''] ?? '3/4';
            $auto  = ($p['imageRatio'] ?? '') === 'auto' ? ' tf-shop-auto' : '';
            $cols  = ['cards-2' => 2, 'cards-3' => 3, 'list' => 2][$variant] ?? 4;
            $body  = $variant === 'marquee' ? self::marquee($cards)
                : ($variant === 'carousel' ? '<div class="tf-shoprow">' . self::carousel($cards, 0) . '</div>'
                : '<div class="tf-shopgrid tf-shop-c' . $cols . '">' . implode('', $cards) . '</div>');
            $body  = '<div class="tf-shopwrap' . $auto . '" style="--shop-ratio:' . $ratio . '">' . $body . '</div>';
        } else {
            $body = $variant === 'marquee' ? self::marquee($cards)
                : ($variant === 'carousel' ? self::carousel($cards)
                : '<div class="' . self::gridClass($variant) . '" style="text-align:left">' . implode('', $cards) . '</div>');
        }
        $inner = self::sectionHeader($p['label'] ?? null, $p['heading'] ?? null, $p['sub'] ?? null) . $body;
        return self::shell($s, $inner);
    }

    private static function secGallery(array $s, array $doc): string
    {
        $p = $s['props'] ?? [];
        $variant = $s['variant'] ?? 'grid-3';
        $imgs = array_values(array_filter($p['images'] ?? [], fn($i) => self::media($i['image'] ?? null)));
        if (!$imgs) return '';
        $g = $variant === 'grid-4' ? 'g4' : 'g3';
        $lightbox = ($p['lightbox'] ?? true) !== false;

        $slides = [];
        foreach ($imgs as $im) {
            $src = self::media($im['image']);
            $fig = '<figure class="tf-galfig"><img src="' . self::esc($src) . '" alt="' . self::esc($im['alt'] ?? '') . '" loading="lazy" style="width:100%;' . self::ratioCss($p['imageRatio'] ?? null, 'height:208px;') . self::imgFit($im['fit'] ?? ($p['imageFit'] ?? null)) . '">'
                 . (!empty($im['alt']) ? '<figcaption>' . self::esc($im['alt']) . '</figcaption>' : '') . '</figure>';
            $slides[] = $lightbox ? '<a href="' . self::esc($src) . '" target="_blank" rel="noopener noreferrer">' . $fig . '</a>' : $fig;
        }
        $body = $variant === 'marquee' ? self::marquee($slides)
            : ($variant === 'slider' ? self::carousel($slides)
            : '<div class="tf-gal ' . $g . '">' . implode('', $slides) . '</div>');
        $inner = self::sectionHeader($p['label'] ?? null, $p['heading'] ?? null, $p['sub'] ?? null) . $body;
        return self::shell($s, $inner);
    }

    /* ------------------------------------------------------- calculators */

    private const CALC_TYPES = ['sip', 'lumpsum', 'goal', 'retirement', 'term', 'emi'];

    /** The site's WhatsApp number as wa.me wants it (country code, digits only). */
    private static function waNumber(array $doc): string
    {
        $n = preg_replace('/\D/', '', (string)($doc['business']['whatsapp'] ?? $doc['business']['phone'] ?? ''));
        return strlen($n) === 10 ? '91' . $n : $n;
    }

    /**
     * Financial calculators — SIP, lumpsum, goal planner, retirement corpus, term
     * cover and EMI, with sliders, live results, a chart and a "send my result on
     * WhatsApp" button.
     *
     * The server lays out the tabs and empty panels; calcScript() builds the
     * inputs and does the maths in the browser, so moving a slider needs no round
     * trip. Each panel carries its configuration in data-cx. The maths is kept in
     * step with Calculators.tsx (the editor canvas).
     */
    private static function secCalculators(array $s, array $doc): string
    {
        $p = $s['props'] ?? [];
        $items = [];
        foreach ((array)($p['items'] ?? []) as $it) {
            if (is_array($it) && in_array($it['type'] ?? '', self::CALC_TYPES, true)) $items[] = $it;
        }
        if (!$items) return '';

        $variant = ($s['variant'] ?? 'tabs') === 'stacked' ? 'stacked' : 'tabs';
        $uid = 'cx' . substr(md5(($s['id'] ?? '') . 'calc'), 0, 6);
        $wa  = ($p['showWhatsapp'] ?? true) !== false ? self::waNumber($doc) : '';
        $names = ['sip' => 'SIP Calculator', 'lumpsum' => 'Lumpsum Calculator', 'goal' => 'Goal Planner',
                  'retirement' => 'Retirement Planner', 'term' => 'Term Insurance Cover', 'emi' => 'EMI Calculator'];
        $tabbed = $variant === 'tabs' && count($items) > 1;

        $tabs = $panels = '';
        foreach ($items as $i => $it) {
            $type  = $it['type'];
            $title = trim((string)($it['title'] ?? '')) ?: $names[$type];
            $tag   = trim((string)($it['tag'] ?? ''));
            $color = self::isColor($it['color'] ?? null) ? ' style="--cx-c:' . $it['color'] . '"' : '';
            $cfg   = ['type' => $type, 'title' => $title];
            if (is_numeric($it['returnRate'] ?? null)) $cfg['rate'] = (float)$it['returnRate'];

            $tabs .= '<button type="button" role="tab" class="tf-cx-tab" id="' . $uid . '-t' . $i . '" data-type="' . $type . '"'
                   . ' aria-controls="' . $uid . '-p' . $i . '" aria-selected="' . ($i === 0 ? 'true' : 'false') . '"' . $color . '>'
                   . ($tag !== '' ? '<span class="tf-cx-tag">' . self::esc($tag) . '</span>' : '')
                   . '<span class="tf-cx-tabt">' . self::esc($title) . '</span></button>';

            $panels .= '<div class="tf-cx-panel"' . ($tabbed ? ' role="tabpanel" aria-labelledby="' . $uid . '-t' . $i . '"' : '')
                     . ' id="' . $uid . '-p' . $i . '"' . ($tabbed && $i > 0 ? ' hidden' : '') . $color
                     . ' data-cx="' . self::esc(json_encode($cfg, JSON_UNESCAPED_UNICODE)) . '">'
                     . (!$tabbed ? ($tag !== '' ? '<p class="tf-cx-tag">' . self::esc($tag) . '</p>' : '') . '<h3 class="tf-cx-h">' . self::esc($title) . '</h3>' : '')
                     . (trim((string)($it['note'] ?? '')) !== '' ? '<p class="tf-cx-note">' . self::esc($it['note']) . '</p>' : '')
                     . '<div class="tf-cx-body"><div class="tf-cx-inputs"></div><div class="tf-cx-out" aria-live="polite"></div></div>'
                     . '<noscript><p class="tf-cx-note">Please turn on JavaScript to use this calculator.</p></noscript>'
                     . '</div>';
        }

        $disc = trim((string)($p['disclaimer'] ?? ''));
        // Variant modifier uses a DOUBLE dash: "tf-cx-tabs" is the tab strip's own
        // class, and giving the wrapper that name turned the whole calculator into
        // one non-wrapping flex row on phones (tabs, panel and disclaimer squeezed
        // side by side into thin columns).
        $body = '<div class="tf-cx tf-cx--' . $variant . '" data-wa="' . self::esc($wa) . '"'
              . ' data-cta="' . self::esc(trim((string)($p['ctaText'] ?? '')) ?: 'Discuss this plan on WhatsApp') . '">'
              . ($tabbed ? '<div class="tf-cx-tabs" role="tablist">' . $tabs . '</div>' : '')
              . $panels
              . ($disc !== '' ? '<p class="tf-cx-disc">' . self::esc($disc) . '</p>' : '')
              . '</div>';

        return self::shell($s, self::sectionHeader($p['label'] ?? null, $p['heading'] ?? null, $p['sub'] ?? null) . $body . self::calcScript());
    }

    /** Calculator maths + UI. Guarded, so several calculator sections share one copy. */
    private static function calcScript(): string
    {
        return <<<'JS'
<script>(function(){if(window.tfCalc)return;window.tfCalc=1;
function inr(n){return "₹"+Math.round(n).toLocaleString("en-IN")}
function short(n){var a=Math.abs(n);if(a>=1e7)return "₹"+(n/1e7).toFixed(2).replace(/\.?0+$/,"")+" Cr";if(a>=1e5)return "₹"+(n/1e5).toFixed(2).replace(/\.?0+$/,"")+" L";return inr(n)}
function esc(t){var d=document.createElement("div");d.textContent=t==null?"":t;return d.innerHTML}
function fvSip(p,r,m){var i=r/1200;return i?p*((Math.pow(1+i,m)-1)/i)*(1+i):p*m}
function sipFor(t,r,m){if(m<=0)return t;var i=r/1200;return i?t*i/((Math.pow(1+i,m)-1)*(1+i)):t/m}
var S={
sip:{f:[["amt","Monthly investment","₹",500,200000,500,5000],["rate","Expected return (p.a.)","%",1,30,0.5,12],["yrs","Time period","yrs",1,40,1,15]],
 run:function(v){var m=v.yrs*12,inv=v.amt*m,fv=fvSip(v.amt,v.rate,m);return{big:[["Estimated future value",fv]],rows:[["Total invested",inv],["Estimated returns",fv-inv]],donut:[inv,fv-inv],dl:["Invested","Returns"],msg:"SIP of "+inr(v.amt)+"/month for "+v.yrs+" years at "+v.rate+"% ≈ "+short(fv)}}},
lumpsum:{f:[["amt","One-time investment","₹",5000,10000000,5000,100000],["rate","Expected return (p.a.)","%",1,30,0.5,12],["yrs","Time period","yrs",1,40,1,10]],
 run:function(v){var fv=v.amt*Math.pow(1+v.rate/100,v.yrs);return{big:[["Estimated future value",fv]],rows:[["Invested",v.amt],["Estimated returns",fv-v.amt]],donut:[v.amt,fv-v.amt],dl:["Invested","Returns"],msg:"Lumpsum of "+inr(v.amt)+" for "+v.yrs+" years at "+v.rate+"% ≈ "+short(fv)}}},
goal:{f:[["cost","Goal cost in today's money","₹",10000,10000000,10000,1500000],["yrs","Years to the goal","yrs",1,30,1,12],["infl","Cost inflation (p.a.)","%",0,15,0.5,8],["rate","Expected return (p.a.)","%",1,30,0.5,12]],
 run:function(v){var fut=v.cost*Math.pow(1+v.infl/100,v.yrs),m=v.yrs*12,sip=sipFor(fut,v.rate,m),lump=fut/Math.pow(1+v.rate/100,v.yrs);return{big:[["Monthly SIP needed",sip]],rows:[["Goal cost after "+v.yrs+" years",fut],["Or one-time investment today",lump],["Total you will invest by SIP",sip*m]],msg:"A goal costing "+inr(v.cost)+" today will need ≈ "+short(fut)+" in "+v.yrs+" years; SIP needed ≈ "+inr(sip)+"/month"}}},
retirement:{f:[["age","Current age","yrs",18,60,1,30],["ret","Retirement age","yrs",40,75,1,60],["exp","Monthly expenses today","₹",5000,500000,1000,30000],["infl","Inflation (p.a.)","%",0,12,0.5,6],["rate","Return before retirement","%",1,20,0.5,12],["post","Return after retirement","%",1,15,0.5,7],["life","Plan till age","yrs",60,100,1,85]],
 run:function(v){var n=v.ret-v.age,yr=Math.max(v.life-v.ret,1),exp=v.exp*Math.pow(1+v.infl/100,n),ann=exp*12,real=(1+v.post/100)/(1+v.infl/100)-1,corpus=Math.abs(real)<1e-6?ann*yr:ann*(1-Math.pow(1+real,-yr))/real*(1+real),sip=sipFor(corpus,v.rate,n*12);return{big:[["Retirement corpus needed",corpus],["Monthly SIP to reach it",sip]],rows:[["Monthly expenses at "+v.ret,exp],["Years left to invest",null,n+" years"],["Years the corpus must last",null,yr+" years"]],msg:"Retiring at "+v.ret+" with "+inr(v.exp)+"/month expenses today: corpus ≈ "+short(corpus)+", SIP ≈ "+inr(sip)+"/month"}}},
term:{f:[["inc","Annual income","₹",100000,10000000,25000,800000],["age","Current age","yrs",18,65,1,32],["loans","Outstanding loans","₹",0,20000000,50000,0],["cover","Existing life cover","₹",0,20000000,50000,0],["sav","Existing savings & investments","₹",0,20000000,50000,0]],
 run:function(v){var mult=Math.max(5,Math.min(20,60-v.age)),base=v.inc*mult,need=Math.max(base+v.loans-v.cover-v.sav,0);return{big:[["Additional life cover to consider",need]],rows:[["Income replacement ("+mult+"× annual income)",base],["Add: outstanding loans",v.loans],["Less: existing cover and savings",v.cover+v.sav]],msg:"Annual income "+inr(v.inc)+" at age "+v.age+": additional term cover to consider ≈ "+short(need)}}},
emi:{f:[["amt","Loan amount","₹",10000,50000000,10000,1000000],["rate","Interest rate (p.a.)","%",1,30,0.1,9],["yrs","Tenure","yrs",1,30,1,10]],
 run:function(v){var i=v.rate/1200,m=v.yrs*12,e=i?v.amt*i*Math.pow(1+i,m)/(Math.pow(1+i,m)-1):v.amt/m,t=e*m;return{big:[["Monthly EMI",e]],rows:[["Principal",v.amt],["Total interest",t-v.amt],["Total payable",t]],donut:[v.amt,t-v.amt],dl:["Principal","Interest"],msg:"Loan of "+inr(v.amt)+" at "+v.rate+"% for "+v.yrs+" years: EMI ≈ "+inr(e)}}}
};
function build(panel,root){var cfg;try{cfg=JSON.parse(panel.getAttribute("data-cx"))}catch(e){return}var sp=S[cfg.type];if(!sp)return;
 var box=panel.querySelector(".tf-cx-inputs"),out=panel.querySelector(".tf-cx-out"),vals={};
 function calc(){if(cfg.type==="retirement"&&vals.ret<=vals.age){out.innerHTML='<p class="tf-cx-warn">Retirement age should be more than your current age.</p>';return}
  var r=sp.run(vals),h="";
  r.big.forEach(function(b){h+='<div class="tf-cx-big"><span>'+b[0]+'</span><strong>'+short(b[1])+'</strong><small>'+inr(b[1])+'</small></div>'});
  if(r.donut){var a=Math.max(r.donut[0],0),b=Math.max(r.donut[1],0),t=a+b||1,C=2*Math.PI*42,pa=Math.round(a/t*100);
   h+='<div class="tf-cx-chart"><svg viewBox="0 0 100 100" role="img" aria-label="'+r.dl[0]+' '+pa+'%, '+r.dl[1]+' '+(100-pa)+'%"><circle cx="50" cy="50" r="42" fill="none" stroke="var(--cx-soft)" stroke-width="13"/><circle class="tf-cx-arc" cx="50" cy="50" r="42" fill="none" stroke="var(--cx-c)" stroke-width="13" stroke-dasharray="'+(b/t*C).toFixed(2)+' '+C.toFixed(2)+'" transform="rotate(-90 50 50)"/><text x="50" y="55" text-anchor="middle">'+(100-pa)+'%</text></svg><ul><li><i class="tf-cx-k1"></i>'+r.dl[0]+' · '+pa+'%</li><li><i class="tf-cx-k2"></i>'+r.dl[1]+' · '+(100-pa)+'%</li></ul></div>'}
  h+='<ul class="tf-cx-rows">';r.rows.forEach(function(x){h+='<li><span>'+x[0]+'</span><b>'+(x[2]!=null?x[2]:inr(x[1]))+'</b></li>'});h+='</ul>';
  var wa=root.getAttribute("data-wa");if(wa)h+='<a class="tf-cx-cta" target="_blank" rel="noopener noreferrer" href="https://wa.me/'+wa+'?text='+encodeURIComponent("Hi, I used the "+cfg.title+" on your website. "+r.msg+". I would like to discuss a plan.")+'"><svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.5 14.4c-.3-.1-1.8-.9-2-1-.3-.1-.5-.1-.7.1-.2.3-.8 1-.9 1.2-.2.2-.3.2-.6.1-.3-.1-1.3-.5-2.4-1.5-.9-.8-1.5-1.8-1.7-2-.2-.3 0-.5.1-.6l.4-.5c.2-.2.2-.3.3-.5.1-.2 0-.4 0-.5l-.9-2.2c-.2-.6-.5-.5-.7-.5h-.6c-.2 0-.5.1-.8.4-.3.3-1 1-1 2.5s1.1 2.9 1.2 3.1c.1.2 2.1 3.2 5.1 4.5 2.5 1 3 .8 3.6.8.5-.1 1.8-.7 2-1.4.2-.7.2-1.3.2-1.4-.1-.2-.3-.3-.6-.4zM12 21.8c-1.8 0-3.5-.5-5-1.4l-.4-.2-3.7 1 1-3.6-.2-.4A9.8 9.8 0 1 1 12 21.8zm8.4-18.2A11.8 11.8 0 0 0 1.8 17.9L.2 24l6.3-1.6a11.8 11.8 0 0 0 5.5 1.4h.1A11.8 11.8 0 0 0 20.4 3.6z"/></svg>'+esc(root.getAttribute("data-cta"))+'</a>';
  out.innerHTML=h}
 sp.f.forEach(function(f){var k=f[0],d=(k==="rate"&&cfg.rate!=null)?cfg.rate:f[6];vals[k]=d;
  var id=panel.id+"-"+k,w=document.createElement("div"),pct=function(x){return((Math.min(x,f[4])-f[3])/(f[4]-f[3])*100)+"%"};w.className="tf-cx-field";
  w.innerHTML='<div class="tf-cx-lab"><label for="'+id+'">'+f[1]+'</label><span class="tf-cx-num">'+(f[2]==="₹"?"<b>₹</b>":"")+'<input type="number" inputmode="decimal" id="'+id+'" min="'+f[3]+'" step="'+f[5]+'" value="'+d+'">'+(f[2]!=="₹"?"<em>"+f[2]+"</em>":"")+'</span></div><input type="range" aria-label="'+f[1]+'" min="'+f[3]+'" max="'+f[4]+'" step="'+f[5]+'" value="'+d+'">';
  var num=w.querySelector('input[type=number]'),rng=w.querySelector('input[type=range]');
  rng.style.setProperty("--pct",pct(d));
  function set(x,src){x=parseFloat(x);if(isNaN(x))return;x=Math.max(f[3],Math.min(x,f[4]*10));vals[k]=x;if(src!==num)num.value=x;rng.value=Math.min(x,f[4]);rng.style.setProperty("--pct",pct(x));calc()}
  rng.addEventListener("input",function(){set(rng.value,rng)});num.addEventListener("input",function(){set(num.value,num)});num.addEventListener("blur",function(){num.value=vals[k]});
  box.appendChild(w)});
 calc()}
function init(root){if(root.getAttribute("data-cxi"))return;root.setAttribute("data-cxi","1");
 var panels=root.querySelectorAll(".tf-cx-panel"),tabs=root.querySelectorAll(".tf-cx-tab");
 for(var i=0;i<panels.length;i++)build(panels[i],root);
 function pick(n){for(var j=0;j<tabs.length;j++){tabs[j].setAttribute("aria-selected",j===n?"true":"false");tabs[j].tabIndex=j===n?0:-1;panels[j].hidden=j!==n}}
 Array.prototype.forEach.call(tabs,function(t,n){t.addEventListener("click",function(){pick(n)});
  t.addEventListener("keydown",function(e){var d=e.key==="ArrowRight"?1:e.key==="ArrowLeft"?-1:0;if(!d)return;e.preventDefault();var m=(n+d+tabs.length)%tabs.length;pick(m);tabs[m].focus()});
  if(n>0)t.tabIndex=-1});
 // Open a specific calculator from a link: /calculators?calc=retirement (or #retirement).
 var h=(location.search.match(/[?&]calc=([a-z]+)/)||[])[1]||(location.hash||"").slice(1);
 for(var n=0;n<tabs.length;n++){if(h&&tabs[n].getAttribute("data-type")===h){pick(n);break}}}
function boot(){Array.prototype.forEach.call(document.querySelectorAll(".tf-cx"),init)}
if(document.readyState!=="loading")boot();else document.addEventListener("DOMContentLoaded",boot);
})();</script>
JS;
    }

    /* ----------------------------------------------------------- pillars */

    /**
     * Pillars — a signature concept presented as colour-coded pillars (five
     * elements, core values, a method).
     *
     * "tabs" is interactive WITHOUT JavaScript: each pillar is a radio button, and
     * a small per-section stylesheet shows the panel whose radio is checked. So it
     * works before scripts load, arrow keys move between pillars for free, and a
     * screen reader announces it as a group of choices.
     */
    private static function secPillars(array $s, array $doc): string
    {
        $p = $s['props'] ?? [];
        $variant = ($s['variant'] ?? 'tabs') === 'cards' ? 'cards' : 'tabs';
        $items = array_values(array_filter((array)($p['items'] ?? []), fn($it) => is_array($it) && trim((string)($it['title'] ?? '')) !== ''));
        if (!$items) return '';
        $uid = 'px' . substr(md5(($s['id'] ?? '') . 'pil'), 0, 6);

        $detail = function (array $it): string {
            $chips = '';
            foreach ((array)($it['focus'] ?? []) as $f) {
                if (is_string($f) && trim($f) !== '') $chips .= '<li>' . self::esc(trim($f)) . '</li>';
            }
            $cta = (!empty($it['cta']['text']) && !empty($it['cta']['href']))
                ? '<a class="tf-px-cta" href="' . self::esc($it['cta']['href']) . '"' . (!empty($it['cta']['newTab']) ? ' target="_blank" rel="noopener noreferrer"' : '') . '>'
                  . self::esc($it['cta']['text']) . ' <span aria-hidden="true">→</span></a>'
                : '';
            return (trim((string)($it['subtitle'] ?? '')) !== '' ? '<p class="tf-px-sub">' . self::esc($it['subtitle']) . '</p>' : '')
                 . '<h3 class="tf-px-title">' . self::esc($it['title']) . '</h3>'
                 . (trim((string)($it['tagline'] ?? '')) !== '' ? '<p class="tf-px-tagline">' . self::esc($it['tagline']) . '</p>' : '')
                 . (trim((string)($it['text'] ?? '')) !== '' ? '<p class="tf-px-text">' . nl2br(self::esc(trim((string)$it['text']))) . '</p>' : '')
                 . ($chips !== '' ? '<ul class="tf-px-chips">' . $chips . '</ul>' : '')
                 . $cta;
        };

        if ($variant === 'cards') {
            $cards = '';
            foreach ($items as $it) {
                $c   = self::isColor($it['color'] ?? null) ? $it['color'] : 'var(--color-primary)';
                $img = self::media($it['image'] ?? null);
                $badge = '<span class="tf-px-badge"><span aria-hidden="true">' . self::esc($it['symbol'] ?? '') . '</span> ' . self::esc($it['name'] ?? '') . '</span>';
                $cards .= '<article class="tf-px-card" style="--px-c:' . $c . '">'
                    . ($img ? '<div class="tf-px-img"><img src="' . self::esc($img) . '" alt="' . self::esc($it['title']) . '" loading="lazy">' . $badge . '</div>'
                            : '<div class="tf-px-top">' . $badge . '</div>')
                    . '<div class="tf-px-cbody">' . $detail($it) . '</div></article>';
            }
            $body = '<div class="tf-px-cards" style="--px-n:' . min(count($items), 5) . '">' . $cards . '</div>';
            return self::shell($s, self::sectionHeader($p['label'] ?? null, $p['heading'] ?? null, $p['sub'] ?? null) . $body);
        }

        $radios = $nav = $panels = $css = '';
        foreach ($items as $i => $it) {
            $c   = self::isColor($it['color'] ?? null) ? $it['color'] : 'var(--color-primary)';
            $rid = $uid . '-' . $i;
            $img = self::media($it['image'] ?? null);
            $radios .= '<input type="radio" class="tf-px-radio" name="' . $uid . '" id="' . $rid . '"' . ($i === 0 ? ' checked' : '') . '>';
            $nav .= '<label for="' . $rid . '" class="tf-px-pick" style="--px-c:' . $c . '">'
                  . '<span class="tf-px-orb" aria-hidden="true">' . self::esc($it['symbol'] ?? '') . '</span>'
                  . '<span class="tf-px-pname">' . self::esc($it['name'] ?? '') . '</span>'
                  . '<span class="tf-px-ptitle">' . self::esc($it['title']) . '</span></label>';
            $panels .= '<div class="tf-px-panel tf-px-p' . $i . '" style="--px-c:' . $c . '">'
                  . ($img ? '<div class="tf-px-pimg"><img src="' . self::esc($img) . '" alt="' . self::esc($it['title']) . '" loading="lazy">'
                           . '<span class="tf-px-bigsym" aria-hidden="true">' . self::esc($it['symbol'] ?? '') . '</span></div>' : '')
                  . '<div class="tf-px-pbody">'
                  . (trim((string)($it['name'] ?? '')) !== '' ? '<p class="tf-px-kicker">' . self::esc($it['name']) . '</p>' : '')
                  . $detail($it) . '</div></div>';
            $css .= '#' . $rid . ':checked~.tf-px-panels .tf-px-p' . $i . '{display:grid}'
                  . '#' . $rid . ':checked~.tf-px-nav label[for="' . $rid . '"]{border-color:var(--px-c);background:color-mix(in srgb,var(--px-c) 10%,var(--color-bg));box-shadow:0 10px 28px color-mix(in srgb,var(--px-c) 25%,transparent);transform:translateY(-3px)}'
                  . '#' . $rid . ':checked~.tf-px-nav label[for="' . $rid . '"] .tf-px-orb{background:var(--px-c);color:#fff}'
                  . '#' . $rid . ':focus-visible~.tf-px-nav label[for="' . $rid . '"]{outline:3px solid var(--px-c);outline-offset:3px}';
        }
        $body = '<style>' . $css . '</style>'
              . '<div class="tf-px" role="radiogroup" aria-label="' . self::esc($p['heading'] ?? 'Pillars') . '">' . $radios
              . '<div class="tf-px-nav" style="--px-n:' . count($items) . '">' . $nav . '</div>'
              . '<div class="tf-px-panels">' . $panels . '</div></div>';
        return self::shell($s, self::sectionHeader($p['label'] ?? null, $p['heading'] ?? null, $p['sub'] ?? null) . $body);
    }

    /* -------------------------------------------------------------- quiz */

    /**
     * Self check-up quiz: Yes / No / Not sure per question, a score with pointers
     * for the gaps, and the answers sent to the business on WhatsApp.
     */
    private static function secQuiz(array $s, array $doc): string
    {
        $p  = $s['props'] ?? [];
        $qs = array_values(array_filter((array)($p['questions'] ?? []), fn($q) => is_array($q) && trim((string)($q['q'] ?? '')) !== ''));
        if (!$qs) return '';
        $uid = 'qz' . substr(md5(($s['id'] ?? '') . 'quiz'), 0, 6);
        $lab = fn(string $k, string $d) => trim((string)($p[$k] ?? '')) ?: $d;
        $opts = [['2', $lab('yesLabel', 'Yes'), 'yes'], ['0', $lab('noLabel', 'No'), 'no'], ['1', $lab('unsureLabel', 'Not sure'), 'unsure']];

        $list = '';
        foreach ($qs as $i => $q) {
            $name = $uid . '-q' . $i;
            $o = '';
            foreach ($opts as [$v, $l, $k]) {
                $o .= '<label class="tf-qz-opt tf-qz-' . $k . '"><input type="radio" name="' . $name . '" value="' . $v . '"><span>' . self::esc($l) . '</span></label>';
            }
            $list .= '<li class="tf-qz-q" data-tip="' . self::esc(trim((string)($q['tip'] ?? ''))) . '">'
                   . '<p class="tf-qz-qt" id="' . $name . '-l"><span class="tf-qz-n">' . ($i + 1) . '</span>' . self::esc($q['q']) . '</p>'
                   . '<div class="tf-qz-opts" role="radiogroup" aria-labelledby="' . $name . '-l">' . $o . '</div></li>';
        }
        $bands = [
            'good' => [$lab('goodTitle', 'You are well prepared'), $lab('goodText', '')],
            'mid'  => [$lab('midTitle', 'A good start, with some gaps'), $lab('midText', '')],
            'low'  => [$lab('lowTitle', 'Time to make a plan'), $lab('lowText', '')],
        ];
        $wa = self::waNumber($doc);
        $quiz = '<div class="tf-qz" data-wa="' . self::esc($wa) . '" data-cta="' . self::esc($lab('ctaText', 'Discuss my result on WhatsApp')) . '"'
              . ' data-title="' . self::esc($p['heading'] ?? 'Check-up') . '" data-bands="' . self::esc(json_encode($bands, JSON_UNESCAPED_UNICODE)) . '">'
              . '<div class="tf-qz-bar"><span class="tf-qz-fill"></span><em class="tf-qz-count">0/' . count($qs) . '</em></div>'
              . '<ol class="tf-qz-list">' . $list . '</ol>'
              . '<div class="tf-qz-result" hidden aria-live="polite"></div></div>';

        $img = self::media($p['image'] ?? null);
        $body = (($s['variant'] ?? 'card') === 'split' && $img)
            ? '<div class="tf-qz-split"><div class="tf-qz-img"><img src="' . self::esc($img) . '" alt="" loading="lazy"></div>' . $quiz . '</div>'
            : '<div class="tf-qz-wrap">' . $quiz . '</div>';
        return self::shell($s, self::sectionHeader($p['label'] ?? null, $p['heading'] ?? null, $p['sub'] ?? null) . $body . self::quizScript());
    }

    private static function quizScript(): string
    {
        return <<<'JS'
<script>(function(){if(window.tfQuiz)return;window.tfQuiz=1;
function esc(t){var d=document.createElement("div");d.textContent=t==null?"":t;return d.innerHTML}
function init(q){if(q.getAttribute("data-qzi"))return;q.setAttribute("data-qzi","1");
 var items=q.querySelectorAll(".tf-qz-q"),n=items.length,res=q.querySelector(".tf-qz-result"),fill=q.querySelector(".tf-qz-fill"),cnt=q.querySelector(".tf-qz-count"),bands={};
 try{bands=JSON.parse(q.getAttribute("data-bands"))}catch(e){}
 function update(){var done=0,sum=0,gaps=[],lines=[];
  Array.prototype.forEach.call(items,function(it,i){var c=it.querySelector("input:checked"),qt=it.querySelector(".tf-qz-qt").textContent.replace(/^\d+/,"").trim();it.classList.toggle("tf-qz-done",!!c);
   if(c){done++;sum+=+c.value;var lbl=c.parentNode.textContent.trim();lines.push((i+1)+". "+qt+" — "+lbl);if(c.value!=="2"&&it.getAttribute("data-tip"))gaps.push(it.getAttribute("data-tip"))}});
  fill.style.width=(done/n*100)+"%";cnt.textContent=done+"/"+n;
  if(done<n){res.hidden=true;return}
  var pct=Math.round(sum/(2*n)*100),k=pct>=75?"good":pct>=40?"mid":"low",b=bands[k]||["",""],C=2*Math.PI*44;
  var h='<div class="tf-qz-score tf-qz-'+k+'"><svg viewBox="0 0 100 100" aria-hidden="true"><circle cx="50" cy="50" r="44" fill="none" stroke="currentColor" stroke-opacity=".15" stroke-width="10"/><circle cx="50" cy="50" r="44" fill="none" stroke="currentColor" stroke-width="10" stroke-linecap="round" stroke-dasharray="'+(pct/100*C).toFixed(1)+' '+C.toFixed(1)+'" transform="rotate(-90 50 50)"/></svg><strong>'+pct+'%</strong></div>'
   +'<div class="tf-qz-rtext"><h3>'+esc(b[0])+'</h3>'+(b[1]?'<p>'+esc(b[1])+'</p>':'')
   +(gaps.length?'<ul class="tf-qz-gaps">'+gaps.map(function(g){return"<li>"+esc(g)+"</li>"}).join("")+'</ul>':'');
  var wa=q.getAttribute("data-wa");
  if(wa)h+='<a class="tf-qz-cta" target="_blank" rel="noopener noreferrer" href="https://wa.me/'+wa+'?text='+encodeURIComponent("Hi, I took the "+q.getAttribute("data-title")+" on your website. My score: "+pct+"%.\n\n"+lines.join("\n")+"\n\nPlease guide me.")+'">'+esc(q.getAttribute("data-cta"))+'</a>';
  h+='<button type="button" class="tf-qz-reset">Start again</button></div>';
  res.innerHTML=h;res.hidden=false;
  res.querySelector(".tf-qz-reset").addEventListener("click",function(){Array.prototype.forEach.call(q.querySelectorAll("input:checked"),function(r){r.checked=false});update();items[0].scrollIntoView({behavior:"smooth",block:"center"})});
  if(!q.getAttribute("data-shown")){q.setAttribute("data-shown","1");res.scrollIntoView({behavior:"smooth",block:"nearest"})}}
 q.addEventListener("change",update)}
function boot(){Array.prototype.forEach.call(document.querySelectorAll(".tf-qz"),init)}
if(document.readyState!=="loading")boot();else document.addEventListener("DOMContentLoaded",boot);
})();</script>
JS;
    }

    /* ------------------------------------------------------------ social */

    /** Brand colour + stroke icon for each platform. Keep in step with Social.tsx. */
    private const SOCIAL_NETWORKS = [
        'instagram'        => ['#E1306C', 'Instagram', '<rect x="2.5" y="2.5" width="19" height="19" rx="5.5"/><circle cx="12" cy="12" r="4.3"/><circle cx="17.6" cy="6.4" r=".9" fill="currentColor"/>'],
        'youtube'          => ['#FF0000', 'YouTube', '<rect x="2" y="5" width="20" height="14" rx="4"/><path d="m10 9 5 3-5 3z" fill="currentColor"/>'],
        'facebook'         => ['#1877F2', 'Facebook', '<path d="M15 3h-2.5A4.5 4.5 0 0 0 8 7.5V10H5.5v3.5H8V21h3.5v-7.5H14l.8-3.5h-3.3V7.8c0-.7.6-1.3 1.3-1.3H15z"/>'],
        'whatsapp'         => ['#25D366', 'WhatsApp', '<path d="M3.5 20.5l1.3-4.3A8.5 8.5 0 1 1 8 19.3z"/><path d="M9 8.5c0 3.6 2.9 6.5 6.5 6.5l1.1-1.6-2.2-1-1 .9a5.6 5.6 0 0 1-2.6-2.6l.9-1-1-2.2z"/>'],
        'whatsapp-channel' => ['#25D366', 'WhatsApp Channel', '<path d="M3 10.5v3a1 1 0 0 0 1 1h2.2L11 18V6L6.2 9.5H4a1 1 0 0 0-1 1z"/><path d="M15 9a4.2 4.2 0 0 1 0 6"/><path d="M17.8 6.2a8.2 8.2 0 0 1 0 11.6"/>'],
        'google-review'    => ['#FBBC04', 'Google Review', '<path d="M12 2.8l2.8 5.8 6.4.9-4.6 4.4 1.1 6.3L12 17.2l-5.7 3 1.1-6.3-4.6-4.4 6.4-.9z"/>'],
        'linkedin'         => ['#0A66C2', 'LinkedIn', '<rect x="2.5" y="2.5" width="19" height="19" rx="3"/><path d="M7.5 10.5V17M7.5 7.2v.01M11.5 17v-3.8a2.7 2.7 0 0 1 5.3 0V17M11.5 10.5V17"/>'],
        'x'                => ['#111111', 'X', '<path d="M4 4l6.6 8.8L4.3 20h1.9l5.2-6.1L15.8 20H20l-7-9.3L18.9 4H17l-4.8 5.6L8.2 4z"/>'],
        'telegram'         => ['#229ED9', 'Telegram', '<path d="M21.5 4 2.5 11.3l6 2 2.2 6.4 3.3-3.9 5 3.7z"/><path d="m8.5 13.3 9-6.3"/>'],
        'website'          => ['', 'Website', '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a14 14 0 0 1 0 18M12 3a14 14 0 0 0 0 18"/>'],
    ];

    private static function secSocial(array $s, array $doc): string
    {
        $p = $s['props'] ?? [];
        $variant = ($s['variant'] ?? 'cards') === 'pills' ? 'pills' : 'cards';
        $cells = '';
        foreach ((array)($p['items'] ?? []) as $it) {
            if (!is_array($it)) continue;
            $href = trim((string)($it['href'] ?? ''));
            $net  = self::SOCIAL_NETWORKS[(string)($it['network'] ?? '')] ?? null;
            if ($href === '' || !$net || !preg_match('#^(https?://|/|mailto:|tel:)#i', $href)) continue;
            [$color, $label, $icon] = $net;
            $color = $color !== '' ? $color : 'var(--color-primary)';
            $title = trim((string)($it['title'] ?? '')) ?: $label;
            $svg = '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $icon . '</svg>';
            $ext = preg_match('#^https?://#i', $href) ? ' target="_blank" rel="noopener noreferrer"' : '';
            if ($variant === 'pills') {
                $cells .= '<a class="tf-sc-pill" href="' . self::esc($href) . '"' . $ext . ' style="--sc:' . $color . '">'
                        . '<span class="tf-sc-ic">' . $svg . '</span>' . self::esc($title) . '</a>';
                continue;
            }
            $action = trim((string)($it['action'] ?? ''));
            $cells .= '<a class="tf-sc" href="' . self::esc($href) . '"' . $ext . ' style="--sc:' . $color . '">'
                    . '<span class="tf-sc-ic">' . $svg . '</span>'
                    . '<span class="tf-sc-t">' . self::esc($title) . '</span>'
                    . (trim((string)($it['text'] ?? '')) !== '' ? '<span class="tf-sc-x">' . self::esc($it['text']) . '</span>' : '')
                    . ($action !== '' ? '<span class="tf-sc-a">' . self::esc($action) . ' <span aria-hidden="true">→</span></span>' : '')
                    . '</a>';
        }
        if ($cells === '') return '';
        // Balanced rows: six cards as 3 + 3 rather than 5 + 1.
        $n = substr_count($cells, $variant === 'pills' ? 'class="tf-sc-pill"' : 'class="tf-sc"');
        $cols = $n <= 5 ? $n : ($n % 3 === 0 ? 3 : 4);
        $body = '<div class="' . ($variant === 'pills' ? 'tf-sc-pills' : 'tf-scs') . '" style="--sc-cols:' . max(1, $cols) . '">' . $cells . '</div>';
        return self::shell($s, self::sectionHeader($p['label'] ?? null, $p['heading'] ?? null, $p['sub'] ?? null) . $body);
    }

    /* ------------------------------------------------------------- steps */

    private static function secSteps(array $s, array $doc): string
    {
        $p = $s['props'] ?? [];
        $variant = ($s['variant'] ?? 'horizontal') === 'vertical' ? 'vertical' : 'horizontal';
        $items = array_values(array_filter((array)($p['items'] ?? []), fn($it) => is_array($it) && trim((string)($it['title'] ?? '')) !== ''));
        if (!$items) return '';
        $li = '';
        foreach ($items as $i => $it) {
            $c   = self::isColor($it['color'] ?? null) ? $it['color'] : 'var(--color-primary)';
            $sym = trim((string)($it['symbol'] ?? '')) !== '' ? $it['symbol'] : (string)($i + 1);
            $li .= '<li class="tf-st-i" style="--st-c:' . $c . '">'
                 . '<span class="tf-st-dot" aria-hidden="true">' . self::esc($sym) . '</span>'
                 . '<div class="tf-st-b"><h3 class="tf-st-t">' . self::esc($it['title']) . '</h3>'
                 . (trim((string)($it['text'] ?? '')) !== '' ? '<p class="tf-st-x">' . self::esc($it['text']) . '</p>' : '')
                 . '</div></li>';
        }
        $cta = self::btn($p['cta'] ?? null, self::isDarkBg($s));
        $body = '<ol class="tf-st tf-st-' . $variant . '" style="--st-n:' . count($items) . '">' . $li . '</ol>'
              . ($cta !== '' ? '<div class="tf-st-cta">' . $cta . '</div>' : '');
        return self::shell($s, self::sectionHeader($p['label'] ?? null, $p['heading'] ?? null, $p['sub'] ?? null) . $body);
    }

    /* ------------------------------------------------------------ videos */

    /**
     * Where an item's video comes from: an upload, or a YouTube / Vimeo /
     * Instagram / direct-file link. null when there is nothing playable, so a
     * half-filled item renders nothing rather than an empty black box.
     */
    private static function videoSource(array $it): ?array
    {
        $file = self::media($it['video'] ?? null);
        if ($file) return ['kind' => 'file', 'src' => $file];

        $url = trim((string)($it['url'] ?? ''));
        if ($url === '') return null;
        if (preg_match('#(?:youtube\.com/(?:watch\?(?:[^\s]*&)?v=|embed/|shorts/|live/)|youtu\.be/)([\w-]{11})#i', $url, $m)) {
            return ['kind' => 'youtube', 'id' => $m[1], 'vertical' => stripos($url, '/shorts/') !== false];
        }
        if (preg_match('#vimeo\.com/(?:video/)?(\d+)#i', $url, $m)) {
            return ['kind' => 'vimeo', 'id' => $m[1]];
        }
        if (preg_match('#instagram\.com/(?:reels?|p|tv)/([\w-]+)#i', $url, $m)) {
            return ['kind' => 'instagram', 'id' => $m[1]];
        }
        if (preg_match('#^https?://\S+\.(?:mp4|webm|mov)(?:\?\S*)?$#i', $url)) {
            return ['kind' => 'file', 'src' => $url];
        }
        return null;
    }

    /**
     * Videos — uploaded clips and YouTube / Vimeo / Instagram reel links, in a
     * grid, one large player, or a swipeable row.
     *
     * YouTube (and Vimeo, when a cover picture is set) load as a still picture
     * with a play button and only swap in the real player when tapped. One
     * YouTube iframe pulls in around a megabyte of script before anyone presses
     * play; a grid of six would make the page crawl on a phone.
     */
    private static function secVideos(array $s, array $doc): string
    {
        $p = $s['props'] ?? [];
        $variant = in_array($s['variant'] ?? '', ['grid-2', 'grid-3', 'single', 'carousel'], true) ? $s['variant'] : 'grid-2';
        $shape = ['portrait' => '9/16', 'square' => '1/1'][$p['shape'] ?? ''] ?? '16/9';
        $loop  = ($p['playback'] ?? 'click') === 'muted-loop';
        $play  = '<span class="tf-vid-btn" aria-hidden="true"><svg width="26" height="26" viewBox="0 0 24 24" fill="#fff"><path d="M8 5.14v13.72a1 1 0 0 0 1.5.86l11.24-6.86a1 1 0 0 0 0-1.72L9.5 4.28A1 1 0 0 0 8 5.14z"/></svg></span>';

        $cards = [];
        foreach ((array)($p['items'] ?? []) as $it) {
            if (!is_array($it)) continue;
            $v = self::videoSource($it);
            if (!$v) continue;

            $title  = trim((string)($it['title'] ?? ''));
            $label  = self::esc($title !== '' ? $title : 'Video');
            $poster = self::media($it['poster'] ?? null);
            $ratio  = $shape;
            $cover  = '';
            $iframe = fn(string $src) => '<iframe class="tf-vid-if" src="' . self::esc($src) . '" title="' . $label . '" loading="lazy" '
                . 'allow="autoplay; encrypted-media; picture-in-picture; fullscreen" allowfullscreen></iframe>';
            $facade = fn(string $embed, string $thumb) => '<button type="button" class="tf-vid-play" data-embed="' . self::esc($embed) . '" aria-label="Play: ' . $label . '">'
                . '<img src="' . self::esc($thumb) . '" alt="" loading="lazy">' . $play . '</button>';

            switch ($v['kind']) {
                case 'file':
                    if ($loop) $cover = ' tf-vid-cover';
                    // No cover picture: "#t=0.1" makes the browser paint the first
                    // frame instead of an empty black box until someone presses play.
                    $vsrc = (!$poster && !$loop && strpos($v['src'], '#') === false) ? $v['src'] . '#t=0.1' : $v['src'];
                    $player = '<video src="' . self::esc($vsrc) . '"' . ($poster ? ' poster="' . self::esc($poster) . '"' : '')
                        . ($loop ? ' autoplay muted loop playsinline' : ' controls playsinline preload="metadata"')
                        . ' aria-label="' . $label . '"></video>';
                    break;
                case 'youtube':
                    if ($v['vertical']) $ratio = '9/16';
                    $id = $v['id'];
                    if ($loop) {
                        $player = $iframe('https://www.youtube-nocookie.com/embed/' . $id . '?autoplay=1&mute=1&loop=1&playlist=' . $id . '&controls=0&playsinline=1&rel=0');
                    } else {
                        $player = $facade('https://www.youtube-nocookie.com/embed/' . $id . '?autoplay=1&playsinline=1&rel=0',
                                          $poster ?: 'https://i.ytimg.com/vi/' . $id . '/hqdefault.jpg');
                    }
                    break;
                case 'vimeo':
                    $base = 'https://player.vimeo.com/video/' . $v['id'] . '?dnt=1';
                    if ($loop) {
                        $player = $iframe($base . '&autoplay=1&muted=1&loop=1&background=1');
                    } elseif ($poster) {
                        $player = $facade($base . '&autoplay=1', $poster);
                    } else {
                        // Vimeo thumbnails need their API, so without a cover picture
                        // the player itself is the cover (still lazy-loaded).
                        $player = $iframe($base);
                    }
                    break;
                default:   // instagram — reels are vertical, with Instagram's own header and footer
                    $ratio  = '9/16';
                    $player = $iframe('https://www.instagram.com/p/' . $v['id'] . '/embed/');
            }

            $cap = '';
            if ($title !== '') $cap .= '<p class="tf-vid-t">' . self::esc($title) . '</p>';
            if (trim((string)($it['caption'] ?? '')) !== '') $cap .= '<p class="tf-vid-x">' . self::esc($it['caption']) . '</p>';

            $cards[] = '<figure class="tf-vid tf-vid-' . $v['kind'] . '">'
                . '<div class="tf-vid-frame' . $cover . '" style="aspect-ratio:' . $ratio . '">' . $player . '</div>'
                . ($cap !== '' ? '<figcaption class="tf-vid-cap">' . $cap . '</figcaption>' : '')
                . '</figure>';
        }
        if (!$cards) return '';

        $cols = ['single' => 1, 'grid-3' => 3][$variant] ?? 2;
        $body = $variant === 'carousel'
            ? '<div class="tf-vidrow">' . self::carousel($cards, 0) . '</div>'
            : '<div class="tf-vids tf-vids-' . $cols . '">' . implode('', $cards) . '</div>';

        // One small script for every Videos section on the page: swap the cover
        // for the real player on tap, and pause other videos when one starts.
        $script = '<script>(function(){if(window.tfVid)return;window.tfVid=1;'
            . 'document.addEventListener("click",function(e){var b=e.target.closest&&e.target.closest(".tf-vid-play");if(!b)return;'
            . 'var f=document.createElement("iframe");f.className="tf-vid-if";f.src=b.getAttribute("data-embed");'
            . 'f.title=(b.getAttribute("aria-label")||"Video").replace(/^Play: /,"");'
            . 'f.allow="autoplay; encrypted-media; picture-in-picture; fullscreen";f.setAttribute("allowfullscreen","");b.replaceWith(f);});'
            . 'document.addEventListener("play",function(e){var v=e.target;if(!v.closest||!v.closest(".tf-vid")||v.muted)return;'
            . 'document.querySelectorAll(".tf-vid video").forEach(function(o){if(o!==v&&!o.muted&&!o.paused)o.pause();});},true);'
            . '})();</script>';

        return self::shell($s, self::sectionHeader($p['label'] ?? null, $p['heading'] ?? null, $p['sub'] ?? null) . $body . $script);
    }

    /* ------------------------------------------------ storefront sections */

    /** Stroke icons for the Trust Badges section (24px grid). Keep in step with Features.tsx. */
    private const FEATURE_ICONS = [
        'gem'     => '<path d="M6 3h12l4 6-10 12L2 9z"/><path d="M11 3 8 9l4 12 4-12-3-6"/><path d="M2 9h20"/>',
        'shield'  => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/>',
        'truck'   => '<path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/><path d="M15 18H9"/><path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.62l-3.48-4.35A1 1 0 0 0 17.52 8H14"/><circle cx="17" cy="18" r="2"/><circle cx="7" cy="18" r="2"/>',
        'gift'    => '<rect x="3" y="8" width="18" height="4" rx="1"/><path d="M12 8v13"/><path d="M19 12v7a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2v-7"/><path d="M7.5 8a2.5 2.5 0 0 1 0-5C10 3 12 8 12 8s2-5 4.5-5a2.5 2.5 0 0 1 0 5"/>',
        'refresh' => '<path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"/><path d="M21 3v5h-5"/><path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"/><path d="M8 16H3v5"/>',
        'sparkle' => '<path d="M12 3l1.9 5.8L20 11l-6.1 2.2L12 19l-1.9-5.8L4 11l6.1-2.2z"/><path d="M19 3v4"/><path d="M21 5h-4"/>',
        'store'   => '<path d="M3 9l1.5-5h15L21 9"/><path d="M3 9h18v2a3 3 0 0 1-6 0 3 3 0 0 1-6 0 3 3 0 0 1-6 0z"/><path d="M5 13v8h14v-8"/><path d="M10 21v-5h4v5"/>',
        'chat'    => '<path d="M21 11.5a8.4 8.4 0 0 1-12.3 7.4L3 21l2.1-5.6A8.4 8.4 0 1 1 21 11.5z"/>',
        'ruler'   => '<path d="M21.3 15.3a2.4 2.4 0 0 1 0 3.4l-2.6 2.6a2.4 2.4 0 0 1-3.4 0L2.7 8.7a2.4 2.4 0 0 1 0-3.4l2.6-2.6a2.4 2.4 0 0 1 3.4 0z"/><path d="m14.5 12.5 2-2"/><path d="m11.5 9.5 2-2"/><path d="m8.5 6.5 2-2"/><path d="m17.5 15.5 2-2"/>',
        'hand'    => '<path d="M18 11V6a2 2 0 0 0-4 0v5"/><path d="M14 10V4a2 2 0 0 0-4 0v6"/><path d="M10 10.5V6a2 2 0 0 0-4 0v8"/><path d="M18 8a2 2 0 1 1 4 0v6a8 8 0 0 1-8 8h-2c-2.8 0-4.5-.86-5.99-2.34l-3.6-3.6a2 2 0 0 1 2.83-2.82L7 15"/>',
        'star'    => '<path d="M12 2l3.1 6.3 6.9 1-5 4.9 1.2 6.8L12 17.8 5.8 21l1.2-6.8-5-4.9 6.9-1z"/>',
        'lock'    => '<rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>',
        'heart'       => '<path d="M19.5 12.6 12 20l-7.5-7.4A5 5 0 1 1 12 6a5 5 0 1 1 7.5 6.6z"/><path d="M3.5 12h4l2-3 3 6 2-3h6"/>',
        'umbrella'    => '<path d="M22 12a10 10 0 0 0-20 0z"/><path d="M12 12v7a2 2 0 0 1-4 0"/><path d="M12 2v1"/>',
        'trending'    => '<path d="m3 17 6-6 4 4 8-8"/><path d="M14 7h7v7"/>',
        'piggy'       => '<path d="M19 9.5c.8.3 1.5 1 1.5 2V13H19a7 7 0 0 1-2.5 3.5V19h-3v-1.5h-3V19h-3v-2.6A7 7 0 0 1 11 5h2a7 7 0 0 1 6 4.5z"/><path d="M8 10h.01"/><path d="M11 5V3.5"/>',
        'graduation'  => '<path d="M22 10 12 5 2 10l10 5z"/><path d="M6 12v5c0 1.5 3 3 6 3s6-1.5 6-3v-5"/><path d="M22 10v6"/>',
        'rings'       => '<circle cx="9" cy="14" r="6"/><circle cx="15" cy="14" r="6"/><path d="m9 3 1.5 2h-3z"/>',
        'stethoscope' => '<path d="M5 3v6a5 5 0 0 0 10 0V3"/><path d="M10 14v2a5 5 0 0 0 10 0v-2"/><circle cx="20" cy="12" r="2"/>',
        'receipt'     => '<path d="M5 3h14v18l-3-2-2 2-2-2-2 2-2-2-3 2z"/><path d="M9 8h6M9 12h6M9 16h4"/>',
        'family'      => '<circle cx="8" cy="6" r="2.5"/><circle cx="16" cy="6" r="2.5"/><circle cx="12" cy="13" r="2"/><path d="M3.5 21v-5a4.5 4.5 0 0 1 8.5-2"/><path d="M12 14a4.5 4.5 0 0 1 8.5 2v5"/>',
        'calculator'  => '<rect x="5" y="2" width="14" height="20" rx="2"/><path d="M8 6h8"/><path d="M8 11h.01M12 11h.01M16 11h.01M8 15h.01M12 15h.01M16 15h.01M8 19h.01M12 19h.01M16 19h.01"/>',
        'target'      => '<circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="5"/><circle cx="12" cy="12" r="1"/>',
        'clipboard'   => '<rect x="6" y="4" width="12" height="17" rx="2"/><path d="M9 4V3h6v1"/><path d="m9 13 2 2 4-4"/>',
        'rupee'       => '<path d="M6 3h12M6 8h12M6 13h3a5 5 0 0 0 0-10"/><path d="m9 13 8 8"/>',
        'award'       => '<circle cx="12" cy="9" r="6"/><path d="m8.5 14-1.5 7 5-3 5 3-1.5-7"/>',
        'clock'       => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
        'phone'       => '<path d="M22 16.9v3a2 2 0 0 1-2.2 2A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7l.5 3a2 2 0 0 1-.6 1.8L7.6 9.9a16 16 0 0 0 6.5 6.5l1.4-1.4a2 2 0 0 1 1.8-.6l3 .5a2 2 0 0 1 1.7 2z"/>',
    ];

    /**
     * Banner slideshow — the rotating promo strip at the top of an online store.
     *
     * Built on carousel(), so arrows, dots, swipe and autoplay come from the one
     * carousel script every page already loads; this only makes each slide the
     * full width and lays the controls over the picture. It runs edge to edge:
     * the tf-ss-sec class lifts the container's max-width for this section only.
     */
    private static function secSlideshow(array $s, array $doc): string
    {
        $p = $s['props'] ?? [];
        $variant = in_array($s['variant'] ?? '', ['split', 'full', 'banner'], true) ? $s['variant'] : 'split';
        $slides = array_values(array_filter((array)($p['slides'] ?? []), fn($sl) => is_array($sl) && self::media($sl['image'] ?? null)));
        if (!$slides) return '';
        $height = ['short' => 'tf-ss-short', 'tall' => 'tf-ss-tall'][$p['height'] ?? 'medium'] ?? 'tf-ss-medium';

        $out = [];
        foreach ($slides as $i => $sl) {
            $img = self::media($sl['image']);
            $mob = self::media($sl['mobileImage'] ?? null);
            $alt = self::esc(trim((string)($sl['heading'] ?? '')) ?: ($doc['site']['name'] ?? ''));
            // Only the first slide is on screen at load; the rest can wait.
            $load = $i === 0 ? ' fetchpriority="high"' : ' loading="lazy"';
            $pic = '<picture>' . ($mob ? '<source media="(max-width:640px)" srcset="' . self::esc($mob) . '">' : '')
                 . '<img class="tf-ss-img" src="' . self::esc($img) . '" alt="' . $alt . '"' . $load
                 . ' style="' . self::imgFit($sl['imageFit'] ?? null) . '"></picture>';
            $href = trim((string)($sl['href'] ?? ''));
            if ($href !== '') $pic = '<a class="tf-ss-link" href="' . self::esc($href) . '" aria-label="' . $alt . '">' . $pic . '</a>';

            $copy = '';
            if (trim((string)($sl['badge'] ?? '')) !== '')   $copy .= '<p class="tf-ss-badge">' . self::esc($sl['badge']) . '</p>';
            if (trim((string)($sl['heading'] ?? '')) !== '') $copy .= '<h2 class="tf-ss-h">' . self::esc($sl['heading']) . '</h2>';
            if (trim((string)($sl['sub'] ?? '')) !== '')     $copy .= '<p class="tf-ss-sub">' . self::esc($sl['sub']) . '</p>';
            $b = self::btn($sl['cta'] ?? null, $variant === 'full');
            if ($b !== '') $copy .= '<div class="tf-ss-cta">' . $b . '</div>';

            if ($variant === 'banner' || $copy === '') {
                $out[] = '<div class="tf-ss-slide tf-ss-banner">' . $pic . '</div>';
            } elseif ($variant === 'full') {
                $out[] = '<div class="tf-ss-slide tf-ss-full">' . $pic . '<div class="tf-ss-shade" aria-hidden="true"></div>'
                       . '<div class="tf-ss-copy">' . $copy . '</div></div>';
            } else {
                $panel = self::isColor($sl['panelColor'] ?? null) ? $sl['panelColor'] : '';
                $ps = $panel !== '' ? ' style="background:' . $panel . ';color:' . self::readableOn($panel) . '"' : '';
                $out[] = '<div class="tf-ss-slide tf-ss-split"><div class="tf-ss-panel"' . $ps . '><div class="tf-ss-copy">' . $copy . '</div></div>'
                       . '<div class="tf-ss-media">' . $pic . '</div></div>';
            }
        }
        $every = max(0, (int)($p['autoplay'] ?? 5));
        return self::shell($s, '<div class="tf-slideshow ' . $height . '">' . self::carousel($out, $every * 1000) . '</div>', 'tf-ss-sec');
    }

    /**
     * Shop by Category — picture tiles that lead into a collection.
     *
     * Tiles and circles become one swipeable row on a phone rather than a tall
     * stack: a shopper expects to flick through categories sideways, and a grid
     * of twelve would push everything else a full screen down.
     */
    private static function secCategories(array $s, array $doc): string
    {
        $p = $s['props'] ?? [];
        $variant = in_array($s['variant'] ?? '', ['tiles', 'circles', 'cards', 'pills'], true) ? $s['variant'] : 'tiles';
        $items = array_values(array_filter((array)($p['items'] ?? []), fn($it) => is_array($it) && trim((string)($it['title'] ?? '')) !== ''));
        if (!$items) return '';
        $cols = in_array((string)($p['columns'] ?? ''), ['3', '4', '5', '6', '8'], true) ? (string)$p['columns'] : '6';

        $cells = '';
        foreach ($items as $it) {
            $href = trim((string)($it['href'] ?? ''));
            $wrap = fn(string $cls, string $inner) => $href !== ''
                ? '<a href="' . self::esc($href) . '" class="' . $cls . '">' . $inner . '</a>'
                : '<div class="' . $cls . '">' . $inner . '</div>';
            $title = self::esc($it['title']);
            $note  = trim((string)($it['note'] ?? '')) !== '' ? '<span class="tf-cat-note">' . self::esc($it['note']) . '</span>' : '';
            if ($variant === 'pills') {
                $cells .= $wrap('tf-cat-pill', $title);
                continue;
            }
            $img = self::media($it['image'] ?? null);
            $pic = $img
                ? '<img src="' . self::esc($img) . '" alt="' . $title . '" loading="lazy">'
                : '<span class="tf-cat-ph" aria-hidden="true">' . self::esc(mb_substr((string)$it['title'], 0, 1)) . '</span>';
            $cells .= $variant === 'cards'
                ? $wrap('tf-cat-card', $pic . '<span class="tf-cat-cap"><span class="tf-cat-title">' . $title . '</span>' . $note . '</span>')
                : $wrap('tf-cat-tile', '<span class="tf-cat-media">' . $pic . '</span><span class="tf-cat-title">' . $title . '</span>' . $note);
        }
        $body = '<div class="tf-cats tf-cats-' . $variant . '" style="--cat-cols:' . $cols . '">' . $cells . '</div>';
        return self::shell($s, self::sectionHeader($p['label'] ?? null, $p['heading'] ?? null, $p['sub'] ?? null) . $body);
    }

    /** Promo banners — clickable pictures side by side, copy laid over a soft shade. */
    private static function secBanners(array $s, array $doc): string
    {
        $p = $s['props'] ?? [];
        $variant = in_array($s['variant'] ?? '', ['grid-2', 'grid-3', 'single'], true) ? $s['variant'] : 'grid-2';
        $items = array_values(array_filter((array)($p['items'] ?? []), fn($it) => is_array($it) && self::media($it['image'] ?? null)));
        if (!$items) return '';
        $cols  = $variant === 'single' ? 1 : ($variant === 'grid-3' ? 3 : 2);
        $shape = (string)($p['shape'] ?? 'wide');
        $ratio = ['landscape' => '16/9', 'square' => '1/1', 'portrait' => '3/4'][$shape] ?? '3/2';
        // One banner across the whole row at 3:2 would be taller than the screen.
        if ($variant === 'single' && $shape === 'wide') $ratio = '3/1';

        $cells = '';
        foreach ($items as $it) {
            $head = trim((string)($it['heading'] ?? ''));
            $href = trim((string)($it['href'] ?? '')) ?: trim((string)($it['cta']['href'] ?? ''));
            $copy = '';
            if (trim((string)($it['eyebrow'] ?? '')) !== '') $copy .= '<p class="tf-bn-eyebrow">' . self::esc($it['eyebrow']) . '</p>';
            if ($head !== '')                                  $copy .= '<h3 class="tf-bn-h">' . self::esc($head) . '</h3>';
            if (trim((string)($it['sub'] ?? '')) !== '')     $copy .= '<p class="tf-bn-sub">' . self::esc($it['sub']) . '</p>';
            $b = self::btn($it['cta'] ?? null, true);
            if ($b !== '') $copy .= '<div class="tf-bn-cta">' . $b . '</div>';
            $cells .= '<div class="tf-bn">'
                . '<img src="' . self::esc(self::media($it['image'])) . '" alt="' . self::esc($head !== '' ? $head : ($p['heading'] ?? '')) . '" loading="lazy" style="' . self::imgFit($it['imageFit'] ?? null) . '">'
                // The whole banner is clickable through an overlay link, not a
                // wrapping <a> — that would nest the button's own link inside it.
                . ($href !== '' ? '<a class="tf-bn-hit" href="' . self::esc($href) . '" aria-label="' . self::esc($head !== '' ? $head : 'Open') . '"></a>' : '')
                . ($copy !== '' ? '<div class="tf-bn-copy">' . $copy . '</div>' : '')
                . '</div>';
        }
        $body = '<div class="tf-bns" style="--bn-cols:' . $cols . ';--bn-ratio:' . $ratio . '">' . $cells . '</div>';
        return self::shell($s, self::sectionHeader($p['label'] ?? null, $p['heading'] ?? null, $p['sub'] ?? null) . $body);
    }

    /** Trust badges — a row of small icon promises above the footer. */
    private static function secFeatures(array $s, array $doc): string
    {
        $p = $s['props'] ?? [];
        $variant = ($s['variant'] ?? 'strip') === 'cards' ? 'cards' : 'strip';
        $items = array_values(array_filter((array)($p['items'] ?? []), fn($it) => is_array($it) && trim((string)($it['title'] ?? '')) !== ''));
        if (!$items) return '';
        $cells = '';
        foreach ($items as $it) {
            $path = self::FEATURE_ICONS[(string)($it['icon'] ?? '')] ?? self::FEATURE_ICONS['gem'];
            $cells .= '<div class="tf-feat">'
                . '<span class="tf-feat-ic" aria-hidden="true"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">' . $path . '</svg></span>'
                . '<p class="tf-feat-t">' . self::esc($it['title']) . '</p>'
                . (trim((string)($it['text'] ?? '')) !== '' ? '<p class="tf-feat-x">' . self::esc($it['text']) . '</p>' : '')
                . '</div>';
        }
        $body = '<div class="tf-feats tf-feats-' . $variant . '" style="--feat-n:' . min(count($items), 6) . '">' . $cells . '</div>';
        return self::shell($s, self::sectionHeader($p['label'] ?? null, $p['heading'] ?? null, $p['sub'] ?? null) . $body);
    }

    /** wa.me link pre-filled with the item's name and price; '' when the site has no number. */
    private static function waOrderHref(array $item, array $biz): string
    {
        $num = preg_replace('/\D/', '', (string)($biz['whatsapp'] ?? $biz['phone'] ?? ''));
        if ($num === '') return '';
        $title = trim((string)($item['title'] ?? ''));
        $price = trim((string)($item['price'] ?? ''));
        $msg = 'Hi, I am interested in *' . ($title !== '' ? $title : 'this item') . '*'
             . ($price !== '' ? ' (' . $price . ')' : '') . '. Is it available?';
        return 'https://wa.me/' . $num . '?text=' . rawurlencode($msg);
    }

    /**
     * A storefront product card (products cardStyle "shop"): tall photo with an
     * optional ribbon badge, the price above a two-line name, and a full-width
     * soft button — the card the big online jewellery and fashion stores use.
     * The top of the card links to the product page; the button repeats that
     * link, or opens WhatsApp in WhatsApp-order mode.
     */
    private static function shopCard(array $it, string $href, array $p, array $biz, bool $waOrder): string
    {
        $img   = self::media($it['image'] ?? null);
        $title = self::esc($it['title'] ?? '');
        [$sell, $mrp, $off] = self::priceBits($it);

        $top = '<span class="tf-shop-media">'
             . ($img ? '<img src="' . self::esc($img) . '" alt="' . $title . '" loading="lazy" style="' . self::imgFit($p['imageFit'] ?? null) . '">' : '')
             . (trim((string)($it['badge'] ?? '')) !== '' ? '<span class="tf-shop-badge">' . self::esc($it['badge']) . '</span>' : '')
             . '</span><span class="tf-shop-body">'
             . ($sell !== '' || $mrp !== ''
                 ? '<span class="tf-shop-price">' . ($sell !== '' ? '<b>' . self::esc($sell) . '</b>' : '')
                   . ($mrp !== '' ? '<s>' . self::esc($mrp) . '</s>' : '') . ($off !== '' ? '<em>' . $off . '</em>' : '') . '</span>'
                 : '')
             . '<span class="tf-shop-title">' . $title . '</span>'
             . (trim((string)($it['meta'] ?? '')) !== '' ? '<span class="tf-shop-meta">' . self::esc($it['meta']) . '</span>' : '')
             . '</span>';
        $c = '<div class="tf-shop">' . ($href !== ''
            ? '<a class="tf-shop-a" href="' . self::esc($href) . '">' . $top . '</a>'
            : '<div class="tf-shop-a">' . $top . '</div>');

        if ($waOrder) {
            $wa = self::waOrderHref($it, $biz);
            if ($wa !== '') {
                $c .= '<a class="tf-shop-btn" href="' . self::esc($wa) . '" target="_blank" rel="noopener noreferrer">'
                    . self::esc(trim((string)($p['orderLabel'] ?? '')) ?: 'Order on WhatsApp') . '</a>';
            }
        } elseif ($href !== '') {
            $c .= '<a class="tf-shop-btn" href="' . self::esc($href) . '">' . self::esc(trim((string)($p['cardButton'] ?? '')) ?: 'View details') . '</a>';
        } elseif (!empty($it['cta']['text']) && !empty($it['cta']['href'])) {
            $nt = !empty($it['cta']['newTab']) ? ' target="_blank" rel="noopener noreferrer"' : '';
            $c .= '<a class="tf-shop-btn" href="' . self::esc($it['cta']['href']) . '"' . $nt . '>' . self::esc($it['cta']['text']) . '</a>';
        }
        return $c . '</div>';
    }

    private static function secStats(array $s, array $doc): string
    {
        $p = $s['props'] ?? [];
        $items = $p['items'] ?? [];
        if (!$items) return '';
        $onDark = self::isDarkBg($s);
        $countUp = ($p['countUp'] ?? true) !== false;
        $cells = '';
        foreach ($items as $it) {
            $raw = (float)($it['value'] ?? 0);
            $val = number_format($raw);
            // Count-up: keep the final value in the HTML (SEO + no-JS), and let the
            // script animate from 0 when the section scrolls into view.
            $num = $countUp
                ? '<span class="tf-count" data-count="' . (int)round($raw) . '">' . self::esc($val) . '</span>'
                : self::esc($val);
            $suffixColor = $onDark ? 'inherit' : 'var(--color-accent)';
            $cells .= '<div style="text-align:center">'
                    . '<div style="font-family:var(--font-heading);font-size:34px;font-weight:700">' . $num
                    . '<span style="color:' . $suffixColor . '">' . self::esc($it['suffix'] ?? '') . '</span></div>'
                    . '<p style="margin-top:4px;font-size:14px;' . ($onDark ? 'opacity:.85' : 'color:var(--tf-text,var(--color-muted))') . '">' . self::esc($it['label'] ?? '') . '</p>'
                    . '</div>';
        }
        $n = count($items);
        $cls = $n >= 4 ? 'tf-c4' : ($n === 3 ? 'tf-c3' : 'tf-c2');
        $inner = self::sectionHeader($p['label'] ?? null, $p['heading'] ?? null, $p['sub'] ?? null, $onDark)
               . '<div class="tf-grid ' . $cls . '">' . $cells . '</div>';
        return self::shell($s, $inner);
    }

    private static function secTeam(array $s, array $doc): string
    {
        $p = $s['props'] ?? [];
        $items = $p['items'] ?? [];
        if (!$items) return '';
        $variant = $s['variant'] ?? 'cards-3';
        $round = $variant === 'circles';
        $cls = $variant === 'cards-4' ? 'tf-c4' : ($variant === 'cards-2' ? 'tf-c2' : 'tf-c3');

        $cards = [];
        foreach ($items as $pr) {
            $img = self::media($pr['photo'] ?? null);
            $c = '<div class="tf-card" style="padding:24px;text-align:center">';
            if ($img) {
                $st = ($round ? 'margin:0 auto;height:112px;width:112px;border-radius:999px;' : 'margin:0 auto;width:100%;border-radius:var(--radius);' . self::ratioCss($p['imageRatio'] ?? null, 'height:160px;')) . self::imgFit($p['imageFit'] ?? null, $round ? '999px' : 'var(--radius)');
                $c .= '<img src="' . self::esc($img) . '" alt="' . self::esc($pr['name'] ?? '') . '" loading="lazy" style="' . $st . '">';
            } else {
                $initial = function_exists('mb_substr') ? mb_substr($pr['name'] ?? '?', 0, 1, 'UTF-8') : substr($pr['name'] ?? '?', 0, 1);
                $c .= '<div aria-hidden="true" style="margin:0 auto;height:112px;width:112px;display:flex;align-items:center;justify-content:center;font-size:24px;font-weight:700;border-radius:999px;background:var(--color-surface);color:var(--tf-text,var(--color-muted))">' . self::esc($initial) . '</div>';
            }
            $c .= '<h3 style="margin-top:16px;font-family:var(--font-heading);font-size:16px;font-weight:600">' . self::esc($pr['name'] ?? '') . '</h3>';
            if (!empty($pr['role'])) $c .= '<p style="margin-top:2px;font-size:12px;font-weight:600;color:var(--color-accent)">' . self::esc($pr['role']) . '</p>';
            if (!empty($pr['meta'])) $c .= '<p style="margin-top:8px;display:inline-block;padding:4px 12px;font-size:12px;background:var(--color-surface);color:var(--tf-text,var(--color-muted));border-radius:999px">' . self::esc($pr['meta']) . '</p>';
            if (!empty($pr['bio'])) $c .= '<p style="margin-top:12px;font-size:14px;color:var(--tf-text,var(--color-muted))">' . self::esc($pr['bio']) . '</p>';
            if (!empty($pr['link']['text'])) { $l = $pr['link']; $l['style'] = $l['style'] ?? 'link'; $c .= '<div style="margin-top:12px">' . self::btn($l) . '</div>'; }
            $c .= '</div>';
            $cards[] = $c;
        }
        $body = $variant === 'marquee' ? self::marquee($cards)
            : ($variant === 'slider' ? self::carousel($cards)
            : '<div class="tf-grid ' . $cls . '">' . implode('', $cards) . '</div>');
        $inner = self::sectionHeader($p['label'] ?? null, $p['heading'] ?? null, $p['sub'] ?? null) . $body;
        return self::shell($s, $inner);
    }

    private static function secTestimonials(array $s, array $doc): string
    {
        $p = $s['props'] ?? [];
        $items = $p['items'] ?? [];
        if (!$items) return '';
        $variant = $s['variant'] ?? 'cards-3';

        $stars = function ($n) {
            $full = max(0, min(5, (int)round($n ?: 5)));
            return '<div style="color:var(--color-accent);font-size:14px">' . str_repeat('★', $full) . '<span style="opacity:.25">' . str_repeat('★', 5 - $full) . '</span></div>';
        };

        if ($variant === 'single') {
            $r = $items[0];
            $photo = self::media($r['photo'] ?? null);
            $inner = self::sectionHeader($p['label'] ?? null, $p['heading'] ?? null, $p['sub'] ?? null)
                   . '<figure style="max-width:768px;margin:0 auto">'
                   . '<blockquote style="font-family:var(--font-heading);font-size:22px;line-height:1.5;margin:0">“' . self::esc($r['quote'] ?? '') . '”</blockquote>'
                   . '<figcaption style="margin-top:20px;display:flex;align-items:center;justify-content:center;gap:12px">'
                   . ($photo ? '<img src="' . self::esc($photo) . '" alt="' . self::esc($r['name'] ?? '') . '" loading="lazy" style="height:44px;width:44px;border-radius:999px;object-fit:cover">' : '')
                   . '<span><span style="display:block;font-size:14px;font-weight:600">' . self::esc($r['name'] ?? '') . '</span>'
                   . (!empty($r['role']) ? '<span style="display:block;font-size:12px;color:var(--tf-text,var(--color-muted))">' . self::esc($r['role']) . '</span>' : '') . '</span>'
                   . '</figcaption></figure>';
            return self::shell($s, $inner);
        }

        $cards = [];
        foreach ($items as $r) {
            $photo = self::media($r['photo'] ?? null);
            $cards[] = '<div class="tf-card" style="padding:24px;text-align:left">' . $stars($r['rating'] ?? 5)
                    . '<blockquote style="margin:12px 0 0;font-size:14px;color:var(--tf-text,var(--color-muted))">“' . self::esc($r['quote'] ?? '') . '”</blockquote>'
                    . '<div style="margin-top:20px;display:flex;align-items:center;gap:12px">'
                    . ($photo ? '<img src="' . self::esc($photo) . '" alt="' . self::esc($r['name'] ?? '') . '" loading="lazy" style="height:40px;width:40px;border-radius:999px;object-fit:cover">' : '')
                    . '<div><p style="font-size:14px;font-weight:600;margin:0">' . self::esc($r['name'] ?? '') . '</p>'
                    . (!empty($r['role']) ? '<p style="font-size:12px;color:var(--tf-text,var(--color-muted));margin:0">' . self::esc($r['role']) . '</p>' : '') . '</div>'
                    . '</div></div>';
        }
        $body = $variant === 'marquee' ? self::marquee($cards)
            : ($variant === 'slider' ? self::carousel($cards, ($p['autoplay'] ?? true) === false ? 0 : 4000)
            : '<div class="tf-grid tf-c3">' . implode('', $cards) . '</div>');
        $inner = self::sectionHeader($p['label'] ?? null, $p['heading'] ?? null, $p['sub'] ?? null) . $body;
        return self::shell($s, $inner);
    }

    private static function secFaq(array $s, array $doc): string
    {
        $p = $s['props'] ?? [];
        $items = array_values(array_filter($p['items'] ?? [], fn($i) => !empty($i['q'])));
        if (!$items) return '';
        $two = ($s['variant'] ?? '') === 'two-column';
        $openFirst = ($p['openFirst'] ?? true) !== false;

        $rows = '';
        foreach ($items as $i => $it) {
            $open = ($openFirst && $i === 0) ? ' open' : '';
            $rows .= '<details class="tf-faqitem"' . $open . ' style="background:var(--color-bg);border:1px solid var(--color-border);border-radius:var(--radius);text-align:left">'
                   . '<summary style="font-family:var(--font-heading);padding:16px 20px;font-size:14px;font-weight:600"><span style="display:flex;justify-content:space-between;gap:16px">' . self::esc($it['q']) . '<span class="tf-faq-plus" aria-hidden="true" style="color:var(--color-accent)">+</span></span></summary>'
                   . (!empty($it['a']) ? '<p style="white-space:pre-line;padding:0 20px 16px;font-size:14px;color:var(--tf-text,var(--color-muted))">' . self::esc($it['a']) . '</p>' : '')
                   . '</details>';
        }
        $wrap = $two ? '<div class="tf-grid tf-c2 tf-faq">' : '<div class="tf-faq" style="max-width:768px;margin:0 auto;display:grid;gap:12px">';
        $inner = self::sectionHeader($p['label'] ?? null, $p['heading'] ?? null, $p['sub'] ?? null) . $wrap . $rows . '</div>';
        return self::shell($s, $inner);
    }

    private static function secCta(array $s, array $doc): string
    {
        $p = $s['props'] ?? [];
        $biz = $doc['business'] ?? [];
        $variant = $s['variant'] ?? 'banner';
        $onDark = self::isDarkBg($s) || $variant === 'card';

        $btns = self::btn($p['ctaPrimary'] ?? null, $onDark) . self::btn($p['ctaSecondary'] ?? null, $onDark, 'ghost');
        if (!empty($p['showWhatsapp']) && !empty($biz['whatsapp'])) $btns .= self::btn(['text'=>'WhatsApp','href'=>'https://wa.me/' . preg_replace('/\D/', '', $biz['whatsapp']),'newTab'=>true,'style'=>'ghost'], $onDark);
        if (!empty($p['showCall']) && !empty($biz['phone'])) $btns .= self::btn(['text'=>'Call now','href'=>'tel:' . $biz['phone'],'style'=>'ghost'], $onDark);

        $text = '<div>' . (!empty($p['heading']) ? '<h2 class="tf-h2">' . self::esc($p['heading']) . '</h2>' : '')
              . (!empty($p['sub']) ? '<p class="tf-lead">' . self::esc($p['sub']) . '</p>' : '') . '</div>';

        if ($variant === 'split') {
            return self::shell($s, '<div class="tf-two" style="text-align:left">' . $text . '<div class="tf-btns" style="justify-content:flex-end">' . $btns . '</div></div>');
        }
        $inner = '<div style="max-width:640px;margin:0 auto">' . $text . '<div class="tf-btns" style="margin-top:24px">' . $btns . '</div></div>';
        if ($variant === 'card') {
            return self::shell($s, '<div style="max-width:768px;margin:0 auto;padding:32px;background:var(--color-primary);color:var(--color-primary-fg);border-radius:var(--radius)">' . $inner . '</div>');
        }
        return self::shell($s, $inner);
    }

    /** Built-in contact form, used when the site has no custom form. Its id and
     *  field set match the fallback in form-submit.php. */
    private static function defaultContactForm(): array
    {
        return [
            'id' => 'contact',
            'submitText' => 'Send message',
            'successMessage' => 'Thank you — your message has been sent.',
            'fields' => [
                ['name'=>'name','label'=>'Your name','type'=>'text','required'=>true],
                ['name'=>'phone','label'=>'Phone','type'=>'tel','required'=>true],
                ['name'=>'email','label'=>'Email','type'=>'email'],
                ['name'=>'message','label'=>'Message','type'=>'textarea'],
            ],
        ];
    }

    /** Optional customer login/signup for e-commerce sites (token in localStorage). */
    private static function secAccount(array $s, array $doc): string
    {
        $p    = $s['props'] ?? [];
        $uid  = 'ac' . substr(md5(($s['id'] ?? '') . 'acc'), 0, 6);
        $ph   = ($p['showPhone'] ?? true) !== false;
        $in   = 'width:100%;padding:10px 12px;font-size:14px;margin-bottom:10px;border:1px solid var(--color-border);border-radius:var(--radius);background:var(--color-bg);color:var(--color-text)';
        $btn  = 'width:100%;padding:12px;font-size:14px;font-weight:600;border:none;border-radius:var(--radius);background:var(--color-primary);color:var(--color-primary-fg);cursor:pointer';
        $tab  = 'flex:1;padding:8px;font-size:14px;font-weight:600;border-radius:var(--radius);cursor:pointer';

        // "Continue with Google" — only when the OAuth client is configured. It
        // links to the fixed start endpoint on the API origin; the page's JS
        // appends the current ?next so login returns the customer to their spot.
        $googleOn = defined('GOOGLE_LOGIN_CLIENT_ID') && GOOGLE_LOGIN_CLIENT_ID !== '' && defined('GOOGLE_LOGIN_CLIENT_SECRET') && GOOGLE_LOGIN_CLIENT_SECRET !== '';
        $gbtn = '';
        if ($googleOn) {
            $gsvg = '<svg width="18" height="18" viewBox="0 0 48 48" style="flex-shrink:0">'
                . '<path fill="#FFC107" d="M43.611 20.083H42V20H24v8h11.303c-1.649 4.657-6.08 8-11.303 8-6.627 0-12-5.373-12-12s5.373-12 12-12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4 12.955 4 4 12.955 4 24s8.955 20 20 20 20-8.955 20-20c0-1.341-.138-2.65-.389-3.917z"/>'
                . '<path fill="#FF3D00" d="M6.306 14.691l6.571 4.819C14.655 15.108 18.961 12 24 12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4 16.318 4 9.656 8.337 6.306 14.691z"/>'
                . '<path fill="#4CAF50" d="M24 44c5.166 0 9.86-1.977 13.409-5.192l-6.19-5.238C29.211 35.091 26.715 36 24 36c-5.202 0-9.619-3.317-11.283-7.946l-6.522 5.025C9.505 39.556 16.227 44 24 44z"/>'
                . '<path fill="#1976D2" d="M43.611 20.083H42V20H24v8h11.303c-.792 2.237-2.231 4.166-4.087 5.571l6.19 5.238C36.971 39.205 44 34 44 24c0-1.341-.138-2.65-.389-3.917z"/>'
                . '</svg>';
            $gurl = self::esc(self::apiBase() . '/api/sites/google-auth-start.php?site=' . rawurlencode(self::$slug));
            $gbtn = '<a data-google href="' . $gurl . '" style="display:flex;align-items:center;justify-content:center;gap:10px;width:100%;padding:11px;font-size:14px;font-weight:600;text-decoration:none;color:var(--color-text);background:var(--color-bg);border:1px solid var(--color-border);border-radius:var(--radius)">' . $gsvg . 'Continue with Google</a>'
                . '<div style="display:flex;align-items:center;gap:10px;margin:16px 0;color:var(--color-muted);font-size:12px"><span style="flex:1;height:1px;background:var(--color-border)"></span>OR<span style="flex:1;height:1px;background:var(--color-border)"></span></div>';
        }

        $card = '<div id="' . $uid . '" style="max-width:420px;margin:0 auto;text-align:left;padding:28px;background:var(--color-bg);border:1px solid var(--color-border);border-radius:var(--radius);box-shadow:0 8px 30px rgba(16,24,40,.08)">'
            // Signed-in view (shown by JS when a token exists).
            . '<div data-view="me" style="display:none;text-align:center">'
            .   '<p style="font-size:18px;font-weight:700;margin:0">Hi <span data-me-name></span> &#128075;</p>'
            .   '<p style="margin:6px 0 0;font-size:14px;color:var(--color-muted)">You&#39;re signed in.</p>'
            .   '<button type="button" data-act="signout" style="margin-top:18px;padding:10px 20px;font-size:14px;font-weight:600;border:none;border-radius:var(--radius);background:var(--color-primary);color:var(--color-primary-fg);cursor:pointer">Sign out</button>'
            .   '<a href="/orders" style="display:block;margin-top:22px;text-align:center;background:var(--color-primary);color:var(--color-primary-fg);border-radius:var(--radius);padding:12px;font-size:14px;font-weight:700;text-decoration:none">&#128230;  View Orders</a>'
            . '</div>'
            // Auth forms.
            . '<div data-view="auth">'
            .   $gbtn
            .   '<div style="display:flex;gap:8px;margin-bottom:18px">'
            .     '<button type="button" data-tab="login" style="' . $tab . ';background:var(--color-primary);color:var(--color-primary-fg);border:none">Sign in</button>'
            .     '<button type="button" data-tab="signup" style="' . $tab . ';background:transparent;color:var(--color-text);border:1px solid var(--color-border)">Create account</button>'
            .   '</div>'
            .   '<form data-form>'
            .     '<input data-signup name="name" placeholder="Your name" style="' . $in . ';display:none">'
            .     '<input name="email" type="email" placeholder="Email" required style="' . $in . '">'
            .     ($ph ? '<input data-signup name="phone" type="tel" placeholder="Phone (optional)" style="' . $in . ';display:none">' : '')
            .     '<input name="password" type="password" placeholder="Password" required minlength="6" style="' . $in . '">'
            .     '<button type="submit" data-submit style="' . $btn . '">Sign in</button>'
            .     '<p data-msg style="margin:12px 0 0;text-align:center;font-size:13px;color:#dc2626"></p>'
            .   '</form>'
            . '</div>'
            . '</div>';

        $cfg = json_encode(['u' => $uid, 'site' => self::$slug, 'api' => self::apiBase()]);
        $script = '<script>(function(){var C=' . $cfg . ';var root=document.getElementById(C.u);if(!root)return;'
            . 'var KEY="tf_customer_"+C.site,mode="login";'
            . 'function $(s){return root.querySelector(s);}function all(s){return root.querySelectorAll(s);}'
            . 'function esc(s){var d=document.createElement("div");d.textContent=(s==null?"":s);return d.innerHTML;}'
            . 'function murl(v){if(!v)return "";if(/^https?:\\/\\//.test(v))return v;var m=/^media:(\\d+)$/.exec(v);return m?(C.api+"/api/sites/media.php?id="+m[1]):"";}'
            . 'var OSTEPS=[["new","Received"],["confirmed","Confirmed"],["completed","Completed"]];'
            . 'function otimeline(st){if(st==="cancelled")return "<div style=\"margin-top:10px;font-size:12px;font-weight:700;color:#dc2626\">Cancelled</div>";var idx={"new":0,"confirmed":1,"completed":2}[st];if(idx==null)idx=0;var h="<div style=\"display:flex;gap:6px;margin-top:10px\">";for(var i=0;i<OSTEPS.length;i++){var on=i<=idx;h+="<div style=\"flex:1;text-align:center\"><div style=\"height:4px;border-radius:2px;background:"+(on?"var(--color-primary)":"var(--color-border)")+"\"></div><div style=\"font-size:10px;margin-top:4px;font-weight:"+(on?"700":"500")+";color:"+(on?"var(--color-primary)":"var(--color-muted)")+"\">"+OSTEPS[i][1]+"</div></div>";}return h+"</div>";}'
            . 'function show(me){$("[data-view=me]").style.display=me?"block":"none";$("[data-view=auth]").style.display=me?"none":"block";if(me&&me.name)$("[data-me-name]").textContent=me.name;}'
            . 'function get(){try{return JSON.parse(localStorage.getItem(KEY));}catch(e){return null;}}'
            . 'function loadOrders(){var a=get();if(!a||!a.token)return;var box=$("[data-orders]");if(!box)return;box.innerHTML="<p style=\"font-size:13px;color:var(--color-muted)\">Loading your orders…</p>";'
            . 'fetch(C.api+"/api/sites/customer-orders.php",{method:"POST",headers:{"Content-Type":"application/json"},body:JSON.stringify({site:C.site,token:a.token})}).then(function(r){return r.json();}).then(function(res){'
            . 'var os=(res&&res.data&&res.data.orders)||[];'
            . 'if(!os.length){box.innerHTML="<p style=\"font-size:15px;font-weight:700;margin:0 0 8px\">My Orders</p><p style=\"font-size:13px;color:var(--color-muted);margin:0\">No orders yet.</p>";return;}'
            . 'var h="<p style=\"font-size:15px;font-weight:700;margin:0 0 12px\">My Orders</p>";'
            . 'os.forEach(function(o){var img=murl(o.item_image);var canR=(o.status==="completed")&&o.item_slug;'
            . 'h+="<div style=\"border:1px solid var(--color-border);border-radius:var(--radius);padding:12px;margin-bottom:10px\">";'
            . 'h+="<div style=\"display:flex;gap:12px\">";'
            . 'if(img)h+="<img src=\""+esc(img)+"\" alt=\"\" style=\"width:64px;height:64px;object-fit:cover;border-radius:8px;flex-shrink:0\">";'
            . 'h+="<div style=\"flex:1;min-width:0\"><div style=\"display:flex;justify-content:space-between;gap:8px\"><strong style=\"font-size:14px\">"+esc(o.item_title||"Order")+"</strong><span style=\"font-size:12px;color:var(--color-muted)\">#"+esc(o.id)+"</span></div>";'
            . 'h+="<div style=\"font-size:13px;color:var(--color-muted);margin-top:3px\">"+esc(o.price||"")+((o.quantity>1)?(" × "+esc(o.quantity)):"")+(o.option_value?(" · "+esc(o.option_value)):"")+"</div></div></div>";'
            . 'h+=otimeline(o.status);'
            . 'if(canR){h+="<button type=\"button\" data-review-toggle style=\"margin-top:10px;background:none;border:1px solid var(--color-primary);color:var(--color-primary);border-radius:8px;padding:6px 12px;font-size:12px;font-weight:700;cursor:pointer\">&#9733; Write a review</button>";'
            . 'h+="<div data-review-form data-slug=\""+esc(o.item_slug)+"\" data-rating=\"5\" style=\"display:none;margin-top:10px\"><div data-stars style=\"font-size:22px;color:#f59e0b;cursor:pointer;letter-spacing:3px\">";'
            . 'for(var s=1;s<=5;s++)h+="<span data-star=\""+s+"\">&#9733;</span>";'
            . 'h+="</div><textarea data-review-text placeholder=\"Share your experience…\" style=\"width:100%;padding:8px 10px;font-size:13px;border:1px solid var(--color-border);border-radius:8px;background:var(--color-bg);color:var(--color-text);margin-top:8px;min-height:60px\"></textarea>";'
            . 'h+="<button type=\"button\" data-review-submit style=\"margin-top:6px;background:var(--color-primary);color:var(--color-primary-fg);border:none;border-radius:8px;padding:8px 14px;font-size:13px;font-weight:700;cursor:pointer\">Submit review</button><p data-review-msg style=\"font-size:12px;margin:6px 0 0\"></p></div>";}'
            . 'h+="</div>";});'
            . 'box.innerHTML=h;'
            . 'if(!box.__wired){box.__wired=1;box.addEventListener("click",function(e){var t=e.target;'
            . 'var tog=t.closest?t.closest("[data-review-toggle]"):null;if(tog){var f=tog.parentNode.querySelector("[data-review-form]");if(f)f.style.display=(f.style.display==="none")?"block":"none";return;}'
            . 'var star=t.closest?t.closest("[data-star]"):null;if(star){var form=star.closest("[data-review-form]");var val=parseInt(star.getAttribute("data-star"),10);form.setAttribute("data-rating",val);var sp=form.querySelectorAll("[data-star]");for(var i=0;i<sp.length;i++)sp[i].style.color=(i<val)?"#f59e0b":"#d1d5db";return;}'
            . 'var sub=t.closest?t.closest("[data-review-submit]"):null;if(sub){var form2=sub.closest("[data-review-form]");var msg=form2.querySelector("[data-review-msg]");var txt=(form2.querySelector("[data-review-text]").value||"").trim();var rating=parseInt(form2.getAttribute("data-rating"),10)||5;var slug=form2.getAttribute("data-slug");if(!txt){msg.style.color="#dc2626";msg.textContent="Please write your review.";return;}var acc=get();sub.disabled=true;sub.textContent="Submitting…";'
            . 'fetch(C.api+"/api/sites/review-submit.php",{method:"POST",headers:{"Content-Type":"application/json","Accept":"application/json"},body:JSON.stringify({site:C.site,item_slug:slug,name:(acc&&acc.name)||"Customer",rating:rating,comment:txt})}).then(function(r){return r.json();}).then(function(res){if(res&&res.success){form2.innerHTML="<p style=\"font-size:13px;color:#16a34a;font-weight:700\">&#10003; Thanks! Your review has been submitted.</p>";}else{msg.style.color="#dc2626";msg.textContent=(res&&res.message)||"Could not submit.";sub.disabled=false;sub.textContent="Submit review";}}).catch(function(){msg.style.color="#dc2626";msg.textContent="Connection error.";sub.disabled=false;sub.textContent="Submit review";});return;}'
            . '});}'
            . '}).catch(function(){box.innerHTML="";});}'
            . 'show(get());'
            // Carry the current ?next onto the Google button so login returns there.
            . 'var GP=new URLSearchParams(location.search);var gb=$("[data-google]");'
            . 'if(gb){var gn=GP.get("next");if(gn)gb.href+=(gb.href.indexOf("?")>-1?"&":"?")+"next="+encodeURIComponent(gn);}'
            // Finish a Google sign-in: swap the one-time handoff code for a token.
            . 'var GC=GP.get("tf_google");if(GC){var av=$("[data-view=auth]");if(av)av.innerHTML="<p style=\"text-align:center;font-size:14px;color:var(--color-muted)\">Signing you in…</p>";'
            . 'fetch(C.api+"/api/sites/customer-auth.php",{method:"POST",headers:{"Content-Type":"application/json","Accept":"application/json"},body:JSON.stringify({action:"google_exchange",site:C.site,code:GC})})'
            . '.then(function(r){return r.json();}).then(function(res){'
            . 'if(res&&res.success){localStorage.setItem(KEY,JSON.stringify({name:res.data.name,email:res.data.email,token:res.data.token}));'
            . 'var nx=GP.get("next");if(nx&&/^\\/[^\\/]/.test(nx)){location.href=nx;return;}'
            . 'history.replaceState(null,"",location.pathname);show({name:res.data.name});}'
            . 'else if(av){av.innerHTML="<p style=\"text-align:center;font-size:14px;color:#dc2626\">"+((res&&res.message)||"Could not sign in with Google.")+"</p>";}})'
            . '.catch(function(){if(av)av.innerHTML="<p style=\"text-align:center;font-size:14px;color:#dc2626\">Connection error.</p>";});}'
            . 'function setMode(m){mode=m;all("[data-tab]").forEach(function(b){var on=b.getAttribute("data-tab")===m;b.style.background=on?"var(--color-primary)":"transparent";b.style.color=on?"var(--color-primary-fg)":"var(--color-text)";b.style.border=on?"none":"1px solid var(--color-border)";});'
            . 'all("[data-signup]").forEach(function(el){el.style.display=(m==="signup")?"block":"none";el.required=(m==="signup"&&el.name==="name");});'
            . '$("[data-submit]").textContent=(m==="login")?"Sign in":"Create account";$("[data-msg]").textContent="";}'
            . 'all("[data-tab]").forEach(function(b){b.addEventListener("click",function(){setMode(b.getAttribute("data-tab"));});});'
            . 'var so=$("[data-act=signout]");if(so)so.addEventListener("click",function(){localStorage.removeItem(KEY);show(null);});'
            . '$("[data-form]").addEventListener("submit",function(e){e.preventDefault();var f=e.target,btn=$("[data-submit]"),msg=$("[data-msg]");'
            . 'var body={action:(mode==="signup"?"register":"login"),site:C.site,email:f.email.value,password:f.password.value};'
            . 'if(mode==="signup"){body.name=f.name.value;if(f.phone)body.phone=f.phone.value;}'
            . 'btn.disabled=true;var ob=btn.textContent;btn.textContent="Please wait…";'
            . 'fetch(C.api+"/api/sites/customer-auth.php",{method:"POST",headers:{"Content-Type":"application/json","Accept":"application/json"},body:JSON.stringify(body)})'
            . '.then(function(r){return r.json();}).then(function(res){btn.disabled=false;btn.textContent=ob;'
            . 'if(res&&res.success){localStorage.setItem(KEY,JSON.stringify({name:res.data.name,email:res.data.email,token:res.data.token}));'
            . 'var nx=null;try{nx=new URLSearchParams(location.search).get("next");}catch(e){}'
            . 'if(nx&&/^\\/[^\\/]/.test(nx)){location.href=nx;return;}show({name:res.data.name});}'
            . 'else{msg.textContent=(res&&res.message)||"Something went wrong.";}})'
            . '.catch(function(){btn.disabled=false;btn.textContent=ob;msg.textContent="Connection error.";});});})();</script>';

        $inner = self::sectionHeader(null, $p['heading'] ?? null, $p['sub'] ?? null, self::isDarkBg($s)) . $card . $script;
        return self::shell($s, $inner);
    }

    private static function secFeedback(array $s, array $doc): string
    {
        $p = $s['props'] ?? [];
        $uid = 'fb' . substr(md5(($s['id'] ?? '') . 'fb'), 0, 6);
        $submitText = self::esc($p['submitText'] ?? 'Send feedback');
        $okMsg = $p['successMessage'] ?? 'Thank you for your feedback!';
        $in = 'width:100%;padding:10px 12px;font-size:14px;margin-bottom:10px;border:1px solid var(--color-border);border-radius:var(--radius);background:var(--color-bg);color:var(--color-text)';
        $btnCss = 'width:100%;padding:12px;font-size:15px;font-weight:700;border:none;border-radius:var(--radius);background:var(--color-primary);color:var(--color-primary-fg);cursor:pointer';

        // ---- Survey grid variant: customisable rows × columns of radio choices ----
        if (($s['variant'] ?? '') === 'survey') {
            $rows = array_values(array_filter(array_map(function ($v) { return trim((string)$v); }, (array)($p['rows'] ?? []))));
            $cols = array_values(array_filter(array_map(function ($v) { return trim((string)$v); }, (array)($p['columns'] ?? []))));
            if (!$cols) $cols = ['Amazing', 'Good', 'Decent', 'Disappointing'];
            if (!$rows) $rows = ['Overall Experience'];
            $commentLabel = self::esc($p['commentLabel'] ?? 'Any comments, questions or suggestions?');
            $nameEl  = (($p['askName'] ?? true) !== false)    ? '<input data-f-name placeholder="Your name" style="' . $in . '">' : '';
            $phoneEl = (($p['askContact'] ?? true) !== false) ? '<input data-f-phone type="tel" placeholder="Contact number" style="' . $in . '">' : '';

            $th = '<th style="padding:8px"></th>';
            foreach ($cols as $c) $th .= '<th style="padding:8px 10px;font-size:13px;font-weight:700;color:var(--color-primary);text-align:center">' . self::esc($c) . '</th>';
            $trs = '';
            foreach ($rows as $ri => $rw) {
                $tds = '<td style="padding:10px 8px;font-size:14px;color:var(--color-text)">' . self::esc($rw) . '</td>';
                foreach ($cols as $ci => $c) {
                    $tds .= '<td style="text-align:center;padding:10px 8px"><input type="radio" name="fb' . $uid . '_' . $ri . '" data-col="' . $ci . '" value="' . self::esc($c) . '" style="width:18px;height:18px;cursor:pointer"></td>';
                }
                $trs .= '<tr style="border-top:1px solid var(--color-border)">' . $tds . '</tr>';
            }

            $form = '<div id="' . $uid . '" style="max-width:660px;margin:0 auto;text-align:left">'
                . '<div style="overflow-x:auto"><table style="width:100%;border-collapse:collapse">'
                . '<thead><tr>' . $th . '</tr></thead><tbody>' . $trs . '</tbody></table></div>'
                . '<label style="display:block;margin:16px 0 6px;font-size:14px;font-weight:600">' . $commentLabel . '</label>'
                . '<textarea data-f-msg rows="4" style="' . $in . '"></textarea>'
                . $nameEl . $phoneEl
                . '<button type="button" data-f-submit style="' . $btnCss . '">' . $submitText . '</button>'
                . '<p data-f-out style="margin:10px 0 0;text-align:center;font-size:13px;font-weight:600"></p>'
                . '</div>';

            $cfg = json_encode(['u' => $uid, 'site' => self::$slug, 'api' => self::apiBase(), 'ok' => $okMsg, 'rows' => $rows, 'cols' => count($cols)]);
            $script = '<script>(function(){var C=' . $cfg . ';var r=document.getElementById(C.u);if(!r)return;function q(s){return r.querySelector(s);}'
                . 'var btn=q("[data-f-submit]"),out=q("[data-f-out]");'
                . 'btn.addEventListener("click",function(){var lines=[],sum=0,cnt=0;'
                . 'for(var i=0;i<C.rows.length;i++){var sel=r.querySelector("input[name=fb"+C.u+"_"+i+"]:checked");if(sel){lines.push(C.rows[i]+": "+sel.value);var ci=parseInt(sel.getAttribute("data-col"),10);var sc=Math.round(((C.cols-1-ci)/Math.max(1,C.cols-1))*4)+1;sum+=sc;cnt++;}else{lines.push(C.rows[i]+": —");}}'
                . 'var comment=(q("[data-f-msg]").value||"").trim();'
                . 'var nEl=q("[data-f-name]"),pEl=q("[data-f-phone]");var name=nEl?(nEl.value||"").trim():"";var phone=pEl?(pEl.value||"").trim():"";'
                . 'if(!cnt&&!comment){out.style.color="#dc2626";out.textContent="Please rate at least one item or leave a comment.";return;}'
                . 'var message=lines.join("\\n")+(comment?("\\n\\nComment: "+comment):"");var rating=cnt?Math.round(sum/cnt):5;'
                . 'btn.disabled=true;var ob=btn.textContent;btn.textContent="Sending…";'
                . 'fetch(C.api+"/api/sites/feedback-submit.php",{method:"POST",headers:{"Content-Type":"application/json","Accept":"application/json"},body:JSON.stringify({site:C.site,name:name,email:"",phone:phone,rating:rating,message:message})})'
                . '.then(function(x){return x.json();}).then(function(res){if(res&&res.success){r.innerHTML="<p style=\"text-align:center;font-size:15px;font-weight:700;color:#16a34a;padding:24px 0\">&#10003; "+C.ok+"</p>";}else{out.style.color="#dc2626";out.textContent=(res&&res.message)||"Could not send.";btn.disabled=false;btn.textContent=ob;}})'
                . '.catch(function(){out.style.color="#dc2626";out.textContent="Connection error.";btn.disabled=false;btn.textContent=ob;});});})();</script>';

            $inner = self::sectionHeader($p['label'] ?? null, $p['heading'] ?? null, $p['sub'] ?? null, self::isDarkBg($s)) . $form . $script;
            return self::shell($s, $inner);
        }

        // ---- default: star-rating form ----
        $showRating = ($p['showRating'] ?? true) !== false;
        $showEmail  = ($p['showEmail'] ?? true) !== false;

        $stars = '';
        if ($showRating) {
            $stars = '<div data-stars style="font-size:30px;color:#f59e0b;cursor:pointer;letter-spacing:5px;margin-bottom:12px;text-align:center">';
            for ($i = 1; $i <= 5; $i++) $stars .= '<span data-star="' . $i . '">&#9733;</span>';
            $stars .= '</div>';
        }

        $form = '<div id="' . $uid . '" data-rating="5" style="max-width:460px;margin:0 auto;text-align:left;padding:24px;background:var(--color-bg);border:1px solid var(--color-border);border-radius:var(--radius);box-shadow:0 8px 30px rgba(16,24,40,.06)">'
            . $stars
            . '<input data-f-name placeholder="Your name" style="' . $in . '">'
            . ($showEmail ? '<input data-f-email type="email" placeholder="Email (optional)" style="' . $in . '">' : '')
            . '<textarea data-f-msg placeholder="Your feedback…" rows="4" style="' . $in . '"></textarea>'
            . '<button type="button" data-f-submit style="width:100%;padding:12px;font-size:15px;font-weight:700;border:none;border-radius:var(--radius);background:var(--color-primary);color:var(--color-primary-fg);cursor:pointer">' . $submitText . '</button>'
            . '<p data-f-out style="margin:10px 0 0;text-align:center;font-size:13px;font-weight:600"></p>'
            . '</div>';

        $cfg = json_encode(['u' => $uid, 'site' => self::$slug, 'api' => self::apiBase(), 'ok' => $p['successMessage'] ?? 'Thank you for your feedback!']);
        $script = '<script>(function(){var C=' . $cfg . ';var r=document.getElementById(C.u);if(!r)return;'
            . 'function q(s){return r.querySelector(s);}'
            . 'var stars=r.querySelectorAll("[data-star]");for(var i=0;i<stars.length;i++){(function(el){el.addEventListener("click",function(){var v=parseInt(el.getAttribute("data-star"),10);r.setAttribute("data-rating",v);for(var j=0;j<stars.length;j++)stars[j].style.color=(j<v)?"#f59e0b":"#d1d5db";});})(stars[i]);}'
            . 'var btn=q("[data-f-submit]"),out=q("[data-f-out]");'
            . 'btn.addEventListener("click",function(){var name=(q("[data-f-name]").value||"").trim();var em=q("[data-f-email]");var email=em?(em.value||"").trim():"";var msg=(q("[data-f-msg]").value||"").trim();var rating=parseInt(r.getAttribute("data-rating"),10)||5;'
            . 'if(!name){out.style.color="#dc2626";out.textContent="Please enter your name.";return;}'
            . 'if(!msg){out.style.color="#dc2626";out.textContent="Please write your feedback.";return;}'
            . 'btn.disabled=true;var ob=btn.textContent;btn.textContent="Sending…";'
            . 'fetch(C.api+"/api/sites/feedback-submit.php",{method:"POST",headers:{"Content-Type":"application/json","Accept":"application/json"},body:JSON.stringify({site:C.site,name:name,email:email,rating:rating,message:msg})})'
            . '.then(function(x){return x.json();}).then(function(res){if(res&&res.success){r.innerHTML="<p style=\"text-align:center;font-size:15px;font-weight:700;color:#16a34a;padding:20px 0\">&#10003; "+C.ok+"</p>";}else{out.style.color="#dc2626";out.textContent=(res&&res.message)||"Could not send.";btn.disabled=false;btn.textContent=ob;}})'
            . '.catch(function(){out.style.color="#dc2626";out.textContent="Connection error.";btn.disabled=false;btn.textContent=ob;});});})();</script>';

        $inner = self::sectionHeader($p['label'] ?? null, $p['heading'] ?? null, $p['sub'] ?? null, self::isDarkBg($s)) . $form . $script;
        return self::shell($s, $inner);
    }

    private static function secContact(array $s, array $doc): string
    {
        $p = $s['props'] ?? [];
        $biz = $doc['business'] ?? [];
        $variant = $s['variant'] ?? 'form-map';

        // The form declared for this section (or the first one).
        $form = null;
        foreach (($doc['forms'] ?? []) as $f) {
            if (($f['id'] ?? null) === ($p['formId'] ?? null)) { $form = $f; break; }
        }
        if (!$form && !empty($doc['forms'])) $form = $doc['forms'][0];
        if (!$form) $form = self::defaultContactForm();  // no forms editor yet — always show a contact form

        // Contact detail rows.
        $rows = '';
        $add = function ($label, $val, $href = null) {
            $v = $href ? '<a href="' . self::esc($href) . '" style="font-size:14px;color:var(--color-text)">' . self::esc($val) . '</a>'
                       : '<p style="font-size:14px;color:var(--tf-text,var(--color-muted));margin:0">' . self::esc($val) . '</p>';
            return '<div><p style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--color-accent);margin:0">' . self::esc($label) . '</p>' . $v . '</div>';
        };
        if (($p['showPhone'] ?? true) !== false && !empty($biz['phone'])) $rows .= $add('Phone', $biz['phone'], 'tel:' . $biz['phone']);
        if (($p['showWhatsapp'] ?? true) !== false && !empty($biz['whatsapp'])) $rows .= $add('WhatsApp', $biz['whatsapp'], 'https://wa.me/' . preg_replace('/\D/', '', $biz['whatsapp']));
        if (($p['showEmail'] ?? true) !== false && !empty($biz['email'])) $rows .= $add('Email', $biz['email'], 'mailto:' . $biz['email']);
        if (($p['showAddress'] ?? true) !== false && !empty($biz['address'])) $rows .= $add('Address', $biz['address']);
        $details = '<div style="display:grid;gap:16px;text-align:left">' . $rows . '</div>';

        // Form.
        $formEl = '';
        if ($form) {
            $api = defined('SITE_URL') ? SITE_URL : 'https://app.tapify.co.in';
            $sent = $_GET['sent'] ?? null;
            $banner = '';
            if ($sent === '1' || $sent === '0') {
                $ok = $sent === '1';
                $msg = $ok ? ($form['successMessage'] ?? 'Thank you — your message has been sent.') : 'Sorry, your message could not be sent. Please try again.';
                $banner = '<div role="status" style="margin-bottom:16px;padding:12px 16px;font-size:14px;border-radius:var(--radius);border:1px solid ' . ($ok ? '#86efac' : '#fca5a5') . ';background:' . ($ok ? '#f0fdf4' : '#fef2f2') . ';color:' . ($ok ? '#166534' : '#991b1b') . '">' . self::esc($msg) . '</div>';
            }
            $fields = '';
            foreach (($form['fields'] ?? []) as $f) {
                $name = $f['name'] ?? ''; if ($name === '') continue;
                $req = !empty($f['required']) ? ' required' : '';
                $lbl = self::esc($f['label'] ?? $name) . (!empty($f['required']) ? ' *' : '');
                $ph = self::esc($f['placeholder'] ?? '');
                $inputStyle = 'width:100%;padding:8px 12px;font-size:14px;border:1px solid var(--color-border);border-radius:var(--radius);background:var(--color-bg);color:var(--color-text)';
                if (($f['type'] ?? '') === 'textarea') {
                    $ctrl = '<textarea id="' . self::esc($name) . '" name="' . self::esc($name) . '"' . $req . ' placeholder="' . $ph . '" rows="4" style="' . $inputStyle . '"></textarea>';
                } elseif (($f['type'] ?? '') === 'select') {
                    $opts = '';
                    foreach (($f['options'] ?? []) as $o) $opts .= '<option value="' . self::esc($o) . '">' . self::esc($o) . '</option>';
                    $ctrl = '<select id="' . self::esc($name) . '" name="' . self::esc($name) . '"' . $req . ' style="' . $inputStyle . '">' . $opts . '</select>';
                } else {
                    $type = self::esc($f['type'] ?? 'text');
                    $ctrl = '<input id="' . self::esc($name) . '" name="' . self::esc($name) . '" type="' . $type . '"' . $req . ' placeholder="' . $ph . '" style="' . $inputStyle . '">';
                }
                $fields .= '<div><label for="' . self::esc($name) . '" style="display:block;margin-bottom:4px;font-size:12px;font-weight:600">' . $lbl . '</label>' . $ctrl . '</div>';
            }
            $formEl = '<form method="post" action="' . self::esc($api) . '/api/sites/form-submit.php" style="display:grid;gap:12px;text-align:left">'
                    . $banner
                    . '<input type="hidden" name="form_id" value="' . self::esc($form['id'] ?? '') . '">'
                    . '<input type="hidden" name="site" value="' . self::esc(self::$slug) . '">'
                    . '<div aria-hidden="true" style="position:absolute;left:-9999px;width:1px;height:1px;overflow:hidden"><label for="' . self::esc($form['id'] ?? 'f') . '-hp">Leave empty</label><input id="' . self::esc($form['id'] ?? 'f') . '-hp" type="text" name="_hp" tabindex="-1" autocomplete="off"></div>'
                    . $fields
                    . '<button type="submit" class="tf-btn" style="background:var(--color-primary);color:var(--color-primary-fg);border-radius:var(--radius)">' . self::esc($form['submitText'] ?? 'Send') . '</button>'
                    . '</form>';
        } elseif (in_array($variant, ['form-map', 'form-only'], true)) {
            // No custom form configured — use the built-in enquiry form. It needs
            // no setup and lands in the dashboard's "Website Inquiries" page.
            $uid = 'iq' . substr(md5(($s['id'] ?? '') . 'inq'), 0, 6);
            $in  = 'width:100%;padding:10px 12px;font-size:14px;border:1px solid var(--color-border);border-radius:var(--radius);background:var(--color-bg);color:var(--color-text)';
            $lb  = 'display:block;margin-bottom:4px;font-size:12px;font-weight:600';
            $formEl = '<form id="' . $uid . '" style="display:grid;gap:12px;text-align:left">'
                . '<div><label style="' . $lb . '">Name *</label><input class="' . $uid . '-f" data-k="name" required style="' . $in . '"></div>'
                . '<div><label style="' . $lb . '">Email</label><input class="' . $uid . '-f" data-k="email" type="email" style="' . $in . '"></div>'
                . '<div><label style="' . $lb . '">Phone *</label><input class="' . $uid . '-f" data-k="phone" type="tel" required style="' . $in . '"></div>'
                . '<div><label style="' . $lb . '">Subject</label><input class="' . $uid . '-f" data-k="subject" style="' . $in . '"></div>'
                . '<div><label style="' . $lb . '">Message *</label><textarea class="' . $uid . '-f" data-k="message" rows="4" required style="' . $in . '"></textarea></div>'
                . '<button type="submit" style="padding:12px;font-size:15px;font-weight:600;border:none;border-radius:var(--radius);background:var(--color-primary);color:var(--color-primary-fg);cursor:pointer">'
                . self::esc($p['submitText'] ?? 'Send enquiry') . '</button>'
                . '<p id="' . $uid . '-msg" style="margin:0;font-size:13px;font-weight:600"></p></form>'
                . '<script>(function(){var U=' . json_encode($uid) . ',S=' . json_encode(self::$slug) . ',B=' . json_encode(self::apiBase()) . ';'
                . 'var fm=document.getElementById(U),m=document.getElementById(U+"-msg");if(!fm)return;'
                . 'fm.addEventListener("submit",function(e){e.preventDefault();var d={};'
                . 'fm.querySelectorAll("."+U+"-f").forEach(function(el){d[el.getAttribute("data-k")]=(el.value||"").trim();});'
                . 'var b=fm.querySelector("button[type=submit]"),ob=b.textContent;b.disabled=true;b.textContent="Sending…";'
                . 'fetch(B+"/api/sites/inquiry-submit.php",{method:"POST",headers:{"Content-Type":"application/json","Accept":"application/json"},'
                . 'body:JSON.stringify({site:S,name:d.name,email:d.email,phone:d.phone,subject:d.subject,message:d.message})})'
                . '.then(function(r){return r.json();}).then(function(res){b.disabled=false;b.textContent=ob;'
                . 'if(res&&res.success){m.style.color="#16a34a";m.textContent="✓ Thank you! We will get back to you shortly.";fm.reset();}'
                . 'else{m.style.color="#dc2626";m.textContent=(res&&res.message)||"Could not send your message.";}})'
                . '.catch(function(){b.disabled=false;b.textContent=ob;m.style.color="#dc2626";m.textContent="Connection error.";});});})();</script>';
        }

        // Map.
        $mapSrc = null;
        if (($p['showMap'] ?? true) !== false && in_array($variant, ['form-map', 'details-map'], true)) {
            $q    = trim((string)($p['mapUrl'] ?? $biz['mapUrl'] ?? ''));
            $addr = trim((string)($biz['address'] ?? ''));

            if ($q !== '' && preg_match('#/maps/embed|output=embed#', $q)) {
                $mapSrc = $q;                     // already an embeddable URL
            } else {
                /**
                 * A PLAIN Maps link cannot be put in an iframe — Google refuses
                 * to frame maps.google.com/?cid=…, /maps/place/… and
                 * maps.app.goo.gl/… . The old code fed the whole URL to
                 * ?q=<urlencoded>, so Maps searched for the literal URL text and
                 * the panel came up empty or on the wrong place. That is exactly
                 * what a listing's googleMapsUri looks like.
                 *
                 * So: search by something Maps can actually resolve — the street
                 * address, else coordinates lifted out of the link, else the
                 * place name in a /maps/place/<Name>/ URL. A link we cannot turn
                 * into a query renders no map rather than a wrong one.
                 */
                $needle = '';
                if ($addr !== '') {
                    $needle = $addr;
                } elseif ($q !== '' && preg_match('#@(-?\d+\.\d+),(-?\d+\.\d+)#', $q, $m)) {
                    $needle = $m[1] . ',' . $m[2];
                } elseif ($q !== '' && preg_match('#/maps/place/([^/@?]+)#', $q, $m)) {
                    $needle = str_replace('+', ' ', rawurldecode($m[1]));
                } elseif ($q !== '' && !preg_match('#^https?://#i', $q)) {
                    $needle = $q;                 // it was plain text all along
                }
                if ($needle !== '') {
                    $mapSrc = 'https://www.google.com/maps?q=' . rawurlencode($needle) . '&output=embed';
                }
            }
        }

        $header = self::sectionHeader($p['label'] ?? null, $p['heading'] ?? null, $p['sub'] ?? null);

        if ($variant === 'cards') {
            $cards = '';
            // reuse detail rows as cards
            $cards = '<div class="tf-grid tf-c4">' . $rows . '</div>';
            return self::shell($s, $header . $cards);
        }

        $left = ($variant === 'form-map' && $formEl) ? ($formEl . '<div style="margin-top:24px">' . $details . '</div>') : ($formEl ?: $details);
        $map = $mapSrc ? '<iframe src="' . self::esc($mapSrc) . '" title="Map" loading="lazy" referrerpolicy="no-referrer-when-downgrade" style="border:0;border-radius:var(--radius);min-height:320px;height:100%;width:100%"></iframe>' : '';
        $grid = $mapSrc ? ('<div class="tf-two" style="align-items:stretch">' . '<div>' . $left . '</div>' . $map . '</div>') : ('<div style="max-width:640px;margin:0 auto">' . $left . '</div>');
        return self::shell($s, $header . $grid);
    }

    private static function secFooter(array $s, array $doc): string
    {
        $p = $s['props'] ?? [];
        $biz = $doc['business'] ?? [];
        $simple = ($s['variant'] ?? '') === 'simple';
        $logo = self::media($p['logo'] ?? null);
        $socialLabels = ['facebook'=>'Facebook','instagram'=>'Instagram','youtube'=>'YouTube','linkedin'=>'LinkedIn','twitter'=>'X','telegram'=>'Telegram'];

        $brand = '<div>';
        $brand .= $logo ? '<img src="' . self::esc($logo) . '" alt="' . self::esc($doc['site']['name'] ?? '') . '" style="height:40px;width:auto;object-fit:contain">'
                        : '<p style="font-size:18px;font-weight:700;font-family:var(--font-heading);margin:0">' . self::esc($doc['site']['name'] ?? '') . '</p>';
        if (!empty($p['blurb'])) $brand .= '<p style="margin-top:12px;max-width:24rem;font-size:14px;opacity:.75">' . self::esc($p['blurb']) . '</p>';
        if (($p['showSocial'] ?? true) !== false && !empty($biz['social'])) {
            $soc = '';
            foreach ($biz['social'] as $k => $url) {
                if (!$url) continue;
                $soc .= '<a href="' . self::esc($url) . '" target="_blank" rel="noopener noreferrer" style="font-size:12px;opacity:.75;text-decoration:none">' . self::esc($socialLabels[$k] ?? $k) . '</a>';
            }
            if ($soc) $brand .= '<div style="margin-top:16px;display:flex;flex-wrap:wrap;gap:12px">' . $soc . '</div>';
        }
        // Custom uploaded social icons (independent of the Business-Info text links).
        $iconPx = ['small'=>28,'medium'=>36,'large'=>48,'extra-large'=>64][$p['socialIconSize'] ?? 'medium'] ?? 36;
        $icons = '';
        foreach (($p['socialIcons'] ?? []) as $si) {
            $src = self::media($si['icon'] ?? null);
            $href = trim((string)($si['href'] ?? ''));
            if (!$src || $href === '') continue;
            $lbl = self::esc($si['label'] ?? 'Social link');
            $icons .= '<a href="' . self::esc($href) . '" target="_blank" rel="noopener noreferrer" aria-label="' . $lbl . '" title="' . $lbl . '" style="display:inline-flex;opacity:.8;text-decoration:none">'
                    . '<img src="' . self::esc($src) . '" alt="' . self::esc($si['label'] ?? '') . '" style="height:' . $iconPx . 'px;width:' . $iconPx . 'px;object-fit:contain"></a>';
        }
        if ($icons) $brand .= '<div style="margin-top:16px;display:flex;flex-wrap:wrap;align-items:center;gap:12px">' . $icons . '</div>';
        $brand .= '</div>';

        $body = $simple ? ('<div style="text-align:center">' . $brand . '</div>') : ('<div class="tf-grid tf-c4" style="text-align:left">' . $brand);
        if (!$simple) {
            foreach (($p['columns'] ?? []) as $col) {
                $links = '';
                foreach (($col['links'] ?? []) as $l) $links .= '<li style="margin-bottom:6px"><a href="' . self::esc($l['href'] ?? '#') . '" style="text-decoration:none;opacity:.75">' . self::esc($l['text'] ?? '') . '</a></li>';
                $body .= '<div><p style="font-size:14px;font-weight:600;margin:0 0 12px">' . self::esc($col['title'] ?? '') . '</p><ul style="list-style:none;padding:0;margin:0;font-size:14px">' . $links . '</ul></div>';
            }
            if (($p['showContact'] ?? true) !== false && (!empty($biz['phone']) || !empty($biz['email']) || !empty($biz['address']))) {
                $ci = '';
                if (!empty($biz['phone'])) $ci .= '<li style="margin-bottom:6px"><a href="tel:' . self::esc($biz['phone']) . '" style="text-decoration:none">' . self::esc($biz['phone']) . '</a></li>';
                if (!empty($biz['email'])) $ci .= '<li style="margin-bottom:6px"><a href="mailto:' . self::esc($biz['email']) . '" style="text-decoration:none">' . self::esc($biz['email']) . '</a></li>';
                if (!empty($biz['address'])) $ci .= '<li>' . self::esc($biz['address']) . '</li>';
                $body .= '<div><p style="font-size:14px;font-weight:600;margin:0 0 12px">Contact</p><ul style="list-style:none;padding:0;margin:0;font-size:14px;opacity:.75">' . $ci . '</ul></div>';
            }
            $body .= '</div>';
        }

        // Registration details and risk disclaimers — small print that must be on
        // every page for regulated businesses (mutual fund distributors, insurance).
        $disc = trim((string)($p['disclaimer'] ?? ''));
        if ($disc !== '') {
            $paras = '';
            foreach (preg_split('/\n\s*\n/', $disc) as $para) {
                if (trim($para) !== '') $paras .= '<p>' . nl2br(self::esc(trim($para))) . '</p>';
            }
            $body .= '<div class="tf-foot-disc">' . $paras . '</div>';
        }

        $year = date('Y');
        $copy = trim((string)($p['copyright'] ?? '')) !== '' ? $p['copyright'] : ('© ' . $year . ' ' . ($doc['site']['name'] ?? '') . '. All rights reserved.');
        // Privacy Policy / Terms links — each shown only when its content exists.
        $legal = '';
        if (($p['showLegal'] ?? true) !== false) {
            $ls = [];
            if (trim((string)($p['privacyBody'] ?? '')) !== '') $ls[] = '<a href="/privacy" style="text-decoration:underline;color:inherit">Privacy Policy</a>';
            if (trim((string)($p['termsBody'] ?? '')) !== '')   $ls[] = '<a href="/terms" style="text-decoration:underline;color:inherit">Terms &amp; Conditions</a>';
            if ($ls) $legal = '<p style="margin:0;display:flex;gap:14px;flex-wrap:wrap">' . implode('', $ls) . '</p>';
        }
        $bottom = '<div style="margin-top:32px;padding-top:24px;border-top:1px solid rgba(255,255,255,.15);display:flex;flex-direction:column;gap:8px;justify-content:space-between;align-items:center;font-size:12px;opacity:.7" class="tf-foot-bottom">'
                . '<p style="margin:0">' . self::esc($copy) . '</p>'
                . $legal
                . (($p['showBranding'] ?? true) !== false ? '<p style="margin:0">Powered by <a href="https://tapify.co.in" target="_blank" rel="noopener noreferrer" style="text-decoration:underline">Tapify</a></p>' : '')
                . '</div>';

        return self::shell($s, $body . $bottom);
    }

    /* ------------------------------------------------- new sections */

    private static function secHours(array $s, array $doc): string
    {
        $p = $s['props'] ?? [];
        $hours = $doc['business']['hours'] ?? [];
        $labels = ['mon'=>'Monday','tue'=>'Tuesday','wed'=>'Wednesday','thu'=>'Thursday','fri'=>'Friday','sat'=>'Saturday','sun'=>'Sunday'];
        $byDay = [];
        foreach ($hours as $h) { if (!empty($h['day'])) $byDay[strtolower($h['day'])] = $h; }
        if (!$byDay) return '';
        $today = strtolower(date('D'));
        $hl = ($p['highlightToday'] ?? true) !== false;
        $rows = '';
        foreach (['mon','tue','wed','thu','fri','sat','sun'] as $d) {
            if (!isset($byDay[$d])) continue;
            $h = $byDay[$d];
            $val = 'Closed';
            if (empty($h['closed']) && (!empty($h['open']) || !empty($h['close']))) {
                $val = self::esc(trim(($h['open'] ?? '') . ' – ' . ($h['close'] ?? ''), " –"));
            }
            $isToday = $hl && ($d === $today);
            $st = 'display:flex;justify-content:space-between;gap:16px;padding:12px 16px;font-size:15px;border-radius:var(--radius)';
            $st .= $isToday ? ';background:var(--color-primary);color:var(--color-primary-fg);font-weight:600' : ';border:1px solid var(--color-border)';
            $rows .= '<li style="' . $st . '"><span>' . self::esc($labels[$d]) . ($isToday ? ' · Today' : '') . '</span><span>' . $val . '</span></li>';
        }
        $inner = self::sectionHeader($p['label'] ?? null, $p['heading'] ?? null, $p['sub'] ?? null, self::isDarkBg($s))
               . '<ul style="list-style:none;margin:0 auto;padding:0;max-width:520px;display:flex;flex-direction:column;gap:8px;text-align:left">' . $rows . '</ul>';
        if (!empty($p['note'])) $inner .= '<p style="margin-top:16px;font-size:13px;opacity:.7">' . self::esc($p['note']) . '</p>';
        return self::shell($s, $inner);
    }

    /* ------------------------------------------------------- blog detail */

    /** URL-safe slug for a post: its slug field, else built from the title. */
    private static function itemSlug(array $item, string $fallback = 'item'): string
    {
        $s = strtolower(trim((string)($item['slug'] ?? '')));
        if ($s === '') $s = strtolower(trim((string)($item['title'] ?? '')));
        $s = preg_replace('/[^a-z0-9]+/', '-', $s);
        $s = trim((string)$s, '-');
        return $s !== '' ? $s : $fallback;
    }

    /** Every post in the document, keyed by slug (first wins on a clash). */
    private static function allPosts(array $doc): array
    {
        $out = [];
        foreach (($doc['pages'] ?? []) as $pg) {
            foreach (($pg['sections'] ?? []) as $s) {
                if (($s['type'] ?? '') !== 'blog') continue;
                foreach (($s['props']['posts'] ?? []) as $post) {
                    $slug = self::itemSlug($post, 'post');
                    if (!isset($out[$slug])) $out[$slug] = $post;
                }
            }
        }
        return $out;
    }

    /** Every service/product with a full description, keyed by slug. */
    /**
     * Does this product/service warrant a page of its own?
     *
     * It used to be "only if it has a full description", which quietly threw
     * away the extra photos: a customer could add ten images to a product, save,
     * publish — and see nothing at all, because with no description the card
     * never linked anywhere and /product/<slug> 404'd. The photos were stored
     * and the detail page would have rendered them perfectly; there was simply
     * no way to reach it.
     *
     * Photos are a reason to have a page in their own right. A gallery of a sofa
     * from six angles needs no paragraph to be worth showing.
     */
    /**
     * Is this section selling straight through WhatsApp instead of a page?
     *
     * The default flow — card, detail page, order form, stored order — is the
     * right one for a real shop. For a small business with twenty items and a
     * phone, it is three screens of friction before anyone can ask "is this in
     * stock?". In that mode the card carries a WhatsApp button, the items get no
     * detail page at all, and the order lands as a message.
     */
    private static function isWhatsappOrder(array $props): bool
    {
        return ($props['orderVia'] ?? 'page') === 'whatsapp';
    }

    /**
     * "Order on WhatsApp" for one card, pre-filled with what they tapped.
     *
     * The item name matters: a bare wa.me link produces an empty chat and the
     * shop owner has to ask what the customer was looking at, which loses most
     * of the point.
     */
    private static function waOrderButton(array $item, array $biz, array $props, bool $onDark): string
    {
        $href = self::waOrderHref($item, $biz);
        if ($href === '') return '';      // nothing to link to; show no button

        $label = trim((string)($props['orderLabel'] ?? '')) ?: 'Order on WhatsApp';
        return '<div style="margin-top:16px">' . self::btn([
            'text'    => $label,
            'href'    => $href,
            'newTab'  => true,
            'style'   => 'primary',
        ], $onDark) . '</div>';
    }

    private static function hasDetailPage(array $item): bool
    {
        if (trim((string)($item['body'] ?? '')) !== '') return true;
        foreach ((array)($item['gallery'] ?? []) as $g) {
            if (!empty($g['image'])) return true;
        }
        return false;
    }

    private static function allServices(array $doc): array
    {
        $out = [];
        foreach (($doc['pages'] ?? []) as $pg) {
            foreach (($pg['sections'] ?? []) as $s) {
                if (($s['type'] ?? '') !== 'services') continue;
                // A WhatsApp-order section has no detail pages by design; indexing
                // them would leave the URL reachable with nothing linking to it,
                // and Google would still find and rank it.
                if (self::isWhatsappOrder($s['props'] ?? [])) continue;
                foreach (self::resolveItems($s['props'] ?? [], $doc) as $item) {
                    if (!self::hasDetailPage($item)) continue;
                    $slug = self::itemSlug($item, 'service');
                    if (!isset($out[$slug])) $out[$slug] = $item;
                }
            }
        }
        return $out;
    }

    /** Every product with a full description, keyed by slug — same idea as allServices(). */
    private static function allProducts(array $doc): array
    {
        $out = [];
        foreach (($doc['pages'] ?? []) as $pg) {
            foreach (($pg['sections'] ?? []) as $s) {
                if (($s['type'] ?? '') !== 'products') continue;
                // A WhatsApp-order section has no detail pages by design; indexing
                // them would leave the URL reachable with nothing linking to it,
                // and Google would still find and rank it.
                if (self::isWhatsappOrder($s['props'] ?? [])) continue;
                // Resolve refs too, or a section drawing from the shared
                // catalogue would show cards with no detail page behind them.
                foreach (self::resolveItems($s['props'] ?? [], $doc) as $item) {
                    if (!self::hasDetailPage($item)) continue;
                    $slug = self::itemSlug($item, 'product');
                    if (!isset($out[$slug])) $out[$slug] = $item;
                }
            }
        }
        return $out;
    }

    /** The first visible section of a type anywhere in the site (for header/footer). */
    /** True when the site's header has the account/login system switched on. */
    private static function accountsEnabled(array $doc): bool
    {
        foreach (($doc['pages'] ?? []) as $pg) {
            foreach (($pg['sections'] ?? []) as $s) {
                if (($s['type'] ?? '') === 'header' && !empty($s['props']['showAccount'])) return true;
            }
        }
        return false;
    }

    /** True when a page (or any page) has at least one visible section of a type. */
    private static function pageHasType(array $page, string $type): bool
    {
        foreach (($page['sections'] ?? []) as $s) {
            if (($s['type'] ?? '') === $type && ($s['visible'] ?? true) !== false && empty($s['style']['hidden'])) {
                return true;
            }
        }
        return false;
    }

    private static function chromeSection(array $doc, string $type): string
    {
        foreach (($doc['pages'] ?? []) as $pg) {
            foreach (($pg['sections'] ?? []) as $s) {
                if (($s['type'] ?? '') === $type && ($s['visible'] ?? true) !== false && empty($s['style']['hidden'])) {
                    return self::section($s, $doc);
                }
            }
        }
        return '';
    }

    /** Plain-text article body -> paragraphs (blank line separates paragraphs). */
    private static function articleBody(string $body): string
    {
        $body = trim($body);
        if ($body === '') return '';
        $out = '';
        foreach (preg_split('/\n\s*\n/', $body) as $para) {
            $para = trim((string)$para);
            if ($para !== '') $out .= '<p style="margin:0 0 18px">' . nl2br(self::esc($para)) . '</p>';
        }
        return $out;
    }

    /** [title, body] for a legal page, pulled from the first footer section. */
    private static function legalContent(array $doc, string $kind): array
    {
        $key   = $kind === 'privacy' ? 'privacyBody' : 'termsBody';
        $title = $kind === 'privacy' ? 'Privacy Policy' : 'Terms & Conditions';
        foreach (($doc['pages'] ?? []) as $pg) {
            foreach (($pg['sections'] ?? []) as $s) {
                if (($s['type'] ?? '') === 'footer' && trim((string)($s['props'][$key] ?? '')) !== '') {
                    return [$title, (string)$s['props'][$key]];
                }
            }
        }
        return [$title, ''];
    }

    /** A simple legal page (title + body) wrapped in the site header/footer. */
    private static function renderLegal(array $doc, string $title, string $body, string $path): string
    {
        $vars  = self::themeVars($doc['theme'] ?? []);
        $fonts = self::googleFonts($doc['theme'] ?? []);
        $pseudo = ['slug' => $path, 'title' => $title, 'seo' => ['title' => trim($title . ' | ' . ($doc['site']['name'] ?? ''), ' |'), 'robots' => 'noindex,follow']];
        $article = '<article class="tf-container" style="max-width:760px;padding-top:calc(56px*var(--space-scale));padding-bottom:calc(56px*var(--space-scale))">'
            . '<h1 style="margin:0 0 24px;font-family:var(--font-heading);font-size:34px;line-height:1.15;font-weight:700">' . self::esc($title) . '</h1>'
            . '<div style="font-size:16px;line-height:1.75">' . self::articleBody($body) . '</div>'
            . '</article>';
        return "<!DOCTYPE html><html lang=\"" . self::esc($doc['site']['locale'] ?? 'en') . "\"><head>"
             . self::head($doc, $pseudo, $fonts)
             . "<style>.tf-site{" . $vars . "}" . self::baseCss() . "</style>"
             . "</head><body>"
             . "<main class=\"tf-site\">" . self::chromeSection($doc, 'header') . $article . self::chromeSection($doc, 'footer') . "</main>"
             . self::carouselScript() . self::animScript()
             . "</body></html>";
    }

    /** Render one blog post on its own page, wrapped in the site header/footer. */
    private static function renderPostDetail(array $doc, array $post): string
    {
        $vars  = self::themeVars($doc['theme'] ?? []);
        $fonts = self::googleFonts($doc['theme'] ?? []);
        $name  = $doc['site']['name'] ?? '';

        $pseudo = [
            'slug'  => '/post/' . self::itemSlug($post, 'post'),
            'title' => $post['title'] ?? 'Post',
            'seo'   => [
                'title'       => trim(($post['title'] ?? 'Post') . ' | ' . $name, ' |'),
                'description' => $post['excerpt'] ?? '',
                'ogImage'     => $post['image'] ?? null,
                'robots'      => 'index,follow',
            ],
        ];

        // Where "Back" goes: the page that holds a blog section, else home.
        $backPath = '/';
        foreach (($doc['pages'] ?? []) as $pg) {
            foreach (($pg['sections'] ?? []) as $s) {
                if (($s['type'] ?? '') === 'blog') { $backPath = $pg['slug'] ?? '/'; break 2; }
            }
        }

        $cover = self::media($post['image'] ?? null);
        $article = '<article class="tf-container" style="max-width:760px;padding-top:calc(56px*var(--space-scale));padding-bottom:calc(56px*var(--space-scale))">'
            . '<a href="' . self::esc($backPath) . '" style="display:inline-block;margin-bottom:22px;font-size:14px;font-weight:600;color:var(--color-primary);text-decoration:none">&larr; Back</a>'
            . (!empty($post['date']) ? '<p style="margin:0 0 8px;font-size:13px;font-weight:600;color:var(--color-accent)">' . self::esc($post['date']) . '</p>' : '')
            . '<h1 style="margin:0 0 22px;font-family:var(--font-heading);font-size:38px;line-height:1.15;font-weight:700">' . self::esc($post['title'] ?? '') . '</h1>'
            . ($cover ? '<img src="' . self::esc($cover) . '" alt="' . self::esc($post['title'] ?? '') . '" style="width:100%;border-radius:var(--radius);margin-bottom:30px">' : '')
            . '<div style="font-size:17px;line-height:1.75">' . self::articleBody($post['body'] ?? '') . '</div>'
            . '</article>';

        return "<!DOCTYPE html><html lang=\"" . self::esc($doc['site']['locale'] ?? 'en') . "\"><head>"
             . self::head($doc, $pseudo, $fonts)
             . "<style>.tf-site{" . $vars . "}" . self::baseCss() . "</style>"
             . "</head><body>"
             . "<main class=\"tf-site\">" . self::chromeSection($doc, 'header') . $article . self::chromeSection($doc, 'footer') . "</main>"
             . self::carouselScript() . self::cartScript() . self::animScript()
             . "</body></html>";
    }

    /**
     * Render one service/product on its own page: a photo gallery (like a
     * product page on Amazon/Flipkart) plus title, price and full description.
     */
    /** Absolute origin of this Tapify backend, for the public JSON endpoints. */
    private static function apiBase(): string
    {
        if (defined('SITE_URL') && SITE_URL) return rtrim(SITE_URL, '/');
        if (defined('PUBLIC_URL') && PUBLIC_URL) return rtrim(PUBLIC_URL, '/');
        return 'https://app.tapify.co.in';
    }

    /**
     * Customer reviews for one item: the approved list plus a "write a review"
     * form. Both talk to the builder's own endpoints — nothing shared with the
     * vCard backend.
     */
    private static function reviewsBlock(array $item, string $kind = 'service'): string
    {
        $slug = self::itemSlug($item, $kind);
        $uid  = 'rv' . substr(md5($slug), 0, 6);
        $in   = 'width:100%;padding:10px 12px;font-size:14px;border:1px solid var(--color-border);border-radius:var(--radius);background:var(--color-bg);color:var(--color-text)';
        $lb   = 'display:block;margin-bottom:4px;font-size:12px;font-weight:700';

        $stars = '';
        for ($i = 5; $i >= 1; $i--) $stars .= '<option value="' . $i . '">' . str_repeat('★', $i) . ' (' . $i . ')</option>';

        return '<section style="margin-top:48px;padding-top:32px;border-top:1px solid var(--color-border);text-align:left">'
            . '<h2 style="margin:0 0 4px;font-family:var(--font-heading);font-size:24px;font-weight:700">Customer reviews</h2>'
            . '<div id="' . $uid . '-list" style="margin-top:16px"><p style="font-size:14px;color:var(--tf-text,var(--color-muted))">Loading reviews…</p></div>'
            . '<form id="' . $uid . '-form" style="margin-top:28px;max-width:460px;padding:18px;border:1px solid var(--color-border);border-radius:var(--radius);background:var(--color-surface)">'
            . '<p style="margin:0 0 12px;font-size:15px;font-weight:700">Write a review</p>'
            . '<div style="margin-bottom:10px"><label style="' . $lb . '">Your name *</label><input name="name" required style="' . $in . '"></div>'
            . '<div style="margin-bottom:10px"><label style="' . $lb . '">Rating *</label><select name="rating" style="' . $in . '">' . $stars . '</select></div>'
            . '<div style="margin-bottom:14px"><label style="' . $lb . '">Review *</label><textarea name="comment" rows="3" required style="' . $in . '"></textarea></div>'
            . '<button type="submit" style="padding:11px 22px;font-size:14px;font-weight:700;border:none;border-radius:var(--radius);background:var(--color-primary);color:var(--color-primary-fg);cursor:pointer">Submit review</button>'
            . '<p id="' . $uid . '-msg" style="margin:10px 0 0;font-size:13px;font-weight:600"></p>'
            . '</form>'
            . '<script>(function(){var U=' . json_encode($uid) . ',S=' . json_encode(self::$slug) . ',I=' . json_encode($slug) . ',B=' . json_encode(self::apiBase()) . ';'
            . 'var list=document.getElementById(U+"-list"),f=document.getElementById(U+"-form"),m=document.getElementById(U+"-msg");'
            . 'function esc(t){var d=document.createElement("div");d.textContent=t;return d.innerHTML;}'
            . 'function load(){fetch(B+"/api/sites/reviews.php?site="+encodeURIComponent(S)+"&item="+encodeURIComponent(I),{headers:{"Accept":"application/json"}})'
            . '.then(function(r){return r.json();}).then(function(res){var d=(res&&res.data)||[];'
            . 'if(!d.length){list.innerHTML="<p style=\'font-size:14px;color:var(--tf-text,var(--color-muted))\'>No reviews yet — be the first.</p>";return;}'
            . 'list.innerHTML=d.map(function(x){return "<div style=\'padding:14px 0;border-bottom:1px solid var(--color-border)\'>"'
            . '+"<div style=\'color:#f59e0b;font-size:14px\'>"+"★".repeat(x.rating)+"</div>"'
            . '+"<p style=\'margin:4px 0 0;font-size:14px;font-weight:700\'>"+esc(x.name)+"</p>"'
            . '+"<p style=\'margin:4px 0 0;font-size:14px;color:var(--tf-text,var(--color-muted))\'>"+esc(x.comment)+"</p></div>";}).join("");})'
            . '.catch(function(){list.innerHTML="";});}'
            . 'load();'
            . 'f.addEventListener("submit",function(e){e.preventDefault();var b=f.querySelector("button[type=submit]");b.disabled=true;'
            . 'fetch(B+"/api/sites/review-submit.php",{method:"POST",headers:{"Content-Type":"application/json","Accept":"application/json"},'
            . 'body:JSON.stringify({site:S,item_slug:I,name:f.name.value,rating:f.rating.value,comment:f.comment.value})})'
            . '.then(function(r){return r.json();}).then(function(res){b.disabled=false;'
            . 'if(res&&res.success){m.style.color="#16a34a";m.textContent="✓ Thank you! Your review has been posted.";f.reset();load();}'
            . 'else{m.style.color="#dc2626";m.textContent=(res&&res.message)||"Could not submit.";}})'
            . '.catch(function(){b.disabled=false;m.style.color="#dc2626";m.textContent="Connection error.";});});})();</script>'
            . '</section>';
    }

    /** Selling price, MRP and the discount label, from an item's free-text prices. */
    private static function priceBits(array $item): array
    {
        $sell = trim((string)($item['price'] ?? ''));
        $mrp  = trim((string)($item['mrp'] ?? ''));
        $off  = '';
        if ($sell !== '' && $mrp !== '') {
            $ns = (float)preg_replace('/[^0-9.]/', '', $sell);
            $nm = (float)preg_replace('/[^0-9.]/', '', $mrp);
            if ($nm > 0 && $ns > 0 && $ns < $nm) $off = round((($nm - $ns) / $nm) * 100) . '% Off';
        }
        return [$sell, $mrp, $off];
    }

    /**
     * The product gallery: one big photo with a thumbnail strip beside it.
     *
     * A carousel hides everything but the current shot, which is the wrong shape
     * for a product page — buyers want to see at a glance how many photos there
     * are and jump straight to the one they care about.
     */
    /**
     * The product's photos.
     *
     * The main image is a horizontal scroll-snap TRACK holding every photo, not
     * one <img> whose src gets swapped. That is what makes it swipeable: on a
     * phone the whole gesture is the browser's own inertial scrolling, which no
     * touch-event handler reproduces convincingly, and it keeps working with
     * JavaScript disabled. The thumbnails and dots just scroll the track.
     */
    private static function productGallery(array $photos, string $uid): string
    {
        if (!$photos) return '';

        $slides = '';
        foreach ($photos as $i => $ph) {
            // Slide 0 keeps the "-main" id: picking a variant colour swaps THAT
            // photo (see the order script), and it is the slide the track starts on.
            $slides .= '<div class="tf-pslide"><img' . ($i ? '' : ' id="' . $uid . '-main"')
                     . ' src="' . self::esc($ph['src']) . '" alt="'
                     . self::esc($ph['alt']) . '"' . ($i ? ' loading="lazy"' : '') . '></div>';
        }
        $track = '<div class="tf-pmain" id="' . $uid . '-track">' . $slides . '</div>';

        if (count($photos) < 2) {
            return '<div class="tf-pgal"><div class="tf-pstage">' . $track . '</div></div>';
        }

        $thumbs = $dots = '';
        foreach ($photos as $i => $ph) {
            $n = $i + 1;
            $thumbs .= '<button type="button" class="tf-pthumb' . ($i === 0 ? ' is-active' : '') . '"'
                     . ' data-i="' . $i . '" aria-label="Photo ' . $n . '">'
                     . '<img src="' . self::esc($ph['src']) . '" alt="" loading="lazy"></button>';
            $dots   .= '<button type="button" class="tf-pdot' . ($i === 0 ? ' is-active' : '') . '"'
                     . ' data-i="' . $i . '" aria-label="Photo ' . $n . '"></button>';
        }

        // Clicking a thumb scrolls the track; scrolling the track (by swipe,
        // trackpad or keyboard) drives the active thumb and dot back. One
        // listener on the wrapper, so the two directions cannot drift apart.
        $script = '<script>(function(){'
                . 'var g=document.getElementById(' . json_encode($uid . '-gal') . ');'
                . 'var t=document.getElementById(' . json_encode($uid . '-track') . ');if(!g||!t)return;'
                . 'var marks=g.querySelectorAll(".tf-pthumb,.tf-pdot");'
                . 'g.addEventListener("click",function(e){var b=e.target.closest?e.target.closest("[data-i]"):null;if(!b)return;'
                . 't.scrollTo({left:t.clientWidth*(+b.getAttribute("data-i")),behavior:"smooth"});});'
                . 'var raf=0;t.addEventListener("scroll",function(){if(raf)return;raf=requestAnimationFrame(function(){raf=0;'
                . 'var i=Math.round(t.scrollLeft/(t.clientWidth||1));'
                . 'Array.prototype.forEach.call(marks,function(m){'
                . 'var on=(+m.getAttribute("data-i"))===i;m.classList.toggle("is-active",on);'
                . 'if(on&&m.className.indexOf("tf-pthumb")===0&&m.scrollIntoView)m.scrollIntoView({block:"nearest",inline:"nearest"});});'
                . '});},{passive:true});})();</script>';

        return '<div class="tf-pgal" id="' . $uid . '-gal">'
             . '<div class="tf-pthumbs">' . $thumbs . '</div>'
             . '<div class="tf-pstage">' . $track . '<div class="tf-pdots">' . $dots . '</div></div>'
             . '</div>' . $script;
    }

    /**
     * Shipping / exchange / returns, shown on EVERY product and service page.
     *
     * Written once on the Footer section, not per product: a shop with 200
     * products cannot maintain the same shipping table 200 times, and the moment
     * one copy is missed the site contradicts itself. The footer is where the
     * other policy text (Privacy, Terms) already lives, and footer edits are
     * already copied to every page by the editor, so this is site-wide for free.
     *
     * <details> rather than a JS accordion — it collapses natively, it is
     * searchable by the browser's find-in-page, and it prints.
     */
    private static function productPolicies(array $doc): string
    {
        $rows = null;
        foreach (($doc['pages'] ?? []) as $pg) {
            foreach (($pg['sections'] ?? []) as $sec) {
                if (($sec['type'] ?? '') === 'footer') { $rows = $sec['props']['productPolicies'] ?? []; break 2; }
            }
        }
        if (!$rows) return '';

        $blocks = '';
        foreach ((array)$rows as $i => $r) {
            $t = trim((string)($r['title'] ?? ''));
            $b = trim((string)($r['body'] ?? ''));
            if ($t === '' && $b === '') continue;
            // First one open, so the shipping cost is visible without a tap —
            // it is the question that stops the sale.
            $blocks .= '<details' . ($blocks === '' ? ' open' : '') . ' style="border-top:1px solid var(--color-border)">'
                     . '<summary style="padding:12px 0;font-size:14px;font-weight:700;cursor:pointer;list-style:revert">' . self::esc($t ?: 'Policy') . '</summary>'
                     . ($b !== '' ? '<p style="margin:0 0 14px;font-size:14px;line-height:1.7;white-space:pre-line;color:var(--tf-text,var(--color-muted))">' . self::esc($b) . '</p>' : '')
                     . '</details>';
        }
        if ($blocks === '') return '';

        return '<section style="margin-top:24px;padding-top:10px;border-bottom:1px solid var(--color-border)">' . $blocks . '</section>';
    }

    /**
     * The product's own Instagram post or reel, embedded on its detail page.
     *
     * Only a POST/REEL permalink works — Instagram's embed renders nothing at
     * all for a profile URL, so that is rejected here rather than shipping a
     * blank gap the shop owner cannot diagnose.
     */
    private static function productInstagram(array $item): string
    {
        $url = trim((string)($item['instagram'] ?? ''));
        if ($url === '' || !preg_match('#^https?://(www\.)?instagram\.com/(p|reel|reels|tv)/#i', $url)) return '';

        return '<div style="margin-top:24px;padding-top:24px;border-top:1px solid var(--color-border)">'
             . '<p style="margin:0 0 12px;font-size:13px;font-weight:700;letter-spacing:.04em;text-transform:uppercase;color:var(--tf-text,var(--color-muted))">See it on Instagram</p>'
             . '<blockquote class="instagram-media" data-instgrm-permalink="' . self::esc($url) . '" data-instgrm-version="14"'
             . ' style="background:#fff;border:0;border-radius:3px;box-shadow:0 0 1px rgba(0,0,0,.5),0 1px 10px rgba(0,0,0,.15);margin:0;max-width:540px;min-width:0;width:100%;padding:0"></blockquote>'
             . '<script async src="//www.instagram.com/embed.js"></script>'
             . '<script>if(window.instgrm&&window.instgrm.Embeds)window.instgrm.Embeds.process();</script>'
             . '</div>';
    }

    /** The optional "Product Information" spec table. */
    private static function productInfoTable(array $item): string
    {
        $rows = '';
        foreach (($item['productInfo'] ?? []) as $r) {
            $k = trim((string)($r['label'] ?? ''));
            $v = trim((string)($r['value'] ?? ''));
            if ($k === '' && $v === '') continue;
            $rows .= '<tr>'
                   . '<th style="padding:9px 0;text-align:left;font-size:13px;font-weight:500;color:var(--tf-text,var(--color-muted));vertical-align:top;width:45%">' . self::esc($k) . '</th>'
                   . '<td style="padding:9px 0;text-align:left;font-size:13px;font-weight:700">' . self::esc($v) . '</td>'
                   . '</tr>';
        }
        if ($rows === '') return '';

        $title = trim((string)($item['productInfoTitle'] ?? '')) ?: 'Product Information';
        return '<section class="tf-card" style="margin-top:22px;padding:18px;text-align:left">'
             . '<h2 style="margin:0 0 8px;font-family:var(--font-heading);font-size:17px;font-weight:700">' . self::esc($title) . '</h2>'
             . '<table style="width:100%;border-collapse:collapse">' . $rows . '</table>'
             . '</section>';
    }

    /**
     * "Best sellers" — the shop's other products, shown under the one being
     * viewed so a visitor who lands straight on a product page has somewhere to
     * go next. Each card opens its own detail page.
     */
    private static function relatedProducts(array $doc, array $item, string $backPath, string $kind = 'service'): string
    {
        // Settings live on the Services/Products section, so find the one holding this item.
        $sectionType = $kind === 'product' ? 'products' : 'services';
        $urlPrefix   = $kind === 'product' ? '/product/' : '/service/';
        $slug = self::itemSlug($item, $kind);
        $cfg = null;
        foreach (($doc['pages'] ?? []) as $pg) {
            foreach (($pg['sections'] ?? []) as $sec) {
                if (($sec['type'] ?? '') !== $sectionType) continue;
                // resolveItems, not props.items: a product drawn from the shared
                // catalogue is not stored on the section, so reading items
                // directly never matched it and this fell through to whichever
                // section happened to come first — taking ITS settings.
                foreach (self::resolveItems($sec['props'] ?? [], $doc) as $it) {
                    if (self::itemSlug($it, $kind) === $slug) { $cfg = $sec['props']; break 3; }
                }
                if ($cfg === null) $cfg = $sec['props'] ?? [];   // fall back to the first one
            }
        }
        if (($cfg['showRelated'] ?? true) === false) return '';

        $others = [];
        $allItems = $kind === 'product' ? self::allProducts($doc) : self::allServices($doc);
        foreach ($allItems as $sl => $it) {
            if ($sl === $slug) continue;
            $others[] = [$sl, $it];
            if (count($others) >= 8) break;
        }
        if (!$others) return '';

        $cards = '';
        foreach ($others as [$sl, $it]) {
            [$sell, $mrp, $off] = self::priceBits($it);
            $img = self::media($it['image'] ?? null);
            $cards .= '<a class="tf-bscard" href="' . $urlPrefix . self::esc($sl) . '">'
                . ($img ? '<img src="' . self::esc($img) . '" alt="' . self::esc($it['title'] ?? '') . '" loading="lazy">' : '')
                . '<p class="tf-bstitle">' . self::esc($it['title'] ?? '') . '</p>'
                . (($sell !== '' || $mrp !== '') ? '<p class="tf-bsprice">'
                    . ($sell !== '' ? '<span class="tf-bssell">' . self::esc($sell) . '</span>' : '')
                    . ($mrp !== '' ? '<s>' . self::esc($mrp) . '</s>' : '')
                    . ($off !== '' ? '<span class="tf-bsoff">' . strtoupper($off) . '</span>' : '')
                    . '</p>' : '')
                . '</a>';
        }

        $heading = trim((string)($cfg['relatedHeading'] ?? '')) ?: 'Best sellers';
        return '<section style="margin-top:56px;text-align:center">'
             . '<h2 style="margin:0;font-family:var(--font-heading);font-size:28px;font-weight:800;letter-spacing:.02em;text-transform:uppercase">' . self::esc($heading) . '</h2>'
             . '<a href="' . self::esc($backPath) . '" style="display:inline-block;margin-top:8px;font-size:13px;font-weight:600;color:inherit;text-decoration:underline">VIEW ALL</a>'
             . '<div class="tf-bsgrid">' . $cards . '</div>'
             . '</section>';
    }

    private static function renderServiceDetail(array $doc, array $item): string
    {
        $vars  = self::themeVars($doc['theme'] ?? []);
        $fonts = self::googleFonts($doc['theme'] ?? []);
        $name  = $doc['site']['name'] ?? '';

        $pseudo = [
            'slug'  => '/service/' . self::itemSlug($item, 'service'),
            'title' => $item['title'] ?? 'Service',
            'seo'   => [
                'title'       => trim(($item['title'] ?? 'Service') . ' | ' . $name, ' |'),
                'description' => $item['desc'] ?? '',
                'ogImage'     => $item['image'] ?? null,
                'robots'      => 'index,follow',
            ],
        ];

        // Where "Back" goes: the page that holds a Services section, else home.
        $backPath = '/';
        foreach (($doc['pages'] ?? []) as $pg) {
            foreach (($pg['sections'] ?? []) as $s) {
                if (($s['type'] ?? '') === 'services') { $backPath = $pg['slug'] ?? '/'; break 2; }
            }
        }

        // Photos: the card's own image first, then the extra gallery photos —
        // a product-style set of shots the customer can flip through.
        $photos = [];
        $cover = self::media($item['image'] ?? null);
        if ($cover) $photos[] = ['src' => $cover, 'alt' => $item['title'] ?? ''];
        foreach (($item['gallery'] ?? []) as $g) {
            $src = self::media($g['image'] ?? null);
            if ($src) $photos[] = ['src' => $src, 'alt' => $g['alt'] ?? ($item['title'] ?? '')];
        }

        $puid = 'pg' . substr(md5(self::itemSlug($item, 'service')), 0, 6);
        $gallery = self::productGallery($photos, $puid);

        // --- price block: selling price, struck-through MRP and the discount % ---
        $priceHtml = '';
        if (!empty($item['price']) || !empty($item['mrp'])) {
            [$sell, $mrp, $off] = self::priceBits($item);
            $priceHtml = '<div style="margin:10px 0 0;display:flex;align-items:baseline;flex-wrap:wrap;gap:10px">'
                . ($sell !== '' ? '<span style="font-size:28px;font-weight:800;color:var(--color-primary)">' . self::esc($sell) . '</span>' : '')
                . ($mrp !== '' ? '<span style="font-size:16px;color:var(--tf-text,var(--color-muted));text-decoration:line-through">' . self::esc($mrp) . '</span>' : '')
                . ($off !== '' ? '<span style="font-size:14px;font-weight:700;color:#16a34a">' . $off . '</span>' : '')
                . '</div>';
        }
        $ctaHtml = !empty($item['cta']['text']) ? self::btn($item['cta']) : '';

        // --- ordering: option picker + Add to Cart / Buy Now + order form ---
        $orderHtml = '';
        if (($item['enableOrder'] ?? true) !== false) {
            $uid  = 'od' . substr(md5(self::itemSlug($item, 'service')), 0, 6);
            $opts = array_values(array_filter(array_map('trim', (array)($item['variantOptions'] ?? []))));
            $vlabel = trim((string)($item['variantLabel'] ?? ''));
            $in = 'width:100%;padding:10px 12px;font-size:14px;border:1px solid var(--color-border);border-radius:var(--radius);background:var(--color-bg);color:var(--color-text)';
            $lb = 'display:block;margin-bottom:4px;font-size:12px;font-weight:700';

            $variantEl = '';
            if ($vlabel !== '' && $opts) {
                $o = '';
                foreach ($opts as $ov) $o .= '<option>' . self::esc($ov) . '</option>';
                $variantEl = '<div style="margin-top:18px;max-width:280px"><label style="' . $lb . '">' . self::esc($vlabel) . '</label>'
                           . '<select id="' . $uid . '-var" style="' . $in . '">' . $o . '</select></div>';
            }

            $btnRow = '<div style="margin-top:20px;display:flex;flex-wrap:wrap;gap:12px">'
                . '<button type="button" id="' . $uid . '-cart" style="padding:13px 26px;font-size:15px;font-weight:700;border:1px solid var(--color-primary);border-radius:var(--radius);background:transparent;color:var(--color-primary);cursor:pointer">Add to Cart</button>'
                . '<button type="button" id="' . $uid . '-buy" style="padding:13px 26px;font-size:15px;font-weight:700;border:none;border-radius:var(--radius);background:var(--color-primary);color:var(--color-primary-fg);cursor:pointer">Buy Now</button>'
                . '<span id="' . $uid . '-note" role="status" style="align-self:center;font-size:13px;font-weight:600;color:#16a34a"></span>'
                . '</div>';

            // Checkout form (hidden until Buy Now / cart checkout).
            $form = '<form id="' . $uid . '-form" style="display:none;margin-top:22px;max-width:420px;text-align:left;padding:18px;border:1px solid var(--color-border);border-radius:var(--radius);background:var(--color-surface)">'
                . '<p style="margin:0 0 12px;font-size:15px;font-weight:700">Your details</p>'
                . '<div style="margin-bottom:10px"><label style="' . $lb . '">Name *</label><input name="name" required style="' . $in . '"></div>'
                . '<div style="margin-bottom:10px"><label style="' . $lb . '">Contact number *</label><input name="phone" type="tel" required style="' . $in . '"></div>'
                . '<div style="margin-bottom:10px"><label style="' . $lb . '">Email</label><input name="email" type="email" style="' . $in . '"></div>'
                . '<div style="margin-bottom:14px"><label style="' . $lb . '">Additional note (optional)</label><textarea name="note" rows="2" placeholder="Any special instructions for this order…" style="' . $in . '"></textarea></div>'
                . '<button type="submit" style="width:100%;padding:12px;font-size:15px;font-weight:700;border:none;border-radius:var(--radius);background:var(--color-primary);color:var(--color-primary-fg);cursor:pointer">Place order</button>'
                . '<p id="' . $uid . '-msg" style="margin:10px 0 0;font-size:13px;font-weight:600"></p>'
                . '</form>';

            $payload = json_encode([
                'site'    => self::$slug,
                'item'    => $item['title'] ?? '',
                'slug'    => self::itemSlug($item, 'service'),
                'price'   => $item['price'] ?? '',
                'mrp'     => $item['mrp'] ?? '',
                'vlabel'  => $vlabel,
                'image'   => $cover ?: '',
                'gate'    => self::accountsEnabled($doc),
            ]);
            $script = '<script>(function(){var U=' . json_encode($uid) . ',D=' . $payload . ';'
                . 'var f=document.getElementById(U+"-form"),n=document.getElementById(U+"-note"),m=document.getElementById(U+"-msg");'
                . 'function variant(){var v=document.getElementById(U+"-var");return v?v.value:"";}'
                . 'document.getElementById(U+"-cart").addEventListener("click",function(){'
                . 'var n2=window.tfCart.add({slug:D.slug,item:D.item,price:D.price,mrp:D.mrp,'
                . 'vlabel:D.vlabel,variant:variant(),image:D.image,qty:1});'
                . 'n.innerHTML="Added to cart ("+n2+") &middot; <a href=\"/cart\" style=\"color:inherit\">View cart</a>";});'
                . 'function token(){try{return (JSON.parse(localStorage.getItem("tf_customer_"+D.site))||{}).token||"";}catch(e){return "";}}'
                . 'document.getElementById(U+"-buy").addEventListener("click",function(){'
                . 'if(D.gate&&!token()){location.href="/account?next="+encodeURIComponent(location.pathname);return;}'
                . 'f.style.display="block";f.scrollIntoView({behavior:"smooth",block:"center"});});'
                . 'f.addEventListener("submit",function(e){e.preventDefault();var b=f.querySelector("button[type=submit]");b.disabled=true;b.textContent="Placing…";'
                . 'var fd={site:D.site,item:D.item,item_slug:D.slug,image:D.image,price:D.price,mrp:D.mrp,option_label:D.vlabel,option_value:variant(),'
                . 'name:f.name.value,phone:f.phone.value,email:f.email.value,note:(f.note?f.note.value:""),'
                . 'customer_token:(function(){try{return (JSON.parse(localStorage.getItem("tf_customer_"+D.site))||{}).token||"";}catch(e){return "";}})()};'
                . 'fetch("' . self::apiBase() . '/api/sites/order-submit.php",{method:"POST",headers:{"Content-Type":"application/json","Accept":"application/json"},body:JSON.stringify(fd)})'
                . '.then(function(r){return r.json();}).then(function(res){'
                . 'if(res&&res.success){var oid=(res.data&&res.data.id)?res.data.id:"";m.style.color="#16a34a";m.textContent="✓ Order placed!"+(oid?" Your order number is #"+oid+".":"")+" We will contact you shortly.";f.reset();b.disabled=false;b.textContent="Place order";}'
                . 'else{m.style.color="#dc2626";m.textContent=(res&&res.message)||"Could not place the order.";b.disabled=false;b.textContent="Place order";}})'
                . '.catch(function(){m.style.color="#dc2626";m.textContent="Connection error.";b.disabled=false;b.textContent="Place order";});});})();</script>';

            $orderHtml = $variantEl . $btnRow . $form . $script;
        }

        $reviewsHtml = self::reviewsBlock($item);

        $article = '<article class="tf-container" style="padding-top:calc(56px*var(--space-scale));padding-bottom:calc(56px*var(--space-scale))">'
            . '<a href="' . self::esc($backPath) . '" style="display:inline-block;margin-bottom:22px;font-size:14px;font-weight:600;color:var(--color-primary);text-decoration:none">&larr; Back</a>'
            . '<div class="tf-two" style="align-items:start">'
            . '<div>' . $gallery . '</div>'
            . '<div style="text-align:left">'
            . (!empty($item['meta']) ? '<p style="margin:0 0 6px;font-size:13px;font-weight:600;color:var(--color-accent)">' . self::esc($item['meta']) . '</p>' : '')
            . '<h1 style="margin:0;font-family:var(--font-heading);font-size:32px;line-height:1.2;font-weight:700">' . self::esc($item['title'] ?? '') . '</h1>'
            . $priceHtml
            . $orderHtml
            . ($ctaHtml ? '<div style="margin-top:20px">' . $ctaHtml . '</div>' : '')
            . self::productInfoTable($item)
            // Only when there IS a description. An item can now reach this page on
            // the strength of its photos alone, and an empty block would draw a
            // stray rule across the page under the gallery.
            . (trim((string)($item['body'] ?? '')) !== ''
                ? '<div style="margin-top:24px;padding-top:24px;border-top:1px solid var(--color-border);font-size:16px;line-height:1.75">'
                  . self::articleBody($item['body']) . '</div>'
                : '')
            . self::productPolicies($doc)
            . self::productInstagram($item)
            . '</div></div>'
            . $reviewsHtml
            . self::relatedProducts($doc, $item, $backPath)
            . '</article>';

        return "<!DOCTYPE html><html lang=\"" . self::esc($doc['site']['locale'] ?? 'en') . "\"><head>"
             . self::head($doc, $pseudo, $fonts)
             . "<style>.tf-site{" . $vars . "}" . self::baseCss() . "</style>"
             . "</head><body>"
             . "<main class=\"tf-site\">" . self::chromeSection($doc, 'header') . $article . self::chromeSection($doc, 'footer') . "</main>"
             . self::carouselScript() . self::cartScript() . self::animScript()
             . "</body></html>";
    }

    /**
     * Product ordering block: price that reacts to the picked variant, the
     * attribute pickers (Colour / Size / ...), stock/availability, Add to Cart
     * / Buy Now and the checkout form. With no attributes it behaves exactly
     * like a simple single-price product.
     *
     * Attributes are option groups (e.g. Colour: Red/Blue). Variants are exact
     * combinations (Red+Large) that can each override price/MRP/stock/photo —
     * mirroring Shopify's option1/option2/option3 model so a static, non-dynamic
     * form schema can still describe a full combinatorial matrix.
     */
    private static function productOrderBlock(array $doc, array $item, string $puid, ?string $cover): string
    {
        $slug = self::itemSlug($item, 'product');
        $uid  = 'od' . substr(md5($slug), 0, 6);
        $in = 'width:100%;padding:10px 12px;font-size:14px;border:1px solid var(--color-border);border-radius:var(--radius);background:var(--color-bg);color:var(--color-text)';
        $lb = 'display:block;margin-bottom:4px;font-size:12px;font-weight:700';

        // --- attributes: server-renders the pickers, JS only tracks selection ---
        $attrNames = [];
        $pickerHtml = '';
        foreach (($item['attributes'] ?? []) as $a) {
            $aname = trim((string)($a['name'] ?? ''));
            $opts  = array_values(array_filter((array)($a['options'] ?? []), function ($o) {
                return trim((string)($o['value'] ?? '')) !== '';
            }));
            if ($aname === '' || !$opts) continue;
            $attrNames[] = $aname;
            $idx = count($attrNames) - 1;

            $btns = '';
            foreach ($opts as $o) {
                $val = trim((string)($o['value'] ?? ''));
                $swatch = trim((string)($o['swatch'] ?? ''));
                $oimg = self::media($o['image'] ?? null) ?: '';
                $inner = '';
                if ($oimg !== '') $inner .= '<img src="' . self::esc($oimg) . '" alt="">';
                elseif ($swatch !== '') $inner .= '<span class="tf-attr-swatch" style="background:' . self::esc($swatch) . '"></span>';
                $inner .= '<span>' . self::esc($val) . '</span>';
                $btns .= '<button type="button" class="tf-attr-opt" data-ai="' . $idx . '" data-val="' . self::esc($val) . '" data-img="' . self::esc($oimg) . '">' . $inner . '</button>';
            }
            $pickerHtml .= '<div style="margin-top:18px"><label style="' . $lb . '">' . self::esc($aname) . '</label>'
                . '<div style="display:flex;flex-wrap:wrap;gap:8px">' . $btns . '</div></div>';
        }
        $attrCount = count($attrNames);

        // --- variants: exact combinations, matched against the picked values ---
        $variantsForJs = [];
        foreach (($item['variants'] ?? []) as $v) {
            $o1 = trim((string)($v['option1'] ?? ''));
            $o2 = trim((string)($v['option2'] ?? ''));
            $o3 = trim((string)($v['option3'] ?? ''));
            if ($o1 === '' && $o2 === '' && $o3 === '') continue;
            $stock = $v['stock'] ?? null;
            $variantsForJs[] = [
                'o1' => $o1, 'o2' => $o2, 'o3' => $o3,
                'sku'   => trim((string)($v['sku'] ?? '')),
                'price' => trim((string)($v['price'] ?? '')),
                'mrp'   => trim((string)($v['mrp'] ?? '')),
                'stock' => ($stock === null || $stock === '') ? null : (int)$stock,
                'image' => self::media($v['image'] ?? null) ?: '',
            ];
        }

        // --- price: server-rendered starting point, JS keeps it live ---
        [$sell, $mrp, $off] = self::priceBits($item);
        $priceHtml = '<div style="margin:10px 0 0;display:flex;align-items:baseline;flex-wrap:wrap;gap:10px">'
            . '<span id="' . $uid . '-sell" style="font-size:28px;font-weight:800;color:var(--color-primary)">' . self::esc($sell) . '</span>'
            . '<span id="' . $uid . '-mrp" style="font-size:16px;color:var(--tf-text,var(--color-muted));text-decoration:line-through' . ($mrp === '' ? ';display:none' : '') . '">' . self::esc($mrp) . '</span>'
            . '<span id="' . $uid . '-off" style="font-size:14px;font-weight:700;color:#16a34a' . ($off === '' ? ';display:none' : '') . '">' . self::esc($off) . '</span>'
            . '</div>'
            . '<p id="' . $uid . '-stock" role="status" style="margin:8px 0 0;font-size:13px;font-weight:700"></p>';

        $btnRow = '<div style="margin-top:20px;display:flex;flex-wrap:wrap;gap:12px">'
            . '<button type="button" id="' . $uid . '-cart" style="padding:13px 26px;font-size:15px;font-weight:700;border:1px solid var(--color-primary);border-radius:var(--radius);background:transparent;color:var(--color-primary);cursor:pointer">Add to Cart</button>'
            . '<button type="button" id="' . $uid . '-buy" style="padding:13px 26px;font-size:15px;font-weight:700;border:none;border-radius:var(--radius);background:var(--color-primary);color:var(--color-primary-fg);cursor:pointer">Buy Now</button>'
            . '<span id="' . $uid . '-note" role="status" style="align-self:center;font-size:13px;font-weight:600;color:#16a34a"></span>'
            . '</div>';

        $form = '<form id="' . $uid . '-form" style="display:none;margin-top:22px;max-width:420px;text-align:left;padding:18px;border:1px solid var(--color-border);border-radius:var(--radius);background:var(--color-surface)">'
            . '<p style="margin:0 0 12px;font-size:15px;font-weight:700">Your details</p>'
            . '<div style="margin-bottom:10px"><label style="' . $lb . '">Name *</label><input name="name" required style="' . $in . '"></div>'
            . '<div style="margin-bottom:10px"><label style="' . $lb . '">Contact number *</label><input name="phone" type="tel" required style="' . $in . '"></div>'
            . '<div style="margin-bottom:10px"><label style="' . $lb . '">Email</label><input name="email" type="email" style="' . $in . '"></div>'
            . '<div style="margin-bottom:14px"><label style="' . $lb . '">Additional note (optional)</label><textarea name="note" rows="2" placeholder="Any special instructions for this order…" style="' . $in . '"></textarea></div>'
            . '<button type="submit" style="width:100%;padding:12px;font-size:15px;font-weight:700;border:none;border-radius:var(--radius);background:var(--color-primary);color:var(--color-primary-fg);cursor:pointer">Place order</button>'
            . '<p id="' . $uid . '-msg" style="margin:10px 0 0;font-size:13px;font-weight:600"></p>'
            . '</form>';

        $payload = json_encode([
            'site'      => self::$slug,
            'item'      => $item['title'] ?? '',
            'slug'      => $slug,
            'price'     => $sell,
            'mrp'       => $mrp,
            'image'     => $cover ?: '',
            'gate'      => self::accountsEnabled($doc),
            'attrNames' => $attrNames,
            'attrCount' => $attrCount,
            'variants'  => $variantsForJs,
        ]);

        $script = '<script>(function(){var U=' . json_encode($uid) . ',PU=' . json_encode($puid) . ',D=' . $payload . ';'
            . 'var sel={},lastImg="",cur={price:D.price,mrp:D.mrp,image:D.image,label:"",value:"",sku:""};'
            . 'var f=document.getElementById(U+"-form"),n=document.getElementById(U+"-note"),m=document.getElementById(U+"-msg"),stockEl=document.getElementById(U+"-stock");'
            . 'var cartBtn=document.getElementById(U+"-cart"),buyBtn=document.getElementById(U+"-buy");'
            . 'function num(s){return parseFloat(String(s||"").replace(/[^0-9.]/g,""))||0;}'
            . 'function fmtOff(sell,mrp){var ns=num(sell),nm=num(mrp);if(nm>0&&ns>0&&ns<nm)return Math.round((nm-ns)/nm*100)+"% Off";return "";}'
            . 'function findVariant(vals){for(var j=0;j<D.variants.length;j++){var vv=D.variants[j],vo=[vv.o1||"",vv.o2||"",vv.o3||""],ok=true;'
            . 'for(var k=0;k<vals.length;k++){if((vals[k]||"")!==(vo[k]||"")){ok=false;break;}}if(ok)return vv;}return null;}'
            . 'function resolve(){'
            . 'var vals=[];for(var i=0;i<D.attrCount;i++)vals.push(sel[i]||"");'
            . 'var allPicked=D.attrCount===0||vals.every(function(v){return v!=="";});'
            . 'var matched=(D.attrCount>0&&D.variants.length&&allPicked)?findVariant(vals):null;'
            . 'var sp=(matched&&matched.price)?matched.price:D.price;'
            . 'var sm=(matched&&matched.mrp)?matched.mrp:D.mrp;'
            . 'var off=fmtOff(sp,sm);'
            . 'var sellEl=document.getElementById(U+"-sell"),mrpEl=document.getElementById(U+"-mrp"),offEl=document.getElementById(U+"-off");'
            . 'if(sellEl)sellEl.textContent=sp;'
            . 'if(mrpEl){mrpEl.textContent=sm;mrpEl.style.display=sm?"":"none";}'
            . 'if(offEl){offEl.textContent=off;offEl.style.display=off?"":"none";}'
            . 'var img=(matched&&matched.image)||lastImg||D.image;'
            // The variant photo replaces the first slide. Scroll back to it so
            // the swap is actually seen — but only when it CHANGED, or every
            // resolve() (including the one on load) would yank the gallery back
            // to slide 1 while the customer is swiping.
            . 'var mainImg=document.getElementById(PU+"-main");'
            . 'if(mainImg&&img&&mainImg.getAttribute("src")!==img){mainImg.src=img;'
            . 'var trk=document.getElementById(PU+"-track");if(trk&&trk.scrollTo)trk.scrollTo({left:0,behavior:"smooth"});}'
            . 'var blocked=false,msg="";'
            . 'if(D.attrCount>0&&!allPicked){msg="Select "+D.attrNames.filter(function(nm,i){return !vals[i];}).join(", ");blocked=true;}'
            . 'else if(D.attrCount>0&&D.variants.length&&allPicked&&!matched){msg="This combination is not available.";blocked=true;}'
            . 'else if(matched&&matched.stock!==null&&matched.stock<=0){msg="Out of stock";blocked=true;}'
            . 'else if(matched&&matched.stock!==null&&matched.stock<=5){msg=matched.stock+" left in stock";}'
            . 'if(stockEl){stockEl.textContent=msg;stockEl.style.color=blocked?"#dc2626":"#d97706";}'
            . 'cartBtn.disabled=blocked;buyBtn.disabled=blocked;'
            . 'cartBtn.style.opacity=blocked?"0.5":"1";buyBtn.style.opacity=blocked?"0.5":"1";'
            . 'cur={price:sp,mrp:sm,image:img,label:D.attrNames.filter(function(nm,i){return vals[i];}).join(", "),'
            . 'value:vals.filter(function(v){return v;}).join(", "),sku:matched?matched.sku:""};'
            . '}'
            . 'Array.prototype.forEach.call(document.querySelectorAll(".tf-attr-opt"),function(b){b.addEventListener("click",function(){'
            . 'var ai=b.getAttribute("data-ai");'
            . 'Array.prototype.forEach.call(document.querySelectorAll(\'.tf-attr-opt[data-ai="\'+ai+\'"]\'),function(x){x.classList.remove("is-sel");});'
            . 'b.classList.add("is-sel");sel[ai]=b.getAttribute("data-val");'
            . 'var im=b.getAttribute("data-img");if(im)lastImg=im;'
            . 'resolve();});});'
            . 'resolve();'
            . 'function token(){try{return (JSON.parse(localStorage.getItem("tf_customer_"+D.site))||{}).token||"";}catch(e){return "";}}'
            . 'cartBtn.addEventListener("click",function(){if(cartBtn.disabled)return;'
            . 'var n2=window.tfCart.add({slug:D.slug,item:D.item,price:cur.price,mrp:cur.mrp,vlabel:cur.label,variant:cur.value,image:cur.image,qty:1});'
            . 'n.innerHTML="Added to cart ("+n2+") &middot; <a href=\"/cart\" style=\"color:inherit\">View cart</a>";});'
            . 'buyBtn.addEventListener("click",function(){if(buyBtn.disabled)return;'
            . 'if(D.gate&&!token()){location.href="/account?next="+encodeURIComponent(location.pathname);return;}'
            . 'f.style.display="block";f.scrollIntoView({behavior:"smooth",block:"center"});});'
            . 'f.addEventListener("submit",function(e){e.preventDefault();var b=f.querySelector("button[type=submit]");b.disabled=true;b.textContent="Placing…";'
            . 'var fd={site:D.site,item:D.item,item_slug:D.slug,image:cur.image,price:cur.price,mrp:cur.mrp,option_label:cur.label,option_value:cur.value,'
            . 'name:f.name.value,phone:f.phone.value,email:f.email.value,note:(f.note?f.note.value:""),'
            . 'customer_token:(function(){try{return (JSON.parse(localStorage.getItem("tf_customer_"+D.site))||{}).token||"";}catch(e){return "";}})()};'
            . 'fetch("' . self::apiBase() . '/api/sites/order-submit.php",{method:"POST",headers:{"Content-Type":"application/json","Accept":"application/json"},body:JSON.stringify(fd)})'
            . '.then(function(r){return r.json();}).then(function(res){'
            . 'if(res&&res.success){var oid=(res.data&&res.data.id)?res.data.id:"";m.style.color="#16a34a";m.textContent="✓ Order placed!"+(oid?" Your order number is #"+oid+".":"")+" We will contact you shortly.";f.reset();b.disabled=false;b.textContent="Place order";}'
            . 'else{m.style.color="#dc2626";m.textContent=(res&&res.message)||"Could not place the order.";b.disabled=false;b.textContent="Place order";}})'
            . '.catch(function(){m.style.color="#dc2626";m.textContent="Connection error.";b.disabled=false;b.textContent="Place order";});});'
            . '})();</script>';

        return $pickerHtml . $priceHtml . $btnRow . $form . $script;
    }

    /** Render one product on its own page — same shape as a service detail page,
     *  but the price/photo react to whichever variant combination is picked. */
    private static function renderProductDetail(array $doc, array $item): string
    {
        $vars  = self::themeVars($doc['theme'] ?? []);
        $fonts = self::googleFonts($doc['theme'] ?? []);
        $name  = $doc['site']['name'] ?? '';

        $pseudo = [
            'slug'  => '/product/' . self::itemSlug($item, 'product'),
            'title' => $item['title'] ?? 'Product',
            'seo'   => [
                'title'       => trim(($item['title'] ?? 'Product') . ' | ' . $name, ' |'),
                'description' => $item['desc'] ?? '',
                'ogImage'     => $item['image'] ?? null,
                'robots'      => 'index,follow',
            ],
        ];

        // Where "Back" goes: the page that holds a Products section, else home.
        $backPath = '/';
        foreach (($doc['pages'] ?? []) as $pg) {
            foreach (($pg['sections'] ?? []) as $s) {
                if (($s['type'] ?? '') === 'products') { $backPath = $pg['slug'] ?? '/'; break 2; }
            }
        }

        $photos = [];
        $cover = self::media($item['image'] ?? null);
        if ($cover) $photos[] = ['src' => $cover, 'alt' => $item['title'] ?? ''];
        foreach (($item['gallery'] ?? []) as $g) {
            $src = self::media($g['image'] ?? null);
            if ($src) $photos[] = ['src' => $src, 'alt' => $g['alt'] ?? ($item['title'] ?? '')];
        }

        $puid = 'pg' . substr(md5(self::itemSlug($item, 'product')), 0, 6);
        $gallery = self::productGallery($photos, $puid);

        $ctaHtml = !empty($item['cta']['text']) ? self::btn($item['cta']) : '';

        $orderHtml = '';
        if (($item['enableOrder'] ?? true) !== false) {
            $orderHtml = self::productOrderBlock($doc, $item, $puid, $cover);
        } else {
            [$sell, $mrp, $off] = self::priceBits($item);
            if ($sell !== '' || $mrp !== '') {
                $orderHtml = '<div style="margin:10px 0 0;display:flex;align-items:baseline;flex-wrap:wrap;gap:10px">'
                    . ($sell !== '' ? '<span style="font-size:28px;font-weight:800;color:var(--color-primary)">' . self::esc($sell) . '</span>' : '')
                    . ($mrp !== '' ? '<span style="font-size:16px;color:var(--tf-text,var(--color-muted));text-decoration:line-through">' . self::esc($mrp) . '</span>' : '')
                    . ($off !== '' ? '<span style="font-size:14px;font-weight:700;color:#16a34a">' . $off . '</span>' : '')
                    . '</div>';
            }
        }

        $reviewsHtml = self::reviewsBlock($item, 'product');

        $article = '<article class="tf-container" style="padding-top:calc(56px*var(--space-scale));padding-bottom:calc(56px*var(--space-scale))">'
            . '<a href="' . self::esc($backPath) . '" style="display:inline-block;margin-bottom:22px;font-size:14px;font-weight:600;color:var(--color-primary);text-decoration:none">&larr; Back</a>'
            . '<div class="tf-two" style="align-items:start">'
            . '<div>' . $gallery . '</div>'
            . '<div style="text-align:left">'
            . (!empty($item['meta']) ? '<p style="margin:0 0 6px;font-size:13px;font-weight:600;color:var(--color-accent)">' . self::esc($item['meta']) . '</p>' : '')
            . '<h1 style="margin:0;font-family:var(--font-heading);font-size:32px;line-height:1.2;font-weight:700">' . self::esc($item['title'] ?? '') . '</h1>'
            . $orderHtml
            . ($ctaHtml ? '<div style="margin-top:20px">' . $ctaHtml . '</div>' : '')
            . self::productInfoTable($item)
            // Only when there IS a description. An item can now reach this page on
            // the strength of its photos alone, and an empty block would draw a
            // stray rule across the page under the gallery.
            . (trim((string)($item['body'] ?? '')) !== ''
                ? '<div style="margin-top:24px;padding-top:24px;border-top:1px solid var(--color-border);font-size:16px;line-height:1.75">'
                  . self::articleBody($item['body']) . '</div>'
                : '')
            . self::productPolicies($doc)
            . self::productInstagram($item)
            . '</div></div>'
            . $reviewsHtml
            . self::relatedProducts($doc, $item, $backPath, 'product')
            . '</article>';

        return "<!DOCTYPE html><html lang=\"" . self::esc($doc['site']['locale'] ?? 'en') . "\"><head>"
             . self::head($doc, $pseudo, $fonts)
             . "<style>.tf-site{" . $vars . "}" . self::baseCss() . "</style>"
             . "</head><body>"
             . "<main class=\"tf-site\">" . self::chromeSection($doc, 'header') . $article . self::chromeSection($doc, 'footer') . "</main>"
             . self::carouselScript() . self::cartScript() . self::animScript()
             . "</body></html>";
    }

    /**
     * Product search at /search?q=… — what the header's search box submits to.
     *
     * Server-side, so no product index is shipped to every visitor and each
     * result is a real link. Every word typed must appear in the name, meta
     * line, short description or badge: "silver ganesh" should not return every
     * silver item on the site.
     */
    private static function renderSearchPage(array $doc, string $q): string
    {
        $vars  = self::themeVars($doc['theme'] ?? []);
        $fonts = self::googleFonts($doc['theme'] ?? []);
        $name  = $doc['site']['name'] ?? '';
        $q     = trim(mb_substr((string)preg_replace('/\s+/u', ' ', $q), 0, 80));

        $pseudo = [
            'slug'  => '/search',
            'title' => 'Search',
            'seo'   => [
                'title'       => trim(($q !== '' ? $q . ' · ' : '') . 'Search | ' . $name, ' |'),
                'description' => '',
                'robots'      => 'noindex,follow',
            ],
        ];

        $terms = $q === '' ? [] : array_values(array_filter(explode(' ', mb_strtolower($q)), fn($t) => $t !== ''));
        $biz   = $doc['business'] ?? [];
        $seen  = [];
        $hits  = [];
        foreach ($terms ? ($doc['pages'] ?? []) : [] as $pg) {
            if (($pg['visible'] ?? true) === false) continue;
            foreach (($pg['sections'] ?? []) as $s) {
                if (($s['type'] ?? '') !== 'products' || ($s['visible'] ?? true) === false) continue;
                $sp = $s['props'] ?? [];
                $wa = self::isWhatsappOrder($sp);
                foreach (self::resolveItems($sp, $doc) as $it) {
                    $slug = self::itemSlug($it, 'product');
                    if (isset($seen[$slug])) continue;
                    $hay = mb_strtolower(implode(' ', [$it['title'] ?? '', $it['meta'] ?? '', $it['desc'] ?? '', $it['badge'] ?? '']));
                    foreach ($terms as $t) {
                        if (mb_strpos($hay, $t) === false) continue 2;
                    }
                    $seen[$slug] = true;
                    $href = (!$wa && self::hasDetailPage($it)) ? '/product/' . $slug : '';
                    $hits[] = self::shopCard($it, $href, ['orderLabel' => $sp['orderLabel'] ?? '', 'cardButton' => $sp['cardButton'] ?? ''], $biz, $wa);
                    if (count($hits) >= 120) break 3;
                }
            }
        }

        $n = count($hits);
        $status = $q === ''
            ? 'Type the name of what you are looking for.'
            : ($n ? $n . ' result' . ($n === 1 ? '' : 's') . ' for “' . self::esc($q) . '”'
                  : 'Nothing matched “' . self::esc($q) . '”. Try a shorter or different word.');
        $form = '<form action="/search" method="get" role="search" class="tf-search-big">'
              . '<input type="search" name="q" value="' . self::esc($q) . '" placeholder="Search products" aria-label="Search products"' . ($q === '' ? ' autofocus' : '') . '>'
              . '<button type="submit" class="tf-btn" style="background:var(--color-primary);color:var(--color-primary-fg);border-radius:var(--radius)">Search</button></form>';
        $article = '<section class="tf-container" style="padding-top:calc(48px*var(--space-scale));padding-bottom:calc(64px*var(--space-scale));text-align:left">'
            . '<h1 style="margin:0 0 18px;font-family:var(--font-heading);font-size:32px;line-height:1.2;font-weight:700">Search</h1>'
            . $form
            . '<p style="margin:16px 0 28px;color:var(--color-muted)">' . $status . '</p>'
            . ($hits ? '<div class="tf-shopwrap" style="--shop-ratio:3/4"><div class="tf-shopgrid tf-shop-c4">' . implode('', $hits) . '</div></div>' : '')
            . '</section>';

        return "<!DOCTYPE html><html lang=\"" . self::esc($doc['site']['locale'] ?? 'en') . "\"><head>"
             . self::head($doc, $pseudo, $fonts)
             . "<style>.tf-site{" . $vars . "}" . self::baseCss() . "</style>"
             . "</head><body>"
             . "<main class=\"tf-site\">" . self::chromeSection($doc, 'header') . $article . self::chromeSection($doc, 'footer') . "</main>"
             . self::carouselScript() . self::cartScript() . self::animScript()
             . "</body></html>";
    }

    /**
     * The cart page at /cart.
     *
     * The list itself only exists in the visitor's browser, so the server sends
     * an empty shell and the script fills it in. Checkout posts one request per
     * line, because order-submit.php records a single item per row -- that keeps
     * every line individually visible in the dashboard's Orders list.
     */
    private static function renderCartPage(array $doc): string
    {
        $vars  = self::themeVars($doc['theme'] ?? []);
        $fonts = self::googleFonts($doc['theme'] ?? []);
        $name  = $doc['site']['name'] ?? '';

        $pseudo = [
            'slug'  => '/cart',
            'title' => 'Your cart',
            'seo'   => [
                'title'       => trim('Your cart | ' . $name, ' |'),
                'description' => '',
                'robots'      => 'noindex,nofollow',
            ],
        ];

        $in = 'width:100%;padding:10px 12px;font-size:14px;border:1px solid var(--color-border);border-radius:var(--radius);background:var(--color-bg);color:var(--color-text)';
        $lb = 'display:block;margin-bottom:4px;font-size:12px;font-weight:700';

        $form = '<form id="tf-cart-form" style="text-align:left">'
            . '<p style="margin:0 0 12px;font-size:15px;font-weight:700">Your details</p>'
            . '<div style="margin-bottom:10px"><label style="' . $lb . '">Name *</label><input name="name" required style="' . $in . '"></div>'
            . '<div style="margin-bottom:10px"><label style="' . $lb . '">Contact number *</label><input name="phone" type="tel" required style="' . $in . '"></div>'
            . '<div style="margin-bottom:10px"><label style="' . $lb . '">Email</label><input name="email" type="email" style="' . $in . '"></div>'
            . '<div style="margin-bottom:14px"><label style="' . $lb . '">Note (optional)</label><textarea name="note" rows="2" style="' . $in . '"></textarea></div>'
            . '<button type="submit" style="width:100%;padding:13px;font-size:15px;font-weight:700;border:none;border-radius:var(--radius);background:var(--color-primary);color:var(--color-primary-fg);cursor:pointer">Place order</button>'
            . '<p id="tf-cart-msg" role="status" style="margin:10px 0 0;font-size:13px;font-weight:600"></p>'
            . '</form>';

        $summary = '<aside class="tf-card" style="padding:20px;text-align:left">'
            . '<div style="display:flex;justify-content:space-between;font-size:15px;font-weight:700;margin-bottom:16px">'
            . '<span>Total</span><span id="tf-cart-total">&mdash;</span></div>'
            . $form . '</aside>';

        $article = '<article class="tf-container" style="padding-top:calc(56px*var(--space-scale));padding-bottom:calc(56px*var(--space-scale))">'
            . '<h1 style="margin:0 0 24px;font-family:var(--font-heading);font-size:30px;font-weight:700">Your cart</h1>'
            . '<p id="tf-cart-empty" style="display:none;font-size:15px;color:var(--tf-text,var(--color-muted))">'
            . 'Your cart is empty. <a href="/" style="color:var(--color-primary);font-weight:600">Continue shopping</a></p>'
            . '<div id="tf-cart-wrap" style="display:none">'
            . '<div class="tf-two" style="align-items:start;text-align:left">'
            . '<div id="tf-cart-items"></div>' . $summary
            . '</div></div>'
            . '<div id="tf-cart-done" style="display:none;text-align:center;padding:40px 0">'
            . '<p style="font-size:20px;font-weight:700;color:#16a34a">&#10003; Order placed</p>'
            . '<p id="tf-cart-nums" style="margin-top:6px;font-size:14px;font-weight:600;color:var(--color-primary)"></p>'
            . '<p style="margin-top:8px;font-size:15px;color:var(--tf-text,var(--color-muted))">Thank you! We will contact you shortly to confirm.</p>'
            . '<a href="/" style="display:inline-block;margin-top:18px;font-size:14px;font-weight:600;color:var(--color-primary)">Continue shopping</a></div>'
            . '</article>';

        $script = str_replace(
            ['__API__', '__SITE__', '__GATE__'],
            [self::apiBase(), self::esc(self::$slug), self::accountsEnabled($doc) ? '1' : '0'],
            self::cartPageScript()
        );

        return "<!DOCTYPE html><html lang=\"" . self::esc($doc['site']['locale'] ?? 'en') . "\"><head>"
             . self::head($doc, $pseudo, $fonts)
             . "<style>.tf-site{" . $vars . "}" . self::baseCss() . "</style>"
             . "</head><body>"
             . "<main class=\"tf-site\">" . self::chromeSection($doc, 'header') . $article . self::chromeSection($doc, 'footer') . "</main>"
             . self::cartScript() . $script . self::animScript()
             . "</body></html>";
    }

    /** Built-in login / signup page — the account section wrapped in header/footer. */
    private static function renderAccountPage(array $doc): string
    {
        $vars  = self::themeVars($doc['theme'] ?? []);
        $fonts = self::googleFonts($doc['theme'] ?? []);
        $name  = $doc['site']['name'] ?? '';

        // Reuse an account section the customer placed on a page (for its heading
        // and phone toggle); otherwise synthesize a default one so the button
        // always lands on a real login/signup page.
        $acc = null;
        foreach (($doc['pages'] ?? []) as $pg) {
            foreach (($pg['sections'] ?? []) as $s) {
                if (($s['type'] ?? '') === 'account') { $acc = $s; break 2; }
            }
        }
        if (!$acc) {
            $acc = [
                'id'    => 'account',
                'type'  => 'account',
                'props' => ['heading' => 'My Account', 'sub' => 'Sign in or create an account to track your orders.'],
                'style' => ['pad' => 'lg'],
            ];
        }
        // Force it visible even if the source section was hidden.
        unset($acc['style']['hidden']);
        $acc['visible'] = true;

        $pseudo = [
            'slug'  => '/account',
            'title' => 'My Account',
            'seo'   => [
                'title'       => trim('My Account | ' . $name, ' |'),
                'description' => '',
                'robots'      => 'noindex,nofollow',
            ],
        ];

        return "<!DOCTYPE html><html lang=\"" . self::esc($doc['site']['locale'] ?? 'en') . "\"><head>"
             . self::head($doc, $pseudo, $fonts)
             . "<style>.tf-site{" . $vars . "}" . self::baseCss() . "</style>"
             . "</head><body>"
             . "<main class=\"tf-site\">"
             .   self::chromeSection($doc, 'header')
             .   self::section($acc, $doc)
             .   self::chromeSection($doc, 'footer')
             . "</main>"
             . self::cartScript() . self::animScript()
             . "</body></html>";
    }

    /** Built-in "My Orders" page — full history with reorder / cancel / review. */
    private static function renderOrdersPage(array $doc): string
    {
        $vars  = self::themeVars($doc['theme'] ?? []);
        $fonts = self::googleFonts($doc['theme'] ?? []);
        $name  = $doc['site']['name'] ?? '';

        $pseudo = [
            'slug'  => '/orders',
            'title' => 'My Orders',
            'seo'   => ['title' => trim('My Orders | ' . $name, ' |'), 'description' => '', 'robots' => 'noindex,nofollow'],
        ];

        $article = '<article class="tf-container" style="padding-top:calc(48px*var(--space-scale));padding-bottom:calc(56px*var(--space-scale))">'
            . '<h1 style="margin:0 0 20px;font-family:var(--font-heading);font-size:28px;font-weight:700">My Orders</h1>'
            . '<div id="tf-orders"><p style="font-size:14px;color:var(--tf-text,var(--color-muted))">Loading your orders…</p></div>'
            . '</article>';

        $script = str_replace(
            ['__API__', '__SITE__'],
            [self::apiBase(), self::esc(self::$slug)],
            self::ordersPageScript()
        );

        return "<!DOCTYPE html><html lang=\"" . self::esc($doc['site']['locale'] ?? 'en') . "\"><head>"
             . self::head($doc, $pseudo, $fonts)
             . "<style>.tf-site{" . $vars . "}" . self::baseCss() . "</style>"
             . "</head><body>"
             . "<main class=\"tf-site\">" . self::chromeSection($doc, 'header') . $article . self::chromeSection($doc, 'footer') . "</main>"
             . self::cartScript() . $script . self::animScript()
             . "</body></html>";
    }

    private static function ordersPageScript(): string
    {
        return <<<'JS'
<script>(function(){
var SITE="__SITE__",API="__API__",KEY="tf_customer_"+SITE;
function acc(){try{return JSON.parse(localStorage.getItem(KEY));}catch(e){return null;}}
var a=acc();
if(!a||!a.token){location.href="/account?next=/orders";return;}
var box=document.getElementById("tf-orders");
function esc(s){var d=document.createElement("div");d.textContent=(s==null?"":s);return d.innerHTML;}
function murl(v){if(!v)return "";if(/^https?:\/\//.test(v))return v;var m=/^media:(\d+)$/.exec(v);return m?(API+"/api/sites/media.php?id="+m[1]):"";}
var STEPS=[["new","Ordered"],["confirmed","Confirmed"],["completed","Delivered"]];
function timeline(st){if(st==="cancelled")return '<div style="margin-top:10px;font-size:12px;font-weight:700;color:#dc2626">Cancelled</div>';var idx={"new":0,"confirmed":1,"completed":2}[st];if(idx==null)idx=0;var h='<div style="display:flex;gap:6px;margin-top:12px">';for(var i=0;i<STEPS.length;i++){var on=i<=idx;h+='<div style="flex:1;text-align:center"><div style="height:5px;border-radius:3px;background:'+(on?"var(--color-primary)":"var(--color-border)")+'"></div><div style="font-size:10px;margin-top:4px;font-weight:'+(on?"700":"500")+';color:'+(on?"var(--color-primary)":"var(--color-muted)")+'">'+STEPS[i][1]+'</div></div>';}return h+"</div>";}
var ORDERS=[];
function card(o){
  var img=murl(o.item_image);
  var canCancel=(o.status==="new"||o.status==="confirmed");
  var done=(o.status==="completed");
  var h='<div data-order="'+esc(o.id)+'" class="tf-card" style="padding:14px;margin-bottom:14px">';
  h+='<div style="display:flex;gap:14px">';
  if(img)h+='<img src="'+esc(img)+'" alt="" style="width:80px;height:80px;object-fit:cover;border-radius:8px;flex-shrink:0">';
  h+='<div style="flex:1;min-width:0"><div style="display:flex;justify-content:space-between;gap:8px"><strong style="font-size:15px">'+esc(o.item_title||"Order")+'</strong><span style="font-size:12px;color:var(--tf-text,var(--color-muted))">#'+esc(o.id)+'</span></div>';
  h+='<div style="font-size:13px;color:var(--tf-text,var(--color-muted));margin-top:3px">'+esc(o.price||"")+((o.quantity>1)?(" × "+esc(o.quantity)):"")+(o.option_value?(" · "+esc(o.option_value)):"")+'</div></div></div>';
  h+=timeline(o.status);
  h+='<div style="display:flex;flex-wrap:wrap;gap:8px;margin-top:12px">';
  h+='<button type="button" data-again style="background:var(--color-primary);color:var(--color-primary-fg);border:none;border-radius:8px;padding:8px 14px;font-size:12px;font-weight:700;cursor:pointer">Buy again</button>';
  if(o.item_slug)h+='<a href="/service/'+esc(o.item_slug)+'" style="border:1px solid var(--color-border);border-radius:8px;padding:8px 14px;font-size:12px;font-weight:700;color:var(--color-text);text-decoration:none">View product</a>';
  if(canCancel)h+='<button type="button" data-cancel style="background:none;border:1px solid #dc2626;color:#dc2626;border-radius:8px;padding:8px 14px;font-size:12px;font-weight:700;cursor:pointer">Cancel</button>';
  if(done&&o.item_slug)h+='<button type="button" data-review-toggle style="background:none;border:1px solid var(--color-primary);color:var(--color-primary);border-radius:8px;padding:8px 14px;font-size:12px;font-weight:700;cursor:pointer">&#9733; Review</button>';
  h+='</div>';
  if(done&&o.item_slug){
    h+='<div data-review-form data-slug="'+esc(o.item_slug)+'" data-rating="5" style="display:none;margin-top:10px"><div data-stars style="font-size:24px;color:#f59e0b;cursor:pointer;letter-spacing:4px">';
    for(var s=1;s<=5;s++)h+='<span data-star="'+s+'">&#9733;</span>';
    h+='</div><textarea data-review-text placeholder="Share your experience…" style="width:100%;padding:8px 10px;font-size:13px;border:1px solid var(--color-border);border-radius:8px;background:var(--color-bg);color:var(--color-text);margin-top:8px;min-height:60px"></textarea>';
    h+='<button type="button" data-review-submit style="margin-top:6px;background:var(--color-primary);color:var(--color-primary-fg);border:none;border-radius:8px;padding:8px 14px;font-size:13px;font-weight:700;cursor:pointer">Submit review</button><p data-review-msg style="font-size:12px;margin:6px 0 0"></p></div>';
  }
  return h+'</div>';
}
function render(){
  if(!ORDERS.length){box.innerHTML='<div class="tf-card" style="padding:24px;text-align:center"><p style="font-size:15px;font-weight:700;margin:0">No orders yet</p><p style="margin:6px 0 0;font-size:13px;color:var(--tf-text,var(--color-muted))">When you place an order it will show up here.</p><a href="/" style="display:inline-block;margin-top:14px;font-size:13px;font-weight:700;color:var(--color-primary)">Continue shopping</a></div>';return;}
  var h="";for(var i=0;i<ORDERS.length;i++)h+=card(ORDERS[i]);box.innerHTML=h;
}
function findOrder(id){for(var i=0;i<ORDERS.length;i++)if(String(ORDERS[i].id)===String(id))return ORDERS[i];return null;}
fetch(API+"/api/sites/customer-orders.php",{method:"POST",headers:{"Content-Type":"application/json"},body:JSON.stringify({site:SITE,token:a.token})}).then(function(r){return r.json();}).then(function(res){ORDERS=(res&&res.data&&res.data.orders)||[];render();}).catch(function(){box.innerHTML='<p style="color:#dc2626">Could not load your orders.</p>';});
box.addEventListener("click",function(e){
  var t=e.target;var cardEl=t.closest?t.closest("[data-order]"):null;if(!cardEl)return;var oid=cardEl.getAttribute("data-order");var o=findOrder(oid);
  if(t.closest&&t.closest("[data-again]")){if(o&&window.tfCart){window.tfCart.add({slug:o.item_slug,item:o.item_title,price:o.price,mrp:o.mrp||"",vlabel:o.option_label||"",variant:o.option_value||"",image:o.item_image||"",qty:1});location.href="/cart";}return;}
  if(t.closest&&t.closest("[data-cancel]")){var cb=t.closest("[data-cancel]");if(!confirm("Cancel this order?"))return;cb.disabled=true;cb.textContent="Cancelling…";fetch(API+"/api/sites/customer-order-cancel.php",{method:"POST",headers:{"Content-Type":"application/json","Accept":"application/json"},body:JSON.stringify({site:SITE,token:a.token,order_id:oid})}).then(function(r){return r.json();}).then(function(res){if(res&&res.success){if(o)o.status="cancelled";render();}else{cb.disabled=false;cb.textContent="Cancel";alert((res&&res.message)||"Could not cancel.");}}).catch(function(){cb.disabled=false;cb.textContent="Cancel";alert("Connection error.");});return;}
  var tog=t.closest?t.closest("[data-review-toggle]"):null;if(tog){var f=cardEl.querySelector("[data-review-form]");if(f)f.style.display=(f.style.display==="none")?"block":"none";return;}
  var star=t.closest?t.closest("[data-star]"):null;if(star){var form=star.closest("[data-review-form]");var val=parseInt(star.getAttribute("data-star"),10);form.setAttribute("data-rating",val);var sp=form.querySelectorAll("[data-star]");for(var i=0;i<sp.length;i++)sp[i].style.color=(i<val)?"#f59e0b":"#d1d5db";return;}
  var sub=t.closest?t.closest("[data-review-submit]"):null;if(sub){var form2=sub.closest("[data-review-form]");var msg=form2.querySelector("[data-review-msg]");var txt=(form2.querySelector("[data-review-text]").value||"").trim();var rating=parseInt(form2.getAttribute("data-rating"),10)||5;var slug=form2.getAttribute("data-slug");if(!txt){msg.style.color="#dc2626";msg.textContent="Please write your review.";return;}sub.disabled=true;sub.textContent="Submitting…";fetch(API+"/api/sites/review-submit.php",{method:"POST",headers:{"Content-Type":"application/json","Accept":"application/json"},body:JSON.stringify({site:SITE,item_slug:slug,name:(a&&a.name)||"Customer",rating:rating,comment:txt})}).then(function(r){return r.json();}).then(function(res){if(res&&res.success){form2.innerHTML='<p style="font-size:13px;color:#16a34a;font-weight:700">&#10003; Thanks! Your review has been submitted.</p>';}else{msg.style.color="#dc2626";msg.textContent=(res&&res.message)||"Could not submit.";sub.disabled=false;sub.textContent="Submit review";}}).catch(function(){msg.style.color="#dc2626";msg.textContent="Connection error.";sub.disabled=false;sub.textContent="Submit review";});return;}
});
})();</script>
JS;
    }

    private static function cartPageScript(): string
    {
        return <<<'JS'
<script>(function(){
var SITE="__SITE__",GATE="__GATE__";
function token(){try{return (JSON.parse(localStorage.getItem("tf_customer_"+SITE))||{}).token||"";}catch(e){return "";}}
var wrap=document.getElementById("tf-cart-wrap"),list=document.getElementById("tf-cart-items"),
    empty=document.getElementById("tf-cart-empty"),done=document.getElementById("tf-cart-done"),
    totalEl=document.getElementById("tf-cart-total"),form=document.getElementById("tf-cart-form"),
    msg=document.getElementById("tf-cart-msg");
// Prices are free text ("Rs 1,299", "$40"), so pull the number out for the maths
// and reuse whatever prefix the customer typed when printing the total.
function num(p){var n=parseFloat(String(p||"").replace(/[^0-9.]/g,""));return isFinite(n)?n:0;}
function cur(p){var m=String(p||"").match(/^[^0-9]+/);return m?m[0].trim():"";}
function esc(t){var d=document.createElement("div");d.textContent=t==null?"":t;return d.innerHTML;}
function render(){
  var c=window.tfCart.read();
  if(!c.length){wrap.style.display="none";empty.style.display="";return;}
  empty.style.display="none";wrap.style.display="";
  var html="",total=0,sym="";
  for(var i=0;i<c.length;i++){
    var it=c[i],q=parseInt(it.qty,10)||1;
    total+=num(it.price)*q; if(!sym)sym=cur(it.price);
    html+='<div class="tf-card" style="display:flex;gap:14px;padding:14px;margin-bottom:12px;align-items:center">'
      +(it.image?'<img src="'+esc(it.image)+'" alt="" style="width:74px;height:74px;object-fit:cover;border-radius:var(--radius);flex-shrink:0">':'')
      +'<div style="flex:1;min-width:0">'
      +'<a href="/service/'+esc(it.slug)+'" style="font-size:15px;font-weight:700;color:inherit;text-decoration:none">'+esc(it.item)+'</a>'
      +(it.variant?'<p style="margin:2px 0 0;font-size:12px;color:var(--tf-text,var(--color-muted))">'+esc(it.vlabel||"Option")+': '+esc(it.variant)+'</p>':'')
      +(it.price?'<p style="margin:4px 0 0;font-size:14px;font-weight:700;color:var(--color-primary)">'+esc(it.price)+'</p>':'')
      +'<div style="margin-top:8px;display:flex;align-items:center;gap:8px">'
      +'<button type="button" data-act="dec" data-i="'+i+'" aria-label="Decrease quantity" style="width:26px;height:26px;border:1px solid var(--color-border);border-radius:6px;background:var(--color-bg);color:inherit;cursor:pointer">&minus;</button>'
      +'<span style="min-width:20px;text-align:center;font-size:14px;font-weight:700">'+q+'</span>'
      +'<button type="button" data-act="inc" data-i="'+i+'" aria-label="Increase quantity" style="width:26px;height:26px;border:1px solid var(--color-border);border-radius:6px;background:var(--color-bg);color:inherit;cursor:pointer">+</button>'
      +'<button type="button" data-act="del" data-i="'+i+'" style="margin-left:6px;font-size:12px;color:#dc2626;background:none;border:0;cursor:pointer">Remove</button>'
      +'</div></div></div>';
  }
  list.innerHTML=html;
  totalEl.textContent=total>0?((sym?sym+" ":"")+total.toLocaleString()):"—";
}
list.addEventListener("click",function(e){
  var b=e.target.closest?e.target.closest("button[data-act]"):null;if(!b)return;
  var c=window.tfCart.read(),i=parseInt(b.getAttribute("data-i"),10),a=b.getAttribute("data-act");
  if(!c[i])return;
  if(a==="inc")c[i].qty=(parseInt(c[i].qty,10)||1)+1;
  else if(a==="dec"){c[i].qty=(parseInt(c[i].qty,10)||1)-1;if(c[i].qty<1)c.splice(i,1);}
  else c.splice(i,1);
  window.tfCart.write(c);render();
});
form.addEventListener("submit",function(e){
  e.preventDefault();
  var c=window.tfCart.read();if(!c.length)return;
  // Checkout requires an account when the site has login enabled.
  if(GATE==="1"&&!token()){location.href="/account?next=/cart";return;}
  var b=form.querySelector("button[type=submit]");b.disabled=true;b.textContent="Placing…";
  msg.style.color="";msg.textContent="";
  // One request per line so each item lands as its own order row.
  Promise.all(c.map(function(it){
    return fetch("__API__/api/sites/order-submit.php",{method:"POST",
      headers:{"Content-Type":"application/json","Accept":"application/json"},
      body:JSON.stringify({site:SITE,item:it.item,item_slug:it.slug,image:it.image||"",price:it.price,mrp:it.mrp||"",
        option_label:it.vlabel||"",option_value:it.variant||"",quantity:parseInt(it.qty,10)||1,
        name:form.name.value,phone:form.phone.value,email:form.email.value,note:form.note.value,
        customer_token:(function(){try{return (JSON.parse(localStorage.getItem("tf_customer_"+SITE))||{}).token||"";}catch(e){return "";}})()})
    }).then(function(r){return r.json();});
  })).then(function(all){
    if(!all.every(function(r){return r&&r.success;}))throw new Error("partial");
    var ids=all.map(function(r){return (r.data&&r.data.id)?("#"+r.data.id):"";}).filter(Boolean);
    var nums=document.getElementById("tf-cart-nums");
    if(nums&&ids.length)nums.textContent=(ids.length>1?"Order numbers: ":"Order number: ")+ids.join(", ");
    window.tfCart.write([]);
    wrap.style.display="none";empty.style.display="none";done.style.display="";
  }).catch(function(){
    msg.style.color="#dc2626";msg.textContent="Could not place the order. Please try again.";
    b.disabled=false;b.textContent="Place order";
  });
});
if(GATE==="1"&&!token()){var sb=form.querySelector("button[type=submit]");if(sb)sb.textContent="Login to checkout";}
if(document.readyState!=="loading")render();else document.addEventListener("DOMContentLoaded",render);
})();</script>
JS;
    }

    private static function secBlog(array $s, array $doc): string
    {
        $p = $s['props'] ?? [];
        $posts = $p['posts'] ?? [];
        if (!$posts) return '';

        // Build each card once — the grid, carousel and slider all reuse them.
        $slides = [];
        foreach ($posts as $b) {
            $img = self::media($b['image'] ?? null);
            $c = '<div class="tf-card">';
            if ($img) $c .= '<img src="' . self::esc($img) . '" alt="' . self::esc($b['title'] ?? '') . '" loading="lazy" style="height:200px;width:100%;object-fit:cover">';
            $c .= '<div style="padding:20px;text-align:left">';
            if (!empty($b['date'])) $c .= '<p style="margin:0 0 6px;font-size:12px;font-weight:600;color:var(--color-accent)">' . self::esc($b['date']) . '</p>';
            $c .= '<h3 style="margin:0;font-family:var(--font-heading);font-size:19px;font-weight:600">' . self::esc($b['title'] ?? '') . '</h3>';
            if (!empty($b['excerpt'])) $c .= '<p style="margin:8px 0 0;font-size:14px;color:var(--tf-text,var(--color-muted))">' . self::esc($b['excerpt']) . '</p>';
            // A post with a full article opens its own page; otherwise fall back to
            // the external link if one was given.
            if (trim((string)($b['body'] ?? '')) !== '') {
                $l = ['text' => ($b['linkText'] ?? 'Read more'), 'href' => '/post/' . self::itemSlug($b, 'post'), 'style' => 'link'];
                $c .= '<div style="margin-top:14px">' . self::btn($l) . '</div>';
            } elseif (!empty($b['href'])) {
                $l = ['text' => ($b['linkText'] ?? 'Read more'), 'href' => $b['href'], 'style' => 'link', 'newTab' => true];
                $c .= '<div style="margin-top:14px">' . self::btn($l) . '</div>';
            }
            $c .= '</div></div>';
            $slides[] = $c;
        }

        $variant = $s['variant'] ?? 'grid-3';
        if ($variant === 'slider') {
            $body = self::marquee($slides);
        } elseif ($variant === 'carousel') {
            $body = self::carousel($slides);
        } else {
            $gcls = $variant === 'grid-2' ? 'tf-grid tf-c2' : 'tf-grid tf-c3';
            $body = '<div class="' . $gcls . '" style="text-align:left">' . implode('', $slides) . '</div>';
        }

        $inner = self::sectionHeader($p['label'] ?? null, $p['heading'] ?? null, $p['sub'] ?? null, self::isDarkBg($s)) . $body;
        return self::shell($s, $inner);
    }

    private static function secAppointment(array $s, array $doc): string
    {
        $p   = $s['props'] ?? [];
        $biz = $doc['business'] ?? [];
        $wa  = preg_replace('/\D+/', '', (string)($biz['whatsapp'] ?? $biz['phone'] ?? ''));
        $email  = trim((string)($biz['email'] ?? ''));
        $notify = $p['alsoNotify'] ?? 'whatsapp';
        if ($notify === 'whatsapp' && $wa === '') $notify = $email !== '' ? 'email' : 'none';
        $services = array_values(array_filter(array_map('trim', (array)($p['services'] ?? []))));
        $uid = 'ap' . substr(md5(($s['id'] ?? '') . 'apt'), 0, 6);
        $in  = 'width:100%;padding:10px 12px;font-size:14px;border:1px solid var(--color-border);border-radius:var(--radius);background:var(--color-bg);color:var(--color-text)';
        $lb  = 'display:block;margin-bottom:4px;font-size:12px;font-weight:600';

        // The time list is filled in by JS once a date is picked — it comes from
        // the owner's availability minus whatever is already booked (see
        // api/sites/slots_public.php). Sites with no availability configured fall
        // back to this open half-hour list, so nothing that worked before breaks.
        $times = '<option value="">Select a date first…</option>';

        $f  = '<div><label style="' . $lb . '">Name *</label><input class="' . $uid . '-f" data-k="name" required style="' . $in . '"></div>';
        $f .= '<div><label style="' . $lb . '">Phone *</label><input class="' . $uid . '-f" data-k="phone" type="tel" required style="' . $in . '"></div>';
        $f .= '<div><label style="' . $lb . '">Email</label><input class="' . $uid . '-f" data-k="email" type="email" style="' . $in . '"></div>';
        $f .= '<div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">'
            . '<div><label style="' . $lb . '">Date *</label><input class="' . $uid . '-f" data-k="date" type="date" required min="' . date('Y-m-d') . '" style="' . $in . '"></div>'
            . '<div><label style="' . $lb . '">Time *</label><select class="' . $uid . '-f" data-k="time" required style="' . $in . '">' . $times . '</select></div>'
            . '</div>';
        // Choosing a service is compulsory whenever services are configured.
        if ($services) {
            $opts = '<option value="">Select a service…</option>';
            foreach ($services as $sv) $opts .= '<option>' . self::esc($sv) . '</option>';
            $f .= '<div><label style="' . $lb . '">Service *</label><select class="' . $uid . '-f" data-k="service" required style="' . $in . '">' . $opts . '</select></div>';
        }
        $f .= '<div><label style="' . $lb . '">Message</label><textarea class="' . $uid . '-f" data-k="notes" rows="3" style="' . $in . '"></textarea></div>';

        $form = '<form id="' . $uid . '" style="display:grid;gap:12px;max-width:480px;margin:0 auto;text-align:left">' . $f
              . '<button type="submit" style="margin-top:4px;padding:12px;font-size:15px;font-weight:600;border:none;border-radius:var(--radius);background:var(--color-primary);color:var(--color-primary-fg);cursor:pointer">'
              . self::esc($p['submitText'] ?? 'Request appointment') . '</button>'
              . '<p id="' . $uid . '-msg" style="margin:0;font-size:13px;font-weight:600"></p></form>';

        $cfg = json_encode([
            'site'   => self::$slug,
            'api'    => self::apiBase(),
            'notify' => $notify,
            'wa'     => $wa,
            'email'  => $email,
        ]);

        $script = '<script>(function(){var U=' . json_encode($uid) . ',C=' . $cfg . ';'
            . 'var fm=document.getElementById(U),m=document.getElementById(U+"-msg");if(!fm)return;'
            // ---- availability: load free slots for the chosen date ----
            . 'var dEl=fm.querySelector("[data-k=date]"),tEl=fm.querySelector("[data-k=time]");'
            // The option VALUE stays the bare time — the submit endpoint parses it.
            // Any "N left" hint goes in the visible text only.
            . 'function setOpts(list,ph){tEl.innerHTML="";var o=document.createElement("option");o.value="";o.textContent=ph;tEl.appendChild(o);'
            . 'list.forEach(function(s){var e=document.createElement("option");e.value=s.label;'
            . 'e.textContent=(s.left>0&&s.left<3)?(s.label+"  ("+s.left+" left)"):s.label;tEl.appendChild(e);});}'
            // The pre-availability behaviour, kept for sites whose owner has not
            // set a schedule yet — otherwise their booking form would go dead.
            . 'function openList(){var a=[];for(var h=0;h<24;h++){[0,30].forEach(function(mm){'
            . 'var ap=h<12?"AM":"PM",hh=(h%12)||12;a.push({label:("0"+hh).slice(-2)+":"+("0"+mm).slice(-2)+" "+ap});});}return a;}'
            . 'function loadSlots(){var dv=dEl&&dEl.value;if(!dv){setOpts([],"Select a date first…");return;}'
            . 'tEl.disabled=true;setOpts([],"Loading times…");'
            . 'fetch(C.api+"/api/sites/slots_public.php?site="+encodeURIComponent(C.site)+"&date="+encodeURIComponent(dv),{headers:{"Accept":"application/json"}})'
            . '.then(function(r){return r.json();}).then(function(res){tEl.disabled=false;'
            . 'var d=(res&&res.data)||{};'
            . 'if(!d.configured){setOpts(openList(),"Select a time…");return;}'
            . 'if(!d.slots||!d.slots.length){setOpts([],"No times available on this date");return;}'
            . 'setOpts(d.slots,"Select a time…");})'
            // A failed lookup must not strand the visitor with an empty dropdown,
            // so fall back to the open list rather than blocking the booking.
            . '.catch(function(){tEl.disabled=false;setOpts(openList(),"Select a time…");});}'
            . 'if(dEl&&tEl){dEl.addEventListener("change",loadSlots);loadSlots();}'
            . 'fm.addEventListener("submit",function(e){e.preventDefault();'
            . 'var d={};fm.querySelectorAll("."+U+"-f").forEach(function(el){d[el.getAttribute("data-k")]=(el.value||"").trim();});'
            . 'var b=fm.querySelector("button[type=submit]");b.disabled=true;var ob=b.textContent;b.textContent="Sending…";'
            . 'fetch(C.api+"/api/sites/appointment-submit.php",{method:"POST",headers:{"Content-Type":"application/json","Accept":"application/json"},'
            . 'body:JSON.stringify({site:C.site,name:d.name,phone:d.phone,email:d.email,date:d.date,time:d.time,service:d.service||"",notes:d.notes||""})})'
            . '.then(function(r){return r.json();}).then(function(res){b.disabled=false;b.textContent=ob;'
            . 'if(res&&res.success){m.style.color="#16a34a";m.textContent="✓ Appointment requested! We will confirm shortly.";'
            . 'var t=["Appointment request","Name: "+d.name,"Phone: "+d.phone];'
            . 'if(d.date)t.push("Date: "+d.date);if(d.time)t.push("Time: "+d.time);if(d.service)t.push("Service: "+d.service);if(d.notes)t.push("Message: "+d.notes);'
            . 'var txt=t.join("\n");'
            . 'if(C.notify==="whatsapp"&&C.wa){window.open("https://wa.me/"+C.wa+"?text="+encodeURIComponent(txt),"_blank");}'
            . 'else if(C.notify==="email"&&C.email){window.open("mailto:"+C.email+"?subject="+encodeURIComponent("Appointment request")+"&body="+encodeURIComponent(txt),"_blank");}'
            // Re-read availability after booking: the slot just taken must not
            // still be offered if they book again for the same day.
            . 'fm.reset();if(dEl&&tEl)loadSlots();}'
            . 'else{m.style.color="#dc2626";m.textContent=(res&&res.message)||"Could not send the request.";}})'
            . '.catch(function(){b.disabled=false;b.textContent=ob;m.style.color="#dc2626";m.textContent="Connection error.";});});})();</script>';

        $header  = self::sectionHeader($p['label'] ?? null, $p['heading'] ?? null, $p['sub'] ?? null, self::isDarkBg($s));
        $variant = $s['variant'] ?? 'form';
        $img     = self::media($p['image'] ?? null);

        // Optional split layout: photo on one side, the form on the other.
        if (in_array($variant, ['image-left', 'image-right'], true) && $img) {
            $image = '<img src="' . self::esc($img) . '" alt="' . self::esc($p['heading'] ?? 'Appointment') . '" loading="lazy"'
                   . ' style="width:100%;border-radius:var(--radius);max-height:520px;' . self::imgFit($p['imageFit'] ?? null, 'var(--radius)') . '">';
            // The form is centred inside its own column, so drop the auto margins.
            $col  = '<div style="text-align:left">' . str_replace('margin:0 auto;', '', $form) . '</div>';
            $cols = $variant === 'image-left' ? ($image . $col) : ($col . $image);
            return self::shell($s, $header . '<div class="tf-two" style="align-items:center;text-align:left">' . $cols . '</div>' . $script);
        }

        return self::shell($s, $header . $form . $script);
    }

    private static function secEmbed(array $s, array $doc): string
    {
        $p = $s['props'] ?? [];
        $variant = $s['variant'] ?? 'grid-2';
        // Instagram's own embed is ~540px wide, which is far too big once it sits
        // in a multi-column grid. Cap each card to its column's share.
        $maxW = $variant === 'grid-3' ? 300 : ($variant === 'single' ? 540 : 380);

        $blocks = '';
        foreach (($p['embeds'] ?? []) as $it) {
            $url = trim((string)($it['url'] ?? ''));
            if ($url === '' || !preg_match('#instagram\.com#i', $url)) continue;
            $blocks .= '<blockquote class="instagram-media" data-instgrm-permalink="' . self::esc($url) . '" data-instgrm-version="14" style="background:#fff;border:0;border-radius:3px;box-shadow:0 0 1px rgba(0,0,0,.5),0 1px 10px rgba(0,0,0,.15);margin:0 auto;max-width:' . $maxW . 'px;min-width:0;width:100%;padding:0"></blockquote>';
        }
        if ($blocks === '') return '';
        $gcls = $variant === 'grid-3' ? 'tf-grid tf-c3' : ($variant === 'single' ? '' : 'tf-grid tf-c2');
        $grid = $gcls ? ('<div class="' . $gcls . '" style="justify-items:center">' . $blocks . '</div>') : ('<div style="max-width:' . $maxW . 'px;margin:0 auto">' . $blocks . '</div>');
        $inner = self::sectionHeader($p['label'] ?? null, $p['heading'] ?? null, $p['sub'] ?? null, self::isDarkBg($s)) . $grid
               . '<script async src="//www.instagram.com/embed.js"></script>'
               . '<script>if(window.instgrm&&window.instgrm.Embeds)window.instgrm.Embeds.process();</script>';
        return self::shell($s, $inner);
    }

    private static function secShare(array $s, array $doc): string
    {
        $p = $s['props'] ?? [];
        $host = self::publicHost();
        $url = trim((string)($p['url'] ?? '')) ?: ('https://' . $host);
        $enc = rawurlencode($url);
        $title = rawurlencode($doc['site']['name'] ?? 'Check this out');
        $light = self::isDarkBg($s);
        $col = $light ? '#fff' : 'var(--color-text)';
        $qr = '';
        if (($p['showQr'] ?? true) !== false) {
            // An uploaded QR wins; otherwise generate one from the link.
            $qsrc = self::media($p['qrImage'] ?? null)
                 ?: ('https://api.qrserver.com/v1/create-qr-code/?size=220x220&margin=8&data=' . $enc);
            $qr = '<div style="display:inline-block;padding:14px;background:#fff;border-radius:var(--radius);box-shadow:0 8px 24px rgba(16,24,40,.12)"><img src="' . self::esc($qsrc) . '" alt="QR code" width="200" height="200" style="display:block;width:200px;height:200px;object-fit:contain"></div>';
        }
        $bStyle = 'display:inline-flex;align-items:center;gap:8px;padding:10px 16px;font-size:14px;font-weight:600;text-decoration:none;border-radius:var(--radius);border:1px solid var(--color-border);color:' . $col;
        $btns = [];
        if (($p['whatsapp'] ?? true) !== false) $btns[] = '<a href="https://wa.me/?text=' . $title . '%20' . $enc . '" target="_blank" rel="noopener" style="' . $bStyle . '">WhatsApp</a>';
        if (($p['facebook'] ?? true) !== false) $btns[] = '<a href="https://www.facebook.com/sharer/sharer.php?u=' . $enc . '" target="_blank" rel="noopener" style="' . $bStyle . '">Facebook</a>';
        if (($p['twitter'] ?? true) !== false) $btns[] = '<a href="https://twitter.com/intent/tweet?url=' . $enc . '&text=' . $title . '" target="_blank" rel="noopener" style="' . $bStyle . '">X</a>';
        $uid = 'sh' . substr(md5(($s['id'] ?? '') . 'shr'), 0, 6);
        $btns[] = '<button type="button" id="' . $uid . '" data-url="' . self::esc($url) . '" style="' . $bStyle . ';background:none;cursor:pointer">Copy link</button>';
        $copyJs = '<script>(function(){var b=document.getElementById(' . json_encode($uid) . ');if(!b)return;b.addEventListener("click",function(){var u=b.getAttribute("data-url");var d=function(){var o=b.textContent;b.textContent="Copied!";setTimeout(function(){b.textContent=o;},1500);};if(navigator.clipboard){navigator.clipboard.writeText(u).then(d,d);}else{d();}});})();</script>';
        $header  = self::sectionHeader($p['label'] ?? null, $p['heading'] ?? null, $p['sub'] ?? null, $light);
        $variant = $s['variant'] ?? 'default';
        $img     = self::media($p['image'] ?? null);

        // Optional split layout: photo on one side, QR + share buttons on the other.
        if (in_array($variant, ['image-left', 'image-right'], true) && $img) {
            $image = '<img src="' . self::esc($img) . '" alt="' . self::esc($p['heading'] ?? 'Share') . '" loading="lazy"'
                   . ' style="width:100%;border-radius:var(--radius);max-height:520px;' . self::imgFit($p['imageFit'] ?? null, 'var(--radius)') . '">';
            $col = '<div style="text-align:center">'
                 . ($qr ? ('<div style="margin-bottom:22px">' . $qr . '</div>') : '')
                 . '<div style="display:flex;flex-wrap:wrap;gap:10px;justify-content:center">' . implode('', $btns) . '</div>'
                 . '</div>';
            $cols = $variant === 'image-left' ? ($image . $col) : ($col . $image);
            return self::shell($s, $header . '<div class="tf-two" style="align-items:center">' . $cols . '</div>' . $copyJs);
        }

        $inner = $header
               . ($qr ? ('<div style="margin-bottom:22px">' . $qr . '</div>') : '')
               . '<div style="display:flex;flex-wrap:wrap;gap:10px;justify-content:center">' . implode('', $btns) . '</div>' . $copyJs;
        return self::shell($s, $inner);
    }

    /* --------------------------------------------------------- carousel js */

    /** Wires every .tf-carousel: arrows, dots, autoplay (pause on hover), swipe. */
    /**
     * The cart itself. Kept in localStorage under a per-site key so one browser
     * can shop several Tapify sites without them bleeding into each other, and
     * exposed as window.tfCart so the product page, the header badge and the
     * cart page all read and write the same list.
     *
     * There is no server-side cart: an order only reaches the backend when the
     * customer checks out, which keeps published sites entirely static.
     */
    private static function cartScript(): string
    {
        $key = json_encode('tf_cart_' . self::$slug);
        return '<script>(function(){var K=' . $key . ';'
             . 'function read(){try{var c=JSON.parse(localStorage.getItem(K)||"[]");return Array.isArray(c)?c:[];}catch(e){return [];}}'
             . 'function write(c){try{localStorage.setItem(K,JSON.stringify(c));}catch(e){}badge();}'
             . 'function count(){var n=0,c=read();for(var i=0;i<c.length;i++)n+=(parseInt(c[i].qty,10)||1);return n;}'
             . 'function badge(){var n=count();Array.prototype.forEach.call(document.querySelectorAll("[data-tf-cart-count]"),'
             . 'function(el){el.textContent=n;el.style.display=n?"":"none";});}'
             // The same product in the same option is one line with a bigger qty,
             // not a second line.
             . 'function add(it){var c=read(),f=null;'
             . 'for(var i=0;i<c.length;i++){if(c[i].slug===it.slug&&(c[i].variant||"")===(it.variant||"")){f=c[i];break;}}'
             . 'if(f){f.qty=(parseInt(f.qty,10)||1)+(parseInt(it.qty,10)||1);}else{c.push(it);}write(c);return count();}'
             . 'window.tfCart={read:read,write:write,count:count,add:add,refresh:badge};'
             . 'window.addEventListener("storage",function(e){if(e.key===K)badge();});'
             . 'if(document.readyState!=="loading")badge();else document.addEventListener("DOMContentLoaded",badge);'
             . '})();</script>';
    }

    private static function carouselScript(): string
    {
        return <<<'JS'
<script>(function(){
function init(car){
  if(car.dataset.tfc)return;car.dataset.tfc="1";
  var track=car.querySelector(".tf-ctrack");if(!track)return;
  var slides=Array.prototype.slice.call(track.children),n=slides.length;if(n<2)return;
  var dots=car.querySelector(".tf-cdots"),prev=car.querySelector(".tf-cprev"),next=car.querySelector(".tf-cnext");
  // Explicit current index (authoritative). Deriving it from scroll position
  // alone breaks wrap-around for multi-card layouts, where the last card can
  // never be scroll-centred — so "next" would stall before the end and never
  // loop. `lock` briefly ignores the scroll handler during our own animation.
  var idx=0,lock=0;
  // Nearest slide to the viewport centre — used only to resync after a manual swipe.
  function nearest(){var c=track.scrollLeft+track.clientWidth/2,b=0,bd=Infinity;for(var i=0;i<n;i++){var e=slides[i],cc=e.offsetLeft+e.offsetWidth/2,d=Math.abs(cc-c);if(d<bd){bd=d;b=i;}}return b;}
  function go(i){idx=(i%n+n)%n;lock=Date.now()+700;track.scrollTo({left:slides[idx].offsetLeft,behavior:"smooth"});sync();}
  if(dots){for(var i=0;i<n;i++){(function(k){var b=document.createElement("button");if(k===0)b.className="active";b.setAttribute("aria-label","Go to slide "+(k+1));b.addEventListener("click",function(){go(k);rest();});dots.appendChild(b);})(i);}}
  function sync(){if(dots)for(var i=0;i<dots.children.length;i++)dots.children[i].className=(i===idx)?"active":"";}
  var tk=false;track.addEventListener("scroll",function(){if(!tk){requestAnimationFrame(function(){if(Date.now()>lock){idx=nearest();sync();}tk=false;});tk=true;}},{passive:true});
  // Arrows wrap continuously: next past the last -> first; prev before the first -> last.
  if(prev)prev.addEventListener("click",function(){go(idx-1);rest();});
  if(next)next.addEventListener("click",function(){go(idx+1);rest();});
  var iv=parseInt(car.getAttribute("data-autoplay"),10)||0,timer=null;
  function play(){stop();if(iv>0)timer=setInterval(function(){go(idx+1);},iv);}
  function stop(){if(timer){clearInterval(timer);timer=null;}}
  function rest(){play();}
  car.addEventListener("mouseenter",stop);car.addEventListener("mouseleave",play);
  car.addEventListener("touchstart",stop,{passive:true});
  play();
}
function boot(){var c=document.querySelectorAll(".tf-carousel");for(var i=0;i<c.length;i++)init(c[i]);}
if(document.readyState!=="loading")boot();else document.addEventListener("DOMContentLoaded",boot);
})();</script>
JS;
    }

    /** Reveal sections with a data-anim on scroll. Progressive enhancement: if
     *  JavaScript is off or IntersectionObserver is missing, nothing is ever
     *  hidden, so the content always shows. */
    private static function animScript(): string
    {
        return <<<'JS'
<script>(function(){var els=document.querySelectorAll("[data-anim]");if(!els.length)return;
if(!("IntersectionObserver" in window)){for(var i=0;i<els.length;i++)els[i].classList.add("tf-in");return;}
document.documentElement.classList.add("tf-anim-ready");
var io=new IntersectionObserver(function(en){en.forEach(function(e){if(e.isIntersecting){e.target.classList.add("tf-in");io.unobserve(e.target);}});},{threshold:0.12,rootMargin:"0px 0px -8% 0px"});
for(var i=0;i<els.length;i++)io.observe(els[i]);})();</script>
JS;
    }

    /** Count numbers up from 0 to their value when the stat scrolls into view. */
    private static function countScript(): string
    {
        return <<<'JS'
<script>(function(){var els=document.querySelectorAll(".tf-count[data-count]");if(!els.length)return;
function fmt(n){try{return n.toLocaleString("en-IN");}catch(e){return String(n);}}
var reduce=window.matchMedia&&window.matchMedia("(prefers-reduced-motion: reduce)").matches;
if(reduce||!("IntersectionObserver" in window)){return;}
function run(el){var target=parseFloat(el.getAttribute("data-count"))||0,done=false;
var io=new IntersectionObserver(function(en){en.forEach(function(e){if(e.isIntersecting&&!done){done=true;var dur=1600,start=null;
function tick(t){if(!start)start=t;var p=Math.min(1,(t-start)/dur);el.textContent=fmt(Math.round(target*(1-Math.pow(1-p,3))));if(p<1)requestAnimationFrame(tick);}
requestAnimationFrame(tick);io.disconnect();}});},{threshold:0.4});io.observe(el);}
for(var i=0;i<els.length;i++)run(els[i]);})();</script>
JS;
    }

    /* -------------------------------------------------------- mobile bar */

    /**
     * Mobile Sticky Bottom Action Bar — visible only on screens <641px.
     * Provides WhatsApp, Add-to-Contacts, Share, Add-to-Home-Screen and
     * Location buttons using data from doc.business.
     */
    private static function mobileBar(array $doc): string
    {
        // ON by default. Every site here is a small business whose visitors are
        // overwhelmingly on a phone, and the bar is the only always-reachable
        // route to WhatsApp/call/directions. A site that has explicitly stored
        // false still gets no bar — this only changes the unset case, which is
        // every document that never had a settings block at all.
        $show = $doc['settings']['showMobileActionBar'] ?? true;
        if (!$show) return '';

        $biz     = $doc['business'] ?? [];
        $wa      = preg_replace('/\D+/', '', $biz['whatsapp'] ?? $biz['phone'] ?? '');
        $phone   = preg_replace('/\D+/', '', $biz['phone'] ?? '');
        $address = trim((string)($biz['address'] ?? ''));
        $name    = self::esc($doc['site']['name'] ?? 'Website');
        $url     = self::esc('https://' . self::publicHost());
        $email   = self::esc($biz['email'] ?? '');
        $waPhone = preg_replace('/\D+/', '', $biz['whatsapp'] ?? '');

        // Find logo from the first header or footer section that has one.
        // Phone contacts apps (iOS, Android) only show a PHOTO embedded as raw
        // base64 — most ignore PHOTO;VALUE=URI. The picture is NOT inlined into
        // the page any more: a 1 MB logo became ~1.4 MB of base64 on every page
        // of the site. The button carries a small thumbnail URL instead and the
        // script fetches and encodes it when "Add to Contacts" is tapped.
        $logoUrl = self::siteLogo($doc);
        // A contact photo is shown at ~100px, so ask Cloudinary for a 256px JPEG
        // rather than the full-size upload.
        if (preg_match('#^https://res\.cloudinary\.com/[^/]+/image/upload/#', $logoUrl)) {
            $logoUrl = preg_replace('#/image/upload/#', '/image/upload/w_256,h_256,c_limit,f_jpg,q_80/', $logoUrl, 1);
        }

        // Social links for vCard (Instagram, Facebook).
        $social = $biz['social'] ?? [];
        $socialIg = !empty($social['instagram']) ? self::esc($social['instagram']) : '';
        $socialFb = !empty($social['facebook']) ? self::esc($social['facebook']) : '';

        // "About us" blurb from the footer for vCard NOTE field.
        $note = '';
        foreach (($doc['pages'] ?? []) as $pg) {
            foreach (($pg['sections'] ?? []) as $s) {
                if (($s['type'] ?? '') === 'footer' && !empty($s['props']['blurb'])) {
                    $note = self::esc(strip_tags((string)$s['props']['blurb']));
                    break 2;
                }
            }
        }

        $h = '<div class="tf-mobile-bar" data-bar="1"><div class="tf-mbar-inner">';

        // WhatsApp
        if ($wa !== '') {
            $h .= '<a class="tf-mbar-btn" href="https://wa.me/' . $wa . '" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp">'
                . '<svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>'
                . '<span>WhatsApp</span></a>';
        }

        // Add to Contacts (vCard)
        if ($phone !== '') {
            $h .= '<button class="tf-mbar-btn" data-action="vcard"'
                . ' data-phone="' . $phone . '"'
                . ' data-name="' . $name . '"'
                . ' data-email="' . $email . '"'
                . ' data-url="' . $url . '"'
                . ' data-org="' . $name . '"'
                . ' data-whatsapp="' . $waPhone . '"'
                . ' data-address="' . self::esc($address) . '"'
                . ($logoUrl ? ' data-logo="' . self::esc($logoUrl) . '"' : '')
                . ($biz['mapUrl'] ?? '' ? ' data-mapurl="' . self::esc($biz['mapUrl']) . '"' : '')
                . ($socialIg ? ' data-social-ig="' . $socialIg . '"' : '')
                . ($socialFb ? ' data-social-fb="' . $socialFb . '"' : '')
                . ($note ? ' data-note="' . $note . '"' : '')
                . ' aria-label="Add to Contacts">'
                . '<svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><path d="M15 14c-2.67 0-8 1.33-8 4v2h16v-2c0-2.67-5.33-4-8-4m-9-4V7H4v3H1v2h3v3h2v-3h3v-2m6-1a4 4 0 100-8 4 4 0 000 8z"/></svg>'
                . '<span>Add to<br>Contacts</span></button>';
        }

        // Share
        $h .= '<button class="tf-mbar-btn" data-action="share" data-url="' . $url . '" data-title="' . $name . '" aria-label="Share">'
            . '<svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><path d="M18 16.08c-.76 0-1.44.3-1.96.77L8.91 12.7c.05-.23.09-.46.09-.7s-.04-.47-.09-.7l7.05-4.11c.54.5 1.25.81 2.04.81 1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3c0 .24.04.47.09.7L8.04 9.81C7.5 9.31 6.79 9 6 9c-1.66 0-3 1.34-3 3s1.34 3 3 3c.79 0 1.5-.31 2.04-.81l7.12 4.16c-.05.21-.08.43-.08.65 0 1.61 1.31 2.92 2.92 2.92 1.61 0 2.92-1.31 2.92-2.92s-1.31-2.92-2.92-2.92z"/></svg>'
            . '<span>Share</span></button>';

        // Add to Home Screen / Install
        $h .= '<button class="tf-mbar-btn" data-action="install" aria-label="Add to Home Screen">'
            . '<svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/></svg>'
            . '<span>Add to<br>Home Screen</span></button>';

        // Location
        if ($address !== '') {
            $mapUrl = 'https://maps.google.com/maps?q=' . rawurlencode($address);
            $h .= '<a class="tf-mbar-btn" href="' . $mapUrl . '" target="_blank" rel="noopener noreferrer" aria-label="Location">'
                . '<svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>'
                . '<span>Location</span></a>';
        }

        $h .= '</div></div>' . self::mobileBarScript();
        return $h;
    }

    /** JavaScript for vCard download + share + install. */
    private static function mobileBarScript(): string
    {
        return <<<'JS'
<script>(function(){var b=document.querySelector('.tf-mobile-bar[data-bar="1"]');if(!b)return;
var _defer=null;window.addEventListener('beforeinstallprompt',function(e){e.preventDefault();_defer=e;});
// The contact photo: fetched and base64-encoded in the background shortly after
// load (a ~20 KB thumbnail), so the vCard can embed it without the page carrying
// it. Contacts apps ignore PHOTO;VALUE=URI, hence the encoding.
var _logo=null,_logoBtn=b.querySelector('[data-action="vcard"][data-logo]');
function _loadLogo(){
 if(_logo||!_logoBtn||!window.fetch||!window.FileReader)return _logo;
 _logo=fetch(_logoBtn.getAttribute('data-logo'),{mode:'cors'}).then(function(r){
  if(!r.ok)throw 0;return r.blob()}).then(function(bl){
  if(!/^image\//.test(bl.type))throw 0;
  return new Promise(function(ok){var fr=new FileReader();fr.onload=function(){
   var m=/^data:(image\/\w+);base64,(.+)$/.exec(fr.result||'');ok(m?{mime:m[1],b64:m[2]}:null)};
   fr.onerror=function(){ok(null)};fr.readAsDataURL(bl)})})['catch'](function(){return null});
 _logo.then(function(v){_logo.v=v});return _logo}
if(_logoBtn)setTimeout(_loadLogo,2500);
function _vcard(t,lo){
  var a=t.getAttribute.bind(t);
  var n=a('data-name')||'Contact',p=a('data-phone')||'',em=a('data-email')||'',u=a('data-url')||'',
   o=a('data-org')||'',w=a('data-whatsapp')||'',ad=a('data-address')||'',mu=a('data-mapurl')||'',
   ig=a('data-social-ig')||'',fb=a('data-social-fb')||'',no=a('data-note')||'',loUrl=a('data-logo')||'',
   // ENCODING=BASE64 (raw inline data) for maximum contacts-app compatibility;
   // the URI form is only a fallback when the photo could not be fetched.
   ph=lo?'\nPHOTO;ENCODING=BASE64;TYPE='+lo.mime.replace('image/','').toUpperCase()+':'+lo.b64
      :(loUrl?'\nPHOTO;VALUE=URI:'+loUrl:''),
   v='BEGIN:VCARD\nVERSION:3.0\nFN:'+n
   +(o?'\nORG:'+o:'')
   +ph
   +'\nTEL;TYPE=CELL:'+p
   +(w&&w!==p?'\nTEL;TYPE=WHATSAPP:'+w:'')
   +(em?'\nEMAIL:'+em:'')
   +(ad?'\nADR;TYPE=WORK:'+ad.replace(/\n/g,'\\n'):'')
   +(u?'\nURL:'+u:'')
   +(mu?'\nURL;TYPE=WORK:'+mu:'')
   +(ig?'\nURL:'+ig:'')
   +(fb?'\nURL:'+fb:'')
   +(no?'\nNOTE:'+no.replace(/\n/g,'\\n'):'')
   +'\nEND:VCARD',
   b2=new Blob([v],{type:'text/vcard;charset=utf-8'}),lk=document.createElement('a');
  lk.href=URL.createObjectURL(b2);lk.download=n.replace(/\s+/g,'_')+'.vcf';
  document.body.appendChild(lk);lk.click();document.body.removeChild(lk);
  setTimeout(function(){URL.revokeObjectURL(lk.href)},5e3)}
b.addEventListener('click',function(e){
 var t=e.target.closest('[data-action]');if(!t)return;e.preventDefault();
 var a=t.getAttribute.bind(t);
 if(t.getAttribute('data-action')==='vcard'){
  var pr=_loadLogo();
  if(!pr||pr.v!==undefined){_vcard(t,pr?pr.v:null)}
  else{ // still loading: wait briefly, never hold the download hostage to the photo
   var done=false,go=function(v){if(done)return;done=true;_vcard(t,v)};
   pr.then(go);setTimeout(function(){go(null)},2500)}}
 if(t.getAttribute('data-action')==='share'){
  var u=a('data-url')||location.href,ti=a('data-title')||document.title;
  if(navigator.share){navigator.share({title:ti,url:u})['catch'](function(){})}else{
   var i=document.createElement('input');i.value=u;document.body.appendChild(i);i.select();
   try{document.execCommand('copy');alert('Link copied to clipboard')}catch(ex){}
   document.body.removeChild(i)}}
 if(t.getAttribute('data-action')==='install'){
  // Never prompt when already in standalone mode (already added to home screen).
  if(!navigator.standalone&&!matchMedia('(display-mode:standalone)').matches){
   if(_defer){_defer.prompt();_defer.userChoice['finally'](function(){_defer=null})}
   else{
    alert('Tap your browser menu and select "Add to Home Screen" or "Install App".')}}}
});})();</script>
JS;
    }

    /* --------------------------------------------------------- base css */

    private static function baseCss(): string
    {
        return <<<CSS
*,*::before,*::after{box-sizing:border-box}
body{margin:0}
img{max-width:100%;display:block}
.tf-site{font-family:var(--font-body);color:var(--color-text);background:var(--color-bg);line-height:1.6;-webkit-font-smoothing:antialiased}
.tf-container{max-width:var(--container);margin:0 auto;padding:0 20px;width:100%}
@media(min-width:768px){.tf-container{padding:0 32px}}
.tf-section{position:relative;overflow:hidden;width:100%;padding-top:calc(var(--tf-pad,72px)*var(--space-scale));padding-bottom:calc(var(--tf-pad,72px)*var(--space-scale))}
/* Full-screen hero. svh (not vh/dvh) is the stable choice on phones: vh is the
   TALLEST state, so with the URL bar showing the hero overflows the screen and
   the buttons fall below the fold; dvh resizes live and makes the video jump
   while scrolling. */
.tf-full{min-height:100vh;min-height:100svh;display:flex;flex-direction:column;justify-content:center}
@media(max-width:640px){
  /* Desktop section padding (72-104px) is far too tall on a phone — scale it
     back so a section isn't mostly empty space. */
  .tf-section{padding-top:calc(var(--tf-pad,72px)*var(--space-scale)*.55);padding-bottom:calc(var(--tf-pad,72px)*var(--space-scale)*.55)}
  /* A 16:9 video covering a 375x800 portrait box has to crop ~80% of its width,
     so the subject is lost. A shorter hero keeps the box closer to the video's
     own shape and much more of the frame stays visible. */
  .tf-full{min-height:78vh;min-height:78svh}
  /* A hero given an image shape takes THAT shape on a phone rather than a
     slice of the viewport, so a portrait photo is not cropped to its middle.
     min-height wins over aspect-ratio, so it has to be cleared. */
  .tf-hero-r-tall,.tf-hero-r-portrait,.tf-hero-r-square,.tf-hero-r-wide,.tf-hero-r-landscape{min-height:0}
  .tf-hero-r-tall{aspect-ratio:2/3}
  .tf-hero-r-portrait{aspect-ratio:3/4}
  .tf-hero-r-square{aspect-ratio:1/1}
  .tf-hero-r-wide{aspect-ratio:3/2}
  .tf-hero-r-landscape{aspect-ratio:16/9}
  .tf-hero-wrap{max-width:100%}
  /* Full-width stacked buttons: side-by-side CTAs get squeezed to a few
     characters at 360px. */
  .tf-btns{margin-top:24px;gap:10px}
  .tf-btns>.tf-btn:not(.tf-btn-link){flex:1 1 100%}
  .tf-two{gap:24px}
}
/* Hero "show the whole picture": a one-cell grid holding the photo and the copy
   in the SAME cell, so the section is as tall as whichever is taller and the
   photo is never cropped at any width. Declared after the media query above on
   purpose — it ties .tf-full on specificity, so source order is what lets it
   clear the min-height that would otherwise stretch the box past the picture. */
.tf-hero-auto{display:grid;min-height:0;padding-top:0;padding-bottom:0}
.tf-hero-auto>*{grid-area:1/1;min-width:0}
.tf-hero-auto>.tf-hero-img{display:block;width:100%;height:auto;z-index:0}
.tf-hero-auto>.tf-hero-scrim{z-index:1}
.tf-hero-auto>.tf-container{z-index:2;align-self:center;padding-top:28px;padding-bottom:28px}
.tf-rel{position:relative;z-index:1}
.tf-bgimg{position:absolute;inset:0;background-size:cover;background-position:center}
.tf-overlay{position:absolute;inset:0}
h1,h2,h3{font-family:var(--font-heading);line-height:1.15;margin:0}
p{margin:0}
a{color:inherit}
.tf-h1{font-size:34px;font-weight:700;line-height:1.1;color:var(--tf-heading,inherit)}
@media(min-width:768px){.tf-h1{font-size:48px}}
@media(min-width:1024px){.tf-h1{font-size:60px}}
@media(min-width:768px){.tf-h1.tf-h1-l{font-size:42px;line-height:1.15}.tf-h1.tf-h1-xl{font-size:36px;line-height:1.2}}
@media(min-width:1024px){.tf-h1.tf-h1-l{font-size:48px}.tf-h1.tf-h1-xl{font-size:40px}}
@media(max-width:767px){.tf-h1.tf-h1-l,.tf-h1.tf-h1-xl{font-size:28px;line-height:1.2}}
.tf-h2{font-size:30px;font-weight:700;color:var(--tf-heading,inherit)}
@media(min-width:768px){.tf-h2{font-size:36px}}
.tf-lead{margin-top:16px;font-size:16px;line-height:1.6;opacity:.9;max-width:640px}
@media(min-width:768px){.tf-lead{font-size:18px}}
.tf-hero-wrap{max-width:768px}
.tf-al-center .tf-hero-wrap,.tf-al-center .tf-sub,.tf-al-center .tf-lead{margin-left:auto;margin-right:auto}
.tf-al-center .tf-btns{justify-content:center}
.tf-al-right .tf-hero-wrap,.tf-al-right .tf-sub,.tf-al-right .tf-lead{margin-left:auto}
.tf-al-right .tf-btns{justify-content:flex-end}
.tf-head{margin-bottom:40px}
.tf-eyebrow{font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.14em;color:var(--tf-text,var(--color-accent));margin:0 0 8px}
.tf-sub{margin-top:12px;font-size:16px;line-height:1.6;color:var(--tf-text,var(--color-muted));max-width:640px}
.tf-badge{display:inline-flex;align-items:center;gap:8px;margin-bottom:20px;padding:8px 16px;font-size:12px;font-weight:600;background:var(--color-accent);color:var(--color-accent-fg);border-radius:999px}
.tf-btns{margin-top:32px;display:flex;flex-wrap:wrap;gap:12px}
.tf-btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:12px 24px;font-size:14px;font-weight:600;text-decoration:none;border:0;cursor:pointer;transition:transform .2s}
.tf-btn:hover{transform:translateY(-2px)}
.tf-btn-link:hover{transform:none}
/* A card paints its OWN light background, so it must set its own text colour too.
   It used to inherit the section's: on a primary/dark/image section that is white,
   and card titles went white-on-white — invisible. The --tf-* overrides are reset
   for the same reason: a section's "white text" setting is for text on the section,
   not inside a white card ("initial" makes var() fall back to the theme colour). */
.tf-card{height:100%;background:var(--color-bg);color:var(--color-text);--tf-heading:initial;--tf-text:initial;border:1px solid var(--color-border);border-radius:var(--radius);overflow:hidden;box-shadow:0 1px 2px rgba(16,24,40,.04),0 8px 24px rgba(16,24,40,.06)}
.tf-grid{display:grid;gap:24px}
.tf-c2,.tf-c3{grid-template-columns:1fr}
.tf-c4{grid-template-columns:1fr 1fr}
@media(min-width:640px){.tf-c2{grid-template-columns:1fr 1fr}.tf-c3{grid-template-columns:1fr 1fr}}
@media(min-width:1024px){.tf-c3{grid-template-columns:repeat(3,1fr)}.tf-c4{grid-template-columns:repeat(4,1fr)}}
.tf-two{display:grid;gap:40px;grid-template-columns:1fr;align-items:center}
/* A grid item's min-width defaults to AUTO, i.e. "never narrower than my
   content". One unshrinkable child — the product page's thumbnail strip, a wide
   table, a long unbroken word — therefore pushes the whole column past its
   track and the PAGE scrolls sideways, which on iOS makes Safari zoom out and
   leaves a blank band beside the header. min-width:0 lets the track win; any
   child that needs to scroll does it internally with its own overflow. */
.tf-two>*{min-width:0}
@media(min-width:1024px){.tf-two{grid-template-columns:1fr 1fr}}
.tf-gal{display:grid;gap:16px;grid-template-columns:1fr 1fr}
@media(min-width:768px){.tf-gal.g3{grid-template-columns:repeat(3,1fr)}.tf-gal.g4{grid-template-columns:repeat(4,1fr)}}
.tf-galfig{position:relative;margin:0;overflow:hidden;border-radius:var(--radius)}
/* Height/ratio comes from the inline style so the section can pick a
   shape; a fixed height here would win over an aspect-ratio. */
.tf-galfig img{width:100%;object-fit:cover}
.tf-galfig figcaption{position:absolute;left:0;right:0;bottom:0;padding:12px;font-size:12px;color:#fff;text-align:left;background:linear-gradient(to top,rgba(0,0,0,.7),transparent)}
.tf-header{position:sticky;top:0;z-index:40;border-bottom:1px solid rgba(120,120,120,.18)}
.tf-header-bar{display:flex;align-items:center;justify-content:space-between;gap:16px;padding:12px 0}
.tf-nav{display:flex;align-items:center;gap:24px}
/* Dropdown nav. Opens on hover AND on keyboard focus — focus-within is what
   makes it usable without a mouse, and it costs one selector. The parent stays
   a real link so a tap still navigates on touch, where there is no hover. */
.tf-drop{position:relative;display:inline-flex;align-items:center}
.tf-drop>a{display:inline-flex;align-items:center;gap:5px}
/* inline-block is load-bearing: this caret sits inside a plain <a>, so it is an
   inline box, and transforms do not apply to non-replaced inline elements —
   the rotate below was silently doing nothing. */
.tf-drop-caret{display:inline-block;font-size:.7em;line-height:1;opacity:.7;transition:transform .18s}
.tf-drop:hover .tf-drop-caret,.tf-drop:focus-within .tf-drop-caret{transform:rotate(180deg)}
.tf-drop-menu{position:absolute;top:100%;left:0;z-index:60;min-width:230px;padding:8px;
  display:flex;flex-direction:column;gap:2px;background:var(--color-surface);
  border:1px solid var(--color-border);border-radius:var(--radius);
  box-shadow:0 12px 32px rgba(16,24,40,.14);opacity:0;visibility:hidden;
  transform:translateY(6px);transition:opacity .16s,transform .16s,visibility .16s}
.tf-drop:hover .tf-drop-menu,.tf-drop:focus-within .tf-drop-menu{opacity:1;visibility:visible;transform:translateY(0)}
.tf-drop-menu a{display:block;padding:8px 10px;border-radius:8px;font-size:.86rem;
  white-space:nowrap;color:var(--color-text);text-decoration:none}
.tf-drop-menu a:hover,.tf-drop-menu a:focus-visible{background:var(--color-bg)}
/* In the burger menu a parent is a <details>: closed until tapped, so a menu
   with twelve categories under it opens as four rows, not forty. */
.tf-mdrop{border:0}
.tf-mdrop>summary{display:flex;align-items:center;justify-content:space-between;gap:8px;
  padding:2px 0;font-size:14px;font-weight:500;opacity:.85;cursor:pointer;list-style:none}
.tf-mdrop>summary::-webkit-details-marker{display:none}
.tf-mdrop>summary:hover{opacity:1}
.tf-mdrop-caret{display:inline-block;font-size:.7em;transition:transform .16s}
.tf-mdrop[open]>summary .tf-mdrop-caret{transform:rotate(180deg)}
.tf-mdrop-list{display:flex;flex-direction:column;padding-left:14px;
  border-left:2px solid var(--color-border);margin:2px 0 6px}
.tf-mdrop-list a{font-size:.86rem;opacity:.85}
@media(prefers-reduced-motion:reduce){.tf-mdrop-caret{transition:none}}
@media(prefers-reduced-motion:reduce){.tf-drop-menu,.tf-drop-caret{transition:none}}
/* nowrap: a two-word label ("About Doctor") otherwise breaks onto two lines once
   a menu gets crowded, which reads as broken rather than tight. */
.tf-nav a{font-size:14px;font-weight:500;opacity:.85;text-decoration:none;white-space:nowrap}
@media(min-width:769px) and (max-width:1100px){.tf-nav{gap:16px}.tf-nav a{font-size:13.5px}}
.tf-nav a:hover{opacity:1}
.tf-burger{display:none;font-size:22px;line-height:1;cursor:pointer;padding:4px 8px;border:1px solid currentColor;border-radius:8px}
.tf-mnav{display:none}
.tf-mobile-only{display:flex}
.tf-brand-center{position:static}
.tf-center-top{display:flex;width:100%;align-items:center;justify-content:space-between}
@media(min-width:769px){.tf-mobile-only{display:none}.tf-brand-center{position:absolute;left:50%;transform:translateX(-50%)}.tf-center-top{justify-content:center}}
@media(max-width:768px){
  .tf-nav-desktop{display:none}
  .tf-burger{display:inline-flex;align-items:center}
  .tf-mnav{flex-direction:column;align-items:stretch;gap:4px;padding:8px 0 12px;border-top:1px solid rgba(120,120,120,.18)}
  .tf-mnav a{padding:8px 4px;border-radius:8px}
  .tf-navtoggle:checked ~ .tf-container .tf-mnav{display:flex}
}
.tf-faqitem summary{cursor:pointer;list-style:none}
.tf-faqitem summary::-webkit-details-marker{display:none}
.tf-faqitem[open] .tf-faq-plus{transform:rotate(45deg)}
.tf-faq-plus{transition:transform .2s;display:inline-block}
@media(min-width:640px){.tf-foot-bottom{flex-direction:row!important}}
iframe{max-width:100%}
.tf-carousel{position:relative}
.tf-cviewport{position:relative}
.tf-ctrack{position:relative;display:flex;gap:24px;overflow-x:auto;scroll-snap-type:x mandatory;scroll-behavior:smooth;scrollbar-width:none;-ms-overflow-style:none}
.tf-ctrack::-webkit-scrollbar{display:none}
.tf-cslide{flex:0 0 85%;max-width:85%;scroll-snap-align:start}
@media(min-width:640px){.tf-cslide{flex:0 0 46%;max-width:46%}}
@media(min-width:1024px){.tf-cslide{flex:0 0 31%;max-width:31%}}
.tf-cprev,.tf-cnext{position:absolute;top:50%;transform:translateY(-50%);display:flex;align-items:center;justify-content:center;width:38px;height:38px;border-radius:999px;border:1px solid var(--color-border);background:var(--color-bg);color:var(--color-text);font-size:20px;line-height:1;cursor:pointer;box-shadow:0 2px 8px rgba(0,0,0,.12);z-index:2}
.tf-cprev{left:-6px}.tf-cnext{right:-6px}
.tf-cprev:hover,.tf-cnext:hover{opacity:.85}
.tf-cdots{display:flex;justify-content:center;gap:8px;margin-top:16px}
.tf-cdots button{width:8px;height:8px;padding:0;border:0;border-radius:999px;background:var(--color-border);cursor:pointer;transition:width .25s,background .25s}
.tf-cdots button.active{width:20px;background:var(--color-primary)}
.tf-marquee{overflow:hidden;position:relative}
.tf-mqtrack{display:flex;width:max-content;animation-name:tf-mqscroll;animation-timing-function:linear;animation-iteration-count:infinite}
.tf-marquee:hover .tf-mqtrack{animation-play-state:paused}
.tf-mqslide{flex:0 0 260px;margin-right:24px}
@media(min-width:768px){.tf-mqslide{flex:0 0 320px}}
@keyframes tf-mqscroll{from{transform:translateX(0)}to{transform:translateX(-50%)}}
@media(prefers-reduced-motion:reduce){.tf-mqtrack{animation:none;overflow-x:auto;max-width:100%}}
.tf-ticker{overflow:hidden;position:relative;padding:11px 0;font-size:.86rem;font-weight:600;letter-spacing:.01em}
.tf-tktrack{display:flex;width:max-content;animation-name:tf-tkscroll;animation-timing-function:linear;animation-iteration-count:infinite}
.tf-tktrack.tf-tkrev{animation-direction:reverse}
.tf-tkpause:hover .tf-tktrack,.tf-tkpause:focus-within .tf-tktrack{animation-play-state:paused}
.tf-tkset{display:flex;flex:0 0 auto;align-items:center;white-space:nowrap}
.tf-tkitem{display:inline-flex;align-items:center;gap:8px;padding:0 4px}
.tf-tkicon{font-size:1.02em;line-height:1}
.tf-tksep{opacity:.45;padding:0 18px 0 22px}
@keyframes tf-tkscroll{from{transform:translateX(0)}to{transform:translateX(-50%)}}
/* A strip of text sliding past forever is exactly what this setting is for:
   stop it, and let the notices be scrolled by hand instead. */
@media(prefers-reduced-motion:reduce){.tf-tktrack{animation:none}.tf-ticker{overflow-x:auto}.tf-tkset+.tf-tkset{display:none}}
.tf-tklink{color:inherit;text-decoration:none}
.tf-tklink:hover{text-decoration:underline}
/* Static bar: centred, wraps on narrow screens instead of overflowing, and the
   separators give way to spacing since nothing is scrolling past. */
.tf-tkstatic{padding:8px 0}
.tf-tkstatic .tf-tkset{flex-wrap:wrap;justify-content:center;white-space:normal;gap:8px 16px;padding:0 16px}
.tf-tkstatic .tf-tksep{display:none}
/* ---- header search ---- */
.tf-hsearch,.tf-msearch{display:flex;align-items:center;border:1px solid var(--color-border);border-radius:6px;background:var(--color-bg);overflow:hidden}
.tf-hsearch{flex:1;max-width:480px;margin:0 24px}
.tf-msearch{margin:4px 0 8px}
.tf-hsearch input,.tf-msearch input{flex:1;min-width:0;border:0;background:transparent;padding:11px 14px;font:inherit;font-size:15px;color:var(--color-text);outline:none}
.tf-hsearch button,.tf-msearch button{display:inline-flex;align-items:center;justify-content:center;width:44px;height:42px;border:0;background:transparent;color:var(--color-text);cursor:pointer}
.tf-hsearch:focus-within,.tf-msearch:focus-within{border-color:var(--color-primary)}
.tf-hsearch-ic{display:none;align-items:center;justify-content:center;padding:6px;color:inherit}
.tf-hsearch-ic.tf-always{display:inline-flex}
.tf-hnav-row{display:flex;justify-content:center;padding:0 0 10px}
.tf-hnav-row .tf-nav{flex-wrap:wrap;justify-content:center}
@media(max-width:768px){.tf-hsearch,.tf-hnav-row{display:none}.tf-hsearch-ic{display:inline-flex}}
.tf-search-big{display:flex;gap:10px;max-width:640px}
.tf-search-big input{flex:1;min-width:0;padding:13px 16px;font:inherit;font-size:16px;border:1px solid var(--color-border);border-radius:var(--radius);background:var(--color-bg);color:var(--color-text)}
/* ---- banner slideshow ---- */
.tf-ss-sec>.tf-container{max-width:none;padding:0}
.tf-slideshow{position:relative;--ss-h:440px}
.tf-ss-short{--ss-h:300px}.tf-ss-tall{--ss-h:600px}
.tf-slideshow .tf-ctrack{gap:0}
.tf-slideshow .tf-cslide{flex:0 0 100%;max-width:100%}
.tf-slideshow .tf-cprev{left:14px}.tf-slideshow .tf-cnext{right:14px}
.tf-slideshow .tf-cdots{position:absolute;left:0;right:0;bottom:14px;margin:0;z-index:2}
.tf-slideshow .tf-cdots button{background:rgba(255,255,255,.75);box-shadow:0 0 0 1px rgba(0,0,0,.12)}
.tf-slideshow .tf-cdots button.active{background:var(--color-primary)}
.tf-ss-slide{position:relative;height:var(--ss-h);overflow:hidden;text-align:left}
.tf-ss-slide picture,.tf-ss-link{display:block;height:100%}
.tf-ss-img{width:100%;height:100%;object-fit:cover}
.tf-ss-banner{height:auto}
.tf-ss-banner .tf-ss-img{height:auto}
.tf-ss-shade{position:absolute;inset:0;background:linear-gradient(90deg,rgba(0,0,0,.6),rgba(0,0,0,.08) 72%);pointer-events:none}
.tf-ss-full .tf-ss-copy{position:absolute;top:0;bottom:0;left:0;display:flex;flex-direction:column;justify-content:center;align-items:flex-start;max-width:720px;padding:0 8%;color:#fff}
.tf-ss-full .tf-ss-h{color:#fff}
.tf-ss-split{display:grid;grid-template-columns:5fr 7fr;background:var(--color-surface)}
/* The left padding clears the carousel's "previous" arrow, which sits over this panel. */
.tf-ss-panel{display:flex;align-items:center;padding:32px 6% 32px max(8%,72px);background:var(--color-surface);color:var(--color-text)}
.tf-ss-media{position:relative;min-width:0;height:100%;overflow:hidden}
.tf-ss-badge{display:inline-block;margin:0 0 14px;padding:5px 14px;border-radius:40px;background:var(--color-secondary);color:#121212;font-size:12px;font-weight:700;letter-spacing:.08em;text-transform:uppercase}
.tf-ss-h{margin:0;font-family:var(--font-heading);font-size:clamp(28px,3.6vw,50px);line-height:1.12;font-weight:400;color:inherit}
.tf-ss-sub{margin:14px 0 0;font-size:clamp(15px,1.3vw,18px);line-height:1.55;opacity:.85;max-width:470px}
.tf-ss-cta{margin-top:24px}
@media(max-width:640px){
  .tf-slideshow{--ss-h:400px}
  .tf-ss-split{grid-template-columns:1fr;height:auto;min-height:100%}
  .tf-ss-split .tf-ss-media{order:-1;height:250px}
  .tf-ss-panel{padding:22px 20px 46px}
  .tf-slideshow .tf-cprev,.tf-slideshow .tf-cnext{display:none}
}
/* ---- shop by category ---- */
.tf-cats{display:grid;grid-template-columns:repeat(var(--cat-cols,6),minmax(0,1fr));gap:22px}
.tf-cats a{color:inherit;text-decoration:none}
.tf-cat-tile{display:flex;flex-direction:column;align-items:center;gap:10px;text-align:center}
.tf-cat-media{display:block;width:100%;aspect-ratio:1/1;border-radius:18px;overflow:hidden;background:var(--color-surface)}
.tf-cats-circles .tf-cat-media{border-radius:999px}
.tf-cat-media img,.tf-cat-card img{width:100%;height:100%;object-fit:cover;transition:transform .4s}
.tf-cat-tile:hover img,.tf-cat-card:hover img{transform:scale(1.06)}
.tf-cat-title{font-size:17px;line-height:1.3;font-weight:400;color:var(--tf-heading,inherit)}
.tf-cat-note{font-size:12px;color:var(--tf-text,var(--color-muted))}
.tf-cat-ph{display:flex;width:100%;height:100%;align-items:center;justify-content:center;font-size:34px;font-family:var(--font-heading);color:var(--color-primary)}
.tf-cat-card{position:relative;display:block;aspect-ratio:3/4;border-radius:var(--radius);overflow:hidden;background:var(--color-surface)}
.tf-cat-cap{position:absolute;left:0;right:0;bottom:0;display:flex;flex-direction:column;gap:2px;padding:48px 16px 16px;text-align:left;color:#fff;background:linear-gradient(to top,rgba(0,0,0,.72),transparent)}
.tf-cat-card .tf-cat-title{font-size:19px;color:#fff}
.tf-cat-card .tf-cat-note{color:rgba(255,255,255,.88)}
.tf-cats-pills{display:flex;flex-wrap:wrap;justify-content:center;gap:10px}
.tf-al-left .tf-cats-pills{justify-content:flex-start}
.tf-cat-pill{display:inline-flex;align-items:center;padding:9px 20px;border-radius:999px;border:1px solid var(--color-border);background:var(--color-bg);color:var(--color-text);font-size:14px;transition:background .2s,border-color .2s}
.tf-cat-pill:hover{background:var(--color-secondary);border-color:var(--color-secondary);color:#121212}
@media(max-width:900px){
  .tf-cats-tiles,.tf-cats-circles{grid-template-columns:none;grid-auto-flow:column;grid-auto-columns:96px;gap:14px;overflow-x:auto;scroll-snap-type:x mandatory;padding-bottom:6px;scrollbar-width:none}
  .tf-cats-tiles::-webkit-scrollbar,.tf-cats-circles::-webkit-scrollbar{display:none}
  .tf-cats-tiles>*,.tf-cats-circles>*{scroll-snap-align:start}
  .tf-cats-tiles .tf-cat-title,.tf-cats-circles .tf-cat-title{font-size:14px}
  .tf-cats-cards{grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}
}
/* ---- promo banners ---- */
.tf-bns{display:grid;grid-template-columns:repeat(var(--bn-cols,2),minmax(0,1fr));gap:20px}
.tf-bn{position:relative;overflow:hidden;border-radius:var(--radius);background:var(--color-surface);aspect-ratio:var(--bn-ratio,3/2)}
.tf-bn img{position:absolute;inset:0;width:100%;height:100%;transition:transform .5s}
.tf-bn:hover img{transform:scale(1.04)}
.tf-bn-hit{position:absolute;inset:0;z-index:1}
.tf-bn-copy{position:absolute;left:0;right:0;bottom:0;z-index:2;padding:64px 24px 22px;text-align:left;color:#fff;background:linear-gradient(to top,rgba(0,0,0,.68),transparent);pointer-events:none}
.tf-bn-copy .tf-btn{pointer-events:auto}
.tf-bn-eyebrow{margin:0 0 6px;font-size:12px;letter-spacing:.12em;text-transform:uppercase;opacity:.92}
.tf-bn-h{margin:0;font-family:var(--font-heading);font-size:clamp(20px,2.2vw,28px);line-height:1.2;font-weight:400;color:#fff}
.tf-bn-sub{margin:6px 0 0;font-size:14px;line-height:1.5;opacity:.92}
.tf-bn-cta{margin-top:14px}
@media(max-width:760px){.tf-bns{grid-template-columns:1fr;gap:14px}.tf-bns[style*="--bn-ratio:3/1"] .tf-bn{aspect-ratio:3/2}}
/* ---- trust badges ---- */
.tf-feats{display:grid;grid-template-columns:repeat(var(--feat-n,4),minmax(0,1fr));gap:24px}
.tf-feat{display:flex;flex-direction:column;align-items:center;gap:8px;text-align:center}
.tf-al-left .tf-feat{align-items:flex-start;text-align:left}
.tf-feat-ic{display:inline-flex;width:58px;height:58px;margin-bottom:4px;border-radius:999px;align-items:center;justify-content:center;background:var(--color-bg);color:var(--color-primary);box-shadow:0 0 0 1px var(--color-border)}
.tf-feats-cards .tf-feat{padding:26px 18px;border-radius:var(--radius);background:var(--color-bg);border:1px solid var(--color-border)}
.tf-feat-t{margin:0;font-size:16px;font-weight:700;line-height:1.3;color:var(--tf-heading,inherit)}
.tf-feat-x{margin:0;font-size:14px;line-height:1.5;color:var(--tf-text,var(--color-muted))}
@media(max-width:900px){.tf-feats{grid-template-columns:repeat(2,minmax(0,1fr));gap:22px 14px}}
/* ---- storefront product cards ---- */
.tf-shopgrid{display:grid;gap:30px 20px;text-align:left}
.tf-shop-c2{grid-template-columns:repeat(2,minmax(0,1fr))}
.tf-shop-c3{grid-template-columns:repeat(3,minmax(0,1fr))}
.tf-shop-c4{grid-template-columns:repeat(4,minmax(0,1fr))}
@media(max-width:1023px){.tf-shop-c4{grid-template-columns:repeat(3,minmax(0,1fr))}}
@media(max-width:700px){.tf-shopgrid{grid-template-columns:repeat(2,minmax(0,1fr));gap:22px 12px}}
.tf-shop{display:flex;flex-direction:column;height:100%;text-align:left;color:var(--color-text);--tf-heading:initial;--tf-text:initial}
.tf-shop-a{display:flex;flex-direction:column;flex:1;color:inherit;text-decoration:none}
.tf-shop-media{position:relative;display:block;aspect-ratio:var(--shop-ratio,3/4);overflow:hidden;border-radius:var(--radius);background:var(--color-surface)}
.tf-shop-media img{width:100%;height:100%;transition:transform .45s}
.tf-shop:hover .tf-shop-media img{transform:scale(1.05)}
.tf-shop-auto .tf-shop-media{aspect-ratio:auto}
.tf-shop-auto .tf-shop-media img{height:auto}
.tf-shop-badge{position:absolute;top:10px;left:0;padding:4px 12px 4px 10px;border-radius:0 40px 40px 0;background:#E9718B;color:#121212;font-size:11px;font-weight:700;letter-spacing:.03em}
.tf-shop-body{display:flex;flex-direction:column;gap:3px;padding:12px 2px;flex:1}
.tf-shop-price{display:flex;flex-wrap:wrap;align-items:baseline;gap:8px}
.tf-shop-price b{font-size:18px;font-weight:600;color:var(--color-text)}
.tf-shop-price s{font-size:14px;color:var(--color-muted)}
.tf-shop-price em{font-style:normal;font-size:12px;font-weight:700;color:#15803d}
.tf-shop-title{font-size:15px;line-height:1.4;color:var(--color-text);display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.tf-shop-meta{font-size:12.5px;color:var(--color-muted)}
.tf-shop-btn{display:flex;align-items:center;justify-content:center;min-height:40px;padding:8px 12px;border-radius:7px;background:linear-gradient(92deg,var(--color-secondary) 12%,color-mix(in srgb,var(--color-secondary) 30%,#fff) 99%);color:#121212;font-size:15px;letter-spacing:.02em;text-align:center;text-decoration:none;transition:filter .2s}
.tf-shop-btn:hover{filter:brightness(.96)}
.tf-shoprow .tf-cslide{flex:0 0 46%;max-width:46%}
@media(min-width:700px){.tf-shoprow .tf-cslide{flex:0 0 31%;max-width:31%}}
@media(min-width:1024px){.tf-shoprow .tf-cslide{flex:0 0 23.5%;max-width:23.5%}}
.tf-shoprow .tf-ctrack{gap:2%}
/* ---- videos ---- */
.tf-vids{display:grid;gap:28px 24px;text-align:left}
.tf-vids-1{grid-template-columns:minmax(0,1fr);max-width:960px;margin:0 auto}
.tf-vids-2{grid-template-columns:repeat(2,minmax(0,1fr))}
.tf-vids-3{grid-template-columns:repeat(3,minmax(0,1fr))}
@media(max-width:900px){.tf-vids-3{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:640px){.tf-vids-2,.tf-vids-3{grid-template-columns:minmax(0,1fr)}}
.tf-vid{margin:0;min-width:0}
.tf-vid-frame{position:relative;width:100%;max-height:80vh;overflow:hidden;border-radius:var(--radius);background:#0b0b0f;box-shadow:0 8px 28px rgba(16,24,40,.12)}
.tf-vid-frame video,.tf-vid-if{position:absolute;inset:0;width:100%;height:100%;border:0;object-fit:contain;background:#000}
.tf-vid-cover video{object-fit:cover}
/* A vertical reel or Short in a wide single-video layout: keep it phone-sized, centred. */
.tf-vids-1 .tf-vid-frame[style*="9/16"]{max-width:420px;margin:0 auto}
.tf-vid-play{position:absolute;inset:0;display:block;width:100%;height:100%;padding:0;border:0;cursor:pointer;background:#000}
.tf-vid-play img{width:100%;height:100%;object-fit:cover;opacity:.9;transition:opacity .25s,transform .45s}
.tf-vid-play:hover img{opacity:1;transform:scale(1.03)}
.tf-vid-btn{position:absolute;left:50%;top:50%;display:flex;width:70px;height:70px;margin:-35px 0 0 -35px;align-items:center;justify-content:center;border-radius:999px;background:rgba(0,0,0,.6);box-shadow:0 6px 24px rgba(0,0,0,.35);transition:background .2s,transform .2s}
.tf-vid-btn svg{margin-left:4px}
.tf-vid-play:hover .tf-vid-btn,.tf-vid-play:focus-visible .tf-vid-btn{background:var(--color-primary);transform:scale(1.07)}
.tf-vid-play:focus-visible{outline:3px solid var(--color-primary);outline-offset:3px}
.tf-vid-cap{margin-top:12px}
.tf-vid-t{margin:0;font-family:var(--font-heading);font-size:17px;font-weight:600;line-height:1.35;color:var(--tf-heading,inherit)}
.tf-vid-x{margin:4px 0 0;font-size:14px;line-height:1.55;color:var(--tf-text,var(--color-muted))}
.tf-al-center .tf-vids-1 .tf-vid-cap{text-align:center}
@media(prefers-reduced-motion:reduce){.tf-vid-play img,.tf-vid-btn{transition:none}}
/* ---- footer disclaimer ---- */
.tf-foot-disc{margin-top:28px;padding:16px 18px;border-radius:var(--radius);background:rgba(127,127,127,.08);font-size:12.5px;line-height:1.6;text-align:left;opacity:.85}
.tf-foot-disc p{margin:0}
.tf-foot-disc p+p{margin-top:8px}
/* ---- calculators ---- */
/* container-type: the calculator lays itself out by ITS OWN width (below), so it
   also stacks correctly inside the builder's 390px mobile preview, where a media
   query would still see the desktop window. */
.tf-cx{--cx-c:var(--color-primary);text-align:left;container-type:inline-size;container-name:tfcx}
.tf-cx-panel,.tf-cx-tab{--cx-soft:color-mix(in srgb,var(--cx-c) 20%,#fff)}
.tf-cx-tabs{display:flex;flex-wrap:wrap;justify-content:center;gap:10px;padding:2px 2px 14px}
.tf-cx-tab{display:flex;flex-direction:column;align-items:flex-start;gap:1px;padding:10px 18px;border:1.5px solid var(--color-border);border-radius:14px;background:var(--color-bg);color:var(--color-text);font:inherit;cursor:pointer;text-align:left;transition:border-color .2s,box-shadow .2s,transform .2s,background .2s}
.tf-cx-tab:hover{transform:translateY(-2px)}
.tf-cx-tab[aria-selected="true"]{border-color:var(--cx-c);background:color-mix(in srgb,var(--cx-c) 8%,var(--color-bg));box-shadow:0 8px 22px color-mix(in srgb,var(--cx-c) 22%,transparent)}
.tf-cx-tab:focus-visible{outline:3px solid var(--cx-c);outline-offset:2px}
.tf-cx-tag{margin:0;font-size:11px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:var(--cx-c)}
.tf-cx-tabt{font-size:15px;font-weight:600}
.tf-cx-panel{padding:30px;border-radius:calc(var(--radius) + 6px);background:var(--color-bg);color:var(--color-text);border:1px solid var(--color-border);box-shadow:0 14px 44px rgba(16,24,40,.08);animation:tf-cxin .35s ease}
.tf-cx-panel[hidden]{display:none}
.tf-cx--stacked .tf-cx-panel+.tf-cx-panel{margin-top:26px}
@keyframes tf-cxin{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:none}}
.tf-cx-h{margin:2px 0 6px;font-family:var(--font-heading);font-size:22px;line-height:1.3}
.tf-cx-note{margin:0 0 20px;font-size:14.5px;line-height:1.6;color:var(--color-muted)}
.tf-cx-body{display:grid;grid-template-columns:minmax(0,1.1fr) minmax(0,1fr);gap:34px;align-items:start}
.tf-cx-field{margin-bottom:22px}
.tf-cx-lab{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:10px;font-size:14.5px;font-weight:600}
.tf-cx-num{display:inline-flex;align-items:center;gap:3px;padding:6px 10px;border-radius:10px;background:color-mix(in srgb,var(--cx-c) 10%,var(--color-bg));color:var(--cx-c);font-weight:700}
.tf-cx-num b{font-weight:700}
.tf-cx-num input{width:104px;border:0;background:transparent;font:inherit;color:inherit;text-align:right;outline:none;-moz-appearance:textfield;appearance:textfield}
.tf-cx-num input::-webkit-outer-spin-button,.tf-cx-num input::-webkit-inner-spin-button{-webkit-appearance:none;margin:0}
.tf-cx-num:focus-within{box-shadow:0 0 0 2px var(--cx-c)}
.tf-cx-num em{font-style:normal;font-size:13px}
.tf-cx-field input[type=range]{--pct:50%;width:100%;height:6px;margin:0;border-radius:99px;-webkit-appearance:none;appearance:none;background:linear-gradient(90deg,var(--cx-c) var(--pct),var(--color-border) var(--pct));cursor:pointer}
.tf-cx-field input[type=range]::-webkit-slider-thumb{-webkit-appearance:none;width:22px;height:22px;border-radius:50%;background:#fff;border:4px solid var(--cx-c);box-shadow:0 2px 8px rgba(0,0,0,.2)}
.tf-cx-field input[type=range]::-moz-range-thumb{width:16px;height:16px;border-radius:50%;background:#fff;border:4px solid var(--cx-c)}
.tf-cx-field input[type=range]:focus-visible{outline:3px solid color-mix(in srgb,var(--cx-c) 45%,transparent);outline-offset:6px}
.tf-cx-out{padding:24px;border-radius:var(--radius);background:color-mix(in srgb,var(--cx-c) 7%,var(--color-surface))}
.tf-cx-big{display:flex;flex-direction:column;margin-bottom:14px}
.tf-cx-big span{font-size:13px;font-weight:600;color:var(--color-muted)}
.tf-cx-big strong{font-family:var(--font-heading);font-size:clamp(28px,3.2vw,38px);line-height:1.15;color:var(--cx-c)}
.tf-cx-big small{font-size:12.5px;color:var(--color-muted)}
.tf-cx-chart{display:flex;align-items:center;gap:18px;margin:4px 0 12px}
.tf-cx-chart svg{width:112px;height:112px;flex:none}
.tf-cx-chart text{font:700 15px var(--font-heading);fill:var(--color-text)}
.tf-cx-arc{transition:stroke-dasharray .4s ease}
.tf-cx-chart ul{list-style:none;margin:0;padding:0;display:grid;gap:7px;font-size:13.5px}
.tf-cx-chart i{display:inline-block;width:11px;height:11px;margin-right:8px;border-radius:3px;vertical-align:-1px}
.tf-cx-k1{background:var(--cx-soft)}.tf-cx-k2{background:var(--cx-c)}
.tf-cx-rows{list-style:none;margin:6px 0 0;padding:0;border-top:1px dashed var(--color-border)}
.tf-cx-rows li{display:flex;justify-content:space-between;gap:14px;padding:10px 0;border-bottom:1px dashed var(--color-border);font-size:14px}
.tf-cx-rows b{white-space:nowrap}
.tf-cx-cta{display:flex;align-items:center;justify-content:center;gap:9px;margin-top:18px;padding:13px 18px;border-radius:var(--radius);background:#11793F;color:#fff;font-weight:700;text-decoration:none;transition:transform .2s,box-shadow .2s}
.tf-cx-cta:hover{transform:translateY(-2px);box-shadow:0 10px 24px rgba(17,121,63,.3)}
.tf-cx-warn{margin:0;font-weight:600;color:#B42318}
.tf-cx-disc{max-width:860px;margin:20px auto 0;font-size:12.5px;line-height:1.6;text-align:center;color:var(--color-muted)}
@container tfcx (max-width:760px){
  .tf-cx-body{grid-template-columns:minmax(0,1fr);gap:20px}
  .tf-cx-panel{padding:20px 16px}
  .tf-cx-tabs{flex-wrap:nowrap;justify-content:flex-start;overflow-x:auto;scroll-snap-type:x proximity;scrollbar-width:none;margin:0 -2px;padding:4px 2px 14px}
  .tf-cx-tabs::-webkit-scrollbar{display:none}
  .tf-cx-tab{flex:none;scroll-snap-align:start;padding:9px 14px}
  .tf-cx-tabt{font-size:14px;white-space:nowrap}
}
@container tfcx (max-width:440px){
  .tf-cx-lab{flex-wrap:wrap;gap:6px 10px;font-size:14px}
  .tf-cx-lab label{flex:1 1 auto;min-width:0}
  .tf-cx-num{margin-left:auto}
  .tf-cx-num input{width:88px}
  .tf-cx-out{padding:18px 14px}
  .tf-cx-chart{gap:14px}
  .tf-cx-chart svg{width:96px;height:96px}
  .tf-cx-rows li{font-size:13.5px}
  .tf-cx-cta{padding:12px 14px;font-size:14.5px}
}
@media(prefers-reduced-motion:reduce){.tf-cx-panel{animation:none}.tf-cx-arc{transition:none}}
/* ---- pillars ---- */
.tf-px{text-align:left}
.tf-px-radio{position:absolute;opacity:0;width:1px;height:1px;pointer-events:none}
.tf-px-nav{display:grid;grid-template-columns:repeat(var(--px-n,5),minmax(0,1fr));gap:14px;margin-bottom:22px}
.tf-px-pick{display:flex;flex-direction:column;align-items:center;gap:6px;padding:18px 10px 16px;border:1.5px solid var(--color-border);border-radius:calc(var(--radius) + 4px);background:var(--color-bg);color:var(--color-text);text-align:center;cursor:pointer;transition:transform .25s,box-shadow .25s,border-color .25s,background .25s}
.tf-px-pick:hover{transform:translateY(-3px);border-color:var(--px-c)}
.tf-px-orb{display:flex;width:60px;height:60px;align-items:center;justify-content:center;border-radius:50%;font-size:28px;background:color-mix(in srgb,var(--px-c) 14%,var(--color-bg));color:var(--px-c);transition:background .25s,color .25s}
.tf-px-pname{font-size:13px;font-weight:700;letter-spacing:.04em;color:var(--px-c)}
.tf-px-ptitle{font-size:14.5px;font-weight:600;line-height:1.3}
.tf-px-panel{display:none;grid-template-columns:minmax(0,5fr) minmax(0,7fr);overflow:hidden;border-radius:calc(var(--radius) + 8px);background:var(--color-bg);color:var(--color-text);border:1px solid var(--color-border);box-shadow:0 18px 50px rgba(16,24,40,.10);animation:tf-pxin .45s ease}
@keyframes tf-pxin{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:none}}
.tf-px-pimg{position:relative;min-height:320px;background:color-mix(in srgb,var(--px-c) 20%,#000)}
.tf-px-pimg img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover}
.tf-px-pimg::after{content:"";position:absolute;inset:0;background:linear-gradient(180deg,transparent 40%,color-mix(in srgb,var(--px-c) 70%,#000) 100%)}
.tf-px-bigsym{position:absolute;left:22px;bottom:18px;z-index:1;font-size:54px;line-height:1;filter:drop-shadow(0 4px 12px rgba(0,0,0,.35))}
.tf-px-pbody{padding:34px 36px;border-top:5px solid var(--px-c)}
.tf-px-panel:not(:has(.tf-px-pimg)){grid-template-columns:minmax(0,1fr)}
.tf-px-kicker{margin:0 0 4px;font-size:13px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--px-c)}
.tf-px-sub{margin:0 0 4px;font-size:14px;font-weight:600;color:var(--color-muted)}
.tf-px-title{margin:0;font-family:var(--font-heading);font-size:clamp(22px,2.4vw,30px);line-height:1.25}
.tf-px-tagline{margin:14px 0 0;padding-left:14px;border-left:3px solid var(--px-c);font-size:17px;font-style:italic;line-height:1.55;color:var(--color-text)}
.tf-px-text{margin:14px 0 0;font-size:15.5px;line-height:1.7;color:var(--color-muted)}
.tf-px-chips{display:flex;flex-wrap:wrap;gap:8px;margin:18px 0 0;padding:0;list-style:none}
.tf-px-chips li{padding:6px 13px;border-radius:99px;font-size:13px;font-weight:600;background:color-mix(in srgb,var(--px-c) 11%,var(--color-bg));color:color-mix(in srgb,var(--px-c) 75%,#000)}
.tf-px-cta{display:inline-flex;align-items:center;gap:8px;margin-top:22px;padding:12px 22px;border-radius:var(--radius);background:var(--px-c);color:#fff;font-weight:700;text-decoration:none;transition:transform .2s,box-shadow .2s}
.tf-px-cta:hover{transform:translateY(-2px);box-shadow:0 10px 24px color-mix(in srgb,var(--px-c) 35%,transparent)}
.tf-px-cards{display:grid;grid-template-columns:repeat(var(--px-n,3),minmax(0,1fr));gap:18px;text-align:left}
.tf-px-card{display:flex;flex-direction:column;overflow:hidden;border-radius:calc(var(--radius) + 4px);background:var(--color-bg);color:var(--color-text);border:1px solid var(--color-border);border-top:4px solid var(--px-c);box-shadow:0 8px 26px rgba(16,24,40,.07);transition:transform .25s,box-shadow .25s}
.tf-px-card:hover{transform:translateY(-4px);box-shadow:0 16px 40px rgba(16,24,40,.12)}
.tf-px-img{position:relative;aspect-ratio:4/3}
.tf-px-img img{width:100%;height:100%;object-fit:cover}
.tf-px-top{padding:18px 20px 0}
.tf-px-badge{display:inline-flex;align-items:center;gap:6px;padding:5px 12px;border-radius:99px;font-size:13px;font-weight:700;background:var(--px-c);color:#fff}
.tf-px-img .tf-px-badge{position:absolute;left:12px;bottom:12px}
.tf-px-cbody{display:flex;flex-direction:column;flex:1;padding:18px 20px 22px}
.tf-px-cbody .tf-px-title{font-size:19px}
.tf-px-cbody .tf-px-tagline{font-size:14.5px}
.tf-px-cbody .tf-px-text{font-size:14.5px}
.tf-px-cbody .tf-px-cta{align-self:flex-start;margin-top:auto;padding-top:12px;padding:10px 16px;margin-top:16px}
@media(max-width:1024px){.tf-px-cards{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:860px){.tf-px-nav{display:flex;overflow-x:auto;scroll-snap-type:x mandatory;scrollbar-width:none;margin:0 -4px;padding:4px}.tf-px-pick{flex:0 0 118px;scroll-snap-align:start}.tf-px-panel{grid-template-columns:minmax(0,1fr)}.tf-px-pimg{min-height:200px}.tf-px-pbody{padding:22px 18px}}
@media(max-width:560px){.tf-px-cards{grid-template-columns:minmax(0,1fr)}}
@media(prefers-reduced-motion:reduce){.tf-px-panel{animation:none}}
/* ---- quiz ---- */
.tf-qz-wrap{max-width:860px;margin:0 auto}
.tf-qz-split{display:grid;grid-template-columns:minmax(0,5fr) minmax(0,7fr);gap:28px;align-items:start}
.tf-qz-img{position:sticky;top:96px;overflow:hidden;border-radius:calc(var(--radius) + 6px)}
.tf-qz-img img{width:100%;aspect-ratio:4/5;object-fit:cover}
.tf-qz{padding:28px;border-radius:calc(var(--radius) + 6px);background:var(--color-bg);color:var(--color-text);border:1px solid var(--color-border);box-shadow:0 14px 44px rgba(16,24,40,.08);text-align:left}
.tf-qz-bar{position:relative;height:10px;margin-bottom:22px;border-radius:99px;background:color-mix(in srgb,var(--color-primary) 12%,var(--color-bg))}
.tf-qz-fill{display:block;width:0;height:100%;border-radius:99px;background:var(--color-primary);transition:width .35s ease}
.tf-qz-count{position:absolute;right:0;top:14px;font-size:12px;font-style:normal;font-weight:700;color:var(--color-muted)}
.tf-qz-list{list-style:none;margin:0;padding:0;display:grid;gap:14px}
.tf-qz-q{padding:16px 18px;border-radius:var(--radius);border:1px solid var(--color-border);transition:border-color .2s,background .2s}
.tf-qz-q.tf-qz-done{border-color:color-mix(in srgb,var(--color-primary) 40%,var(--color-border));background:color-mix(in srgb,var(--color-primary) 4%,var(--color-bg))}
.tf-qz-qt{display:flex;gap:10px;margin:0 0 12px;font-size:16px;font-weight:600;line-height:1.45}
.tf-qz-n{flex:none;display:inline-flex;width:26px;height:26px;align-items:center;justify-content:center;border-radius:50%;font-size:13px;background:var(--color-primary);color:var(--color-primary-fg)}
.tf-qz-opts{display:flex;flex-wrap:wrap;gap:8px;padding-left:36px}
.tf-qz-opt{position:relative;cursor:pointer}
.tf-qz-opt input{position:absolute;opacity:0;width:1px;height:1px}
.tf-qz-opt span{display:inline-block;padding:8px 16px;border-radius:99px;border:1.5px solid var(--color-border);font-size:14px;font-weight:600;transition:all .18s}
.tf-qz-opt:hover span{border-color:var(--color-primary)}
.tf-qz-opt input:focus-visible+span{outline:3px solid var(--color-primary);outline-offset:2px}
.tf-qz-yes input:checked+span{background:#15803D;border-color:#15803D;color:#fff}
.tf-qz-no input:checked+span{background:#B42318;border-color:#B42318;color:#fff}
.tf-qz-unsure input:checked+span{background:#B45309;border-color:#B45309;color:#fff}
.tf-qz-result{display:flex;gap:24px;align-items:flex-start;margin-top:22px;padding:24px;border-radius:var(--radius);background:color-mix(in srgb,var(--color-primary) 7%,var(--color-surface));animation:tf-cxin .4s ease}
.tf-qz-result[hidden]{display:none}
.tf-qz-score{position:relative;flex:none;width:120px;height:120px}
.tf-qz-score svg{width:100%;height:100%}
.tf-qz-score strong{position:absolute;inset:0;display:flex;align-items:center;justify-content:center;font-family:var(--font-heading);font-size:28px;color:var(--color-text)}
.tf-qz-good{color:#15803D}.tf-qz-mid{color:#B45309}.tf-qz-low{color:#B42318}
.tf-qz-rtext h3{margin:0;font-family:var(--font-heading);font-size:21px}
.tf-qz-rtext p{margin:8px 0 0;font-size:15px;line-height:1.6;color:var(--color-muted)}
.tf-qz-gaps{margin:12px 0 0;padding-left:20px;font-size:14.5px;line-height:1.6}
.tf-qz-cta{display:inline-flex;align-items:center;margin-top:16px;padding:12px 20px;border-radius:var(--radius);background:#11793F;color:#fff;font-weight:700;text-decoration:none}
.tf-qz-reset{margin:16px 0 0 12px;padding:10px 14px;border:0;background:none;font:inherit;font-weight:600;color:var(--color-muted);text-decoration:underline;cursor:pointer}
@media(max-width:860px){.tf-qz-split{grid-template-columns:minmax(0,1fr)}.tf-qz-img{position:static}.tf-qz-img img{aspect-ratio:16/10}}
@media(max-width:560px){.tf-qz{padding:18px 14px}.tf-qz-opts{padding-left:0}.tf-qz-result{flex-direction:column;align-items:center;text-align:center}.tf-qz-gaps{text-align:left}.tf-qz-reset{margin-left:0}}
/* ---- social ---- */
.tf-scs{display:grid;grid-template-columns:repeat(var(--sc-cols,3),minmax(0,1fr));gap:16px;text-align:left}
@media(max-width:900px){.tf-scs{grid-template-columns:repeat(min(var(--sc-cols,3),3),minmax(0,1fr))}}
.tf-sc{display:flex;flex-direction:column;gap:6px;padding:20px;border-radius:calc(var(--radius) + 4px);border:1.5px solid var(--color-border);background:var(--color-bg);color:var(--color-text);text-decoration:none;transition:transform .25s,box-shadow .25s,border-color .25s}
.tf-sc:hover,.tf-sc:focus-visible{transform:translateY(-4px);border-color:var(--sc);box-shadow:0 14px 34px color-mix(in srgb,var(--sc) 22%,transparent)}
.tf-sc-ic{display:inline-flex;width:52px;height:52px;margin-bottom:6px;align-items:center;justify-content:center;border-radius:14px;background:color-mix(in srgb,var(--sc) 13%,var(--color-bg));color:var(--sc);transition:background .25s,color .25s}
.tf-sc:hover .tf-sc-ic,.tf-sc:focus-visible .tf-sc-ic{background:var(--sc);color:#fff}
.tf-sc-t{font-family:var(--font-heading);font-size:17px;font-weight:700}
.tf-sc-x{font-size:14px;line-height:1.5;color:var(--color-muted)}
.tf-sc-a{margin-top:auto;padding-top:6px;font-size:14px;font-weight:700;color:var(--color-text)}
.tf-sc-pills{display:flex;flex-wrap:wrap;justify-content:center;gap:10px}
.tf-sc-pill{display:inline-flex;align-items:center;gap:8px;padding:8px 16px 8px 8px;border-radius:99px;border:1.5px solid var(--color-border);background:var(--color-bg);color:var(--color-text);font-weight:600;text-decoration:none;transition:border-color .2s,transform .2s}
.tf-sc-pill:hover{border-color:var(--sc);transform:translateY(-2px)}
.tf-sc-pill .tf-sc-ic{width:34px;height:34px;margin:0;border-radius:50%}
.tf-sc-pill .tf-sc-ic svg{width:18px;height:18px}
@media(max-width:560px){.tf-scs{grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}.tf-sc{padding:16px 14px}}
/* ---- steps ---- */
.tf-st{list-style:none;margin:0;padding:0;counter-reset:st}
.tf-st-horizontal{position:relative;display:grid;grid-template-columns:repeat(var(--st-n,4),minmax(0,1fr));gap:18px}
.tf-st-horizontal::before{content:"";position:absolute;top:32px;left:calc(50% / var(--st-n,4));right:calc(50% / var(--st-n,4));height:3px;border-radius:3px;background:linear-gradient(90deg,var(--color-primary),var(--color-secondary))}
.tf-st-i{position:relative}
.tf-st-horizontal .tf-st-i{display:flex;flex-direction:column;align-items:center;text-align:center}
.tf-st-dot{position:relative;z-index:1;display:inline-flex;width:66px;height:66px;align-items:center;justify-content:center;border-radius:50%;background:var(--color-bg);border:3px solid var(--st-c);color:var(--st-c);font-family:var(--font-heading);font-size:24px;font-weight:700;box-shadow:0 8px 22px color-mix(in srgb,var(--st-c) 25%,transparent);transition:transform .25s,background .25s,color .25s}
.tf-st-i:hover .tf-st-dot{transform:scale(1.08);background:var(--st-c);color:#fff}
.tf-st-t{margin:14px 0 0;font-family:var(--font-heading);font-size:17px;line-height:1.3;color:var(--tf-heading,inherit)}
.tf-st-x{margin:6px 0 0;font-size:14px;line-height:1.55;color:var(--tf-text,var(--color-muted))}
.tf-st-vertical{max-width:760px;margin:0 auto;text-align:left}
.tf-st-vertical .tf-st-i{display:flex;gap:20px;padding-bottom:28px}
.tf-st-vertical .tf-st-i:not(:last-child)::before{content:"";position:absolute;left:32px;top:66px;bottom:0;width:3px;background:color-mix(in srgb,var(--st-c) 35%,transparent)}
.tf-st-vertical .tf-st-t{margin-top:8px}
.tf-st-cta{margin-top:32px;text-align:center}
@media(max-width:860px){.tf-st-horizontal{grid-template-columns:minmax(0,1fr);max-width:520px;margin:0 auto;text-align:left}.tf-st-horizontal::before{display:none}.tf-st-horizontal .tf-st-i{flex-direction:row;align-items:flex-start;gap:16px;text-align:left}.tf-st-horizontal .tf-st-t{margin-top:8px}.tf-st-dot{flex:none;width:54px;height:54px;font-size:20px}}
.tf-tkstatic .tf-tklink{display:inline-block;border:1px solid currentColor;border-radius:4px;padding:3px 12px}
.tf-tkstatic .tf-tklink:hover{text-decoration:none;background:rgba(255,255,255,.12)}
.tf-pgal{display:flex;gap:14px;align-items:flex-start}
.tf-pthumbs{display:flex;flex-direction:column;gap:10px;width:78px;flex-shrink:0;max-height:520px;overflow-y:auto;scrollbar-width:thin}
.tf-pthumb{padding:0;width:78px;height:78px;border:2px solid var(--color-border);border-radius:8px;background:var(--color-surface);cursor:pointer;overflow:hidden;flex-shrink:0}
.tf-pthumb.is-active{border-color:var(--color-primary)}
.tf-pthumb img{width:100%;height:100%;object-fit:cover;display:block}
.tf-pstage{flex:1;min-width:0}
/* Swipe track. scroll-snap does the whole gesture natively — no touch handlers,
   and it still works with JavaScript off (you just lose the dots updating). */
.tf-pmain{width:100%;height:520px;background:var(--color-surface);border-radius:var(--radius);display:flex;overflow-x:auto;overflow-y:hidden;scroll-snap-type:x mandatory;-webkit-overflow-scrolling:touch;scrollbar-width:none}
.tf-pmain::-webkit-scrollbar{display:none}
.tf-pslide{flex:0 0 100%;width:100%;height:100%;display:flex;align-items:center;justify-content:center;scroll-snap-align:center;scroll-snap-stop:always}
.tf-pslide img{max-width:100%;max-height:100%;object-fit:contain}
.tf-pdots{display:none;gap:6px;justify-content:center;margin-top:12px}
.tf-pdot{width:7px;height:7px;padding:0;border:0;border-radius:999px;background:var(--color-border);cursor:pointer;transition:width .2s,background .2s}
.tf-pdot.is-active{width:20px;background:var(--color-primary)}
@media(max-width:640px){
  /* align-items:flex-start (the desktop rule) would size the thumbnail row to
     its CONTENT once it is stacked, so a product with eight photos makes a
     694px-wide strip. stretch + min-width:0 keeps it to the column and lets its
     own overflow-x do the scrolling. */
  .tf-pgal{flex-direction:column-reverse;align-items:stretch}
  .tf-pthumbs{flex-direction:row;width:auto;min-width:0;max-height:none;overflow-x:auto}
  .tf-pmain{height:340px}
  /* The thumbnail row sits below the photo on a phone, so the dots are what
     actually say "there are more, swipe". */
  .tf-pdots{display:flex}
}
/* minmax(0,1fr), not 1fr: a column's minimum is otherwise its min-content, and the
   one-line (nowrap) titles made the strip 614px wide on a 375px phone. */
.tf-bsgrid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:26px;margin-top:34px;text-align:left}
@media(min-width:640px){.tf-bsgrid{grid-template-columns:repeat(3,minmax(0,1fr))}}
@media(min-width:1024px){.tf-bsgrid{grid-template-columns:repeat(4,minmax(0,1fr))}}
.tf-bscard{display:block;color:inherit;text-decoration:none}
.tf-bscard img{width:100%;height:230px;object-fit:cover;border-radius:6px}
.tf-bstitle{margin:14px 0 0;font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:.01em;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.tf-bsprice{margin:8px 0 0;display:flex;align-items:baseline;flex-wrap:wrap;gap:8px;font-size:13px}
.tf-bssell{font-size:17px;font-weight:800}
.tf-bsprice s{color:var(--tf-text,var(--color-muted))}
.tf-bsoff{font-weight:800;color:#dc2626}
.tf-attr-opt{display:inline-flex;align-items:center;gap:7px;padding:8px 14px;border:2px solid var(--color-border);border-radius:999px;background:var(--color-surface);color:inherit;font-size:13px;font-weight:600;cursor:pointer;transition:border-color .15s}
.tf-attr-opt.is-sel{border-color:var(--color-primary)}
.tf-attr-opt img{width:22px;height:22px;border-radius:50%;object-fit:cover}
.tf-attr-swatch{display:inline-block;width:16px;height:16px;border-radius:50%;border:1px solid var(--color-border)}
.tf-anim-ready [data-anim]{opacity:0;transition:opacity .7s ease,transform .7s ease;will-change:opacity,transform}
.tf-anim-ready [data-anim="slide-up"]{transform:translateY(34px)}
.tf-anim-ready [data-anim="zoom"]{transform:scale(.93)}
.tf-anim-ready [data-anim].tf-in{opacity:1;transform:none}
@media(prefers-reduced-motion:reduce){.tf-anim-ready [data-anim]{opacity:1;transform:none;transition:none}}
/* Mobile sticky bottom action bar */
.tf-mobile-bar{position:fixed;bottom:0;left:0;right:0;z-index:1000;background:var(--color-surface,#fff);border-top:1px solid var(--color-border,#e5e7eb);padding:6px 0 calc(6px + env(safe-area-inset-bottom,0px));box-shadow:0 -2px 12px rgba(0,0,0,.1)}
.tf-mbar-inner{display:flex;align-items:stretch;justify-content:space-around;max-width:500px;margin:0 auto}
.tf-mbar-btn{flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:2px;padding:4px 2px;background:none;border:none;color:var(--color-text,#111827);cursor:pointer;font-size:9px;font-weight:600;line-height:1.2;text-decoration:none;min-height:44px;transition:opacity .15s;-webkit-tap-highlight-color:transparent;touch-action:manipulation}
.tf-mbar-btn:active{opacity:.6}
.tf-mbar-btn svg{width:20px;height:20px;display:block}
.tf-mbar-btn span{display:block;text-align:center}
@media(min-width:641px){.tf-mobile-bar{display:none!important}}
CSS;
    }
}
