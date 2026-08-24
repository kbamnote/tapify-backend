<?php
/**
 * TAPIFY — website from the WhatsApp chat, built AND published.
 *
 *   POST /api/public/site-from-chat.php
 *   Header: X-Tapify-Bot-Key: <VISIBILITY_BOT_KEY>
 *
 *   { phone, email?, business?, type?, services, audience }
 *      → { site_id, slug, url }     // url = https://<slug>.tapify.co.in
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
if ($_SERVER['REQUEST_METHOD'] !== 'POST')    { sendError('POST only', 405); }

$expected = getenv('VISIBILITY_BOT_KEY') ?: '';
$given    = $_SERVER['HTTP_X_TAPIFY_BOT_KEY'] ?? '';
if ($expected === '') {
    sendError('Site intake is not configured on this server.', 503);
}
if (!hash_equals($expected, (string)$given)) {
    sendError('Not authorised.', 401);
}

$input    = getInput();
$email    = strtolower(trim((string)($input['email'] ?? '')));
$phone    = trim((string)($input['phone'] ?? ''));
$business = trim((string)($input['business'] ?? ''));
$type     = trim((string)($input['type'] ?? ''));
$services = trim((string)($input['services'] ?? ''));
$audience = trim((string)($input['audience'] ?? ''));

// The three chat answers are the payload; without them there is nothing to build.
if ($type === '' || $services === '' || $audience === '') {
    sendError('type, services and audience are required.', 422);
}
$name = $business !== '' ? $business : ucfirst($type);
$placeId = trim((string)($input['place_id'] ?? ''));

try {
    $pdo = getDB();

    /* ── 0. His real Google listing — the source of truth for content. ──── */
    // One paid Details call per WEBSITE BUILD (never per message; score
    // checks stay cheap behind place_score_cache). A website is built once,
    // and building it out of placeholder copy would defeat the point.
    $places = new PlacesClient();
    $gmb = null;
    if ($placeId !== '' && $places->isConfigured() && PlacesClient::spendAllowed($pdo)) {
        PlacesClient::countCall($pdo);
        $gmb = $places->detailsFull($placeId);
    }
    if ($gmb) {
        if (!empty($gmb['name']))    $name = $gmb['name'];
        if (!empty($gmb['category']) && $type === '') $type = $gmb['category'];
    }

    /* ── 1. Owner: the account the bot already created and quoted back. ──── */
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

    /* ── 2. Address: their business name, made unique. ───────────────────── */
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

    /* ── 3. Industry: match their words to a recipe when one fits. ───────── */
    $industry = null;
    foreach (SchemaRegistry::industries() as $id => $meta) {
        $hay = strtolower($id . ' ' . (is_array($meta) ? ($meta['label'] ?? '') : $meta));
        if ($type !== '' && (stripos($hay, $type) !== false || stripos($type, $id) !== false)) {
            $industry = $id;
            break;
        }
    }

    /* ── 4. Document — generated from HIS Google listing, not a demo. ────── */
    $recipe  = SchemaRegistry::industries()[$industry] ?? null;
    $content = $recipe['content'] ?? [];

    // Sections that would render demo PEOPLE / PRODUCTS / REVIEWS we did not
    // earn from his listing are dropped outright. Structural and contact
    // sections stay; real Google numbers replace testimonials via stats.
    $strip = array_flip(['testimonials','team','blog','faq','products',
                         'appointment','embed','feedback','account','share']);
    $typesIn = $recipe['sections'] ?? ['header','hero','about','services','gallery','contact'];
    $types = [];
    foreach ($typesIn as $t) { if (!isset($strip[$t])) $types[] = $t; }
    if ($gmb && ($gmb['reviews_total'] ?? 0) > 0 && !in_array('stats', $types, true)) {
        $at = array_search('services', $types, true);
        array_splice($types, $at === false ? max(0, count($types) - 2) : $at + 1, 0, ['stats']);
    }

    $photoOf = fn(int $i) => $gmb['gmb_photos'][$i] ?? null;

    /**
     * Three content layers, lowest priority first:
     *   manifest defaults -> industry recipe copy -> HIS Google/chat data.
     * Whatever Google answered wins; recipe copy only fills what Google left
     * blank, so the page never shows another business's story.
     */
    $buildSection = function (string $t) use ($content, $gmb, $name, $services, $audience, $photoOf) {
        $instance = SchemaRegistry::newSectionInstance($t);
        if (!$instance) return null;

        $seed = (array)($content[$t] ?? null);
        if ($seed) {
            if (!empty($seed['variant'])) $instance['variant'] = $seed['variant'];
            if (isset($seed['props']))  $instance['props'] = array_merge((array)($instance['props'] ?? []), (array)$seed['props']);
            if (isset($seed['style']))  $instance['style'] = array_merge((array)($instance['style'] ?? []), (array)$seed['style']);
        }

        $p =& $instance['props'];
        switch ($t) {
            case 'hero':
                $p['heading'] = mb_substr($name, 0, 120);
                $sub = $gmb ? ($gmb['gmb_description'] ?: $services) : $services;
                if ($sub !== '') { $p['sub'] = mb_substr($sub, 0, 400); }
                if ($gmb && $gmb['category'] !== '') { $p['badge'] = mb_substr($gmb['category'], 0, 60); }
                if ($img = $photoOf(0)) { $p['image'] = $img['url']; $p['fullHeight'] = true; }
                $p['showCall'] = true;
                break;

            case 'about':
                $body = $gmb ? $gmb['gmb_description'] : '';
                if ($body === '') {
                    $line = $name;
                    if ($gmb && $gmb['category'] !== '') { $line .= ' is a ' . strtolower($gmb['category']); }
                    if ($audience !== '')                { $line .= " serving {$audience}"; }
                    $body = $line . '. ' . ($services !== '' ? $services : '');
                }
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
                if (!$items && $gmb && $gmb['category'] !== '') {
                    $items[] = ['title' => mb_substr($gmb['category'], 0, 80)];
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
                    if ($k < 3 || $k >= 8) continue;   // 0-2 already used above
                    $imgs[] = ['image' => $im['url'],
                               'alt'   => mb_substr($name . ' — photo ' . ($k + 1), 0, 120)];
                }
                if ($imgs) { $p['images'] = $imgs; }
                break;

            case 'stats':
                $rev = (int)($gmb['reviews_total'] ?? 0);
                $rat = (float)($gmb['rating'] ?? 0);
                if ($rev > 0) {
                    $items = [['value' => $rev, 'suffix' => '+', 'label' => 'Google reviews']];
                    if ($rat > 0) {
                        $items[] = ['value' => (int)round($rat * 10), 'suffix' => '/10', 'label' => 'Google rating'];
                    }
                    $p['items'] = $items;
                }
                break;

            case 'hours':
                if (!empty($gmb['gmb_hours_lines'])) {
                    $note = implode(' · ', array_slice($gmb['gmb_hours_lines'], 0, 2));
                    $p['note'] = mb_substr('From Google: ' . $note . ' …', 0, 160);
                }
                break;
        }
        unset($p);
        return $instance;
    };

    $sections = [];
    foreach ($types as $t) {
        $i = $buildSection($t);
        if ($i) $sections[] = $i;
    }
    if (!$sections) {
        $hero = SchemaRegistry::newSectionInstance('hero');
        if ($hero) $sections[] = $hero;
    }
    $pages = [[
        'id'    => 'home',
        'slug'  => '/',
        'title' => 'Home',
        'seo'   => ['title' => $name, 'robots' => 'index,follow'],
        'sections' => $sections,
    ]];
    $headerNav = [['label' => 'Home', 'pageId' => 'home']];

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
    if ($errors) {
        sendError('Could not build a valid starter site: ' . implode('; ', array_slice($errors, 0, 5)), 500);
    }

    /* ── 5. Create + PUBLISH — live immediately, like any other client. ──── */
    $site = SiteRepo::create($ownerId, $name, $slug, $industry, json_decode(json_encode($doc), true));
    $full = SiteRepo::findById($site['id']);
    SiteRepo::publish($full, $ownerId, 'Built from WhatsApp chat', 'whatsapp-bot');

    sendSuccess('Website built and published', [
        'site_id' => (int)$site['id'],
        'slug'    => $slug,
        'url'     => 'https://' . $slug . '.tapify.co.in',
    ]);

} catch (Exception $e) {
    error_log('[SITE-CHAT] intake failed: ' . $e->getMessage());
    sendError('Could not build the website right now.', 500);
}
