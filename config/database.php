<?php
/**
 * ====================================================
 * TAPIFY - Database Configuration
 * ====================================================
 * REAL Hostinger credentials pre-filled for Abid
 */

// === SITE CONFIGURATION ===
define('SITE_URL', getenv('SITE_URL') ?: 'https://tapify-backend-production.up.railway.app');
define('SITE_NAME', 'Tapify');
define('UPLOAD_PATH', __DIR__ . '/../uploads/');
define('UPLOAD_URL', SITE_URL . '/backend/uploads/');

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
    $host = getenv('DB_HOST');
    $port = getenv('DB_PORT');
    $database = getenv('DB_DATABASE');
    $username = getenv('DB_USERNAME');
    $password = getenv('DB_PASSWORD');

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

// CORS Headers
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}
