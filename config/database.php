<?php
/**
 * ====================================================
 * TAPIFY - Database Configuration
 * ====================================================
 * REAL Hostinger credentials pre-filled for Abid
 */

// === SITE CONFIGURATION ===
// SITE_URL = backend origin (serves assets, uploads, API). Keep on app.* subdomain.
define('SITE_URL', getenv('SITE_URL') ?: 'https://app.tapify.co.in');
// PUBLIC_URL = public-facing domain used in share links and QR codes.
define('PUBLIC_URL', getenv('PUBLIC_URL') ?: 'https://tapify.co.in');
// PUBLIC_BASE_DOMAIN = apex domain that hosts the wildcard business subdomains
// (e.g. <slug>.tapify.co.in). Keep in sync with the wildcard DNS record / TLS cert.
define('PUBLIC_BASE_DOMAIN', getenv('PUBLIC_BASE_DOMAIN') ?: 'tapify.co.in');
// USE_SUBDOMAIN_URLS: when true, public share links are rendered in the subdomain
// form (<slug>.tapify.co.in). Subdomain ROUTING works regardless of this flag; it
// only controls which URL is advertised by the dashboard/API. Set to '0' until the
// wildcard DNS record + wildcard TLS cert are live, then flip to '1'.
define('USE_SUBDOMAIN_URLS', (getenv('USE_SUBDOMAIN_URLS') ?: '1') === '1');
define('SITE_NAME', 'Tapify');
define('UPLOAD_PATH', __DIR__ . '/../uploads/');
define('UPLOAD_URL', SITE_URL . '/backend/uploads/');
 
// === CLOUDINARY CONFIGURATION ===
define('CLOUDINARY_CLOUD_NAME', getenv('CLOUDINARY_CLOUD_NAME') ?: 'dpawkimbc');
define('CLOUDINARY_API_KEY', getenv('CLOUDINARY_API_KEY') ?: '222713572894261');
define('CLOUDINARY_API_SECRET', getenv('CLOUDINARY_API_SECRET') ?: 'ywysF7LQEMa1kuZUQORCbUdCGi0');

// === AI CONFIGURATION ===
define('OPENROUTER_API_KEY', getenv('OPENROUTER_API_KEY') ?: '');

// --- AI Growth Center (provider-independent AI layer) ---
// Which provider the AiProviderFactory should build by default. One of:
// 'gemini' | 'openai' | 'claude' | 'openrouter'. Switch providers here (or via
// the AI_PROVIDER env var) WITHOUT touching any business logic.
define('AI_PROVIDER', getenv('AI_PROVIDER') ?: 'gemini');

// Google Gemini (default). Get a key at https://aistudio.google.com/apikey
define('GEMINI_API_KEY', getenv('GEMINI_API_KEY') ?: '');
define('GEMINI_MODEL',   getenv('GEMINI_MODEL')   ?: 'gemini-2.5-flash');

// OpenAI (optional — future ready; only used when AI_PROVIDER=openai)
define('OPENAI_API_KEY', getenv('OPENAI_API_KEY') ?: '');
define('OPENAI_MODEL',   getenv('OPENAI_MODEL')   ?: 'gpt-4o-mini');

// Anthropic Claude (optional — future ready; only used when AI_PROVIDER=claude)
define('ANTHROPIC_API_KEY', getenv('ANTHROPIC_API_KEY') ?: '');
define('ANTHROPIC_MODEL',   getenv('ANTHROPIC_MODEL')   ?: 'claude-haiku-4-5-20251001');

// Shared HTTP behaviour for all AI providers.
define('AI_HTTP_TIMEOUT', (int) (getenv('AI_HTTP_TIMEOUT') ?: 45));   // seconds per request
define('AI_MAX_RETRIES',  (int) (getenv('AI_MAX_RETRIES')  ?: 2));    // retries on 429/5xx/timeouts

