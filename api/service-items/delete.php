<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
requireAuth();
blockStaffDelete(); // card-editor accounts may not delete

if ($_SERVER['REQUEST_METHOD'] !== 'POST') sendError('Only POST allowed', 405);

$input = getInput();
$id = (int)($input['id'] ?? 0);

if (!$id) sendError('Item ID required');

try {
    $pdo = getDB();
    $userId = getCurrentUserId();

    // Verify the item belongs to a vCard the current user may edit (admins: any)
    if (isAdmin()) {
        $stmt = $pdo->prepare("SELECT id FROM vcard_service_items WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
    } else {
        $stmt = $pdo->prepare("
            SELECT si.id FROM vcard_service_items si
            JOIN vcard_service_categories sc ON sc.id = si.category_id
            JOIN vcards v ON v.id = sc.vcard_id
            WHERE si.id = ? AND v.user_id = ? LIMIT 1
        ");
        $stmt->execute([$id, $userId]);
    }
    if (!$stmt->fetch()) sendError('Access denied', 403);

    $stmt = $pdo->prepare("DELETE FROM vcard_service_items WHERE id = ?");
    $stmt->execute([$id]);

    if ($stmt->rowCount() === 0) sendError('Not found', 404);
    sendSuccess('Service deleted');
} catch (Exception $e) {
    sendError('Failed: ' . $e->getMessage(), 500);
}
