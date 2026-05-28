<?php
/**
 * TAPIFY - Save / Update My Business Profile (upsert)
 * POST /api/business/save.php
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') sendError('Only POST allowed', 405);

$input = getInput();
$userId = getCurrentUserId();

$businessName = sanitize($input['business_name'] ?? '');
$gstin        = strtoupper(sanitize($input['gstin'] ?? ''));
$category     = sanitize($input['category'] ?? '');
$description  = sanitize($input['description'] ?? '');
$city         = sanitize($input['city'] ?? '');
$website      = sanitize($input['website'] ?? '');
$phone        = sanitize($input['phone'] ?? '');
$listed       = isset($input['listed']) ? (int)(bool)$input['listed'] : 1;

// Validation
if (empty($businessName))           sendError('Business name is required');
if (strlen($businessName) > 200)    sendError('Business name too long (max 200 characters)');
if (strlen($description) > 300)     sendError('Description too long (max 300 characters)');

// GSTIN format: 2 digits + 5 uppercase letters + 4 digits + 1 uppercase + 1 alphanumeric + Z + 1 alphanumeric
if (!empty($gstin) && !preg_match('/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/', $gstin)) {
    sendError('Invalid GSTIN format. Expected: 22AAAAA0000A1Z5 (15 characters)');
}

if (!empty($website) && !filter_var($website, FILTER_VALIDATE_URL)) {
    sendError('Invalid website URL. Must start with http:// or https://');
}

try {
    $pdo = getDB();

    // Check if record exists for this user
    $stmt = $pdo->prepare("SELECT id FROM businesses WHERE user_id = ?");
    $stmt->execute([$userId]);
    $existing = $stmt->fetch();

    if ($existing) {
        $stmt = $pdo->prepare("
            UPDATE businesses
            SET business_name = ?,
                gstin         = ?,
                category      = ?,
                description   = ?,
                city          = ?,
                website       = ?,
                phone         = ?,
                listed        = ?
            WHERE user_id = ?
        ");
        $stmt->execute([
            $businessName,
            $gstin       ?: null,
            $category    ?: null,
            $description ?: null,
            $city        ?: null,
            $website     ?: null,
            $phone       ?: null,
            $listed,
            $userId,
        ]);
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO businesses
                (user_id, business_name, gstin, category, description, city, website, phone, listed)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $userId,
            $businessName,
            $gstin       ?: null,
            $category    ?: null,
            $description ?: null,
            $city        ?: null,
            $website     ?: null,
            $phone       ?: null,
            $listed,
        ]);
    }

    sendSuccess('Business profile saved successfully');

} catch (Exception $e) {
    sendError('Failed: ' . $e->getMessage(), 500);
}
