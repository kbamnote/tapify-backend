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

if (!isset($data->vcard_id) || empty($data->url)) {
    echo json_encode(['success' => false, 'message' => 'vCard ID and URL are required']);
    exit;
}

try {
    $pdo = getDB();
    
    if (isset($data->id) && $data->id > 0) {
        $stmt = $pdo->prepare("UPDATE vcard_iframes SET url = ? WHERE id = ? AND vcard_id = ?");
        $stmt->execute([$data->url, $data->id, $data->vcard_id]);
        echo json_encode(['success' => true, 'message' => 'Iframe updated successfully']);
    } else {
        $stmt = $pdo->prepare("INSERT INTO vcard_iframes (vcard_id, url) VALUES (?, ?)");
        $stmt->execute([$data->vcard_id, $data->url]);
        echo json_encode(['success' => true, 'message' => 'Iframe added successfully', 'id' => $pdo->lastInsertId()]);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
