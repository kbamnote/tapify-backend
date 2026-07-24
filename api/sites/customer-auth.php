<?php
/**
 * POST /api/sites/customer-auth.php   (PUBLIC)
 *
 * Signup / login for a customer of a PUBLISHED builder site (the optional
 * account system for e-commerce sites). Scoped per site — accounts never cross
 * sites and are unrelated to the Tapify dashboard user.
 *
 *   { action:"register", site, name, email, password, phone? }
 *   { action:"login",    site, email, password }
 *   { action:"me",       site, token }
 *
 * Returns { token, name, email } on success. The token is a random opaque
 * string the site stores client-side; passwords are hashed, never returned.
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../builder/lib/SiteRepo.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') sendError('Only POST allowed', 405);

$in     = getInput();
$action = trim((string)($in['action'] ?? ''));
$slug   = strtolower(trim((string)($in['site'] ?? '')));
if ($slug === '') sendError('This form is not configured correctly.');

try {
    $site = SiteRepo::findBySlug($slug);
    if (!$site || ($site['status'] ?? '') === 'disabled') sendError('This website is not available.', 404);
    if (!SiteRepo::getPublished($site)) sendError('This website is not published yet.', 400);
    $siteId = (int)$site['id'];
    $db = getDB();

    // Create the table on first use so this works before the migration is run.
    if (!$db->query("SHOW TABLES LIKE 'site_customers'")->fetchColumn()) {
        $db->exec("CREATE TABLE site_customers (
            id INT AUTO_INCREMENT PRIMARY KEY, site_id INT NOT NULL,
            name VARCHAR(150) NOT NULL, email VARCHAR(190) NOT NULL, phone VARCHAR(30) DEFAULT NULL,
            password_hash VARCHAR(255) NOT NULL, token VARCHAR(64) DEFAULT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY uk_site_email (site_id, email), KEY idx_token (token)
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
    }

    $newToken = fn() => bin2hex(random_bytes(24));

    // Redeem the one-time code minted by the Google OAuth callback for the real
    // session token. Single use, short-lived — see google-auth-callback.php.
    if ($action === 'google_exchange') {
        $code = trim((string)($in['code'] ?? ''));
        if ($code === '') sendError('Missing sign-in code', 400);
        if (!$db->query("SHOW COLUMNS FROM site_customers LIKE 'handoff_code'")->fetchColumn()) {
            sendError('Google sign-in is not set up on this site.', 400);
        }
        $st = $db->prepare("SELECT id, name, email, token FROM site_customers
                             WHERE site_id = ? AND handoff_code = ? AND handoff_expires > NOW() LIMIT 1");
        $st->execute([$siteId, $code]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        if (!$row) sendError('This sign-in link has expired. Please try again.', 401);
        // Burn the code so it can't be replayed.
        $db->prepare("UPDATE site_customers SET handoff_code = NULL, handoff_expires = NULL WHERE id = ?")
           ->execute([(int)$row['id']]);
        sendSuccess('Signed in', ['token' => $row['token'], 'name' => $row['name'], 'email' => $row['email']]);
    }

    if ($action === 'me') {
        $token = trim((string)($in['token'] ?? ''));
        if ($token === '') sendError('Not signed in', 401);
        $st = $db->prepare("SELECT name, email FROM site_customers WHERE site_id = ? AND token = ? LIMIT 1");
        $st->execute([$siteId, $token]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        if (!$row) sendError('Session expired. Please sign in again.', 401);
        sendSuccess('OK', ['name' => $row['name'], 'email' => $row['email']]);
    }

    if ($action === 'register') {
        $name  = trim((string)($in['name'] ?? ''));
        $email = strtolower(trim((string)($in['email'] ?? '')));
        $phone = trim((string)($in['phone'] ?? ''));
        $pass  = (string)($in['password'] ?? '');
        if ($name === '')  sendError('Please enter your name.');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) sendError('Please enter a valid email.');
        if (strlen($pass) < 6) sendError('Password must be at least 6 characters.');

        $st = $db->prepare("SELECT id FROM site_customers WHERE site_id = ? AND email = ? LIMIT 1");
        $st->execute([$siteId, $email]);
        if ($st->fetchColumn()) sendError('An account with this email already exists. Please sign in.');

        $token = $newToken();
        $st = $db->prepare("INSERT INTO site_customers (site_id, name, email, phone, password_hash, token)
                            VALUES (?,?,?,?,?,?)");
        $st->execute([$siteId, mb_substr($name, 0, 150), $email, mb_substr($phone, 0, 30),
                      password_hash($pass, PASSWORD_DEFAULT), $token]);
        sendSuccess('Account created', ['token' => $token, 'name' => $name, 'email' => $email]);
    }

    if ($action === 'login') {
        $email = strtolower(trim((string)($in['email'] ?? '')));
        $pass  = (string)($in['password'] ?? '');
        if ($email === '' || $pass === '') sendError('Enter your email and password.');

        $st = $db->prepare("SELECT id, name, password_hash FROM site_customers WHERE site_id = ? AND email = ? LIMIT 1");
        $st->execute([$siteId, $email]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        if (!$row || !password_verify($pass, $row['password_hash'])) sendError('Wrong email or password.', 401);

        $token = $newToken();
        $db->prepare("UPDATE site_customers SET token = ? WHERE id = ?")->execute([$token, (int)$row['id']]);
        sendSuccess('Signed in', ['token' => $token, 'name' => $row['name'], 'email' => $email]);
    }

    sendError('Unknown action', 400);
} catch (Exception $e) {
    error_log('customer-auth: ' . $e->getMessage());
    sendError('Something went wrong. Please try again.', 500);
}
