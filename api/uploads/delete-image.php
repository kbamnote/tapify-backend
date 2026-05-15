<?php
/**
 * TAPIFY - Image Delete API
 * POST /backend/api/uploads/delete-image.php
 * Body: { vcard_id, type, target_id, image_id (for gallery) }
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Only POST allowed', 405);
}

$input = getInput();
$vcardId = (int)($input['vcard_id'] ?? 0);
$type = $input['type'] ?? '';
$targetId = (int)($input['target_id'] ?? 0);
$imageId = (int)($input['image_id'] ?? 0);

if (!$vcardId) sendError('vCard ID required');

try {
    $pdo = getDB();
    $userId = getCurrentUserId();

    // Verify vCard ownership
    $stmt = $pdo->prepare("SELECT id FROM vcards WHERE id = ? AND user_id = ? LIMIT 1");
    $stmt->execute([$vcardId, $userId]);
    if (!$stmt->fetch()) sendError('Access denied', 403);

    $imagePath = null;

    switch ($type) {
        case 'cover':
            $stmt = $pdo->prepare("SELECT cover_image FROM vcards WHERE id = ?");
            $stmt->execute([$vcardId]);
            $imagePath = $stmt->fetch()['cover_image'] ?? null;
            $pdo->prepare("UPDATE vcards SET cover_image = NULL WHERE id = ?")->execute([$vcardId]);
            break;

        case 'profile':
            $stmt = $pdo->prepare("SELECT profile_image FROM vcards WHERE id = ?");
            $stmt->execute([$vcardId]);
            $imagePath = $stmt->fetch()['profile_image'] ?? null;
            $pdo->prepare("UPDATE vcards SET profile_image = NULL WHERE id = ?")->execute([$vcardId]);
            break;

        case 'favicon':
            $stmt = $pdo->prepare("SELECT favicon_image FROM vcards WHERE id = ?");
            $stmt->execute([$vcardId]);
            $imagePath = $stmt->fetch()['favicon_image'] ?? null;
            $pdo->prepare("UPDATE vcards SET favicon_image = NULL WHERE id = ?")->execute([$vcardId]);
            break;

        case 'product':
            if ($targetId > 0) {
                $stmt = $pdo->prepare("SELECT image FROM vcard_products WHERE id = ? AND vcard_id = ?");
                $stmt->execute([$targetId, $vcardId]);
                $imagePath = $stmt->fetch()['image'] ?? null;
                $pdo->prepare("UPDATE vcard_products SET image = NULL WHERE id = ? AND vcard_id = ?")->execute([$targetId, $vcardId]);
            }
            break;

        case 'blog':
            if ($targetId > 0) {
                $stmt = $pdo->prepare("SELECT image FROM vcard_blogs WHERE id = ? AND vcard_id = ?");
                $stmt->execute([$targetId, $vcardId]);
                $imagePath = $stmt->fetch()['image'] ?? null;
                $pdo->prepare("UPDATE vcard_blogs SET image = NULL WHERE id = ? AND vcard_id = ?")->execute([$targetId, $vcardId]);
            }
            break;

        case 'testimonial':
            if ($targetId > 0) {
                $stmt = $pdo->prepare("SELECT image FROM vcard_testimonials WHERE id = ? AND vcard_id = ?");
                $stmt->execute([$targetId, $vcardId]);
                $imagePath = $stmt->fetch()['image'] ?? null;
                $pdo->prepare("UPDATE vcard_testimonials SET image = NULL WHERE id = ? AND vcard_id = ?")->execute([$targetId, $vcardId]);
            }
            break;

        case 'gallery':
            // For gallery, delete specific image by image_id
            if ($imageId > 0) {
                $stmt = $pdo->prepare("
                    SELECT gi.image_url
                    FROM vcard_gallery_images gi
                    JOIN vcard_galleries g ON g.id = gi.gallery_id
                    WHERE gi.id = ? AND g.vcard_id = ?
                ");
                $stmt->execute([$imageId, $vcardId]);
                $imagePath = $stmt->fetch()['image_url'] ?? null;
                $pdo->prepare("DELETE FROM vcard_gallery_images WHERE id = ?")->execute([$imageId]);
            }
            break;

        default:
            sendError('Invalid type');
    }

    // Delete actual file
    if ($imagePath) {
        $fullPath = __DIR__ . '/../../../' . $imagePath;
        if (file_exists($fullPath) && is_file($fullPath)) {
            @unlink($fullPath);
        }
    }

    sendSuccess('Image deleted');

} catch (Exception $e) {
    sendError('Delete failed: ' . $e->getMessage(), 500);
}
