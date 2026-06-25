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

// === SECURITY ===
define('JWT_SECRET', getenv('JWT_SECRET') ?: 'tapify-secret-key-12345');
define('JWT_ALGO', 'HS256');
define('TOKEN_EXPIRY', 86400 * 7);

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
$origin = $_SERVER['HTTP_ORIGIN'] ?? '*';
header("Access-Control-Allow-Origin: $origin");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Access-Control-Allow-Credentials: true');

// Handle preflight OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}
