<?php
/**
 * CRM Bridge — Create Tapify User + vCard
 * Called by salescrm-pro backend. Secured by X-CRM-API-Key header.
 */
require_once '../../config/database.php';
require_once '../../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Method not allowed', 405);
}

// API key auth — shared secret between salescrm-pro and tapify-backend
$apiKey = $_SERVER['HTTP_X_CRM_API_KEY'] ?? '';
$expectedKey = getenv('CRM_API_KEY') ?: 'changeme-set-CRM_API_KEY-env';
if (!$apiKey || $apiKey !== $expectedKey) {
    sendError('Unauthorized', 401);
}

$input = getInput();

$name        = sanitize($input['name'] ?? '');
$email       = sanitize($input['email'] ?? '');
$password    = $input['password'] ?? '';
$phone       = sanitize($input['phone'] ?? '');
$vcardName   = sanitize($input['vcard_name'] ?? $name);
$occupation  = sanitize($input['occupation'] ?? '');
$firstName   = sanitize($input['first_name'] ?? '');
$lastName    = sanitize($input['last_name'] ?? '');
$company     = sanitize($input['company'] ?? '');
$description = sanitize($input['description'] ?? '');
$templateId  = sanitize($input['template_id'] ?? 'vcard46');
// Optional business-card content (CRM page-2 details). Kept separate from the
// account fields: vcard_email is the card's PUBLIC email (the login stays
// `email`); profile_image is the business logo (may be a full Cloudinary URL).
$vcardEmail   = sanitize($input['vcard_email'] ?? '');
$profileImage = trim($input['profile_image'] ?? '');

if (!$name)                          sendError('name is required');
if (!$email || !isValidEmail($email)) sendError('Valid email is required');
if (!$password || strlen($password) < 6) sendError('password must be at least 6 characters');
if (!$vcardName) $vcardName = $name;

$pdo = getDB();

try {
    $pdo->beginTransaction();

    // Check email uniqueness
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $pdo->rollBack();
        sendError('Email already registered in Tapify', 409);
    }

    // Create user
    $stmt = $pdo->prepare(
        "INSERT INTO users (name, email, password, phone, role, email_verified, status)
         VALUES (?, ?, ?, ?, 'user', 0, 1)"
    );
    $stmt->execute([$name, $email, hashPassword($password), $phone]);
    $userId = (int) $pdo->lastInsertId();

    // Free Trial subscription (30 days)
    $stmt = $pdo->prepare(
        "INSERT INTO subscriptions (user_id, plan_name, vcards_limit, stores_limit, price, subscribed_date, expiry_date, status)
         VALUES (?, 'Free Trial', 1, 0, 0, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 30 DAY), 'active')"
    );
    $stmt->execute([$userId]);

    // Generate unique URL alias
    $urlAlias = generateUniqueAlias($vcardName);

    // Create vCard
    $stmt = $pdo->prepare(
        "INSERT INTO vcards (user_id, url_alias, vcard_name, occupation, description, first_name, last_name, email, phone, company, profile_image, template_id, status)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)"
    );
    $stmt->execute([$userId, $urlAlias, $vcardName, $occupation, $description, $firstName, $lastName, ($vcardEmail ?: $email), $phone, $company, $profileImage, $templateId]);
    $vcardId = (int) $pdo->lastInsertId();

    // Default business hours (Mon-Sat open, Sun closed)
    $hoursStmt = $pdo->prepare(
        "INSERT INTO vcard_business_hours (vcard_id, day_name, is_open, open_time, close_time)
         VALUES (?, ?, ?, '10:00 AM', '06:00 PM')"
    );
    foreach (['MONDAY','TUESDAY','WEDNESDAY','THURSDAY','FRIDAY','SATURDAY','SUNDAY'] as $day) {
        $hoursStmt->execute([$vcardId, $day, $day === 'SUNDAY' ? 0 : 1]);
    }

    $pdo->commit();

    sendSuccess('Tapify profile created successfully', [
        'user'  => ['id' => $userId, 'name' => $name, 'email' => $email, 'phone' => $phone],
        'vcard' => ['id' => $vcardId, 'user_id' => $userId, 'url_alias' => $urlAlias, 'preview_url' => public_card_url($urlAlias)]
    ]);

} catch (Exception $e) {
    $pdo->rollBack();
    sendError('Failed to create profile: ' . $e->getMessage(), 500);
}
