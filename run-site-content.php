<?php
/**
 * TAPIFY — one-time content loader for a builder site.
 *
 * Replaces a site's DRAFT with a prepared document built from the customer's
 * existing vCard. Open in a browser, signed in as an admin:
 *
 *   https://app.tapify.co.in/run-site-content.php?slug=aayasdental&confirm=apply
 *   https://app.tapify.co.in/run-site-content.php?slug=secnospects&confirm=apply
 *
 * Add &publish=1 to publish immediately instead of leaving it as a draft.
 *
 * The document is validated strictly before anything is written, and the write
 * inserts a NEW draft version under the site row lock rather than overwriting
 * the existing one — so the previous version stays in site_versions and can be
 * restored from the builder's version history.
 *
 * The slug is looked up in a fixed map rather than used to build a filename:
 * this script runs before any content validation, and letting a query string
 * choose which file to read is how you turn an admin tool into a file-disclosure
 * bug. To load another site, add a line to $SITES.
 *
 * DELETE THIS FILE AND THE content-*.json FILES once the content is in.
 */

$SITES = [
    'aayasdental' => ['file' => 'content-aayas-dental.json', 'label' => 'Dr. Aayas Dental Clinic'],
    'secnospects' => ['file' => 'content-secno-spects.json', 'label' => 'Secno Spects'],
    // NOTE the slugs: the existing vCards live at jamal-tours-solutions and
    // sas-services. index.php resolves a published BUILDER site before it looks
    // at vcards.url_alias, and SiteRepo::slugAvailable never consults the vcards
    // table — so reusing those slugs here would silently take the vCard's URL.
    'jamaltours'  => ['file' => 'content-jamal-tours.json',  'label' => 'Jamal Tours Solutions'],
    'sasservices' => ['file' => 'content-sas-services.json', 'label' => 'SAS Services'],
];

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/builder/lib/SiteRepo.php';
require_once __DIR__ . '/builder/lib/SiteValidator.php';

header('Content-Type: text/html; charset=utf-8');
echo "<meta name='viewport' content='width=device-width,initial-scale=1'>";
echo "<h2>Tapify — load site content</h2>";

$ok   = fn($m) => print("<p style='color:green'>OK &mdash; $m</p>");
$bad  = fn($m) => print("<p style='color:#b91c1c'><b>Stopped</b> &mdash; $m</p>");
$note = fn($m) => print("<p style='color:#b45309'>$m</p>");

// This overwrites a live customer's content, so it is not something a stray
// URL hit should be able to do: admin session AND an explicit confirm.
if (!function_exists('isAdmin') || !isAdmin()) {
    $bad("Sign in to the Tapify admin panel first, in this same browser, then reload this page.");
    exit;
}
if (($_GET['confirm'] ?? '') !== 'apply') {
    $note("Add <code>&amp;confirm=apply</code> to the URL to actually write the content. Nothing has been changed.");
    exit;
}

$slug = preg_replace('/[^a-z0-9-]/', '', strtolower($_GET['slug'] ?? ''));
if (!isset($SITES[$slug])) {
    $bad("Unknown slug. Pass one of: " . implode(', ', array_map(
        fn($s) => "<code>?slug=$s&amp;confirm=apply</code>", array_keys($SITES)
    )));
    exit;
}
$label = $SITES[$slug]['label'];
echo "<p>Target: <b>" . htmlspecialchars($label) . "</b> (" . htmlspecialchars($slug) . ".tapify.co.in)</p>";

