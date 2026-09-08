<?php
/**
 * TAPIFY — MERGE a small content patch into a live builder site.
 *
 *   https://app.tapify.co.in/patch-site-content.php?slug=westernnx&confirm=apply
 *   add &publish=1 to publish straight away
 *
 * Why this exists alongside run-site-content.php: that script REPLACES the draft
 * with a prepared file. Once a customer has edited their site in the builder
 * (Western NX changed its hero photo and picture shape), the content-*.json on
 * disk is stale and re-running the loader would throw the customer's work away.
 * This reads the CURRENT draft and changes only the keys named in the patch.
 *
 * Everything here is IDEMPOTENT: footer props are overwritten with the patch
 * value, and a section is skipped if its id is already in the document. Running
 * it twice does the same thing as running it once.
 *
 * Like the loader, this writes a NEW draft version under the site row lock, so
 * the previous draft stays in site_versions and the builder's version history
 * can restore it.
 *
 * DELETE THIS FILE AND THE patch-*.json FILES once the content is in.
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/builder/lib/SiteRepo.php';
require_once __DIR__ . '/builder/lib/SiteValidator.php';

// A fixed map, for the same reason the loader uses one: a query string must
// never choose which file is read off disk.
$PATCHES = [
    'westernnx' => ['file' => 'patch-western-nx.json', 'label' => 'Western NX — policies, legal pages, reviews'],
];

header('Content-Type: text/html; charset=utf-8');
echo '<!doctype html><meta charset="utf-8"><title>Patch site content</title>'
   . '<style>body{font:14px/1.6 system-ui;max-width:820px;margin:40px auto;padding:0 20px}'
   . 'code{background:#f4f4f5;padding:1px 5px;border-radius:4px}li{margin:2px 0}</style>'
   . '<h1>Patch site content</h1>';

$ok  = fn($m) => print('<p style="color:#15803d">✓ ' . $m . '</p>');
$bad = fn($m) => print('<p style="color:#b91c1c"><b>✗ ' . $m . '</b></p>');

if (!function_exists('isAdmin') || !isAdmin()) { $bad('Sign in as an admin first.'); exit; }

$slug = preg_replace('/[^a-z0-9-]/', '', strtolower($_GET['slug'] ?? ''));
if (!isset($PATCHES[$slug])) {
    $bad('Unknown slug. Pass one of: ' . implode(', ', array_map(
        fn($s) => "<code>?slug=$s&amp;confirm=apply</code>", array_keys($PATCHES))));
    exit;
}
echo '<p>Target: <b>' . htmlspecialchars($PATCHES[$slug]['label']) . '</b></p>';

try {
    $file = __DIR__ . '/' . $PATCHES[$slug]['file'];
    if (!is_file($file)) { $bad(htmlspecialchars($PATCHES[$slug]['file']) . ' not found next to this script.'); exit; }
    $patch = json_decode(file_get_contents($file), true);
    if (!is_array($patch)) { $bad('Patch file is not valid JSON: ' . json_last_error_msg()); exit; }

    $site = SiteRepo::findBySlug($slug);
    if (!$site) { $bad('No site with slug <b>' . htmlspecialchars($slug) . '</b>.'); exit; }
    $ok('Found site #' . (int)$site['id'] . ' — ' . htmlspecialchars($site['name']));

    // The CURRENT draft is the base. This is the whole point of the script.
    $pdo = getDB();
    $q = $pdo->prepare('SELECT rev, doc FROM site_versions WHERE id = ?');
    $q->execute([(int)$site['draft_version_id']]);
    $row = $q->fetch(PDO::FETCH_ASSOC);
    if (!$row) { $bad('This site has no draft version to patch.'); exit; }
    $doc = json_decode($row['doc'], true);
    if (!is_array($doc)) { $bad('The stored draft is not valid JSON.'); exit; }
    $ok('Loaded the CURRENT draft (rev ' . (int)$row['rev'] . ', ' . strlen($row['doc']) . ' bytes, '
        . count($doc['pages'] ?? []) . ' pages) — the customer\'s edits are the base.');

    $changes = [];

    // --- footer props: written to EVERY footer, since they are site-wide ------
    $fp = $patch['footerProps'] ?? [];
    if ($fp) {
        $touched = 0;
        foreach ($doc['pages'] as &$pg) {
            foreach ($pg['sections'] as &$sec) {
                if (($sec['type'] ?? '') !== 'footer') continue;
                foreach ($fp as $k => $v) $sec['props'][$k] = $v;
                $touched++;
            }
            unset($sec);
        }
        unset($pg);
        if ($touched === 0) $bad('No footer section found — the policies have nowhere to live.');
        else $changes[] = 'set ' . implode(', ', array_keys($fp)) . ' on ' . $touched . ' footer section(s)';
    }

    // --- header props: written to EVERY header, like the footer --------------
    $hp = $patch['headerProps'] ?? [];
    if ($hp) {
        $touched = 0;
        foreach ($doc['pages'] as &$pg) {
            foreach ($pg['sections'] as &$sec) {
                if (($sec['type'] ?? '') !== 'header') continue;
                foreach ($hp as $k => $v) $sec['props'][$k] = $v;
                $touched++;
            }
            unset($sec);
        }
        unset($pg);
        if ($touched === 0) $bad('No header section found.');
        else $changes[] = 'set ' . implode(', ', array_keys($hp)) . ' on ' . $touched . ' header section(s)';
    }

    // --- opening hours -------------------------------------------------------
    // The structured array behind the Business Hours section and the footer.
    if (!empty($patch['businessHours'])) {
        $doc['business'] = $doc['business'] ?? [];
        $before = json_encode($doc['business']['hours'] ?? null);
        $doc['business']['hours'] = $patch['businessHours'];
        $changes[] = ($before === json_encode($patch['businessHours']))
            ? 'opening hours already correct'
            : 'set opening hours on all ' . count($patch['businessHours']) . ' days';
    }

    // --- literal text replacements -------------------------------------------
    // The hours are ALSO written into prose — a ticker line, two section
    // sub-headings and a page's SEO description. Changing only the structured
    // array would leave the site telling visitors two different things. Exact
    // phrases, so this cannot touch anything it was not aimed at, and re-running
    // it is a no-op once the old phrase is gone.
    if (!empty($patch['textReplacements'])) {
        $hits = 0;
        $walk = function (&$node) use (&$walk, $patch, &$hits) {
            if (is_array($node)) {
                foreach ($node as &$v) $walk($v);
                unset($v);
            } elseif (is_string($node)) {
                foreach ($patch['textReplacements'] as $from => $to) {
                    if (strpos($node, $from) !== false) {
                        $node = str_replace($from, $to, $node);
                        $hits++;
                    }
                }
            }
        };
        $walk($doc);
        $changes[] = $hits ? ('rewrote ' . $hits . ' text mention(s) of the old hours') : 'no old hours text left to rewrite';
    }

    // --- item prices, matched by TITLE --------------------------------------
    // By title, not by position: the same garment appears on both the home page
    // and its category page, and pricing per card would show one dress at two
    // different prices. Only items still saying "On request" (or nothing) are
    // touched — a price the shop has already set is never overwritten, which is
    // what makes this safe to re-run after the client starts editing.
    $prices = $patch['itemPrices'] ?? [];
    if ($prices) {
        $set = 0; $kept = 0; $unmatched = [];
        $applyTo = function (array &$item) use ($prices, &$set, &$kept, &$unmatched) {
            $t = trim((string)($item['title'] ?? ''));
            if ($t === '' || !isset($prices[$t])) { if ($t !== '') $unmatched[$t] = true; return; }
            $cur = trim((string)($item['price'] ?? ''));
            if ($cur !== '' && strcasecmp($cur, 'On request') !== 0) { $kept++; return; }
            $item['price'] = $prices[$t];
            $set++;
        };

        foreach ($doc['pages'] as &$pg) {
            foreach ($pg['sections'] as &$sec) {
                if (!in_array($sec['type'] ?? '', ['products', 'services'], true)) continue;
                foreach (($sec['props']['items'] ?? []) as &$item) {
                    if (is_array($item)) $applyTo($item);
                }
                unset($item);
            }
            unset($sec);
        }
        unset($pg);
        // Shared-catalogue products live outside the pages.
        foreach (($doc['catalog']['products'] ?? []) as &$item) {
            if (is_array($item)) $applyTo($item);
        }
        unset($item);

        $changes[] = 'priced ' . $set . ' item(s); left ' . $kept . ' that already had a price';
        if ($unmatched) {
            $changes[] = 'no price in the patch for ' . count($unmatched) . ' item(s) — left as they are: '
                       . htmlspecialchars(implode(', ', array_slice(array_keys($unmatched), 0, 6)));
        }
    }

    // --- new sections --------------------------------------------------------
    $existingIds = [];
    foreach ($doc['pages'] as $pg) foreach ($pg['sections'] as $s) $existingIds[$s['id'] ?? ''] = true;

    foreach (($patch['addSections'] ?? []) as $add) {
        $sec  = $add['section'] ?? null;
        $want = $add['pageSlug'] ?? '/';
        if (!$sec || empty($sec['id'])) { $bad('A patch section has no id — skipped.'); continue; }
        if (isset($existingIds[$sec['id']])) { $changes[] = 'section ' . $sec['id'] . ' already present — left alone'; continue; }

        $placed = false;
        foreach ($doc['pages'] as &$pg) {
            if (($pg['slug'] ?? '') !== $want) continue;
            $at = count($pg['sections']);
            if (!empty($add['beforeSectionId'])) {
                foreach ($pg['sections'] as $i => $s) {
                    if (($s['id'] ?? '') === $add['beforeSectionId']) { $at = $i; break; }
                }
            }
            array_splice($pg['sections'], $at, 0, [$sec]);
            $placed = true;
            $changes[] = 'added ' . $sec['type'] . ' (' . $sec['id'] . ') to ' . $want . ' at position ' . $at;
            break;
        }
        unset($pg);
        if (!$placed) $bad('No page with slug ' . htmlspecialchars($want) . ' — ' . $sec['id'] . ' not added.');
    }

    if (!$changes) { $bad('Nothing to do.'); exit; }
    echo '<p><b>Changes</b></p><ul>';
    foreach ($changes as $c) echo '<li>' . htmlspecialchars($c) . '</li>';
    echo '</ul>';

    // Strict validation BEFORE anything is written — a bad patch must not be
    // able to break a live site.
    $errors = (new SiteValidator())->validate($doc, true);
    if ($errors) {
        $bad('The patched document did not validate, nothing was written:');
        echo '<ul>';
        foreach (array_slice($errors, 0, 20) as $e) echo '<li>' . htmlspecialchars($e) . '</li>';
        echo '</ul>';
        exit;
    }
    $ok('Patched document passed strict validation.');

    if (($_GET['confirm'] ?? '') !== 'apply') {
        echo '<p>Nothing written — this was a dry run. Add <code>&amp;confirm=apply</code> to write it.</p>';
        exit;
    }

    $pdo->beginTransaction();
    try {
        $lock = $pdo->prepare('SELECT id FROM sites WHERE id = ? FOR UPDATE');
        $lock->execute([(int)$site['id']]);

        $nr = $pdo->prepare('SELECT COALESCE(MAX(rev), 0) + 1 FROM site_versions WHERE site_id = ?');
        $nr->execute([(int)$site['id']]);
        $newRev = (int)$nr->fetchColumn();

        $encoded = json_encode($doc, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($encoded === false) throw new Exception('Could not encode: ' . json_last_error_msg());

        $ins = $pdo->prepare(
            'INSERT INTO site_versions (site_id, rev, doc, schema_version, kind, label, author_user_id, source)
             VALUES (?, ?, ?, ?, \'draft\', ?, ?, \'web\')'
        );
        $ins->execute([
            (int)$site['id'], $newRev, $encoded, (int)($doc['schemaVersion'] ?? 1),
            'Patch: ' . $PATCHES[$slug]['label'],
            getCurrentUserId() ? (int)getCurrentUserId() : null,
        ]);
        $versionId = (int)$pdo->lastInsertId();

        $upd = $pdo->prepare('UPDATE sites SET draft_version_id = ? WHERE id = ?');
        $upd->execute([$versionId, (int)$site['id']]);

        $pdo->commit();
        $ok('Wrote draft version id ' . $versionId . ' (rev ' . $newRev . '). The previous draft is still in version history.');
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }

    if (($_GET['publish'] ?? '') === '1') {
        // publish() takes the SITE ROW, not an id — and it must be re-read, so it
        // sees the draft pointer this script just moved.
        $fresh = SiteRepo::findBySlug($slug);
        SiteRepo::publish($fresh, getCurrentUserId(), 'Patch: ' . $PATCHES[$slug]['label'], 'web');
        $ok('Published. Open <a href="https://' . htmlspecialchars($slug) . '.tapify.co.in/">'
            . htmlspecialchars($slug) . '.tapify.co.in</a>');
    } else {
        echo '<p>Left as a DRAFT. Review it in the builder, then publish — or re-run with '
           . '<code>&amp;publish=1</code>.</p>';
    }
} catch (Throwable $e) {
    // Throwable, not Exception: a TypeError here would otherwise be a bare 500
    // with nothing to read (see the site-from-chat notes).
    $bad('Failed: ' . htmlspecialchars($e->getMessage()) . ' @ ' . htmlspecialchars(basename($e->getFile())) . ':' . $e->getLine());
}
