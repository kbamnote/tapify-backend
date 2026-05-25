<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');
header('Access-Control-Allow-Credentials: true');

require_once '../../config/database.php';
session_start();

$vcardId = isset($_GET['vcard_id']) ? (int)$_GET['vcard_id'] : 0;

if ($vcardId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Valid vCard ID is required']);
    exit;
}

try {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT * FROM vcard_instagram_feeds WHERE vcard_id = ? ORDER BY display_order ASC, id DESC");
    $stmt->execute([$vcardId]);
    $feeds = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => [
            'feeds' => $feeds
        ]
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