try {
    $file = __DIR__ . '/' . $SITES[$slug]['file'];
    if (!file_exists($file)) { $bad(htmlspecialchars($SITES[$slug]['file']) . " not found next to this script."); exit; }

    $doc = json_decode(file_get_contents($file), true);
    if (!is_array($doc)) { $bad(htmlspecialchars($SITES[$slug]['file']) . " is not valid JSON: " . json_last_error_msg()); exit; }
    $ok("Loaded document — " . count($doc['pages'] ?? []) . " pages.");

    $site = SiteRepo::findBySlug($slug);
    if (!$site) { $bad("No site with slug <b>" . htmlspecialchars($slug) . "</b>. Pass the right one as ?slug=…"); exit; }
    $ok("Found site #" . (int)$site['id'] . " — " . htmlspecialchars($site['name']) . " (" . htmlspecialchars($site['slug']) . ".tapify.co.in)");

    // Validate strictly here: publishing re-validates anyway, and a problem is
    // far easier to read on this page than as a 422 in the builder.
    $errors = (new SiteValidator())->validate($doc, true);
    if ($errors) {
        $bad("The document did not validate, nothing was written:");
        echo "<ul>";
        foreach (array_slice($errors, 0, 20) as $e) echo "<li>" . htmlspecialchars($e) . "</li>";
        echo "</ul>";
        exit;
    }
    $ok("Document passed strict validation.");

    // Show the real state first — if anything goes wrong this is what explains it.
    $pdo = getDB();
    $cur = $pdo->prepare(
        "SELECT id, rev, kind, LENGTH(doc) AS bytes, created_at
           FROM site_versions WHERE site_id = ? ORDER BY rev DESC LIMIT 5"
    );
    $cur->execute([(int)$site['id']]);
    $rows = $cur->fetchAll(PDO::FETCH_ASSOC);
    echo "<p><b>Current versions</b> (draft_version_id = " . (int)$site['draft_version_id']
       . ", published_version_id = " . (int)$site['published_version_id'] . ")</p><ul>";
    foreach ($rows as $r) {
        $marks = [];
        if ((int)$r['id'] === (int)$site['draft_version_id'])     $marks[] = 'DRAFT POINTER';
        if ((int)$r['id'] === (int)$site['published_version_id']) $marks[] = 'PUBLISHED POINTER';
        echo "<li>id " . (int)$r['id'] . " &middot; rev " . (int)$r['rev'] . " &middot; kind <b>"
           . htmlspecialchars($r['kind']) . "</b> &middot; " . (int)$r['bytes'] . " bytes &middot; "
           . htmlspecialchars($r['created_at']) . ($marks ? ' &middot; <b>' . implode(' + ', $marks) . '</b>' : '') . "</li>";
    }
    echo "</ul>";

    // Write a NEW draft version and repoint the site at it, inside the site row
    // lock. Deliberately NOT saveDraft(): its rev check guards two people editing
    // at once, which is not this — it only made a one-off admin load fail with a
    // conflict it can do nothing about. The old draft row is left in place, so
    // the builder's version history can still restore it.
    $pdo->beginTransaction();
    try {
        $lock = $pdo->prepare("SELECT id FROM sites WHERE id = ? FOR UPDATE");
        $lock->execute([(int)$site['id']]);

        $nr = $pdo->prepare("SELECT COALESCE(MAX(rev), 0) + 1 FROM site_versions WHERE site_id = ?");
        $nr->execute([(int)$site['id']]);
        $newRev = (int)$nr->fetchColumn();

        $encoded = json_encode($doc, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($encoded === false) throw new Exception('Could not encode the document: ' . json_last_error_msg());

        $ins = $pdo->prepare(
            "INSERT INTO site_versions (site_id, rev, doc, schema_version, kind, label, author_user_id, source)
             VALUES (?, ?, ?, ?, 'draft', ?, ?, 'web')"
        );
        $ins->execute([
            (int)$site['id'], $newRev, $encoded,
            (int)($doc['schemaVersion'] ?? 1),
            'Content load: ' . $label,
            getCurrentUserId() ? (int)getCurrentUserId() : null,
        ]);
        $vid = (int)$pdo->lastInsertId();

        $pdo->prepare("UPDATE sites SET draft_version_id = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?")
            ->execute([$vid, (int)$site['id']]);

        $pdo->commit();
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        throw $e;
    }

    $ok("Draft written — new version id $vid at revision $newRev (" . number_format(strlen($encoded)) . " bytes). "
      . "The previous draft is still in site_versions and can be restored from the builder's version history.");

    if (($_GET['publish'] ?? '') === '1') {
        $fresh = SiteRepo::findById($site['id']);
        SiteRepo::publish($fresh, getCurrentUserId(), 'Content load: ' . $label, 'web');
        $ok("Published. Live now at <a href='https://" . htmlspecialchars($slug) . ".tapify.co.in'>"
            . htmlspecialchars($slug) . ".tapify.co.in</a>");
    } else {
        $note("Saved as a DRAFT only. Review it in the builder, then hit Publish "
            . "(or re-run this URL with <code>&amp;publish=1</code>).");
    }

    $note("<b>Delete run-site-content.php and the content-*.json files once every site is loaded.</b>");

} catch (Exception $e) {
    $bad(htmlspecialchars($e->getMessage()));
}
