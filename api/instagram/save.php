<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
requireAuth();

$data = getInput();

if (!isset($data['vcard_id']) || empty($data['tag'])) {
    sendError('vCard ID and Tag are required');
}

try {
    $pdo = getDB();

    if (!userCanEditVcard($pdo, (int)$data['vcard_id'])) sendError('Access denied', 403);

    $embedUrl = null;
    $rawTag = $data['tag'] ?? '';

    // Try to extract src from an <iframe> embed tag
    if (preg_match('/src=["\']([^"\']+)["\']/', $rawTag, $matches)) {
        $embedUrl = $matches[1];
    }
    // Accept a direct Instagram post/reel/tv URL — convert to embed URL
    elseif (preg_match('#https?://(?:www\.)?instagram\.com/(p|reel|tv)/([A-Za-z0-9_-]+)#', $rawTag, $matches)) {
        $embedUrl = 'https://www.instagram.com/' . $matches[1] . '/' . $matches[2] . '/embed/';
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
