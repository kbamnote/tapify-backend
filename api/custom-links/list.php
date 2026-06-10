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

    $stmt = $pdo->prepare("SELECT * FROM vcard_custom_links WHERE vcard_id = ? ORDER BY display_order, id");
    $stmt->execute([$vcardId]);
    $links = $stmt->fetchAll();

    sendSuccess('Custom links loaded', ['links' => $links]);
} catch (Exception $e) {
    sendError('Failed: ' . $e->getMessage(), 500);
}
