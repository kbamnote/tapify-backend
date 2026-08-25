<?php
/**
 * TAPIFY — website from the WhatsApp chat, built AND published.
 *
 *   POST /api/public/site-from-chat.php
 *   Header: X-Tapify-Bot-Key: <VISIBILITY_BOT_KEY>
 *
 *   { phone, email?, business?, type?, services, audience }
 *      â†’ { site_id, slug, url }     // url = https://<slug>.tapify.co.in
 *
 * WHAT THIS DOES. Exactly what an admin does from the app's Website Builder
 * "New Website" screen (api/sites/create.php), driven by the three answers
 * the customer typed in chat: resolve (or create) the client login the bot
 * already promised, pick the closest industry recipe, assemble + validate a
 * starter document, SiteRepo::create() so it shows under THEIR account, then
 * SiteRepo::publish() so <slug>.tapify.co.in serves it immediately.
 * No vCards anywhere; only builder tables are touched.
 */
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/google/PlacesClient.php';
require_once __DIR__ . '/../../builder/lib/SiteRepo.php';
require_once __DIR__ . '/../../builder/lib/SiteValidator.php';
require_once __DIR__ . '/../../builder/lib/SchemaRegistry.php';

ini_set('display_errors', '0');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

// Authenticate FIRST, then route by method. The "POST only" guard used to sit
// above this and rejected every GET with 405 — which made the industries menu
// below unreachable dead code, so the bot could never fetch the category list.
$expected = getenv('VISIBILITY_BOT_KEY') ?: '';
$given    = $_SERVER['HTTP_X_TAPIFY_BOT_KEY'] ?? '';
if ($expected === '') {
    sendError('Site intake is not configured on this server.', 503);
}
if (!hash_equals($expected, (string)$given)) {
    sendError('Not authorised.', 401);
}

// GET ?list=industries — the menu the WhatsApp bot shows so the owner can TAP
// their category instead of typing it. Same bot key; read-only.
if ($_SERVER['REQUEST_METHOD'] === 'GET' && ($_GET['list'] ?? '') === 'industries') {
    $out = [];
    foreach (SchemaRegistry::industries() as $id => $meta) {
        $out[] = [
            'id'    => (string)$id,
            'label' => (string)($meta['label'] ?? $id),
            'desc'  => mb_substr((string)($meta['description'] ?? ''), 0, 72),
        ];
    }
    usort($out, fn($a, $b) => strcasecmp($a['label'], $b['label']));
    sendSuccess('Industries', ['industries' => $out]);
}

// Anything that is not the industries GET has to be the POST that builds a site.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { sendError('POST only', 405); }

$input    = getInput();
$email    = strtolower(trim((string)($input['email'] ?? '')));
$phone    = trim((string)($input['phone'] ?? ''));
$business = trim((string)($input['business'] ?? ''));
$type     = trim((string)($input['type'] ?? ''));
$services = trim((string)($input['services'] ?? ''));
$audience = trim((string)($input['audience'] ?? ''));

// What Google does NOT know about him — collected in chat, used verbatim.
$industrySel = strtolower(trim((string)($input['industry'] ?? '')));  // tapped from the list
$story       = trim((string)($input['story'] ?? ''));                // his own words for About
$yearsIn     = (int)($input['years'] ?? 0);                          // "12 years" â†’ stats card
$instaRaw    = trim((string)($input['instagram'] ?? ''));
$logoUrl     = trim((string)($input['logo_url'] ?? ''));             // Cloudinary URL of his uploaded logo

// Normalise the Instagram answer into a followable link.
$instagramUrl = null;
if ($instaRaw !== '' && $instaRaw !== '-') {
    if (preg_match('~instagram\.com/([A-Za-z0-9_.]+)~i', $instaRaw, $m)) {
        $instagramUrl = 'https://instagram.com/' . $m[1];
    } elseif ($instaRaw[0] === '@') {
        $instagramUrl = 'https://instagram.com/' . ltrim(substr($instaRaw, 1), '@');
    } elseif (preg_match('~^[A-Za-z0-9_.]{1,30}$~', $instaRaw)) {
        $instagramUrl = 'https://instagram.com/' . $instaRaw;
    }
}