// Per-user guardrails on LIVE generations (cached results don't count) so a
// single account can't drive unbounded paid provider spend.
define('AI_RATE_PER_MIN', (int) (getenv('AI_RATE_PER_MIN') ?: 15));   // live calls / minute
define('AI_RATE_PER_DAY', (int) (getenv('AI_RATE_PER_DAY') ?: 200));  // live calls / day

// === GOOGLE BUSINESS PROFILE (Google My Business) ===
// OAuth 2.0 Web client credentials from Google Cloud Console. The redirect URI
// registered there MUST match GOOGLE_OAUTH_REDIRECT exactly.
define('GOOGLE_CLIENT_ID',     getenv('GOOGLE_CLIENT_ID')     ?: '');
define('GOOGLE_CLIENT_SECRET', getenv('GOOGLE_CLIENT_SECRET') ?: '');
define('GOOGLE_OAUTH_REDIRECT', getenv('GOOGLE_OAUTH_REDIRECT') ?: (SITE_URL . '/api/google/gbp/callback.php'));
// Restricted scope — requires OAuth verification + Business Profile API allowlisting.
define('GOOGLE_BUSINESS_SCOPE', 'https://www.googleapis.com/auth/business.manage');
// Deep link the OAuth callback bounces back to so the app knows we're done.
define('APP_DEEP_LINK', getenv('APP_DEEP_LINK') ?: 'tapifapp://gbp-connected');

// === SECURITY ===
define('JWT_SECRET', getenv('JWT_SECRET') ?: 'tapify-secret-key-12345');
define('JWT_ALGO', 'HS256');
define('TOKEN_EXPIRY', 86400 * 7);

// === WHATSAPP CLOUD API ===
// Verify token — must match the token you enter in Meta's webhook configuration.
// Set this as an environment variable (no hardcoded default).
define('WHATSAPP_VERIFY_TOKEN', getenv('WHATSAPP_VERIFY_TOKEN') ?: '');
// Cloud API sender — Phone number ID + permanent access token (Tapify number).
// Set both as environment variables; without them WhatsApp sends are skipped.
define('WHATSAPP_PHONE_ID', getenv('WHATSAPP_PHONE_ID') ?: '');
define('WHATSAPP_ACCESS_TOKEN', getenv('WHATSAPP_ACCESS_TOKEN') ?: '');
define('WHATSAPP_WABA_ID', getenv('WHATSAPP_WABA_ID') ?: '');

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Kolkata');

/**
 * Hosts that are NOT business mini-sites and must never be treated as a slug.
 */
function tapify_reserved_subdomains() {
    return ['app','www','api','admin','dashboard','login','mail','m','cdn',
            'static','assets','ftp','smtp','ns1','ns2','webmail','cpanel'];
}

/**
 * Extract a business url_alias from a wildcard-subdomain hostname.
 *   "business-slug.tapify.co.in"  =>  "business-slug"
 * Returns '' for the apex, reserved hosts (app/www/api/...), multi-label
 * subdomains, or any host that is not directly under PUBLIC_BASE_DOMAIN.
 */
function tapify_subdomain_slug($host) {
    $host = strtolower(trim((string)$host));
    $host = preg_replace('/:\d+$/', '', $host);            // strip :port
    $base = '.' . PUBLIC_BASE_DOMAIN;
    if ($host === '' || substr($host, -strlen($base)) !== $base) return '';
    $sub = substr($host, 0, -strlen($base));
    if ($sub === '' || strpos($sub, '.') !== false) return '';   // single label only
    if (in_array($sub, tapify_reserved_subdomains(), true)) return '';
    return $sub;
}

/**
 * Canonical public URL for a mini-site, given its url_alias.
 * Subdomain form (https://slug.tapify.co.in) when USE_SUBDOMAIN_URLS is on and the
 * slug is a valid DNS label; otherwise the legacy path form
 * (https://tapify.co.in/slug), which always keeps working for backward compat.
 */
function public_card_url($alias) {
    $alias = ltrim((string)$alias, '/');
    if (USE_SUBDOMAIN_URLS && $alias !== ''
        && preg_match('/^[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$/i', $alias)) {
        return 'https://' . $alias . '.' . PUBLIC_BASE_DOMAIN;
    }
    return rtrim(PUBLIC_URL, '/') . '/' . $alias;
}

