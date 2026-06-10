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

    // Get galleries with image count
    $stmt = $pdo->prepare("
        SELECT g.*, COUNT(gi.id) AS image_count
        FROM vcard_galleries g
        LEFT JOIN vcard_gallery_images gi ON gi.gallery_id = g.id
        WHERE g.vcard_id = ?
        GROUP BY g.id
        ORDER BY g.display_order, g.id
    ");
    $stmt->execute([$vcardId]);
    $galleries = $stmt->fetchAll();

    foreach ($galleries as &$g) {
        $g['image_count'] = (int)$g['image_count'];
    }

    sendSuccess('Galleries loaded', ['galleries' => $galleries]);
} catch (Exception $e) {
    sendError('Failed: ' . $e->getMessage(), 500);
}
