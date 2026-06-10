<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
requireAuth();

$vcardId = (int)($_GET['vcard_id'] ?? 0);
if (!$vcardId) sendError('vCard ID required');

try {
    $pdo = getDB();
    $userId = getCurrentUserId();

    if (!userCanEditVcard($pdo, $vcardId)) sendError('Access denied', 403);

    $stmt = $pdo->prepare("SELECT * FROM vcard_service_categories WHERE vcard_id = ? ORDER BY display_order, id");
    $stmt->execute([$vcardId]);
    $categories = $stmt->fetchAll();

    foreach ($categories as &$c) {
        $itemStmt = $pdo->prepare("SELECT * FROM vcard_service_items WHERE category_id = ? ORDER BY display_order, id");
        $itemStmt->execute([$c['id']]);
        $c['items'] = $itemStmt->fetchAll();
    }
    unset($c);

    sendSuccess('Service categories loaded', ['categories' => $categories]);
} catch (Exception $e) {
    sendError('Failed: ' . $e->getMessage(), 500);
}
