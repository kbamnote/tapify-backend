<?php
/**
 * TAPIFY — one-time creator for the sales team's personal portfolio sites.
 *
 * Creates a login and a 4-page portfolio site for each representative below,
 * from the sales-portfolio recipe, substituting their name and email.
 *
 * Open in a browser, signed in as an admin:
 *   https://app.tapify.co.in/run-sales-portfolios.php?confirm=apply
 * Add &publish=1 to publish each site as well as creating it.
 *
 * Safe to re-run: a rep whose site already exists is skipped, not duplicated.
 * It mirrors api/sites/create.php exactly — same user row, same Free Plan
 * subscription, same SiteRepo::create — so these sites behave like any other.
 *
 * NOTE ON PASSWORDS: an existing account is NEVER given a new password here,
 * for the same reason create.php refuses to. If a rep's email already exists,
 * the site is assigned to that account and their current password stands.
 *
 * DELETE THIS FILE once the five sites are up.
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/builder/lib/SiteRepo.php';
require_once __DIR__ . '/builder/lib/SiteValidator.php';
require_once __DIR__ . '/builder/lib/SchemaRegistry.php';

const RECIPE_ID   = 'sales-portfolio';
const REP_PASSWORD = '123456';

$REPS = [
    ['name' => 'Vaibhav', 'email' => 'vaibhav@tapify.co.in', 'slug' => 'vaibhav-tapify'],
    ['name' => 'Rahul',   'email' => 'rahul@tapify.co.in',   'slug' => 'rahul-tapify'],
    ['name' => 'Manoj',   'email' => 'manoj@tapify.co.in',   'slug' => 'manoj-tapify'],
    ['name' => 'Vasim',   'email' => 'vasim@tapify.co.in',   'slug' => 'vasim-tapify'],
    ['name' => 'Atharva', 'email' => 'atharva@tapify.co.in', 'slug' => 'atharva-tapify'],
];

header('Content-Type: text/html; charset=utf-8');
echo "<meta name='viewport' content='width=device-width,initial-scale=1'>";
echo "<style>body{font:14px/1.5 system-ui;max-width:900px;margin:24px auto;padding:0 16px}"
   . "td,th{padding:6px 10px;border-bottom:1px solid #ddd;text-align:left}code{background:#f3f3f3;padding:1px 4px}</style>";
echo "<h2>Tapify — create the sales team portfolio sites</h2>";

$ok   = fn($m) => print("<p style='color:#15803d'>OK &mdash; $m</p>");
$bad  = fn($m) => print("<p style='color:#b91c1c'><b>Stopped</b> &mdash; $m</p>");
$note = fn($m) => print("<p style='color:#b45309'>$m</p>");

if (!function_exists('isAdmin') || !isAdmin()) {
    $bad("Sign in to the Tapify admin panel first, in this same browser, then reload this page.");
    exit;
}
if (($_GET['confirm'] ?? '') !== 'apply') {
    $note("This will create <b>" . count($REPS) . " logins and " . count($REPS) . " websites</b>. "
        . "Add <code>&amp;confirm=apply</code> to the URL to go ahead. Nothing has been changed.");
    exit;
}
$doPublish = ($_GET['publish'] ?? '') === '1';

/** Recursively substitute {{REP_NAME}} / {{REP_EMAIL}} through the whole recipe. */
function substituteTokens($value, array $map) {
    if (is_string($value)) return strtr($value, $map);
    if (is_array($value)) {
        $out = [];
        foreach ($value as $k => $v) $out[$k] = substituteTokens($v, $map);
        return $out;
    }
    return $value;
}

