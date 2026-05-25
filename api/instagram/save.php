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

if (!isset($data->vcard_id) || empty($data->tag)) {
    echo json_encode(['success' => false, 'message' => 'vCard ID and Tag are required']);
    exit;
}

try {
    $pdo = getDB();
    
    // Simple parsing to extract embed URL if they pasted a full blockquote or iframe
    $embedUrl = null;
    if (preg_match('/src="([^"]+)"/', $data->tag, $matches)) {
        $embedUrl = $matches[1];
    }
    
    if (isset($data->id) && $data->id > 0) {
        $stmt = $pdo->prepare("UPDATE vcard_instagram_feeds SET type = ?, embed_url = ?, tag = ? WHERE id = ? AND vcard_id = ?");
        $stmt->execute([$data->type ?? 'post', $embedUrl, $data->tag, $data->id, $data->vcard_id]);
        echo json_encode(['success' => true, 'message' => 'Feed updated successfully']);
    } else {
        $stmt = $pdo->prepare("INSERT INTO vcard_instagram_feeds (vcard_id, type, embed_url, tag) VALUES (?, ?, ?, ?)");
        $stmt->execute([$data->vcard_id, $data->type ?? 'post', $embedUrl, $data->tag]);
        echo json_encode(['success' => true, 'message' => 'Feed added successfully', 'id' => $pdo->lastInsertId()]);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
