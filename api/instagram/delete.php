<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');
header('Access-Control-Allow-Credentials: true');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once '../../config/database.php';
session_start();

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->id) || !isset($data->vcard_id)) {
    echo json_encode(['success' => false, 'message' => 'ID and vCard ID are required']);
    exit;
}

try {
    $pdo = getDB();
    $stmt = $pdo->prepare("DELETE FROM vcard_instagram_feeds WHERE id = ? AND vcard_id = ?");
    $stmt->execute([$data->id, $data->vcard_id]);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Feed deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Feed not found or permission denied']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
