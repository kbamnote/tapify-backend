<?php
/**
 * POST /api/sites/set-domain.php
 *   { "site_id": 12, "domain": "www.galaxycardecor.com" }   attach / change
 *   { "site_id": 12, "domain": "" }                          detach
 *
 * Records the customer's own domain for a site. It does NOT configure any DNS
 * or certificates.
 *
 * WHY THERE IS NOTHING TO PROVISION. Railway caps custom domains per service
 * and both slots are taken (app.tapify.co.in and the *.tapify.co.in wildcard),
 * so customer domains are never added to Railway. Cloudflare sits in front and
 * fetches the page from the site's ordinary subdomain instead:
 *
 *   www.galaxycardecor.com -> Cloudflare -> galaxycardecor.tapify.co.in -> Railway
 *
 * Railway never sees the custom domain, so the cap never applies and the
 * certificate is Cloudflare's to issue.
 *
 * This row exists so the RENDERER can trust the X-Forwarded-Host that Cloudflare
 * passes through, and emit canonical tags, share links and QR codes on the
 * customer's domain rather than the tapify subdomain. Without it Google indexes
 * the subdomain and the custom domain achieves nothing.
 */
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../builder/lib/SiteRepo.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') sendError('Only POST allowed', 405);

requireAuth();

$input  = getInput();
$siteId = $input['site_id'] ?? null;
if (!$siteId || !is_numeric($siteId)) sendError('site_id is required', 422);

$site = SiteRepo::findById($siteId);
if (!$site) sendError('Site not found', 404);
if (!SiteRepo::ownedBy($site, getCurrentUserId()) && !isStaffOrAdmin()) {
    sendError('Access denied', 403);
}

$domain = strtolower(trim((string)($input['domain'] ?? '')));
$action = trim((string)($input['action'] ?? ''));

/* ---------------------------------------------------------------- verify */
/*
 * Prove the customer's domain actually reaches THIS site before we start
 * treating it as canonical. Fetch it and look for the <meta name="tapify-site">
 * marker the renderer emits. A parked page, a competitor's server or a
 * half-finished Cloudflare route all fail this — which is the point, because the
 * renderer 301s the tapify subdomain away once a domain is marked verified, and
 * doing that against a domain that is not really live would take the site down.
 */
if ($action === 'verify') {
    $d = (string)($site['domain'] ?? '');
    if ($d === '') sendError('No domain is attached to this site yet.', 422);

    $ch = curl_init('https://' . $d . '/');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS      => 5,
        CURLOPT_TIMEOUT        => 15,
        CURLOPT_USERAGENT      => 'Tapify-DomainCheck/1.0',
    ]);
    $body = curl_exec($ch);
    $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err  = curl_error($ch);
    curl_close($ch);

    if ($body === false) {
        sendError('Could not reach https://' . $d . ' — ' . ($err ?: 'no response') .
                  '. Check the DNS record and that Cloudflare is proxying it.', 502);
    }
    if ($code !== 200) {
        sendError('https://' . $d . ' answered ' . $code . ', not 200. Is the Cloudflare route set up?', 502);
    }

    $wantSlug = (string)$site['slug'];
    $found = preg_match('/<meta\s+name=["\']tapify-site["\']\s+content=["\']([a-z0-9-]+)["\']/i',
                        (string)$body, $m) ? $m[1] : '';
    if ($found === '') {
        sendError('That domain is reachable but is not serving a Tapify site. The Cloudflare '
                . 'route is probably pointing somewhere else.', 409);
    }
    if ($found !== $wantSlug) {
        sendError("That domain is serving a DIFFERENT Tapify site ({$found}). The Cloudflare "
                . "route should fetch {$wantSlug}." . PUBLIC_BASE_DOMAIN . '.', 409);
    }

    try {
        $st = getDB()->prepare("UPDATE sites SET domain_verified_at = CURRENT_TIMESTAMP WHERE id = ?");
        $st->execute([(int)$site['id']]);
    } catch (Exception $e) {
        sendError('Verified, but could not save. Has migration_site_custom_domain.sql been run?', 500);
    }

    sendSuccess('Domain verified and now live', [
        'domain'   => $d,
        'verified' => true,
        'note'     => 'The ' . $site['slug'] . '.' . PUBLIC_BASE_DOMAIN
                    . ' address now redirects here permanently.',
    ]);
}

/* ---------------------------------------------------------------- detach */
if ($domain === '') {
    try {
        $st = getDB()->prepare("UPDATE sites SET domain = NULL, domain_verified_at = NULL WHERE id = ?");
        $st->execute([(int)$site['id']]);
    } catch (Exception $e) {
        sendError('Could not remove the domain. Has migration_site_custom_domain.sql been run?', 500);
    }
    sendSuccess('Custom domain removed', ['domain' => null]);
}

/* -------------------------------------------------------------- validate */
// Strip anything people paste by habit: scheme, path, trailing dot, port.
$domain = preg_replace('~^https?://~', '', $domain);
$domain = explode('/', $domain)[0];
$domain = explode(':', $domain)[0];
$domain = rtrim($domain, '.');

if (!preg_match('/^(?=.{4,190}$)([a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z]{2,}$/', $domain)) {
    sendError('That does not look like a domain. Use the form www.example.com', 422);
}
// Our own space is served by the wildcard; letting someone claim a tapify
// hostname here would let them hijack canonical tags on a sibling site.
if ($domain === PUBLIC_BASE_DOMAIN || str_ends_with($domain, '.' . PUBLIC_BASE_DOMAIN)) {
    sendError('That is a Tapify address and is already handled automatically.', 422);
}

/* ------------------------------------------------------------------ save */
try {
    $st = getDB()->prepare(
        "UPDATE sites SET domain = ?, domain_verified_at = NULL WHERE id = ?"
    );
    $st->execute([$domain, (int)$site['id']]);
} catch (PDOException $e) {
    // uk_domain — one domain, one site.
    if ($e->getCode() === '23000') {
        sendError('That domain is already attached to another site.', 409);
    }
    sendError('Could not save the domain. Has migration_site_custom_domain.sql been run?', 500);
}

$target = $site['slug'] . '.' . PUBLIC_BASE_DOMAIN;
sendSuccess('Custom domain saved', [
    'domain' => $domain,
    // Everything the operator needs to finish the job in Cloudflare. Returned
    // rather than documented elsewhere so the UI can just print it.
    'setup'  => [
        'proxy_to'  => $target,
        'steps'     => [
            "Add {$domain} to Cloudflare (free plan is enough).",
            "Point it at Cloudflare's nameservers at the registrar.",
            "Add a proxied (orange cloud) DNS record for {$domain}.",
            "Add a Worker or Origin Rule that fetches https://{$target} and forwards the visitor's "
              . "hostname in the X-Forwarded-Host header.",
            "Cloudflare issues the HTTPS certificate automatically.",
        ],
        'note' => 'Do NOT add this domain to Railway — its custom-domain slots are full, and it '
                . 'does not need to know about the domain at all.',
    ],
]);
