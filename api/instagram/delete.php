<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
requireAuth();
blockStaffDelete(); // card-editor accounts may not delete

$data = getInput();

if (!isset($data['id']) || !isset($data['vcard_id'])) {
    sendError('ID and vCard ID are required');
}

try {
    $pdo = getDB();

    if (!userCanEditVcard($pdo, (int)$data['vcard_id'])) sendError('Access denied', 403);

    $stmt = $pdo->prepare("DELETE FROM vcard_instagram_feeds WHERE id = ? AND vcard_id = ?");
    $stmt->execute([$data['id'], $data['vcard_id']]);
    
    if ($stmt->rowCount() > 0) {
        sendSuccess('Feed deleted successfully');
    } else {
        sendError('Feed not found or permission denied', 404);
    }
} catch (PDOException $e) {
    sendError('Database error: ' . $e->getMessage(), 500);
}
