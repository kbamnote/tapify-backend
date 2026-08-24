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

try {
    $pdo = getDB();

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

    /* ── 4. Starter document — same assembly as api/sites/create.php. ────── */
    $recipe  = SchemaRegistry::industries()[$industry] ?? null;
    $types   = $recipe['sections'] ?? ['hero', 'about', 'services', 'gallery', 'contact'];
    $content = $recipe['content'] ?? [];

    $buildSection = function (string $t) use ($content) {
        $instance = SchemaRegistry::newSectionInstance($t);
        if (!$instance) return null;              // silently skip unbuilt types
        $seed = (array)($content[$t] ?? null);
        if ($seed) {
            if (!empty($seed['variant'])) $instance['variant'] = $seed['variant'];
            if (isset($seed['props']))  $instance['props'] = array_merge((array)($instance['props'] ?? []), (array)$seed['props']);
            if (isset($seed['style']))  $instance['style'] = array_merge((array)($instance['style'] ?? []), (array)$seed['style']);
        }
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

    // The chat answers become real copy: what they do and who they serve go
    // into the business block that contact/hours sections read.
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
                'description' => $services,
                'audience'    => $audience,
                'phone'       => preg_replace('/\D/', '', $phone) ?: null,
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
