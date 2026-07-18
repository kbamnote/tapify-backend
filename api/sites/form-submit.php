<?php
/**
 * POST /api/sites/form-submit.php
 *
 * Receives a contact/enquiry submission from a PUBLISHED builder site.
 *
 * This is a PUBLIC endpoint — the sender is a visitor, not a logged-in user —
 * so everything is treated as hostile:
 *   - the site must exist and be published
 *   - the form must be declared in that site's PUBLISHED document
 *   - only fields declared in that form are stored (no arbitrary junk)
 *   - required fields are enforced server-side, not just in the browser
 *   - a honeypot field and a per-IP rate limit keep out casual bots
 *
 * The section renders a plain <form>, so this works with no JavaScript and
 * redirects the visitor back to the site afterwards. It also answers JSON when
 * asked, so the builder can add an AJAX version later without changing this.
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../builder/lib/SiteRepo.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') sendError('Only POST allowed', 405);

/** Wants JSON, or a browser form post we should redirect? */
function wantsJson(): bool
{
    $accept = strtolower($_SERVER['HTTP_ACCEPT'] ?? '');
    $xhr    = strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '');
    return strpos($accept, 'application/json') !== false || $xhr === 'xmlhttprequest';
}

/** Send the visitor back to the page they came from. */
function finish(bool $ok, string $message, ?string $back): void
{
    if (wantsJson()) {
        if ($ok) sendSuccess($message);
        sendError($message, 400);
    }
    $url = $back ?: '/';
    $url .= (strpos($url, '?') === false ? '?' : '&') . ($ok ? 'sent=1' : 'sent=0');
    header('Location: ' . $url, true, 303);
    exit;
}

$input   = getInput(); // handles JSON body and form-encoded POST
$formId  = trim((string)($input['form_id'] ?? ''));
$slug    = strtolower(trim((string)($input['site'] ?? '')));

// Where to send the visitor back to. Only ever a URL on our own base domain, so
// this can't be turned into an open redirect.
$referer = $_SERVER['HTTP_REFERER'] ?? '';
$back = null;
if ($referer) {
    $host = parse_url($referer, PHP_URL_HOST);
    $base = '.' . (defined('PUBLIC_BASE_DOMAIN') ? PUBLIC_BASE_DOMAIN : 'tapify.co.in');
    if ($host && (substr($host, -strlen($base)) === $base || $host === ltrim($base, '.'))) {
        $back = strtok($referer, '?');
    }
}

// Honeypot: a hidden field real people never fill in. Pretend it worked so the
// bot doesn't learn anything, but store nothing.
if (trim((string)($input['_hp'] ?? '')) !== '') {
    finish(true, 'Thank you — your message has been sent.', $back);
}

if ($formId === '') finish(false, 'This form is not configured correctly.', $back);
if ($slug === '')   finish(false, 'This form is not configured correctly.', $back);

try {
    $site = SiteRepo::findBySlug($slug);
    if (!$site || ($site['status'] ?? '') === 'disabled') {
        finish(false, 'This website is not available.', $back);
    }

    // Only the PUBLISHED document counts — a form that exists solely in someone's
    // draft must not be able to collect submissions.
    $published = SiteRepo::getPublished($site);
    if (!$published) finish(false, 'This website is not published yet.', $back);

    $form = null;
    foreach (($published['doc']['forms'] ?? []) as $f) {
        if (($f['id'] ?? null) === $formId) { $form = $f; break; }
    }
    if (!$form) finish(false, 'This form is no longer available.', $back);

    // --- rate limit: per IP, per site, per hour ---
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    if ($ip !== '') {
        $stmt = getDB()->prepare(
            "SELECT COUNT(*) FROM form_submissions
             WHERE site_id = ? AND ip_address = ? AND created_at > (NOW() - INTERVAL 1 HOUR)"
        );
        $stmt->execute([(int)$site['id'], $ip]);
        if ((int)$stmt->fetchColumn() >= 10) {
            finish(false, 'Too many messages sent. Please try again later.', $back);
        }
    }

    // --- collect ONLY the fields this form declares ---
    $data = [];
    $missing = [];
    foreach (($form['fields'] ?? []) as $field) {
        $name = $field['name'] ?? null;
        if (!$name) continue;

        // File uploads are not accepted yet — silently skipping would lose data
        // without telling anyone, so the field is simply not collected and the
        // form should not declare one until upload support exists.
        if (($field['type'] ?? '') === 'file') continue;

        $raw = $input[$name] ?? '';
        $val = is_array($raw) ? implode(', ', array_map('strval', $raw)) : trim((string)$raw);

        if ($val === '') {
            if (!empty($field['required'])) $missing[] = $field['label'] ?? $name;
            continue;
        }

        if (($field['type'] ?? '') === 'email' && !filter_var($val, FILTER_VALIDATE_EMAIL)) {
            finish(false, 'Please enter a valid email address.', $back);
        }

        // Cap length so one submission can't bloat the row.
        $data[$name] = [
            'label' => $field['label'] ?? $name,
            'value' => mb_substr($val, 0, 5000),
        ];
    }

    if ($missing) {
        finish(false, 'Please fill in: ' . implode(', ', $missing), $back);
    }
    if (!$data) {
        finish(false, 'Please fill in the form before sending.', $back);
    }

    $stmt = getDB()->prepare(
        "INSERT INTO form_submissions (site_id, form_id, data, page_slug, ip_address, user_agent)
         VALUES (?, ?, ?, ?, ?, ?)"
    );
    $stmt->execute([
        (int)$site['id'],
        $formId,
        json_encode($data, JSON_UNESCAPED_UNICODE),
        $back ? parse_url($back, PHP_URL_PATH) : null,
        $ip,
        mb_substr((string)($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255),
    ]);

    finish(true, $form['successMessage'] ?? 'Thank you — your message has been sent.', $back);

} catch (Exception $e) {
    error_log('form-submit failed: ' . $e->getMessage());
    // Never leak internals to a public visitor.
    finish(false, 'Sorry, something went wrong. Please try again.', $back);
}
