<?php
/**
 * ====================================================
 * TAPIFY - On-site SEO for public vCard mini-sites
 * ====================================================
 * Phase 1 (pro templates): build and inject SEO <head> tags — an optimized
 * <title>, meta description, canonical URL (subdomain form), Open Graph /
 * Twitter cards, and LocalBusiness JSON-LD — WITHOUT touching any template
 * body/design. Injection is confined to the <head> section only.
 *
 * Used by vcard.php: it output-buffers the rendered template and passes the
 * HTML through tapify_inject_seo_head() before sending it to the browser.
 *
 * All functions are namespaced with a `tapify_seo_` prefix and are guarded so
 * this file is safe to require_once more than once.
 */

if (!function_exists('tapify_seo_pro_templates')):
/**
 * Template IDs that are considered "pro / premium" and receive the SEO head.
 * Legacy templates (vcard1–vcard42) are intentionally excluded for now.
 *   - Premium standalone set:      vcard01–vcard28
 *   - Premium exact-design set:    vcard43–vcard71
 * @return string[]
 */
function tapify_seo_pro_templates() {
    $ids = [];
    for ($i = 1; $i <= 28; $i++)  $ids[] = 'vcard' . str_pad((string)$i, 2, '0', STR_PAD_LEFT); // vcard01..vcard28
    for ($i = 43; $i <= 71; $i++) $ids[] = 'vcard' . $i;                                          // vcard43..vcard71
    return $ids;
}
endif;

if (!function_exists('tapify_seo_is_pro_template')):
function tapify_seo_is_pro_template($templateId) {
    return in_array((string)$templateId, tapify_seo_pro_templates(), true);
}
endif;

if (!function_exists('tapify_seo_abs_url')):
/**
 * Turn a stored image path into an absolute URL suitable for Open Graph / JSON-LD
 * (social crawlers require absolute URLs). Absolute paths are returned as-is;
 * root-relative/relative paths are prefixed with the asset host (SITE_URL).
 */
function tapify_seo_abs_url($path) {
    $path = trim((string)$path);
    if ($path === '') return '';
    if (preg_match('#^https?://#i', $path)) return $path;
    $base = defined('SITE_URL') ? rtrim(SITE_URL, '/') : '';
    return $base . '/' . ltrim($path, '/');
}
endif;

if (!function_exists('tapify_seo_city_from_location')):
/**
 * Best-effort city extraction from a free-text address for use in the <title>
 * (e.g. "Prime Motors — Car Dealer in Nagpur"). Conservative: returns '' when it
 * can't find a plausible city rather than guessing wrongly.
 *
 * Heuristic tuned for the common Indian format
 *   "Shop 4, MG Road, Dhantoli, Nagpur, Maharashtra 440012, India"
 * Drops segments containing digits (house numbers / PINs), the country, and
 * Indian state/UT names, then takes the last remaining segment as the city.
 */
function tapify_seo_city_from_location($location) {
    $location = trim((string)$location);
    if ($location === '') return '';

    // Strip an embedded map URL if the address field accidentally holds one.
    if (preg_match('#^https?://#i', $location)) return '';

    $states = [
        'andhra pradesh','arunachal pradesh','assam','bihar','chhattisgarh','goa',
        'gujarat','haryana','himachal pradesh','jharkhand','karnataka','kerala',
        'madhya pradesh','maharashtra','manipur','meghalaya','mizoram','nagaland',
        'odisha','orissa','punjab','rajasthan','sikkim','tamil nadu','telangana',
        'tripura','uttar pradesh','uttarakhand','west bengal','delhi','new delhi',
        'jammu and kashmir','ladakh','puducherry','chandigarh','andaman and nicobar',
        'dadra and nagar haveli','daman and diu','lakshadweep',
    ];
    $stop = array_merge($states, ['india','bharat']);

    $parts = array_map('trim', explode(',', $location));
    $kept  = [];
    foreach ($parts as $p) {
        if ($p === '') continue;
        if (preg_match('/\d/', $p)) continue;          // house no. / PIN codes
        if (in_array(mb_strtolower($p), $stop, true)) continue;
        $kept[] = $p;
    }
    if (empty($kept)) return '';
    $city = end($kept);
    // Guard against overly long fragments (likely not a bare city name).
    if (mb_strlen($city) > 40) return '';
    return $city;
}
endif;

