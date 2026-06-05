<?php
/**
 * ====================================================
 * TAPIFY - Database Configuration
 * ====================================================
 * REAL Hostinger credentials pre-filled for Abid
 */

// === SITE CONFIGURATION ===
define('SITE_URL', getenv('SITE_URL') ?: 'https://app.tapify.co.in');
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

if (session_status() === PHP_SESSION_NONE) {
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