// The three chat answers are the payload; without them there is nothing to build.
if ($type === '' || $services === '' || $audience === '') {
    sendError('type, services and audience are required.', 422);
}
$name = $business !== '' ? $business : ucfirst($type);
$placeId = trim((string)($input['place_id'] ?? ''));

try {
    $pdo = getDB();

    /* â”€â”€ 0. His real Google listing — the source of truth for content. â”€â”€â”€â”€ */
    // One paid Details call per WEBSITE BUILD (never per message; score
    // checks stay cheap behind place_score_cache). A website is built once,
    // and building it out of placeholder copy would defeat the point.
    $places = new PlacesClient();
    $gmb = null;
    if ($placeId !== '' && $places->isConfigured() && PlacesClient::spendAllowed($pdo)) {
        PlacesClient::countCall($pdo);
        $gmb = $places->detailsFull($placeId);
        // ?? [] matters: every other read of gmb_photos guards, and count(null)
        // is a TypeError in PHP 8 — which is an Error, not an Exception, so it
        // would escape the catch below as an uncaught fatal with no JSON body.
        error_log('[SITE-CHAT] gmb fetch: ' . ($gmb === null ? 'NULL' : ('ok, photos=' . count($gmb['gmb_photos'] ?? []))));
    } else {
        error_log('[SITE-CHAT] gmb skipped (no place_id / key / daily cap)');
    }
    if ($gmb) {
        if (!empty($gmb['name']))    $name = $gmb['name'];
        if (!empty($gmb['category']) && $type === '') $type = $gmb['category'];
    }

    /* â”€â”€ 1. Owner: the account the bot already created and quoted back. â”€â”€â”€â”€ */
    $ownerId = null;
    if ($email !== '') {
        $st = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $st->execute([$email]);
        $ownerId = $st->fetchColumn() ?: null;
    }
    if (!$ownerId) {
        // Belt-and-braces: register should have made this login already. If
        // not, create it here with the SAME password rule the bot quoted
        // (their 10-digit number), plus the starter subscription every other
        // client gets — otherwise limit checks elsewhere find no row.
        if ($email === '') sendError('email is required so the website has an owner.', 422);
        $pass = substr(preg_replace('/\D/', '', $phone), -10);
        if (strlen($pass) < 6) sendError('A valid 10-digit contact number is required.', 422);

        $st = $pdo->prepare("INSERT INTO users (name, email, password, role, status) VALUES (?, ?, ?, 'user', 1)");
        $st->execute([$name, $email, hashPassword($pass)]);
        $ownerId = (int)$pdo->lastInsertId();

        $st = $pdo->prepare(
            "INSERT INTO subscriptions (user_id, plan_name, vcards_limit, stores_limit, price, subscribed_date, expiry_date, status)
             VALUES (?, 'Free Plan', 5, 1, 0, ?, ?, 'active')"
        );
        $st->execute([$ownerId, date('Y-m-d'), date('Y-m-d', strtotime('+1 year'))]);
    }
    error_log('[SITE-CHAT] owner resolved: ' . var_export($ownerId, true));

    /* â”€â”€ 2. Address: their business name, made unique. â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
    $slug = SiteRepo::normaliseSlug('', $name);
    if ($slug === '') sendError(SiteRepo::SLUG_RULE);
    if (!SiteRepo::slugAvailable($slug)) {
        // First choice taken — do NOT fail the chat over it. -2 … -9, then a
        // random suffix; the customer is told the exact address either way.
        $base = $slug;
        for ($i = 2; $i <= 9 && !SiteRepo::slugAvailable($slug); $i++) {
            $slug = $base . '-' . $i;
        }
        while (!SiteRepo::slugAvailable($slug)) {
            $slug = $base . '-' . substr(bin2hex(random_bytes(2)), 0, 4);
        }
    }
    error_log('[SITE-CHAT] slug chosen: ' . $slug);

    /* â”€â”€ 3. Industry: his tapped choice first, then a word-match fallback. â”€â”€ */
    $allInd   = SchemaRegistry::industries();
    $industry = null;
    if ($industrySel !== '' && isset($allInd[$industrySel])) {
        $industry = $industrySel;                       // tapped from our list
    } elseif ($type !== '') {
        foreach ($allInd as $id => $meta) {
            $aliases = is_array($meta) ? implode(' ', (array)($meta['aliases'] ?? [])) : '';
            $hay = strtolower($id . ' ' . (is_array($meta) ? ($meta['label'] ?? '') : $meta) . ' ' . $aliases
                . ' ' . ($gmb['category'] ?? ''));
            if (stripos($hay, $type) !== false || stripos($type, $id) !== false) {
                $industry = $id;
                break;
            }
        }
    }

    /* â”€â”€ 4. Document — generated from HIS Google listing, not a demo. â”€â”€â”€â”€â”€â”€ */
    $recipe  = SchemaRegistry::industries()[$industry] ?? null;
    $content = $recipe['content'] ?? [];
    $gcat    = $gmb['category'] ?? '';

    // Real-data gates: a section we cannot fill with HIS truth ships empty-
    // handed rather than with placeholder numbers or photos.
    $hasStatsData  = $yearsIn > 0 || ($gmb && ((int)($gmb['reviews_total'] ?? 0) > 0 || (float)($gmb['rating'] ?? 0) > 0));
    $hasGalleryImg = $gmb && count($gmb['gmb_photos'] ?? []) >= 4;

    // Sections that would render demo PEOPLE / PRODUCTS / REVIEWS we did not
    // earn from his listing are dropped outright; his real Google numbers
    // become a stats strip instead of fake testimonials.
    $strip = array_flip(['testimonials','team','blog','faq','products',
                         'appointment','embed','feedback','account','share']);
    $typesIn = $recipe['sections'] ?? ['header','hero','about','services','gallery','contact'];
    $types = [];
    foreach ($typesIn as $t) {
        if (isset($strip[$t])) continue;
        if ($t === 'stats' && !$hasStatsData) continue;   // never demo numbers
        $types[] = $t;
    }
    if ($hasStatsData && !in_array('stats', $types, true)) {
        $at = array_search('services', $types, true);
        array_splice($types, $at === false ? max(0, count($types) - 2) : $at + 1, 0, ['stats']);
    }

    $photoOf = fn(int $i) => $gmb['gmb_photos'][$i] ?? null;

    /**
     * Content layers, lowest priority first:
     *   manifest defaults -> industry recipe -> HIS Google data -> HIS chat words.
     * Whatever he typed or Google answered wins; recipe copy only fills what
     * both left blank, so no page ever tells another business's story.
     */
    // $gcat MUST be imported: the hero, about and services cases all read it,
    // and a closure inherits nothing automatically. Without it $gcat was null
    // inside here, so `$gcat !== ''` was always TRUE — the hero badge was set to
    // an empty string and the About fallback read "<name> is a  serving <x>".
    $buildSection = function (string $t, array $pageSeed = []) use (
        $content, $gmb, $gcat, $name, $services, $audience,
        $photoOf, $story, $yearsIn, $instagramUrl, $logoUrl
    ) {
        $instance = SchemaRegistry::newSectionInstance($t);
        if (!$instance) return null;

        $seed = array_replace_recursive((array)($content[$t] ?? null), (array)($pageSeed[$t] ?? null));
        if ($seed) {
            if (!empty($seed['variant'])) $instance['variant'] = $seed['variant'];
            if (isset($seed['props']))  $instance['props'] = array_merge((array)($instance['props'] ?? []), (array)$seed['props']);
            if (isset($seed['style']))  $instance['style'] = array_merge((array)($instance['style'] ?? []), (array)$seed['style']);
        }

        $p =& $instance['props'];
        switch ($t) {
            case 'hero':
                $p['heading'] = mb_substr($name, 0, 120);
                $sub = $story ?: ($gmb ? ($gmb['gmb_description'] ?: $services) : $services);
                if ($sub !== '') { $p['sub'] = mb_substr($sub, 0, 400); }
                if ($gcat !== '') { $p['badge'] = mb_substr($gcat, 0, 60); }
                if ($img = $photoOf(0)) { $p['image'] = $img['url']; $p['fullHeight'] = true; }
                $p['showCall'] = true;
                break;

            case 'about':
                // HIS story beats Google's summary beats a composed line.
                $body = $story ?: ($gmb ? $gmb['gmb_description'] : '');
                if ($body === '') {
                    $line = $name;
                    if ($gcat !== '')                     { $line .= ' is a ' . strtolower($gcat); }
                    if ($audience !== '')                 { $line .= " serving {$audience}"; }
                    $body = $line . '. ' . ($services !== '' ? $services : '');
                }
                if ($instagramUrl) { $body .= "\n\nFollow us on Instagram: {$instagramUrl}"; }
                $p['body'] = mb_substr($body, 0, 5000);
                if ($img = $photoOf(1)) { $p['image'] = $img['url']; }
                break;

            case 'services':
                // His typed list REPLACES the demo menu items outright.
                $items = [];
                foreach (preg_split('/\s*(?:,|and|\/|\||\n)\s*/i', $services) as $s) {
                    $s = trim((string)$s);
                    if ($s === '') continue;
                    $items[] = ['title' => mb_substr($s, 0, 80)];
                    if (count($items) >= 6) break;
                }
                if (!$items && $gcat !== '') {
                    $items[] = ['title' => mb_substr($gcat, 0, 80)];
                }
                foreach ($items as $k => &$it) {
                    if ($img = $photoOf($k + 2)) { $it['image'] = $img['url']; }
                }
                unset($it);
                if ($items) { $p['items'] = $items; }
                break;

            case 'gallery':
                $imgs = [];
                foreach (($gmb['gmb_photos'] ?? []) as $k => $im) {
                    if ($k < 3 || $k >= 9) continue;   // 0-2 already used above
                    $imgs[] = ['image' => $im['url'],
                               'alt'   => mb_substr($name . ' — photo ' . ($k + 1), 0, 120)];
                }
                if ($imgs) { $p['images'] = $imgs; }
                break;

            case 'stats':
                $items = [];
                if ($yearsIn > 0) {
                    $items[] = ['value' => min(99, $yearsIn), 'suffix' => '+', 'label' => 'Years in business'];
                }
                $rev = (int)($gmb['reviews_total'] ?? 0);
                $rat = (float)($gmb['rating'] ?? 0);
                if ($rev > 0) { $items[] = ['value' => $rev, 'suffix' => '+', 'label' => 'Google reviews']; }
                if ($rat > 0) { $items[] = ['value' => (int)round($rat * 10), 'suffix' => '/10', 'label' => 'Google rating']; }
                if (count($items) >= 2) { $p['items'] = $items; }   // manifest min is 2
                break;

            case 'hours':
                if (!empty($gmb['gmb_hours_lines'])) {
                    $note = implode(' Â· ', array_slice($gmb['gmb_hours_lines'], 0, 2));
                    $p['note'] = mb_substr('From Google: ' . $note . ' …', 0, 160);
                }
                break;

            case 'header':
            case 'footer':
                // His uploaded logo — only where the manifest has a logo prop.
                if ($logoUrl !== '' && array_key_exists('logo', $p)) { $p['logo'] = $logoUrl; }
                break;
        }
        unset($p);
        return $instance;
    };

    /* â”€â”€ 5. PAGES. Recipes with real pages get them; every other business
       still gets a proper four-page site instead of one long scroll. */
    $recipePages = $recipe['pages'] ?? null;
    $pages = [];
    if (is_array($recipePages) && count($recipePages)) {
        foreach ($recipePages as $i => $pd) {
            $pageSections = [];
            foreach ((array)($pd['sections'] ?? []) as $t) {
                if (isset($strip[$t])) continue;         // demo sections never ship
                $i2 = $buildSection($t, (array)($pd['content'] ?? []));
                if ($i2) $pageSections[] = $i2;
            }
            if (!$pageSections) continue;
            $pid     = $pd['id'] ?? ('page-' . ($i + 1));
            $title   = $pd['title'] ?? ucfirst(str_replace('-', ' ', $pid));
            $pages[] = [
                'id'       => $pid,
                'slug'     => $pd['slug'] ?? ($i === 0 ? '/' : '/' . $pid),
                'title'    => $title,
                'seo'      => array_merge(['title' => ($i === 0 ? $name : $name . ' — ' . $title), 'robots' => 'index,follow'], (array)($pd['seo'] ?? [])),
                'sections' => $pageSections,
            ];
        }
    }
    if (!count($pages)) {
        // Synthesised four-pager: Home / About Us / Gallery / Contact.
        $groups = [
            ['id' => 'home',    'slug' => '/',       'title' => 'Home',     'secs' => ['hero', 'stats', 'services', 'cta']],
            ['id' => 'about',   'slug' => 'about',   'title' => 'About Us', 'secs' => ['about']],
            ['id' => 'gallery', 'slug' => 'gallery', 'title' => 'Gallery',  'secs' => ['gallery']],
            ['id' => 'contact', 'slug' => 'contact', 'title' => 'Contact',  'secs' => ['hours', 'contact']],
        ];
        foreach ($groups as $g) {
            if ($g['id'] === 'gallery' && !$hasGalleryImg) continue;   // no real photos, no page
            $secs = [];
            foreach ($g['secs'] as $t) {
                if (!in_array($t, $types, true)) continue;   // respect strip list & availability
                $i3 = $buildSection($t);
                if ($i3) $secs[] = $i3;
            }
            if (!$secs) continue;                            // a page needs body
            $pages[] = [
                'id'       => $g['id'],
                'slug'     => $g['slug'],
                'title'    => $g['title'],
                'seo'      => ['title' => $g['id'] === 'home' ? $name : $name . ' — ' . $g['title'], 'robots' => 'index,follow'],
                'sections' => $secs,
            ];
        }
        if (!count($pages)) {
            $h = $buildSection('hero');
            $pages = [[ 'id' => 'home', 'slug' => '/', 'title' => 'Home',
                        'seo' => ['title' => $name, 'robots' => 'index,follow'],
                        'sections' => $h ? [$h] : [] ]];
        }
    }

    // Header nav from REAL pages — and the header section's own links must
    // point at those pages, because anchors like #services die the moment
    // services moves to its own page.
    $headerNav = array_map(fn($pg) => ['label' => $pg['title'], 'pageId' => $pg['id']], $pages);
    if (count($pages) > 1) {
        foreach ($pages as &$pg) {
            foreach (($pg['sections'] ?? []) as &$sec) {
                if (($sec['type'] ?? '') === 'header') {
                    // $headerNav rows are ['label','pageId'] — there is no 'id'
                    // key. Reading $x['id'] gave null for every row, so EVERY
                    // nav link resolved to '/' and the whole menu pointed home.
                    $sec['props']['links'] = array_map(
                        fn($x) => ['text' => $x['label'],
                                   'href' => $x['pageId'] === 'home' ? '/' : '/' . $x['pageId']],
                        $headerNav
                    );
                }
            }
            unset($sec);
        }
        unset($pg);
    }

    $themeRef     = $recipe['theme'] ?? null;
    $presetName   = is_array($themeRef) ? ($themeRef['preset'] ?? 'default') : (is_string($themeRef) ? $themeRef : 'default');
    $presetTokens = SchemaRegistry::themes()[$presetName]['tokens'] ?? [];

    $theme = array_replace_recursive(
        [
            'mode'      => 'light',
            'color'     => ['primary' => '#2563EB', 'accent' => '#F7941D', 'bg' => '#FFFFFF', 'text' => '#111827'],
            'font'      => ['heading' => 'Poppins', 'body' => 'Poppins'],
            'radius'    => 'md',
            'spacing'   => 'comfortable',
            'container' => 'normal',
        ],
        is_array($presetTokens) ? $presetTokens : []
    );
    $theme['preset'] = $presetName;

    // Business Info — every contact/hours/footer section reads this. His
    // Google listing wins where it answered; the number he typed in chat is
    // the fallback, formatted the way humans read it.
    $digits   = preg_replace('/\D/', '', ($gmb['gmb_phone'] ?? '') ?: $phone);
    $bizPhone = $digits !== '' ? '+91 ' . substr($digits, -10, 5) . ' ' . substr($digits, -5) : null;

    $doc = [
        'schemaVersion' => 1,
        'site'  => array_filter([
            'name'     => $name,
            'industry' => $industry,
            'locale'   => 'en-IN',
        ]),
        'theme' => $theme,
        'nav'   => ['header' => $headerNav],
        'pages' => $pages,
        'business' => array_merge(
            (array)($recipe['business'] ?? []),
            array_filter([
                'name'        => $name,
                'category'    => $gmb['category'] ?? null,
                'description' => $services !== '' ? $services : ($gmb['gmb_description'] ?? null),
                'audience'    => $audience !== '' ? $audience : null,
                'phone'       => $bizPhone,
                'whatsapp'    => $bizPhone,
                'email'       => $email !== '' ? $email : null,
                'address'     => $gmb['address'] ?? null,
                'website'     => !empty($gmb['website']) ? $gmb['website'] : null,
                'mapUrl'      => $gmb['maps_url'] ?? null,
            ])
        ),
    ];

    // The starter doc must itself be valid — catches a broken manifest early.
    $errors = (new SiteValidator())->validate(json_decode(json_encode($doc), true));
    error_log('[SITE-CHAT] doc built. pages=' . count($pages) . ' home_secs=' . count($pages[0]['sections'] ?? []));
    if ($errors) {
        error_log('[SITE-CHAT] VALIDATION FAILED: ' . implode(' | ', $errors));
        sendError('Could not build a valid starter site: ' . implode('; ', array_slice($errors, 0, 5)), 500);
    }

    /* â”€â”€ 5. Create + PUBLISH — live immediately, like any other client. â”€â”€â”€â”€ */
    $site = SiteRepo::create($ownerId, $name, $slug, $industry, json_decode(json_encode($doc), true));
    $full = SiteRepo::findById($site['id']);
    SiteRepo::publish($full, $ownerId, 'Built from WhatsApp chat', 'whatsapp-bot');
    error_log('[SITE-CHAT] PUBLISHED https://' . $slug . '.tapify.co.in');

    sendSuccess('Website built and published', [
        'site_id' => (int)$site['id'],
        'slug'    => $slug,
        'url'     => 'https://' . $slug . '.tapify.co.in',
        'pages'   => count($pages),
    ]);

} catch (Throwable $e) {
    // Throwable, not Exception: a TypeError or ArgumentCountError is an Error,
    // and `catch (Exception)` lets those through as an uncaught fatal — the
    // caller then gets a bare 500 with no JSON body and no clue what broke.
    error_log('[SITE-CHAT] intake failed at ' . $e->getFile() . ':' . $e->getLine()
              . ' — ' . get_class($e) . ': ' . $e->getMessage());
    sendError('Could not build the website right now.', 500);
}

