<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
requireAuth();

$vcardId = isset($_GET['vcard_id']) ? (int)$_GET['vcard_id'] : 0;

if ($vcardId <= 0) {
    sendError('Valid vCard ID is required');
}

try {
    $pdo = getDB();

    if (!userCanEditVcard($pdo, $vcardId)) sendError('Access denied', 403);

    $stmt = $pdo->prepare("SELECT * FROM vcard_instagram_feeds WHERE vcard_id = ? ORDER BY display_order ASC, id DESC");
    $stmt->execute([$vcardId]);
    $feeds = $stmt->fetchAll(PDO::FETCH_ASSOC);

    sendSuccess('Instagram feeds loaded', ['feeds' => $feeds]);
} catch (PDOException $e) {
    sendError('Database error: ' . $e->getMessage(), 500);
}
