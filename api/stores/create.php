<?php
/**
 * TAPIFY - Create WhatsApp Store
 * POST /backend/api/stores/create.php
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Only POST allowed', 405);
}

$input = getInput();
$storeName = sanitize($input['store_name'] ?? '');
$urlAlias = sanitize($input['url_alias'] ?? '');
$ownerName = sanitize($input['owner_name'] ?? '');
$whatsappNumber = sanitize($input['whatsapp_number'] ?? '');
$email = sanitize($input['email'] ?? '');
$phone = sanitize($input['phone'] ?? '');
$tagline = sanitize($input['tagline'] ?? '');
$description = $input['description'] ?? '';

if (empty($storeName)) sendError('Store name is required');
if (empty($whatsappNumber)) sendError('WhatsApp number is required');
if (strlen($storeName) > 200) sendError('Store name too long');

// Auto-generate URL if empty
if (empty($urlAlias)) {
    $urlAlias = generateSlug($storeName);
}

// Validate URL
if (!preg_match('/^[a-z0-9-]+$/', $urlAlias)) {
    sendError('URL can only contain lowercase letters, numbers, and hyphens');
}

// Clean WhatsApp number (only digits)
$whatsappNumber = preg_replace('/\D/', '', $whatsappNumber);
if (strlen($whatsappNumber) < 10) sendError('Invalid WhatsApp number');

try {
    $pdo = getDB();
    $userId = getCurrentUserId();

    // Check URL uniqueness across both vcards and stores
    $stmt = $pdo->prepare("SELECT id FROM whatsapp_stores WHERE url_alias = ? LIMIT 1");
    $stmt->execute([$urlAlias]);
    if ($stmt->fetch()) sendError('This URL is already taken. Please choose another.');

    $stmt = $pdo->prepare("SELECT id FROM vcards WHERE url_alias = ? LIMIT 1");
    $stmt->execute([$urlAlias]);
    if ($stmt->fetch()) sendError('This URL is already used by a vCard. Please choose another.');

    // Default WhatsApp order template
    $defaultTemplate = "🛍️ NEW ORDER\n\nName: {customer_name}\nPhone: {customer_phone}\n\n{items}\n\nTotal: {total}\n\nThank you!";

    // Insert
    $stmt = $pdo->prepare("INSERT INTO whatsapp_stores
        (user_id, url_alias, store_name, owner_name, whatsapp_number, email, phone,
         tagline, description, order_message_template, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)");
    $stmt->execute([
        $userId, $urlAlias, $storeName, $ownerName, $whatsappNumber,
        $email, $phone, $tagline, $description, $defaultTemplate
    ]);

    $storeId = $pdo->lastInsertId();

    sendSuccess('Store created successfully', [
        'store_id' => $storeId,
        'url_alias' => $urlAlias,
        'preview_url' => SITE_URL . '/' . $urlAlias
    ]);

} catch (Exception $e) {
    sendError('Failed: ' . $e->getMessage(), 500);
}
