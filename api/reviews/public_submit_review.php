<?php
/**
 * TAPIFY - Public Submit Review (For React App)
 * Saves private 1-3 star reviews and handles media upload.
 */
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
require_once __DIR__ . '/../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

try {
    $pdo = getDB();

    $funnelId = $_POST['funnel_id'] ?? null;
    $rating = $_POST['rating'] ?? null;
    $feedbackText = $_POST['feedback_text'] ?? '';

    if (!$funnelId || !$rating) {
        echo json_encode(['success' => false, 'message' => 'Funnel ID and Rating are required']);
        exit;
    }

    $mediaUrl = null;

    // Handle File Upload
    if (isset($_FILES['media']) && $_FILES['media']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../../uploads/reviews/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileInfo = pathinfo($_FILES['media']['name']);
        $ext = strtolower($fileInfo['extension']);
        $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'mp4', 'mov', 'webm'];

        if (in_array($ext, $allowedExts)) {
            $fileName = uniqid('review_') . '.' . $ext;
            $destination = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['media']['tmp_name'], $destination)) {
                // Generate absolute URL for the database (assuming typical setup)
                $scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? "https" : "http";
                $host = $_SERVER['HTTP_HOST'];
                $mediaUrl = "$scheme://$host/tapify-backend/uploads/reviews/$fileName";
            }
        }
    }

    $stmt = $pdo->prepare("INSERT INTO funnel_reviews (funnel_id, rating, feedback_text, media_url) VALUES (?, ?, ?, ?)");
    $stmt->execute([$funnelId, $rating, $feedbackText, $mediaUrl]);

    echo json_encode(['success' => true, 'message' => 'Review submitted securely']);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