/**
 * Database Connection (PDO)
 */
function getDB() {
    // Railway defaults or custom variables
    $host = getenv('DB_HOST') ?: getenv('MYSQLHOST');
    $port = getenv('DB_PORT') ?: getenv('MYSQLPORT');
    $database = getenv('DB_DATABASE') ?: getenv('MYSQLDATABASE');
    $username = getenv('DB_USERNAME') ?: getenv('MYSQLUSER');
    $password = getenv('DB_PASSWORD') ?: getenv('MYSQLPASSWORD');

    // Default to localhost if nothing found (for local testing)
    $host = $host ?: 'localhost';
    $port = $port ?: '3306';

    $dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";

    try {
        $pdo = new PDO(
            $dsn,
            $username,
            $password,
            [
                PDO::ATTR_TIMEOUT => 10,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
            ]
        );

        return $pdo;

    } catch (PDOException $e) {
        die(json_encode([
            "success" => false,
            "message" => "Database connection failed (Host: {$host}): " . $e->getMessage()
        ]));
    }
}

// === CORS & SESSIONS ===
// Set session cookie params for cross-domain compatibility
// Railway uses a reverse proxy — trust X-Forwarded-Proto for HTTPS detection
$isSecure = (
    (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ||
    (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
);

// Persistent session for 1 year
$lifetime = 60 * 60 * 24 * 365;
ini_set('session.gc_maxlifetime', $lifetime);
ini_set('session.cookie_lifetime', $lifetime);

ini_set('session.cookie_samesite', 'None');
ini_set('session.cookie_secure', 'True'); // Always True — Railway always serves HTTPS
ini_set('session.cookie_httponly', 'True');

// Persist sessions in the DB so logins survive container redeploys/restarts.
// Railway's filesystem is ephemeral, so default file sessions are wiped on every
// deploy (causing the re-login). Falls back to file sessions if the table is
// missing, so deploying this before running migration_db_sessions.sql is safe.
require_once __DIR__ . '/../includes/db_session.php';
if (session_status() === PHP_SESSION_NONE) {
    if (DBSessionHandler::tableExists()) {
        session_set_save_handler(new DBSessionHandler(), true);
    }
    session_start();
}

// CORS Headers
// SECURITY: only reflect the Origin + allow credentials (cookies) for trusted
// Tapify origins. Reflecting ANY origin together with Allow-Credentials:true lets
// any site the logged-in user visits make credentialed calls and read the
// response (CSRF + data exfiltration). Untrusted origins get non-credentialed
// wildcard access, which still serves public endpoints but blocks cookie-bearing
// cross-origin reads. Add extra browser origins via CORS_ALLOWED_ORIGINS (CSV).
$corsAllowed = array_filter(array_map('trim', explode(',', getenv('CORS_ALLOWED_ORIGINS') ?: '')));
if (!$corsAllowed) {
    $corsAllowed = [SITE_URL, PUBLIC_URL]; // https://app.tapify.co.in, https://tapify.co.in
}
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
$corsTrusted = false;
if ($origin !== '') {
    if (in_array($origin, $corsAllowed, true)) {
        $corsTrusted = true;
    } else {
        // Any wildcard business subdomain, e.g. https://<slug>.tapify.co.in
        $host = parse_url($origin, PHP_URL_HOST);
        $base = '.' . PUBLIC_BASE_DOMAIN;
        if ($host && ($host === PUBLIC_BASE_DOMAIN || substr($host, -strlen($base)) === $base)) {
            $corsTrusted = true;
        }
    }
}
if ($corsTrusted) {
    header("Access-Control-Allow-Origin: $origin");
    header('Access-Control-Allow-Credentials: true');
    header('Vary: Origin');
} else {
    // Public, non-credentialed access only (cookies are ignored by the browser).
    header('Access-Control-Allow-Origin: *');
}
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

// Handle preflight OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}
