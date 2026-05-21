<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Method not allowed', 405);
}

$input = json_decode(file_get_contents('php://input'), true);
$design_id = isset($input['design_id']) ? (int) $input['design_id'] : 0;

if (!$design_id) {
    sendError('design_id is required', 400);
}

$user_id = getCurrentUserId();

try {
    $pdo = getDB();

    // Check if already saved
    $stmt = $pdo->prepare(
        "SELECT id FROM user_saved_designs WHERE user_id = ? AND design_id = ?"
    );
    $stmt->execute([$user_id, $design_id]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        // Already saved → unsave (delete)
        $del = $pdo->prepare(
            "DELETE FROM user_saved_designs WHERE user_id = ? AND design_id = ?"
        );
        $del->execute([$user_id, $design_id]);
        sendSuccess('Design unsaved', ['saved' => false]);
    } else {
        // Not saved → save (insert)
        $ins = $pdo->prepare(
            "INSERT INTO user_saved_designs (user_id, design_id, saved_at) VALUES (?, ?, NOW())"
        );
        $ins->execute([$user_id, $design_id]);
        sendSuccess('Design saved', ['saved' => true]);
    }
} catch (Exception $e) {
    sendError('Failed to toggle save: ' . $e->getMessage(), 500);
}