if (!function_exists('tapify_seo_clip')):
/**
 * Normalize then trim text for use in meta tags / titles:
 *  - decode HTML entities (so "&nbsp;"/"&amp;" don't show literally in snippets)
 *  - collapse non-breaking spaces and runs of whitespace
 *  - cut to $max on a word boundary, appending an ellipsis when truncated.
 */
function tapify_seo_clip($text, $max = 158) {
    $text = html_entity_decode((string)$text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $text = str_replace("\xC2\xA0", ' ', $text);        // NBSP -> regular space
    $collapsed = preg_replace('/\s+/u', ' ', $text);
    $text = trim($collapsed !== null ? $collapsed : preg_replace('/\s+/', ' ', $text));
    if ($text === '' || mb_strlen($text) <= $max) return $text;
    $cut = mb_substr($text, 0, $max - 1);
    $sp  = mb_strrpos($cut, ' ');
    if ($sp !== false && $sp > $max * 0.5) $cut = mb_substr($cut, 0, $sp);
    return rtrim($cut, " ,.;:-") . '…';
}
endif;

if (!function_exists('tapify_seo_hours_spec')):
/**
 * Convert vcard_business_hours rows into schema.org openingHoursSpecification
 * entries. Times are stored as free-text 12h strings ("10:00 AM"); each is
 * parsed defensively and any day that does not cleanly parse is skipped so the
 * emitted JSON-LD is always valid.
 *
 * @param array $rows Rows: [day_name, is_open, open_time, close_time]
 * @return array<int, array<string,string>>
 */
function tapify_seo_hours_spec(array $rows) {
    $dayMap = [
        'MONDAY' => 'Monday', 'TUESDAY' => 'Tuesday', 'WEDNESDAY' => 'Wednesday',
        'THURSDAY' => 'Thursday', 'FRIDAY' => 'Friday', 'SATURDAY' => 'Saturday',
        'SUNDAY' => 'Sunday',
    ];
    $to24 = function ($t) {
        $t = strtoupper(trim((string)$t));
        if ($t === '') return null;
        foreach (['g:i A', 'h:i A', 'g:iA', 'h:iA', 'G:i', 'H:i'] as $fmt) {
            $dt = DateTime::createFromFormat('!' . $fmt, $t);
            $err = DateTime::getLastErrors();
            if ($dt instanceof DateTime && empty($err['warning_count']) && empty($err['error_count'])) {
                return $dt->format('H:i');
            }
        }
        return null;
    };

    $spec = [];
    foreach ($rows as $r) {
        if (isset($r['is_open']) && (int)$r['is_open'] !== 1) continue;
        $day = $dayMap[strtoupper(trim((string)($r['day_name'] ?? '')))] ?? null;
        if ($day === null) continue;
        $opens  = $to24($r['open_time'] ?? '');
        $closes = $to24($r['close_time'] ?? '');
        if ($opens === null || $closes === null) continue;
        $spec[] = [
            '@type'     => 'OpeningHoursSpecification',
            'dayOfWeek' => 'https://schema.org/' . $day,
            'opens'     => $opens,
            'closes'    => $closes,
        ];
    }
    return $spec;
}
endif;

if (!function_exists('tapify_seo_build_vcard')):
/**
 * Build the SEO data set for a vCard from its DB row (+ optional related data).
 *
 * @param array $vcard  Row from `vcards`.
 * @param array $ctx    Optional: ['services'=>[], 'serviceCategories'=>[],
 *                       'socialLinks'=>[], 'fullName'=>string, 'host'=>string]
 * @return array {title, description, image, canonical, siteName, jsonld}
 */
function tapify_seo_build_vcard(array $vcard, array $ctx = []) {
    $fullName = trim((string)($ctx['fullName'] ?? ''));
    if ($fullName === '') {
        $fullName = trim(((string)($vcard['first_name'] ?? '')) . ' ' . ((string)($vcard['last_name'] ?? '')));
    }

    // Business/display name: prefer company, then card name, then person name.
    $company  = trim((string)($vcard['company'] ?? ''));
    $cardName = trim((string)($vcard['vcard_name'] ?? ''));
    $name = $company !== '' ? $company : ($cardName !== '' ? $cardName : $fullName);
    if ($name === '') $name = 'Tapify Card';

    // Descriptor: what the business does.
    $descriptor = trim((string)($vcard['occupation'] ?? ''));
    if ($descriptor === '') $descriptor = trim((string)($vcard['job_title'] ?? ''));

    $city = tapify_seo_city_from_location($vcard['location'] ?? '');

    // ---- <title> ----
    // Prefer an admin-set SEO title (vcards.seo_home_title / seo_site_title);
    // otherwise build "{Name} — {Descriptor} in {City}" (parts omitted when empty).
    $overrideTitle = trim((string)($vcard['seo_home_title'] ?? ''));
    if ($overrideTitle === '') $overrideTitle = trim((string)($vcard['seo_site_title'] ?? ''));
    if ($overrideTitle !== '') {
        $title = tapify_seo_clip($overrideTitle, 70);
    } else {
        $title = $name;
        if ($descriptor !== '' && mb_stripos($name, $descriptor) === false) {
            $title .= ' — ' . $descriptor;
        }
        if ($city !== '' && mb_stripos($title, $city) === false) {
            $title .= ' in ' . $city;
        }
        $title = tapify_seo_clip($title, 65);
    }

    // ---- meta description ----
    // Prefer an admin-set description (vcards.seo_meta_description); otherwise use
    // the card description; otherwise synthesize one from name/services/city.
    $overrideDesc = trim(strip_tags((string)($vcard['seo_meta_description'] ?? '')));
    $rawDesc = $overrideDesc !== '' ? $overrideDesc : trim(strip_tags((string)($vcard['description'] ?? '')));
    if ($rawDesc === '') {
        $bits = [];
        $lead = $name . ($descriptor !== '' ? ' — ' . $descriptor : '');
        if ($city !== '') $lead .= ' in ' . $city;
        $bits[] = $lead . '.';
        // Fold in a few service names if available.
        $svcNames = [];
        foreach (($ctx['services'] ?? []) as $s) {
            $n = trim((string)($s['title'] ?? $s['name'] ?? $s['service_name'] ?? ''));
            if ($n !== '') $svcNames[] = $n;
        }
        foreach (($ctx['serviceCategories'] ?? []) as $sc) {
            $n = trim((string)($sc['name'] ?? $sc['category_name'] ?? ''));
            if ($n !== '') $svcNames[] = $n;
        }
        if (!empty($svcNames)) {
            $svcNames = array_slice(array_values(array_unique($svcNames)), 0, 6);
            $bits[] = 'Services: ' . implode(', ', $svcNames) . '.';
        }
        $bits[] = 'Contact, address, hours & more on this digital card.';
        $rawDesc = implode(' ', $bits);
    }
    $description = tapify_seo_clip($rawDesc, 158);

    // ---- image (absolute) ----
    $imgRaw = trim((string)($vcard['profile_image'] ?? ''));
    if ($imgRaw === '') $imgRaw = trim((string)($vcard['cover_image'] ?? ''));
    $image = tapify_seo_abs_url($imgRaw);

    // ---- canonical (subdomain form via existing helper) ----
    $alias = trim((string)($vcard['url_alias'] ?? ''));
    $canonical = function_exists('public_card_url') && $alias !== ''
        ? public_card_url($alias)
        : (defined('PUBLIC_URL') ? rtrim(PUBLIC_URL, '/') . '/' . $alias : '');

    // ---- telephone ----
    $phone = trim((string)($vcard['phone'] ?? ''));
    $tel = '';
    if ($phone !== '') {
        $cc = trim((string)($vcard['phone_country_code'] ?? ''));
        $tel = ($cc !== '' && strpos($phone, '+') !== 0) ? ($cc . ' ' . $phone) : $phone;
    }

    // ---- social sameAs ----
    $sameAs = [];
    foreach (($ctx['socialLinks'] ?? []) as $sl) {
        $u = trim((string)($sl['url'] ?? $sl['link'] ?? ''));
        if ($u !== '' && preg_match('#^https?://#i', $u)) $sameAs[] = $u;
    }

    // ---- LocalBusiness JSON-LD ----
    $ld = [
        '@context' => 'https://schema.org',
        '@type'    => 'LocalBusiness',
        'name'     => $name,
    ];
    if ($canonical !== '')   $ld['url'] = $canonical;
    if ($image !== '')       $ld['image'] = $image;
    if ($description !== '') $ld['description'] = $description;
    if ($tel !== '')         $ld['telephone'] = $tel;
    $email = trim((string)($vcard['email'] ?? ''));
    if ($email !== '')       $ld['email'] = $email;
    $locText = trim((string)($vcard['location'] ?? ''));
    if ($locText !== '' && !preg_match('#^https?://#i', $locText)) {
        $addr = ['@type' => 'PostalAddress', 'streetAddress' => $locText, 'addressCountry' => 'IN'];
        if ($city !== '') $addr['addressLocality'] = $city;
        $ld['address'] = $addr;
    }
    if (!empty($sameAs)) $ld['sameAs'] = array_values(array_unique($sameAs));
    $hours = tapify_seo_hours_spec($ctx['businessHours'] ?? []);
    if (!empty($hours)) $ld['openingHoursSpecification'] = $hours;

    return [
        'title'       => $title,
        'description' => $description,
        'image'       => $image,
        'canonical'   => $canonical,
        'siteName'    => defined('SITE_NAME') ? SITE_NAME : 'Tapify',
        'jsonld'      => $ld,
    ];
}
endif;

if (!function_exists('tapify_seo_head_tags')):
/**
 * Render the SEO tags as an HTML string (title + metas + canonical + OG/Twitter
 * + JSON-LD). Attribute values are escaped; JSON-LD is hardened against
 * </script> breakouts via JSON_HEX_TAG/JSON_HEX_AMP.
 */
function tapify_seo_head_tags(array $seo) {
    $e = function ($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); };
    $out = [];

    $title = trim((string)($seo['title'] ?? ''));
    if ($title !== '') $out[] = '<title>' . $e($title) . '</title>';

    $desc = trim((string)($seo['description'] ?? ''));
    if ($desc !== '') $out[] = '<meta name="description" content="' . $e($desc) . '">';

    $canonical = trim((string)($seo['canonical'] ?? ''));
    if ($canonical !== '') $out[] = '<link rel="canonical" href="' . $e($canonical) . '">';

    $img  = trim((string)($seo['image'] ?? ''));
    $site = trim((string)($seo['siteName'] ?? 'Tapify'));

    // Open Graph
    $out[] = '<meta property="og:type" content="website">';
    if ($title !== '')     $out[] = '<meta property="og:title" content="' . $e($title) . '">';
    if ($desc !== '')      $out[] = '<meta property="og:description" content="' . $e($desc) . '">';
    if ($canonical !== '') $out[] = '<meta property="og:url" content="' . $e($canonical) . '">';
    if ($site !== '')      $out[] = '<meta property="og:site_name" content="' . $e($site) . '">';
    if ($img !== '')       $out[] = '<meta property="og:image" content="' . $e($img) . '">';

    // Twitter
    $out[] = '<meta name="twitter:card" content="' . ($img !== '' ? 'summary_large_image' : 'summary') . '">';
    if ($title !== '') $out[] = '<meta name="twitter:title" content="' . $e($title) . '">';
    if ($desc !== '')  $out[] = '<meta name="twitter:description" content="' . $e($desc) . '">';
    if ($img !== '')   $out[] = '<meta name="twitter:image" content="' . $e($img) . '">';

    // JSON-LD
    if (!empty($seo['jsonld']) && is_array($seo['jsonld'])) {
        $json = json_encode(
            $seo['jsonld'],
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP
        );
        if ($json !== false) {
            $out[] = '<script type="application/ld+json">' . $json . '</script>';
        }
    }

    return "\n" . implode("\n", $out) . "\n";
}
endif;

