<?php
/**
 * TAPIFY - Gallery Images List
 * GET /backend/api/uploads/gallery-images.php?gallery_id=X
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

requireAuth();

$galleryId = (int)($_GET['gallery_id'] ?? 0);
if (!$galleryId) sendError('Gallery ID required');

try {
    $pdo = getDB();
    $userId = getCurrentUserId();

    // Verify ownership through gallery → vcard chain
    $stmt = $pdo->prepare("
        SELECT g.id, g.name, g.vcard_id
        FROM vcard_galleries g
        JOIN vcards v ON v.id = g.vcard_id
        WHERE g.id = ? AND v.user_id = ?
        LIMIT 1
    ");
    $stmt->execute([$galleryId, $userId]);
    $gallery = $stmt->fetch();
    if (!$gallery) sendError('Access denied', 403);

    // Get images
    $stmt = $pdo->prepare("SELECT * FROM vcard_gallery_images WHERE gallery_id = ? ORDER BY display_order, id");
    $stmt->execute([$galleryId]);
    $images = $stmt->fetchAll();

    // Add public URL
    foreach ($images as &$img) {
        $img['public_url'] = SITE_URL . '/' . $img['image_url'];
    }

    sendSuccess('Images loaded', [
        'gallery' => $gallery,
        'images' => $images,
        'total' => count($images)
    ]);

} catch (Exception $e) {
    sendError('Failed: ' . $e->getMessage(), 500);
}
