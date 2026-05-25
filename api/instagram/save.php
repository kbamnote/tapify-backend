<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

$data = getInput();

if (!isset($data['vcard_id']) || empty($data['tag'])) {
    sendError('vCard ID and Tag are required');
}

try {
    $pdo = getDB();
    
    $embedUrl = null;
    if (preg_match('/src="([^"]+)"/', $data['tag'], $matches)) {
        $embedUrl = $matches[1];
    }
    
    $type = $data['type'] ?? 'post';
    
    if (isset($data['id']) && $data['id'] > 0) {
        $stmt = $pdo->prepare("UPDATE vcard_instagram_feeds SET type = ?, embed_url = ?, tag = ? WHERE id = ? AND vcard_id = ?");
        $stmt->execute([$type, $embedUrl, $data['tag'], $data['id'], $data['vcard_id']]);
        sendSuccess('Feed updated successfully');
    } else {
        $stmt = $pdo->prepare("INSERT INTO vcard_instagram_feeds (vcard_id, type, embed_url, tag) VALUES (?, ?, ?, ?)");
        $stmt->execute([$data['vcard_id'], $type, $embedUrl, $data['tag']]);
        sendSuccess('Feed added successfully', ['id' => $pdo->lastInsertId()]);
    }
} catch (PDOException $e) {
    sendError('Database error: ' . $e->getMessage(), 500);
}