if (!function_exists('tapify_seo_inject_head')):
/**
 * Inject the SEO tags into the <head> of a fully-rendered template.
 *
 * Guarantees (so template DESIGN is never altered):
 *  - Only the <head> is modified; everything from </head> onward is untouched.
 *  - Replaces the first existing <title> with the SEO title.
 *  - Removes any pre-existing <meta name="description"> in <head> to avoid
 *    duplicates (some templates ship one), then appends the SEO block before </head>.
 *  - On any failure / unexpected shape, returns the original HTML unchanged.
 */
function tapify_seo_inject_head($html, array $seo) {
    try {
        $html = (string)$html;
        if ($html === '') return $html;

        // Locate the first </head> (case-insensitive). Without it, do nothing.
        if (!preg_match('#</head>#i', $html, $m, PREG_OFFSET_CAPTURE)) {
            return $html;
        }
        $headEnd = $m[0][1];
        $head = substr($html, 0, $headEnd);
        $rest = substr($html, $headEnd);            // "</head>...." — never modified

        $tags = tapify_seo_head_tags($seo);

        // Replace the first <title>...</title> with the SEO title (already inside $tags).
        $newTitle = '';
        if (($t = trim((string)($seo['title'] ?? ''))) !== '') {
            $newTitle = '<title>' . htmlspecialchars($t, ENT_QUOTES, 'UTF-8') . '</title>';
        }
        $titleReplaced = false;
        if ($newTitle !== '') {
            $head = preg_replace_callback('#<title\b[^>]*>.*?</title>#is', function () use (&$titleReplaced, $newTitle) {
                if ($titleReplaced) return '';        // drop any accidental duplicate titles
                $titleReplaced = true;
                return $newTitle;
            }, $head, 2);
        }
        // If we placed the title inline, strip the duplicate <title> from the appended block.
        if ($titleReplaced) {
            $tags = preg_replace('#<title\b[^>]*>.*?</title>\n?#is', '', $tags, 1);
        }

        // Remove any existing description metas in <head> to prevent duplicates.
        $head = preg_replace('#[ \t]*<meta\b[^>]*name=(["\'])description\1[^>]*>\s*#is', '', $head);

        return $head . $tags . $rest;
    } catch (\Throwable $e) {
        return $html; // never break the page for an SEO tweak
    }
}
endif;

