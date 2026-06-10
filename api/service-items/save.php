<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') sendError('Only POST allowed', 405);

$input = getInput();
$itemId = (int)($input['id'] ?? 0);
$categoryId = (int)($input['category_id'] ?? 0);
$name = sanitize($input['name'] ?? '');

if (!$categoryId) sendError('Category ID required');
if (empty($name)) sendError('Service name is required');

try {
    $pdo = getDB();
    $userId = getCurrentUserId();

    // Verify the category belongs to a vCard the current user may edit (admins: any)
    if (isAdmin()) {
        $stmt = $pdo->prepare("SELECT id FROM vcard_service_categories WHERE id = ? LIMIT 1");
        $stmt->execute([$categoryId]);
    } else {
        $stmt = $pdo->prepare("
            SELECT sc.id FROM vcard_service_categories sc
            JOIN vcards v ON v.id = sc.vcard_id
            WHERE sc.id = ? AND v.user_id = ? LIMIT 1
        ");
        $stmt->execute([$categoryId, $userId]);
    }
    if (!$stmt->fetch()) sendError('Access denied', 403);

    if ($itemId > 0) {
        $stmt = $pdo->prepare("UPDATE vcard_service_items SET name = ? WHERE id = ? AND category_id = ?");
        $stmt->execute([$name, $itemId, $categoryId]);
        sendSuccess('Service updated', ['id' => $itemId]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO vcard_service_items (category_id, name) VALUES (?, ?)");
        $stmt->execute([$categoryId, $name]);
        sendSuccess('Service added', ['id' => $pdo->lastInsertId()]);
    }
} catch (Exception $e) {
    sendError('Failed: ' . $e->getMessage(), 500);
}
