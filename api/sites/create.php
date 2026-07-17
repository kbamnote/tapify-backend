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

$input    = getInput();
$name     = sanitize($input['name'] ?? '');
$industry = isset($input['industry']) ? sanitize($input['industry']) : null;
$slug     = strtolower(trim($input['slug'] ?? ''));

if ($name === '') sendError('name is required');

// Slug must be a valid DNS label (so <slug>.tapify.co.in stays possible) and
// must not collide with another site.
if ($slug === '') {
    $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $name));
}
$slug = trim(preg_replace('/-+/', '-', $slug), '-');
if (!preg_match('/^[a-z0-9](?:[a-z0-9-]{1,61}[a-z0-9])?$/', $slug)) {
    sendError('slug must be 3-63 chars, a-z 0-9 and hyphens, not starting/ending with a hyphen');
}

try {
    if (!SiteRepo::slugAvailable($slug)) {
        sendError('That address is already taken. Try another.', 409);
    }

    // ---- assemble the starter document ----
    $recipe = SchemaRegistry::industries()[$industry] ?? null;
    $types  = $recipe['sections'] ?? ['hero', 'about', 'services', 'gallery', 'contact'];

    $sections = [];
    foreach ($types as $type) {
        $instance = SchemaRegistry::newSectionInstance($type);
        if ($instance) $sections[] = $instance;   // silently skip types not built yet
    }
    if (!$sections) {
        $hero = SchemaRegistry::newSectionInstance('hero');
        if ($hero) $sections[] = $hero;
    }

    $doc = [
        'schemaVersion' => 1,
        'site'  => array_filter([
            'name'     => $name,
            'industry' => $industry,
            'locale'   => 'en-IN',
        ]),
        'theme' => [
            'preset'  => $recipe['theme'] ?? 'default',
            'mode'    => 'light',
            'color'   => [
                'primary' => '#2563EB',
                'accent'  => '#F7941D',
                'bg'      => '#FFFFFF',
                'text'    => '#111827',
            ],
            'font'    => ['heading' => 'Poppins', 'body' => 'Poppins'],
            'radius'  => 'md',
            'spacing' => 'comfortable',
            'container' => 'normal',
        ],
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
        'business' => new stdClass(),
    ];

    // The starter doc must itself be valid — catches a broken manifest early.
    $errors = (new SiteValidator())->validate(json_decode(json_encode($doc), true));
    if ($errors) {
        sendError('Could not build a valid starter site: ' . implode('; ', array_slice($errors, 0, 5)), 500);
    }

    $site = SiteRepo::create(getCurrentUserId(), $name, $slug, $industry, json_decode(json_encode($doc), true));
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
