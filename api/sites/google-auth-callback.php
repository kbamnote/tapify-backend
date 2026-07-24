<?php
/**
 * GET /api/sites/google-auth-callback.php?code=..&state=..   (PUBLIC)
 *
 * The single, fixed OAuth redirect URI for customer "Sign in with Google".
 * Google sends every customer of every builder site here. We:
 *   1. verify the signed state -> recover the site slug + return path
 *   2. exchange the code for tokens, read the id_token (email, name)
 *   3. upsert the customer in site_customers and mint a session token
 *   4. redirect to <slug>.tapify.co.in/account?tf_google=<one-time code>
 *
 * The session token is NOT put in the URL — a short-lived one-time handoff code
 * is, which the site exchanges for the real token via customer-auth.php.
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../builder/lib/SiteRepo.php';
require_once __DIR__ . '/../../builder/lib/CustomerGoogleAuth.php';

/** Bounce back to a site's /account page, optionally flagging an error. */
function tf_google_back(string $slug, string $next = '/account', string $err = ''): void
{
    $url = CustomerGoogleAuth::publicSiteUrl($slug) . '/account';
    $q = [];
    if ($next !== '' && $next !== '/account') $q['next'] = $next;
    if ($err !== '') $q['tf_google_error'] = $err;
    if ($q) $url .= '?' . http_build_query($q);
    header('Location: ' . $url);
    exit;
}

if (!CustomerGoogleAuth::isConfigured()) { http_response_code(404); echo 'Not available.'; exit; }

$state = (string)($_GET['state'] ?? '');
$st = CustomerGoogleAuth::verifyState($state);
if (!$st) { http_response_code(400); echo 'This sign-in link has expired. Please try again.'; exit; }
$slug = $st['site'];
$next = $st['next'];

// User declined at Google's consent screen.
if (!empty($_GET['error']) || empty($_GET['code'])) tf_google_back($slug, $next, '1');
$code = (string)$_GET['code'];

try {
    // --- 1. exchange the authorization code for tokens ---
    $ch = curl_init('https://oauth2.googleapis.com/token');
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_POSTFIELDS => http_build_query([
            'code'          => $code,
            'client_id'     => GOOGLE_LOGIN_CLIENT_ID,
            'client_secret' => GOOGLE_LOGIN_CLIENT_SECRET,
            'redirect_uri'  => GOOGLE_LOGIN_REDIRECT,
            'grant_type'    => 'authorization_code',
        ]),
    ]);
    $res  = curl_exec($ch);
    $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $tok = json_decode((string)$res, true);
    if ($http !== 200 || empty($tok['id_token'])) {
        error_log('google-auth: token exchange failed: ' . $res);
        tf_google_back($slug, $next, '1');
    }

    // --- 2. read the id_token payload (trusted: came straight from Google over TLS) ---
    $seg = explode('.', $tok['id_token']);
    if (count($seg) < 2) tf_google_back($slug, $next, '1');
    $claims = json_decode(base64_decode(strtr($seg[1], '-_', '+/')) ?: '', true);
    $email  = strtolower(trim((string)($claims['email'] ?? '')));
    $name   = trim((string)($claims['name'] ?? '')) ?: (strpos($email, '@') ? substr($email, 0, strpos($email, '@')) : 'Customer');
    $gid    = (string)($claims['sub'] ?? '');
    if ($email === '' || empty($claims['email_verified'])) tf_google_back($slug, $next, '2');

    // --- 3. upsert the customer for this site ---
    $site = SiteRepo::findBySlug($slug);
    if (!$site || ($site['status'] ?? '') === 'disabled' || !SiteRepo::getPublished($site)) {
        http_response_code(404); echo 'This website is not available.'; exit;
    }
    $siteId = (int)$site['id'];
    $db = getDB();

    // Create the table (with the Google columns) on first use.
    if (!$db->query("SHOW TABLES LIKE 'site_customers'")->fetchColumn()) {
        $db->exec("CREATE TABLE site_customers (
            id INT AUTO_INCREMENT PRIMARY KEY, site_id INT NOT NULL,
            name VARCHAR(150) NOT NULL, email VARCHAR(190) NOT NULL, phone VARCHAR(30) DEFAULT NULL,
            provider VARCHAR(20) NOT NULL DEFAULT 'email', google_id VARCHAR(40) DEFAULT NULL,
            password_hash VARCHAR(255) DEFAULT NULL, token VARCHAR(64) DEFAULT NULL,
            handoff_code VARCHAR(64) DEFAULT NULL, handoff_expires DATETIME DEFAULT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY uk_site_email (site_id, email), KEY idx_token (token), KEY idx_handoff (handoff_code)
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
    }

    $token   = bin2hex(random_bytes(24));
    $handoff = bin2hex(random_bytes(24));
    $expires = date('Y-m-d H:i:s', time() + 180);   // 3-minute handoff window

    $sel = $db->prepare("SELECT id FROM site_customers WHERE site_id = ? AND email = ? LIMIT 1");
    $sel->execute([$siteId, $email]);
    $existingId = $sel->fetchColumn();

    if ($existingId) {
        $db->prepare("UPDATE site_customers
                         SET token = ?, google_id = ?, handoff_code = ?, handoff_expires = ?, name = COALESCE(NULLIF(name,''), ?)
                       WHERE id = ?")
           ->execute([$token, $gid, $handoff, $expires, mb_substr($name, 0, 150), (int)$existingId]);
    } else {
        $db->prepare("INSERT INTO site_customers
                         (site_id, name, email, provider, google_id, password_hash, token, handoff_code, handoff_expires)
                       VALUES (?,?,?,?,?,?,?,?,?)")
           ->execute([$siteId, mb_substr($name, 0, 150), $email, 'google', $gid, null, $token, $handoff, $expires]);
    }

    // --- 4. hand the one-time code back to the customer's own subdomain ---
    $url = CustomerGoogleAuth::publicSiteUrl($slug) . '/account?tf_google=' . urlencode($handoff);
    if ($next !== '' && $next !== '/account') $url .= '&next=' . urlencode($next);
    header('Location: ' . $url);
    exit;
} catch (Exception $e) {
    error_log('google-auth-callback: ' . $e->getMessage());
    tf_google_back($slug, $next, '1');
}
