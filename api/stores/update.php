<?php
/**
 * TAPIFY - Update Store
 * POST /backend/api/stores/update.php
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Only POST allowed', 405);
}

$input = getInput();
$id = (int)($input['id'] ?? 0);
if (!$id) sendError('Store ID required');

try {
    $pdo = getDB();
    $userId = getCurrentUserId();

    // Verify ownership
    $stmt = $pdo->prepare("SELECT id, url_alias FROM whatsapp_stores WHERE id = ? AND user_id = ? LIMIT 1");
    $stmt->execute([$id, $userId]);
    $existing = $stmt->fetch();
    if (!$existing) sendError('Store not found or access denied', 403);

    // Whitelist of allowed fields
    $allowed = [
        'url_alias', 'store_name', 'owner_name', 'whatsapp_number',
        'email', 'phone', 'address', 'location', 'location_url',
        'tagline', 'description', 'currency', 'currency_symbol',
        'min_order_amount', 'delivery_charge', 'cod_available',
        'show_search', 'show_categories', 'show_featured',
        'order_message_template', 'primary_color', 'secondary_color',
        'template_id'
    ];

    $updates = [];
    $values = [];

    foreach ($input as $key => $val) {
        if (in_array($key, $allowed)) {
            // Special handling
            if ($key === 'url_alias') {
                $newAlias = sanitize($val);
                if (!preg_match('/^[a-z0-9-]+$/', $newAlias)) {
                    sendError('Invalid URL format');
                }
                if ($newAlias !== $existing['url_alias']) {
                    // Check uniqueness
                    $checkStmt = $pdo->prepare("SELECT id FROM whatsapp_stores WHERE url_alias = ? AND id != ? LIMIT 1");
                    $checkStmt->execute([$newAlias, $id]);
                    if ($checkStmt->fetch()) sendError('URL already taken');

                    $checkStmt = $pdo->prepare("SELECT id FROM vcards WHERE url_alias = ? LIMIT 1");
                    $checkStmt->execute([$newAlias]);
                    if ($checkStmt->fetch()) sendError('URL conflicts with a vCard');
                }
                $val = $newAlias;
            } elseif ($key === 'whatsapp_number') {
                $val = preg_replace('/\D/', '', $val);
            } elseif (in_array($key, ['cod_available', 'show_search', 'show_categories', 'show_featured'])) {
                $val = $val ? 1 : 0;
            }

            $updates[] = "`$key` = ?";
            $values[] = $val;
        }
    }

    if (empty($updates)) sendError('No valid fields to update');

    $values[] = $id;
    $values[] = $userId;

    $sql = "UPDATE whatsapp_stores SET " . implode(', ', $updates) . " WHERE id = ? AND user_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($values);

    sendSuccess('Store updated successfully');

} catch (Exception $e) {
    sendError('Failed: ' . $e->getMessage(), 500);
}
