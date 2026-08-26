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
require_once __DIR__ . '/../../includes/StockImage.php';
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
        // Typical services for the category, so the bot can OFFER a list to
        // accept instead of making every owner type one from scratch.
        //
        // These are a SUGGESTION he confirms or replaces — not a claim about his
        // business. They have to be offered rather than assumed because Places
        // does not expose a listing's own services or products at all (see the
        // note on $services in the POST handler below).
        $sugg = [];
        foreach ((array)(($meta['content']['services']['props']['items'] ?? [])) as $it) {
            $t = trim((string)($it['title'] ?? ''));
            if ($t !== '') $sugg[] = $t;
            if (count($sugg) >= 6) break;
        }
        $out[] = [
            'id'       => (string)$id,
            'label'    => (string)($meta['label'] ?? $id),
            'desc'     => mb_substr((string)($meta['description'] ?? ''), 0, 72),
            'services' => $sugg,
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

/**
 * The Instagram answer, which may be a handle OR one or more post/reel links.
 *
 * A handle alone cannot be turned into a post: Instagram serves no public feed
 * for scraping, and the oEmbed/Graph route needs the owner to authorise our app.
 * So a handle becomes a follow link, and any /p/, /reel/ or /tv/ permalink he
 * pastes becomes a real embed.
 *
 * The old regex here matched `instagram.com/(word)` against a pasted reel URL
 * and produced "https://instagram.com/reel" — a dead link presented as his
 * profile. Permalinks are now matched FIRST, before the handle fallback.
 */
$instagramUrl = null;
$embedUrls    = [];
if ($instaRaw !== '' && $instaRaw !== '-') {
    // 1. Post / reel / IGTV permalinks — these are embeddable as-is.
    if (preg_match_all('~https?://(?:www\.)?instagram\.com/(?:p|reel|reels|tv)/([A-Za-z0-9_-]+)~i',
                       $instaRaw, $mm)) {
        foreach ($mm[1] as $code) {
            $u = 'https://www.instagram.com/p/' . $code . '/';
            if (!in_array($u, $embedUrls, true)) $embedUrls[] = $u;
            if (count($embedUrls) >= 6) break;          // manifest allows 9
        }
    }
    // 2. A profile link or bare handle — the follow link, never an embed.
    if (preg_match('~instagram\.com/(?!p/|reel/|reels/|tv/)([A-Za-z0-9_.]{1,30})~i', $instaRaw, $m)) {
        $instagramUrl = 'https://instagram.com/' . $m[1];
    } elseif ($instaRaw[0] === '@') {
        $handle = ltrim(substr($instaRaw, 1), '@');
        if (preg_match('~^[A-Za-z0-9_.]{1,30}$~', $handle)) {
            $instagramUrl = 'https://instagram.com/' . $handle;
        }
    } elseif (preg_match('~^[A-Za-z0-9_.]{1,30}$~', $instaRaw)) {
        $instagramUrl = 'https://instagram.com/' . $instaRaw;
    }
}

// The three chat answers SHAPE the site, but none of them is worth refusing to
// build over. A missing answer is filled from the Google listing further down
// (category becomes the type, the category name becomes the service) and the
// page copy simply carries less detail. Refusing here used to throw away a
// customer who had already answered five other questions.
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
        // No email on the payload — the bot normally collects it, but a site
        // must not be lost to a missing field. The register step used the same
        // phone, so look the owner up by that before considering it hopeless.
        if ($email === '') {
            $d10 = substr(preg_replace('/\D/', '', $phone), -10);
            if ($d10 !== '') {
                $st = $pdo->prepare(
                    "SELECT id FROM users WHERE REPLACE(REPLACE(phone,' ',''),'+','') LIKE ?
                      ORDER BY id DESC LIMIT 1");
                $st->execute(['%' . $d10]);
                $ownerId = $st->fetchColumn() ?: null;
            }
            if (!$ownerId) sendError('email is required so the website has an owner.', 422);
        }
        $pass = substr(preg_replace('/\D/', '', $phone), -10);
        // A short or missing number gets a generated password rather than a
        // dead end; the owner can reset it, and the bot quotes what it set.
        if (strlen($pass) < 6) $pass = 'tp' . bin2hex(random_bytes(3));

        if (!$ownerId) {
        $st = $pdo->prepare("INSERT INTO users (name, email, password, role, status) VALUES (?, ?, ?, 'user', 1)");
        $st->execute([$name, $email, hashPassword($pass)]);
        $ownerId = (int)$pdo->lastInsertId();

        $st = $pdo->prepare(
            "INSERT INTO subscriptions (user_id, plan_name, vcards_limit, stores_limit, price, subscribed_date, expiry_date, status)
             VALUES (?, 'Free Plan', 5, 1, 0, ?, ?, 'active')"
        );
        $st->execute([$ownerId, date('Y-m-d'), date('Y-m-d', strtotime('+1 year'))]);
        }
    }
    error_log('[SITE-CHAT] owner resolved: ' . var_export($ownerId, true));

    /* â”€â”€ 2. Address: their business name, made unique. â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
    $slug = SiteRepo::normaliseSlug('', $name);
    if ($slug === '') {
        // A name with no Latin characters normalises to nothing. That is a
        // reason to generate an address, not to refuse the customer one.
        $slug = SiteRepo::normaliseSlug('', $type ?: 'business');
        if ($slug === '') $slug = 'site-' . bin2hex(random_bytes(3));
        error_log('[SITE-CHAT] name did not normalise; generated slug ' . $slug);
    }
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
    $hasGalleryImg = $gmb && count($gmb['gmb_photos'] ?? []) >= 2;   // uses every photo now

    // Sections that would render demo PEOPLE / PRODUCTS / REVIEWS we did not
    // earn from his listing are dropped outright; his real Google numbers
    // become a stats strip instead of fake testimonials.
    //
    // Two of them are no longer always demo, so they are kept when — and only
    // when — we hold his own content for them: real Google review text, and a
    // real Instagram post/reel URL he pasted in chat.
    $gmbReviews = array_values(array_filter((array)($gmb['gmb_reviews'] ?? []),
        fn($r) => trim((string)($r['quote'] ?? '')) !== ''));
    $hasReviews = count($gmbReviews) > 0;
    $hasEmbed   = $embedUrls !== [];

    $stripList = ['team','blog','faq','products','appointment','feedback','account','share'];
    if (!$hasReviews) $stripList[] = 'testimonials';
    if (!$hasEmbed)   $stripList[] = 'embed';
    $strip = array_flip($stripList);
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
    // A recipe's own section list rarely mentions testimonials or embed, so
    // without this they would be built and then dropped by the availability
    // guard below — silently, exactly like header and footer were.
    if ($hasReviews && !in_array('testimonials', $types, true)) $types[] = 'testimonials';
    if ($hasEmbed   && !in_array('embed', $types, true))        $types[] = 'embed';
    foreach (['header', 'footer'] as $t) {
        if (!in_array($t, $types, true)) $types[] = $t;
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
    // Passed into the closure explicitly: a closure inherits nothing, and
    // $gcat being missing from this list is exactly how the hero badge broke.
    $pdoRef  = $pdo;
    $typeCtx = $type;

    $buildSection = function (string $t, array $pageSeed = []) use (
        $content, $gmb, $gcat, $name, $services, $audience,
        $photoOf, $story, $yearsIn, $instagramUrl, $logoUrl,
        $gmbReviews, $embedUrls, $pdoRef, $typeCtx
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
                // His typed list REPLACES the demo menu items outright, and ALL
                // of it ships — the old cap of 6 quietly dropped everything he
                // listed after the sixth. The manifest allows 24.
                $items = [];
                foreach (preg_split('/\s*(?:,|and|\/|\||\n)\s*/i', $services) as $s) {
                    $s = trim((string)$s);
                    if ($s === '') continue;
                    $items[] = ['title' => mb_substr($s, 0, 80)];
                    if (count($items) >= 24) break;
                }
                if (!$items && $gcat !== '') {
                    $items[] = ['title' => mb_substr($gcat, 0, 80)];
                }
                /**
                 * Art for each service card, best source first:
                 *   1. HIS OWN Google photo — always preferred, it is really him.
                 *   2. A stock photo OF THE SERVICE HE TYPED, searched live, so
                 *      "keratin treatment" gets a keratin picture rather than a
                 *      generic salon shot. Needs PEXELS_API_KEY.
                 *   3. The recipe's curated image for the closest matching
                 *      service, matched on shared words in the title.
                 *   4. The next unused recipe image, so no card ships blank.
                 *
                 * 3 and 4 are the safety net, not the plan: the live search is
                 * unreviewed and is occasionally wrong, and without an API key it
                 * returns nothing at all. Owners can swap any picture in the app.
                 */
                $recipeItems = array_values(array_filter(
                    (array)($content['services']['props']['items'] ?? []),
                    fn($r) => !empty($r['image'])
                ));
                $usedArt = [];
                foreach ($items as $k => &$it) {
                    // A picture of THIS service, FIRST. The category rides along
                    // as context because short service words are ambiguous on
                    // their own — "Fillings" alone returns cake.
                    //
                    // His Google photos are deliberately NOT used here any more.
                    // They are shop and premises shots, not pictures of "keratin
                    // treatment", and because a listing usually has ~10 of them
                    // they filled every card and the service search never ran at
                    // all. They belong in the gallery, which is where they go.
                    $shot = StockImage::forService($pdoRef, (string)$it['title'], $gcat ?: $typeCtx);
                    if ($shot) { $it['image'] = $shot; continue; }
                    $words = preg_split('/[^\p{L}\p{N}]+/u', mb_strtolower((string)$it['title']),
                                        -1, PREG_SPLIT_NO_EMPTY) ?: [];
                    $best = null; $bestScore = 0;
                    foreach ($recipeItems as $ri => $r) {
                        if (isset($usedArt[$ri])) continue;
                        $rw = preg_split('/[^\p{L}\p{N}]+/u', mb_strtolower((string)($r['title'] ?? '')),
                                         -1, PREG_SPLIT_NO_EMPTY) ?: [];
                        $score = count(array_intersect($words, $rw));
                        if ($score > $bestScore) { $bestScore = $score; $best = $ri; }
                    }
                    if ($best === null) {
                        foreach ($recipeItems as $ri => $r) {
                            if (!isset($usedArt[$ri])) { $best = $ri; break; }
                        }
                    }
                    if ($best !== null) {
                        $it['image'] = $recipeItems[$best]['image'];
                        $usedArt[$best] = true;
                    }
                }
                unset($it);
                if ($items) { $p['items'] = $items; }
                $instance['variant'] = 'marquee';        // auto-scrolling ticker
                break;

            case 'gallery':
                // EVERY remaining photo. Indices 0 and 1 are the hero and about
                // images; from 2 onward they double as service card art, but a
                // photo used on a card is still worth showing full-size here.
                $imgs = [];
                foreach (($gmb['gmb_photos'] ?? []) as $k => $im) {
                    // EVERY Google photo. The hero and About reuse 0 and 1, but
                    // this section exists precisely to show his real premises, so
                    // skipping them left a thin gallery for no benefit.
                    $imgs[] = ['image' => $im['url'],
                               'alt'   => mb_substr($name . ' — photo ' . ($k + 1), 0, 120)];
                    if (count($imgs) >= 60) break;      // manifest ceiling
                }
                if ($imgs) { $p['images'] = $imgs; }
                $instance['variant'] = 'marquee';
                break;

            case 'testimonials':
                // Real Google reviews only — never invented, and only the ones
                // that actually carry words.
                $items = [];
                foreach ($gmbReviews as $r) {
                    $row = ['quote' => $r['quote'], 'name' => $r['name'], 'role' => 'Google review'];
                    if (!empty($r['rating'])) $row['rating'] = max(1, min(5, (int)$r['rating']));
                    if (!empty($r['photo']))  $row['photo']  = $r['photo'];
                    $items[] = $row;
                    if (count($items) >= 30) break;
                }
                if ($items) { $p['items'] = $items; }
                $p['heading'] = 'What our customers say on Google';
                $instance['variant'] = 'marquee';
                break;

            case 'embed':
                // The post/reel links he pasted in chat.
                if ($embedUrls) {
                    $p['embeds']  = array_map(fn($u) => ['url' => $u], $embedUrls);
                    $p['heading'] = 'From our Instagram';
                }
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

            case 'contact':
                // His real Google Maps listing, not a generic address lookup.
                // showMap/mapUrl only exist on the map-bearing variants, so the
                // variant has to be set too or the props are ignored.
                if (!empty($gmb['maps_url'])) {
                    $instance['variant'] = 'form-map';
                    $p['showMap'] = true;
                    $p['mapUrl']  = $gmb['maps_url'];
                }
                $p['showPhone'] = true;
                $p['showWhatsapp'] = true;
                if (!empty($gmb['address'])) $p['showAddress'] = true;
                break;

            case 'hours':
                if (!empty($gmb['gmb_hours_lines'])) {
                    $note = implode(' Â· ', array_slice($gmb['gmb_hours_lines'], 0, 2));
                    $p['note'] = mb_substr('From Google: ' . $note . ' …', 0, 160);
                }
                break;

            case 'footer':
                // Pexels' API guidelines require a visible credit when their
                // photos are shown. Only added when one was actually used, so a
                // site built entirely from his own photos does not carry it.
                if (StockImage::used()) {
                    $p['copyright'] = trim((string)($p['copyright'] ?? ''))
                        . (empty($p['copyright']) ? '' : '  ·  ') . 'Some photos via Pexels';
                }
                // fall through — the logo applies to header and footer alike
            case 'header':
                /**
                 * His uploaded logo — only where the manifest declares a logo prop.
                 *
                 * The guard used to be array_key_exists('logo', $p), which asked
                 * the wrong object. $p starts as the manifest's `defaults.props`
                 * — {sticky} for header, {showBranding,showContact,showSocial}
                 * for footer — and NEITHER lists a logo, because a logo has no
                 * sensible default. So the condition was false every single time
                 * and the uploaded logo was silently dropped.
                 *
                 * Ask the manifest's declared props, which is what was meant.
                 */
                if ($logoUrl !== '') {
                    foreach ((array)(SchemaRegistry::section($t)['props'] ?? []) as $pr) {
                        if (is_array($pr) && ($pr['key'] ?? '') === 'logo') {
                            $p['logo'] = $logoUrl;
                            break;
                        }
                    }
                }
                break;
        }
        unset($p);
        return $instance;
    };

    /* â”€â”€ 5. PAGES. Recipes with real pages get them; every other business
       still gets a proper four-page site instead of one long scroll. */
    /**
     * Force a page slug into the shape SiteValidator accepts (^/[a-z0-9/-]*$).
     * The literals above are only half the problem — a recipe is free to define
     * 'slug' => 'services' and would fail the document exactly the same way,
     * with an error naming the page index rather than the recipe. Normalising
     * here means neither source can produce an invalid slug again.
     */
    $pageSlug = function ($raw, string $fallbackId, int $i): string {
        $s = strtolower(trim((string)$raw));
        if ($s === '') $s = ($i === 0 ? '/' : $fallbackId);
        $s = '/' . ltrim($s, '/');
        $s = preg_replace('~[^a-z0-9/-]+~', '-', $s);
        $s = preg_replace('~-{2,}~', '-', $s);
        if ($s !== '/') $s = rtrim($s, '/-');
        return $s === '' ? '/' : $s;
    };

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
                'slug'     => $pageSlug($pd['slug'] ?? null, $pid, $i),
                'title'    => $title,
                'seo'      => array_merge(['title' => ($i === 0 ? $name : $name . ' — ' . $title), 'robots' => 'index,follow'], (array)($pd['seo'] ?? [])),
                'sections' => $pageSections,
            ];
        }
    }
    if (!count($pages)) {
        // Synthesised four-pager: Home / About Us / Gallery / Contact.
        // Every slug MUST start with "/" — SiteValidator enforces
        // ^/[a-z0-9/-]*$ and rejected the whole document over the three that
        // were written bare ("about", "gallery", "contact"). Home passed only
        // because it happened to be "/", which is why the failure looked like
        // three separate errors rather than one mistake.
        $groups = [
            ['id' => 'home',    'slug' => '/',        'title' => 'Home',     'secs' => ['hero', 'stats', 'services', 'gallery', 'testimonials', 'embed', 'cta', 'contact']],
            ['id' => 'about',   'slug' => '/about',   'title' => 'About Us', 'secs' => ['about']],
            ['id' => 'gallery', 'slug' => '/gallery', 'title' => 'Gallery',  'secs' => ['gallery']],
            ['id' => 'contact', 'slug' => '/contact', 'title' => 'Contact',  'secs' => ['hours', 'contact']],
        ];
        foreach ($groups as $g) {
            if ($g['id'] === 'gallery' && !$hasGalleryImg) continue;   // no real photos, no page
            $secs = [];
            foreach ($g['secs'] as $t) {
                if (!in_array($t, $types, true)) continue;   // respect strip list & availability
                if ($t === 'gallery' && !$hasGalleryImg) continue;   // never an empty gallery
                $i3 = $buildSection($t);
                if ($i3) $secs[] = $i3;
            }
            if (!$secs) continue;                            // a page needs body
            $pages[] = [
                'id'       => $g['id'],
                'slug'     => $pageSlug($g['slug'], $g['id'], $g['id'] === 'home' ? 0 : 1),
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

    /**
     * Every page gets a header and a footer.
     *
     * THIS IS WHY THEY WERE MISSING: the synthesised page groups list only body
     * sections ('hero','stats','services','cta' …) and the availability guard
     * `in_array($t, $types)` would have dropped 'footer' anyway, since the
     * fallback $types has no footer in it. Wrapping here fixes the recipe path
     * too, and is idempotent — a recipe that already lists them is left alone.
     */
    foreach ($pages as &$pgW) {
        $have = array_column($pgW['sections'] ?? [], 'type');
        if (!in_array('header', $have, true)) {
            $h = $buildSection('header');
            if ($h) array_unshift($pgW['sections'], $h);
        }
        if (!in_array('footer', $have, true)) {
            $f = $buildSection('footer');
            if ($f) $pgW['sections'][] = $f;
        }
    }
    unset($pgW);

    /**
     * Give every page a real body, and put his photos on the home page.
     *
     * Pages arrive from one of two paths — the recipe's own pages[], or the
     * synthesised four-pager — and they disagree about what a page contains. A
     * recipe page can be a single `about` section, which renders as a hero and
     * two lines of text. So composition is settled HERE, once, for both paths.
     *
     * Only sections we can fill with HIS data are used as filler: his Google
     * photos, his Google numbers, his reviews, his Instagram. Nothing is added
     * that would render a demo.
     */
    $already = function (array $pg, string $t): bool {
        foreach (($pg['sections'] ?? []) as $sc) {
            if (($sc['type'] ?? '') === $t) return true;
        }
        return false;
    };
    // Ordered by how much they earn their place on a small business page.
    $filler = [];
    if ($hasGalleryImg)              $filler[] = 'gallery';
    if ($hasReviews)                 $filler[] = 'testimonials';
    if ($hasStatsData)               $filler[] = 'stats';
    if ($hasEmbed)                   $filler[] = 'embed';
    $filler[] = 'cta';               // always available, needs no data
    $filler[] = 'contact';           // ditto — business info is already known

    foreach ($pages as $pi => $pg) {
        $isHome = ($pg['id'] ?? '') === 'home';

        // His premises belong on the page most people will ever see.
        if ($isHome && $hasGalleryImg && !$already($pg, 'gallery')) {
            $g = $buildSection('gallery');
            if ($g) {
                $at = count($pg['sections']);
                foreach ($pg['sections'] as $k => $sc) {
                    if (in_array($sc['type'] ?? '', ['cta', 'contact', 'footer'], true)) {
                        $at = $k;
                        break;
                    }
                }
                array_splice($pages[$pi]['sections'], $at, 0, [$g]);
                $pg = $pages[$pi];
            }
        }

        // A page with one section is a page nobody scrolls. Top up to three.
        $body = 0;
        foreach (($pg['sections'] ?? []) as $sc) {
            if (!in_array($sc['type'] ?? '', ['header', 'footer'], true)) $body++;
        }
        // Rotate the order per page. Without this every page takes the first
        // filler and the whole site reads as the same page four times — a
        // gallery under Contact, a gallery under Products, and so on.
        $rot = $filler;
        if ($rot && $pi > 0) {
            $shift = $pi % count($rot);
            $rot = array_merge(array_slice($rot, $shift), array_slice($rot, 0, $shift));
        }
        foreach ($rot as $t) {
            if ($body >= 3) break;
            if ($already($pages[$pi], $t)) continue;
            $sec = $buildSection($t);
            if (!$sec) continue;
            // before the footer, so the footer stays last
            $at = count($pages[$pi]['sections']);
            foreach ($pages[$pi]['sections'] as $k => $sc) {
                if (($sc['type'] ?? '') === 'footer') { $at = $k; break; }
            }
            array_splice($pages[$pi]['sections'], $at, 0, [$sec]);
            $body++;
        }
    }

    /**
     * Kill "#anchor" hrefs — the reason Portfolio and Contact did not open.
     *
     * Recipe copy is written for a ONE-PAGE layout, so its buttons point at
     * "#contact", "#portfolio" and friends. Once those sections live on separate
     * pages the anchor matches no element on the current page, so the browser
     * stays put and the link looks broken. Section ids are generated
     * ("sec-a1b2c3"), never "contact", so these could never have resolved.
     *
     * Rewrite each one to the real page when a page of that name exists, and
     * send the rest home rather than leaving a link that visibly does nothing.
     */
    $byName = [];
    foreach ($pages as $pg) {
        $byName[strtolower((string)$pg['id'])] = $pg['slug'];
        $byName[strtolower(ltrim((string)$pg['slug'], '/'))] = $pg['slug'];
        // ALSO map by the SECTION TYPES the page carries. "#services" and
        // "#packages" name a section, not a page, so matching on page id alone
        // sent every one of them to '/' — which is what "clicking Packages goes
        // to the home page" was. Now the link lands on whichever page actually
        // holds that section. First page wins, so home keeps precedence.
        foreach (($pg['sections'] ?? []) as $sc) {
            $t = strtolower((string)($sc['type'] ?? ''));
            if ($t !== '' && !isset($byName[$t])) $byName[$t] = $pg['slug'];
        }
    }
    // Words a recipe uses for a section that is not its type name.
    foreach (['packages' => 'services', 'menu' => 'services', 'pricing' => 'services',
              'work' => 'gallery', 'portfolio' => 'gallery', 'photos' => 'gallery',
              'reviews' => 'testimonials', 'enquiry' => 'contact', 'book' => 'contact',
              'story' => 'about'] as $alias => $type) {
        if (!isset($byName[$alias]) && isset($byName[$type])) $byName[$alias] = $byName[$type];
    }
    $fixAnchors = function (&$node) use (&$fixAnchors, $byName) {
        if (is_array($node)) {
            foreach ($node as $k => &$v) {
                if ($k === 'href' && is_string($v) && $v !== '' && $v[0] === '#') {
                    $key = strtolower(trim(substr($v, 1)));
                    $v = $byName[$key] ?? '/';
                } else {
                    $fixAnchors($v);
                }
            }
            unset($v);
        }
    };
    $fixAnchors($pages);

    // Header nav from REAL pages — and the header section's own links must
    // point at those pages, because anchors like #services die the moment
    // services moves to its own page.
    $headerNav = array_map(fn($pg) => ['label' => $pg['title'], 'pageId' => $pg['id']], $pages);

    /**
     * Replace the recipe's menu with the pages that actually exist.
     *
     * WHY THIS USED TO DO NOTHING: the inner loop was
     *     foreach (($pg['sections'] ?? []) as &$sec)
     * and `$pg['sections'] ?? []` is an EXPRESSION producing a temporary. PHP
     * cannot bind a reference into a temporary, so every write to $sec landed
     * in a throwaway copy and was discarded — leaving the recipe's own
     * "Services / Packages / Journal" anchors in the header, and no About Us.
     * Index assignment writes to the real array.
     *
     * The href is each page's REAL slug, not '/' . id — a recipe is free to give
     * a page an id and a slug that differ, and guessing produced a dead link.
     */
    $navLinks = [];
    foreach ($pages as $pg) {
        $navLinks[] = ['text' => (string)$pg['title'], 'href' => (string)$pg['slug']];
    }
    // UNCONDITIONAL. This used to be skipped when the site had only one page,
    // which left the recipe's own invented menu — "Services", "Packages",
    // "Journal" — pointing at pages that do not exist. The header must never
    // advertise anything the site does not actually have.
    foreach ($pages as $pi => $pg) {
        foreach (($pg['sections'] ?? []) as $si => $sec) {
            if (($sec['type'] ?? '') === 'header') {
                $pages[$pi]['sections'][$si]['props']['links'] = $navLinks;
            }
        }
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
        // Sticky WhatsApp / call / directions bar on phones. Set explicitly
        // rather than relying on the renderer default, so the value is visible
        // in the document and in the editor's toggle.
        'settings' => ['showMobileActionBar' => true],
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

    /**
     * Validate — and if it fails, REPAIR rather than refuse.
     *
     * A validation error here used to end the conversation with "could not
     * build a valid starter site", after the customer had answered six
     * questions. Almost every such error is one bad section in one page, so:
     *
     *   1. drop the exact sections the validator named, and try again
     *   2. failing that, ship a minimal site built only from business info,
     *      whose shape is fixed and therefore cannot be invalid
     *
     * The full error is always logged, because degrading quietly forever would
     * hide a real manifest bug.
     */
    $flat  = fn($d) => json_decode(json_encode($d), true);
    $errors = (new SiteValidator())->validate($flat($doc));
    error_log('[SITE-CHAT] doc built. pages=' . count($pages) . ' home_secs=' . count($pages[0]['sections'] ?? []));

    if ($errors) {
        error_log('[SITE-CHAT] VALIDATION FAILED: ' . implode(' | ', $errors));

        // (1) Errors name their path: pages[2].sections[4](gallery).props...
        $dropped = [];
        foreach ($errors as $e) {
            if (preg_match('~pages\[(\d+)\]\.sections\[(\d+)\]~', $e, $m)) {
                $dropped[(int)$m[1]][(int)$m[2]] = true;
            }
        }
        foreach ($dropped as $pi => $sis) {
            krsort($sis);                       // remove from the end, keep indexes valid
            foreach (array_keys($sis) as $si) {
                if (isset($doc['pages'][$pi]['sections'][$si])) {
                    array_splice($doc['pages'][$pi]['sections'], $si, 1);
                }
            }
        }
        // a page emptied by that is no longer a page
        $doc['pages'] = array_values(array_filter($doc['pages'], function ($pg) {
            foreach (($pg['sections'] ?? []) as $sc) {
                if (!in_array($sc['type'] ?? '', ['header', 'footer'], true)) return true;
            }
            return false;
        }));

        $errors = $doc['pages'] ? (new SiteValidator())->validate($flat($doc)) : ['no pages left'];
        if (!$errors) {
            error_log('[SITE-CHAT] recovered by dropping ' . count($dropped, COUNT_RECURSIVE) . ' section(s)');
        } else {
            // (2) Last resort. Fixed shape, business info only, no repeaters,
            //     no media, nothing a manifest change can invalidate.
            error_log('[SITE-CHAT] STILL INVALID, falling back to a minimal site: '
                      . implode(' | ', $errors));
            $mk = function (string $t) {
                $i = SchemaRegistry::newSectionInstance($t);
                if ($i && !isset($i['props'])) $i['props'] = new stdClass();
                return $i;
            };
            $minSecs = [];
            foreach (['header', 'hero', 'contact', 'footer'] as $t) {
                $i = $mk($t);
                if (!$i) continue;
                if ($t === 'hero') {
                    $i['props'] = ['heading' => mb_substr($name, 0, 120),
                                   'sub'     => mb_substr($services ?: ($gcat ?: ''), 0, 300),
                                   'showCall' => true];
                }
                $minSecs[] = $i;
            }
            $doc['pages'] = [[
                'id' => 'home', 'slug' => '/', 'title' => 'Home',
                'seo' => ['title' => $name, 'robots' => 'index,follow'],
                'sections' => $minSecs,
            ]];
            $doc['nav'] = ['header' => [['label' => 'Home', 'pageId' => 'home']]];
            $errors = (new SiteValidator())->validate($flat($doc));
            if ($errors) {
                // Nothing left to try; this is a broken deployment, not bad input.
                error_log('[SITE-CHAT] MINIMAL DOC ALSO INVALID: ' . implode(' | ', $errors));
                sendError('Could not build the website right now.', 500);
            }
            error_log('[SITE-CHAT] shipped the minimal fallback');
        }
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