try {
    $pdo    = getDB();
    $recipe = SchemaRegistry::industries()[RECIPE_ID] ?? null;
    if (!$recipe) { $bad("Recipe <code>" . RECIPE_ID . "</code> not found. Deploy builder/schema/industries/sales-portfolio.json first."); exit; }
    $ok("Loaded recipe: " . htmlspecialchars($recipe['label'] ?? RECIPE_ID));

    $themeTokens = SchemaRegistry::themes()[$recipe['theme']['preset'] ?? '']['tokens'] ?? [];
    if (!$themeTokens) { $bad("Theme preset <code>" . htmlspecialchars($recipe['theme']['preset'] ?? '?') . "</code> not found."); exit; }

    echo "<table><tr><th>Rep</th><th>Login</th><th>Website</th><th>Result</th></tr>";

    foreach ($REPS as $rep) {
        $map = ['{{REP_NAME}}' => $rep['name'], '{{REP_EMAIL}}' => $rep['email']];
        $r   = substituteTokens($recipe, $map);
        $row = "<tr><td><b>" . htmlspecialchars($rep['name']) . "</b></td>";

        // ---- already done? ----
        if (SiteRepo::findBySlug($rep['slug'])) {
            echo $row . "<td>&mdash;</td><td>" . htmlspecialchars($rep['slug']) . "</td>"
               . "<td style='color:#b45309'>Skipped — site already exists</td></tr>";
            continue;
        }

        // ---- the login ----
        $st = $pdo->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        $st->execute([$rep['email']]);
        $userId = (int)$st->fetchColumn();
        $loginNote = '';

        if ($userId) {
            // Deliberately NOT resetting the password — same rule as create.php.
            $loginNote = "existing account, password unchanged";
        } else {
            $st = $pdo->prepare("INSERT INTO users (name, email, password, role, status) VALUES (?, ?, ?, 'user', 1)");
            $st->execute([$rep['name'], $rep['email'], hashPassword(REP_PASSWORD)]);
            $userId = (int)$pdo->lastInsertId();
            $st = $pdo->prepare(
                "INSERT INTO subscriptions (user_id, plan_name, vcards_limit, stores_limit, price, subscribed_date, expiry_date, status)
                 VALUES (?, 'Free Plan', 5, 1, 0, ?, ?, 'active')"
            );
            $st->execute([$userId, date('Y-m-d'), date('Y-m-d', strtotime('+1 year'))]);
            $loginNote = "created, password <code>" . htmlspecialchars(REP_PASSWORD) . "</code>";
        }

        // ---- build the document, exactly as create.php does ----
        $content = $r['content'] ?? [];
        $buildSection = function (string $type, array $pageSeeds) use ($content) {
            $instance = SchemaRegistry::newSectionInstance($type);
            if (!$instance) return null;
            foreach ([$content[$type] ?? null, $pageSeeds[$type] ?? null] as $seed) {
                if (!is_array($seed)) continue;
                if (!empty($seed['variant'])) $instance['variant'] = $seed['variant'];
                if (isset($seed['props'])) $instance['props'] = array_merge((array)($instance['props'] ?? []), (array)$seed['props']);
                if (isset($seed['style'])) $instance['style'] = array_merge((array)($instance['style'] ?? []), (array)$seed['style']);
            }
            return $instance;
        };

        $pages = [];
        foreach ($r['pages'] as $i => $pageDef) {
            $sections = [];
            foreach ((array)($pageDef['sections'] ?? []) as $type) {
                $s = $buildSection($type, (array)($pageDef['content'] ?? []));
                if ($s) $sections[] = $s;
            }
            $pages[] = [
                'id'       => $pageDef['id'],
                'slug'     => $pageDef['slug'],
                'title'    => $pageDef['title'],
                'seo'      => (array)($pageDef['seo'] ?? []),
                'sections' => $sections,
            ];
        }

        $doc = [
            'schemaVersion' => 1,
            'site'  => ['name' => $rep['name'], 'industry' => RECIPE_ID, 'locale' => 'en-IN'],
            'theme' => array_merge($themeTokens, ['preset' => $recipe['theme']['preset']]),
            'nav'   => ['header' => array_map(fn($p) => ['label' => $p['title'], 'pageId' => $p['id']], $pages)],
            'pages' => $pages,
            'business' => (array)($r['business'] ?? []),
        ];

        $errors = (new SiteValidator())->validate($doc, true);
        if ($errors) {
            echo $row . "<td>$loginNote</td><td>" . htmlspecialchars($rep['slug']) . "</td>"
               . "<td style='color:#b91c1c'>Invalid: " . htmlspecialchars(implode('; ', array_slice($errors, 0, 3))) . "</td></tr>";
            continue;
        }

        $site = SiteRepo::create($userId, $rep['name'], $rep['slug'], RECIPE_ID, $doc);
        $result = "site #" . (int)$site['id'] . " created";

        if ($doPublish) {
            SiteRepo::publish(SiteRepo::findById($site['id']), getCurrentUserId(), 'Initial publish', 'web');
            $result .= " and published";
        }

        echo $row . "<td>" . $loginNote . "</td>"
           . "<td><a href='https://" . htmlspecialchars($rep['slug']) . ".tapify.co.in' target='_blank'>"
           . htmlspecialchars($rep['slug']) . ".tapify.co.in</a></td>"
           . "<td style='color:#15803d'>" . $result . "</td></tr>";
    }

    echo "</table>";
    $ok("Done.");
    if (!$doPublish) $note("Sites were created as DRAFTS. Review them in the builder and hit Publish, or re-run this URL with <code>&amp;publish=1</code>.");
    $note("<b>Each rep must add their own photograph and phone number, and replace the placeholder testimonials, before sharing their page.</b>");
    $note("<b>Delete run-sales-portfolios.php now.</b>");

} catch (Exception $e) {
    $bad(htmlspecialchars($e->getMessage()));
}
