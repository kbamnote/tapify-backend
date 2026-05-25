<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

$data = getInput();

if (!isset($data['id']) || !isset($data['vcard_id'])) {
    sendError('ID and vCard ID are required');
}

try {
    $pdo = getDB();
    $stmt = $pdo->prepare("DELETE FROM vcard_iframes WHERE id = ? AND vcard_id = ?");
    $stmt->execute([$data['id'], $data['vcard_id']]);
    
    if ($stmt->rowCount() > 0) {
        sendSuccess('Iframe deleted successfully');
    } else {
        sendError('Iframe not found or permission denied', 404);
    }
} catch (PDOException $e) {
    sendError('Database error: ' . $e->getMessage(), 500);
}
