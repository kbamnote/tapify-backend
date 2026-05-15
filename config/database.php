<?php
/**
 * ====================================================
 * TAPIFY - Database Configuration
 * ====================================================
 * REAL Hostinger credentials pre-filled for Abid
 */

// === DATABASE CREDENTIALS (Dynamic for Railway/Hostinger) ===
define('DB_HOST', getenv('DB_HOST') ?: getenv('MYSQLHOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: getenv('MYSQLDATABASE') ?: 'u125734122_tapify');
define('DB_USER', getenv('DB_USER') ?: getenv('MYSQLUSER') ?: 'u125734122_tapify');
define('DB_PASS', getenv('DB_PASS') ?: getenv('MYSQLPASSWORD') ?: 'World@2018#');
define('DB_CHARSET', 'utf8mb4');

// === SITE CONFIGURATION ===
// Use Railway domain if available, otherwise fallback to Hostinger testing URL
define('SITE_URL', getenv('SITE_URL') ?: 'https://testing.mrprintworld.com');
define('SITE_NAME', 'Tapify');
define('UPLOAD_PATH', __DIR__ . '/../uploads/');
define('UPLOAD_URL', SITE_URL . '/backend/uploads/');

// === SECURITY ===
define('JWT_SECRET', getenv('JWT_SECRET') ?: 'tapify-secret-key-change-this-12345');
define('SESSION_LIFETIME', 86400 * 30); // 30 days

// Error reporting (turn off in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set timezone
date_default_timezone_set('Asia/Kolkata');

/**
 * Database Connection (PDO)
 */
function getDB() {
    static $pdo = null;

    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die(json_encode([
                'success' => false,
                'message' => 'Database connection failed: ' . $e->getMessage()
            ]));
        }
    }

    return $pdo;
}

// CORS Headers
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}
