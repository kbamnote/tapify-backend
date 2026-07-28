<?php
/**
 * POST /api/sites/create.php
 * Body: { "name": "Impulsse", "slug": "impulsse", "industry": "coaching" }
 *
 * Creates a site + its first draft. The starter document is assembled from the
 * industry recipe (which sections that industry usually needs) and each
 * section's manifest defaults — so the customer lands on a real, editable page
 * instead of a blank screen.
 *
 * This is also exactly the seam the AI generator will use later: it produces the
 * same document shape and hands it to the same validator.
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../builder/lib/SiteRepo.php';
require_once __DIR__ . '/../../builder/lib/SiteValidator.php';
require_once __DIR__ . '/../../builder/lib/SchemaRegistry.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') sendError('Only POST allowed', 405);

requireAuth();

// Only admins and staff may create websites. Clients can edit/view the site
// that's been assigned to them, but never create new ones.
if (!isStaffOrAdmin()) {
    sendError('Only admins and staff can create websites.', 403);
}

$input    = getInput();
$name     = sanitize($input['name'] ?? '');
$industry = isset($input['industry']) ? sanitize($input['industry']) : null;
$slug     = strtolower(trim($input['slug'] ?? ''));
$assignTo = (int)($input['user_id'] ?? 0);   // the client this site is created for

if ($name === '') sendError('name is required');

// Slug must be a valid DNS label (so <slug>.tapify.co.in stays possible) and
// must not collide with another site. Checked BEFORE any account is created —
// otherwise a rejected slug would leave an orphan customer login behind.
if ($slug === '') {
    $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $name));
}
$slug = trim(preg_replace('/-+/', '-', $slug), '-');
if (!preg_match('/^[a-z0-9](?:[a-z0-9-]{1,61}[a-z0-9])?$/', $slug)) {
    sendError('slug must be 3-63 chars, a-z 0-9 and hyphens, not starting/ending with a hyphen');
}

// Everything below touches the database, so it all sits inside one try — a
// connection failure must still answer JSON, not a bare HTML 500.
try {

if (!SiteRepo::slugAvailable($slug)) {
    sendError('That address is already taken. Try another.', 409);
}

// Resolve the owner: an admin/staff creates a site FOR a client (user_id). If no
// client is given, the site is owned by the creator (e.g. a template/demo).
//
// One connection for the whole request: getDB() dials a NEW PDO every call, so an
// INSERT and its lastInsertId() must share one handle or the id comes back 0.
$pdo = getDB();
$ownerId = getCurrentUserId();

// Inline "create a new client" — same fields and rules as the vCard flow
// (api/vcards/create.php), so an admin can make the login and the website in one
// step instead of creating the user separately first.
$customerEmail    = trim((string)($input['customer_email'] ?? ''));
$customerPassword = (string)($input['customer_password'] ?? '');
$customerName     = sanitize($input['customer_name'] ?? '');

if ($customerEmail !== '') {
    if (!filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) {
        sendError('Please enter a valid customer login email.');
    }

    $st = $pdo->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
    $st->execute([$customerEmail]);
    $existing = $st->fetchColumn();

    if ($existing) {
        // Already a client — hand the site to them. The typed password is NOT
        // applied: an admin creating a website must not be able to silently
        // reset an existing account's login.
        $ownerId = (int)$existing;
    } else {
        if (strlen($customerPassword) < 6) {
            sendError('Customer password must be at least 6 characters.');
        }
        $st = $pdo->prepare("INSERT INTO users (name, email, password, role, status) VALUES (?, ?, ?, 'user', 1)");
        $st->execute([$customerName !== '' ? $customerName : $name, $customerEmail, hashPassword($customerPassword)]);
        $ownerId = (int)$pdo->lastInsertId();

        // Give them the same starter subscription a vCard-created client gets,
        // so limit checks elsewhere find a row instead of nothing.
        $st = $pdo->prepare(
            "INSERT INTO subscriptions (user_id, plan_name, vcards_limit, stores_limit, price, subscribed_date, expiry_date, status)
             VALUES (?, 'Free Plan', 5, 1, 0, ?, ?, 'active')"
        );
        $st->execute([$ownerId, date('Y-m-d'), date('Y-m-d', strtotime('+1 year'))]);
    }
} elseif ($assignTo > 0) {
    $u = $pdo->prepare("SELECT id FROM users WHERE id = ? LIMIT 1");
    $u->execute([$assignTo]);
    if (!$u->fetchColumn()) sendError('The selected client account was not found.', 404);
    $ownerId = $assignTo;
}

    // ---- assemble the starter document ----
    $recipe = SchemaRegistry::industries()[$industry] ?? null;
    $types  = $recipe['sections'] ?? ['hero', 'about', 'services', 'gallery', 'contact'];

    // A recipe may carry ready-made copy and photos per section type. Layering it
    // over the manifest defaults is what turns a starter site into something
    // presentable instead of "Your headline goes here / Service one".
    $content = $recipe['content'] ?? [];

    $sections = [];
    foreach ($types as $type) {
        $instance = SchemaRegistry::newSectionInstance($type);
        if (!$instance) continue;                 // silently skip types not built yet

        $seed = $content[$type] ?? null;
        if (is_array($seed)) {
            if (!empty($seed['variant'])) $instance['variant'] = $seed['variant'];
            if (isset($seed['props'])) {
                // Shallow merge: a seeded key replaces the default outright (a
                // deep merge would splice seeded list items into default ones).
                $instance['props'] = array_merge((array)($instance['props'] ?? []), (array)$seed['props']);
            }
            if (isset($seed['style'])) {
                $instance['style'] = array_merge((array)($instance['style'] ?? []), (array)$seed['style']);
            }
        }
        $sections[] = $instance;
    }
    if (!$sections) {
        $hero = SchemaRegistry::newSectionInstance('hero');
        if ($hero) $sections[] = $hero;
    }

    // ---- theme ----
    // "theme" is either a preset name ("navy-gold") or an object that may name a
    // preset and override tokens. The preset's OWN tokens are applied here —
    // previously only its name was stored, so every site rendered with the
    // generic blue palette no matter which preset the recipe asked for.
    $themeRef     = $recipe['theme'] ?? null;
    $presetName   = is_array($themeRef) ? ($themeRef['preset'] ?? 'default') : (is_string($themeRef) ? $themeRef : 'default');
    $presetTokens = SchemaRegistry::themes()[$presetName]['tokens'] ?? [];
    $themeOverride = is_array($themeRef) ? array_diff_key($themeRef, ['preset' => 1]) : [];

    $theme = array_replace_recursive(
        [
            'mode'      => 'light',
            'color'     => ['primary' => '#2563EB', 'accent' => '#F7941D', 'bg' => '#FFFFFF', 'text' => '#111827'],
            'font'      => ['heading' => 'Poppins', 'body' => 'Poppins'],
            'radius'    => 'md',
            'spacing'   => 'comfortable',
            'container' => 'normal',
        ],
        is_array($presetTokens) ? $presetTokens : [],
        $themeOverride
    );
    $theme['preset'] = $presetName;

    $doc = [
        'schemaVersion' => 1,
        'site'  => array_filter([
            'name'     => $name,
            'industry' => $industry,
            'locale'   => 'en-IN',
        ]),
        'theme' => $theme,
        'nav' => [
            'header' => [['label' => 'Home', 'pageId' => 'home']],
        ],
        'pages' => [[
            'id'    => 'home',
            'slug'  => '/',
            'title' => 'Home',
            'seo'   => ['title' => $name, 'robots' => 'index,follow'],
            'sections' => $sections,
        ]],
        // Contact/hours sections read this, so a recipe seeds placeholder details
        // rather than rendering an empty "Get in touch" block on a demo site.
        'business' => !empty($recipe['business']) ? $recipe['business'] : new stdClass(),
    ];

    // The starter doc must itself be valid — catches a broken manifest early.
    $errors = (new SiteValidator())->validate(json_decode(json_encode($doc), true));
    if ($errors) {
        sendError('Could not build a valid starter site: ' . implode('; ', array_slice($errors, 0, 5)), 500);
    }

    $site = SiteRepo::create($ownerId, $name, $slug, $industry, json_decode(json_encode($doc), true));
    $draft = SiteRepo::getDraft($site);

    sendSuccess('Site created', [
        'site' => [
            'id'       => (int)$site['id'],
            'slug'     => $site['slug'],
            'name'     => $site['name'],
            'industry' => $site['industry'],
            'status'   => $site['status'],
        ],
        'rev' => $draft['rev'] ?? 1,
        'doc' => $draft['doc'] ?? $doc,
    ]);

} catch (Exception $e) {
    sendError('Failed to create site: ' . $e->getMessage(), 500);
}
