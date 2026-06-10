<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
requireAuth();

$data = getInput();

if (!isset($data['vcard_id']) || empty($data['url'])) {
    sendError('vCard ID and URL are required');
}

try {
    $pdo = getDB();

    if (!userCanEditVcard($pdo, (int)$data['vcard_id'])) sendError('Access denied', 403);

    if (isset($data['id']) && $data['id'] > 0) {
        $stmt = $pdo->prepare("UPDATE vcard_iframes SET url = ? WHERE id = ? AND vcard_id = ?");
        $stmt->execute([$data['url'], $data['id'], $data['vcard_id']]);
        sendSuccess('Iframe updated successfully');
    } else {
        $stmt = $pdo->prepare("INSERT INTO vcard_iframes (vcard_id, url) VALUES (?, ?)");
        $stmt->execute([$data['vcard_id'], $data['url']]);
        sendSuccess('Iframe added successfully', ['id' => $pdo->lastInsertId()]);
    }
} catch (PDOException $e) {
    sendError('Database error: ' . $e->getMessage(), 500);
}
