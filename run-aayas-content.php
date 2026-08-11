<?php
/**
 * TAPIFY — one-time content loader for Dr. Aayas Dental Clinic.
 *
 * Replaces the site's DRAFT with the prepared document in
 * content-aayas-dental.json (4 pages, 18 treatments, real patient reviews,
 * clinic hours and contact details taken from the client's vCard).
 *
 * Open in a browser, signed in as an admin:
 *   https://app.tapify.co.in/run-aayas-content.php?slug=aayasdental&confirm=apply
 * Add &publish=1 to publish immediately instead of leaving it as a draft.
 *
 * It writes through SiteRepo::saveDraft, so the document is validated, the
 * revision counter is respected and the previous version stays in site_versions
 * and can be restored from the builder's version history.
 *
 * DELETE THIS FILE once the content is in.
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/builder/lib/SiteRepo.php';
require_once __DIR__ . '/builder/lib/SiteValidator.php';

header('Content-Type: text/html; charset=utf-8');
echo "<meta name='viewport' content='width=device-width,initial-scale=1'>";
echo "<h2>Tapify — load content for Dr. Aayas Dental Clinic</h2>";

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

$slug = preg_replace('/[^a-z0-9-]/', '', strtolower($_GET['slug'] ?? 'aayasdental'));

try {
    $file = __DIR__ . '/content-aayas-dental.json';
    if (!file_exists($file)) { $bad("content-aayas-dental.json not found next to this script."); exit; }

    $doc = json_decode(file_get_contents($file), true);
    if (!is_array($doc)) { $bad("content-aayas-dental.json is not valid JSON: " . json_last_error_msg()); exit; }
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

    $draft = SiteRepo::getDraft($site);
    $rev   = $draft ? (int)$draft['rev'] : 0;
    $res   = SiteRepo::saveDraft($site, $doc, $rev, getCurrentUserId(), 'web');
    $ok("Draft saved — now at revision " . (int)$res['rev'] . ". The previous version is still in the builder's version history.");

    if (($_GET['publish'] ?? '') === '1') {
        $fresh = SiteRepo::findById($site['id']);
        SiteRepo::publish($fresh, getCurrentUserId(), 'Dental content load', 'web');
        $ok("Published. Live now at <a href='https://" . htmlspecialchars($slug) . ".tapify.co.in'>"
            . htmlspecialchars($slug) . ".tapify.co.in</a>");
    } else {
        $note("Saved as a DRAFT only. Review it in the builder, then hit Publish "
            . "(or re-run this URL with <code>&amp;publish=1</code>).");
    }

    $note("<b>Delete run-aayas-content.php and content-aayas-dental.json now.</b>");

} catch (Exception $e) {
    $bad(htmlspecialchars($e->getMessage()));
}