if (!function_exists('tapify_seo_w3c_date')):
/** Convert a MySQL datetime ("2026-06-30 14:05:00") to a W3C date for <lastmod>. */
function tapify_seo_w3c_date($ts) {
    $ts = trim((string)$ts);
    if ($ts === '' || $ts === '0000-00-00 00:00:00') return '';
    $t = strtotime($ts);
    return $t ? date('c', $t) : '';
}
endif;

if (!function_exists('tapify_build_sitemap_xml')):
/**
 * Build a sitemap urlset from entries. Each entry: ['loc'=>url, 'lastmod'=>w3c?].
 * Deduplicates by loc and skips empties, so the output is always valid XML.
 * @param array<int, array<string,string>> $entries
 */
function tapify_build_sitemap_xml(array $entries) {
    $seen = [];
    $out = [];
    $out[] = '<?xml version="1.0" encoding="UTF-8"?>';
    $out[] = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
    foreach ($entries as $e) {
        $loc = trim((string)($e['loc'] ?? ''));
        if ($loc === '' || isset($seen[$loc])) continue;
        $seen[$loc] = true;
        $out[] = '  <url>';
        $out[] = '    <loc>' . htmlspecialchars($loc, ENT_QUOTES, 'UTF-8') . '</loc>';
        $lm = trim((string)($e['lastmod'] ?? ''));
        if ($lm !== '') $out[] = '    <lastmod>' . htmlspecialchars($lm, ENT_QUOTES, 'UTF-8') . '</lastmod>';
        $out[] = '  </url>';
    }
    $out[] = '</urlset>';
    return implode("\n", $out) . "\n";
}
endif;

if (!function_exists('tapify_build_robots_txt')):
/** Build robots.txt allowing crawl of public pages and pointing at the sitemap. */
function tapify_build_robots_txt($sitemapUrl) {
    $out = [];
    $out[] = 'User-agent: *';
    $out[] = 'Allow: /';
    $out[] = 'Disallow: /api/';
    $sitemapUrl = trim((string)$sitemapUrl);
    if ($sitemapUrl !== '') {
        $out[] = '';
        $out[] = 'Sitemap: ' . $sitemapUrl;
    }
    return implode("\n", $out) . "\n";
}
endif;

if (!function_exists('tapify_seo_ob_callback')):
/**
 * ob_start() callback used by vcard.php. Reads the prepared SEO data from a
 * global set just before rendering. Registered as an output-buffer handler so
 * injection still runs even if a template calls exit/die mid-render.
 */
function tapify_seo_ob_callback($html) {
    $seo = $GLOBALS['__tapify_seo'] ?? null;
    if (!is_array($seo)) return $html;
    return tapify_seo_inject_head($html, $seo);
}
endif;
