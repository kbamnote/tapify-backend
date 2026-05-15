<?php
/**
 * TAPIFY - Services List API
 * GET /backend/api/services/list.php?vcard_id=X
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

requireAuth();

$vcardId = (int)($_GET['vcard_id'] ?? 0);
if (!$vcardId) sendError('vCard ID required');

try {
    $pdo = getDB();
    $userId = getCurrentUserId();

    // Verify vCard ownership
    $stmt = $pdo->prepare("SELECT id FROM vcards WHERE id = ? AND user_id = ? LIMIT 1");
    $stmt->execute([$vcardId, $userId]);
    if (!$stmt->fetch()) sendError('Access denied', 403);

    // Get services
    $stmt = $pdo->prepare("SELECT * FROM vcard_services WHERE vcard_id = ? ORDER BY display_order ASC, id ASC");
    $stmt->execute([$vcardId]);
    $services = $stmt->fetchAll();

    sendSuccess('Services loaded', [
        'services' => $services,
        'total' => count($services)
    ]);

} catch (Exception $e) {
    sendError('Failed: ' . $e->getMessage(), 500);
}
