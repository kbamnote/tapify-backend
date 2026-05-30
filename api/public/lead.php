<?php
/**
 * TAPIFY - Public Website Lead Capture
 * POST /api/public/lead.php
 *
 * No auth required — saves a prospective customer lead.
 * Auto-creates the website_leads table if it doesn't exist.
 *
 * Body (JSON or form-data):
 *   name     string  required
 *   phone    string  required
 *   email    string  optional
 *   city     string  optional
 *   source   string  optional  (e.g. "hero_cta", "sticky_cta", "demo_generator")
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST')    { sendError('POST only', 405); }

$input = getInput();

$name   = sanitize($input['name']   ?? '');
$phone  = sanitize($input['phone']  ?? '');
$email  = sanitize($input['email']  ?? '');
$city   = sanitize($input['city']   ?? '');
$source = sanitize($input['source'] ?? 'website');

if (empty($name))  sendError('Name is required');
if (empty($phone)) sendError('Phone number is required');
if (strlen($name)  > 150) sendError('Name too long');
if (strlen($phone) > 20)  sendError('Phone too long');
if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) sendError('Invalid email address');

try {
    $pdo = getDB();

    // Auto-create table if missing
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS website_leads (
            id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name        VARCHAR(150) NOT NULL,
            phone       VARCHAR(20)  NOT NULL,
            email       VARCHAR(150) DEFAULT NULL,
            city        VARCHAR(100) DEFAULT NULL,
            source      VARCHAR(80)  DEFAULT 'website',
            ip          VARCHAR(45)  DEFAULT NULL,
            created_at  DATETIME     DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");

    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? null;

    $stmt = $pdo->prepare("
        INSERT INTO website_leads (name, phone, email, city, source, ip, created_at)
        VALUES (?, ?, ?, ?, ?, ?, NOW())
    ");
    $stmt->execute([$name, $phone, $email ?: null, $city ?: null, $source, $ip]);

    sendSuccess('Thank you! We will contact you shortly.', ['id' => (int)$pdo->lastInsertId()]);

} catch (Exception $e) {
    sendError('Something went wrong. Please try again.', 500);
}
