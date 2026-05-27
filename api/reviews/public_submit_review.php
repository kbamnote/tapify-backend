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
    $customerName = $_POST['customer_name'] ?? '';
    $customerPhone = $_POST['customer_phone'] ?? '';

    if (!$funnelId || !$rating) {
        echo json_encode(['success' => false, 'message' => 'Funnel ID and Rating are required']);
        exit;
    }

    $mediaUrl = null;

    // Handle File Upload via Cloudinary
    if (isset($_FILES['media']) && $_FILES['media']['error'] === UPLOAD_ERR_OK) {
        $fileInfo = pathinfo($_FILES['media']['name']);
        $ext = strtolower($fileInfo['extension']);
        $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'mp4', 'mov', 'webm'];

        if (in_array($ext, $allowedExts)) {
            $cloudName   = CLOUDINARY_CLOUD_NAME;
            $apiKey      = CLOUDINARY_API_KEY;
            $apiSecret   = CLOUDINARY_API_SECRET;
            $timestamp   = time();
            $publicId    = 'reviews/review_' . uniqid();

            // Build Cloudinary signature
            $paramsToSign = "public_id={$publicId}&timestamp={$timestamp}";
            $signature = sha1($paramsToSign . $apiSecret);

            // Determine resource type
            $resourceType = in_array($ext, ['mp4', 'mov', 'webm']) ? 'video' : 'image';

            // Upload to Cloudinary via cURL
            $uploadUrl = "https://api.cloudinary.com/v1_1/{$cloudName}/{$resourceType}/upload";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $uploadUrl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, [
                'file'      => new CURLFile($_FILES['media']['tmp_name'], $_FILES['media']['type'], $_FILES['media']['name']),
                'public_id' => $publicId,
                'timestamp' => $timestamp,
                'api_key'   => $apiKey,
                'signature' => $signature,
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200) {
                $cloudData = json_decode($response, true);
                $mediaUrl  = $cloudData['secure_url'] ?? null;
            }
            // If upload fails, we continue without media (don't block the review)
        }
    }

    $stmt = $pdo->prepare("INSERT INTO funnel_reviews (funnel_id, rating, feedback_text, customer_name, customer_phone, media_url) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$funnelId, $rating, $feedbackText, $customerName, $customerPhone, $mediaUrl]);
    $reviewId = $pdo->lastInsertId();

    // === PUSH NOTIFICATIONS ===
    try {
        $stmtUser = $pdo->prepare("SELECT user_id FROM review_funnels WHERE id = ?");
        $stmtUser->execute([$funnelId]);
        $funnel = $stmtUser->fetch();
        if ($funnel && file_exists(__DIR__ . '/../../includes/notifications.php')) {
            require_once __DIR__ . '/../../includes/notifications.php';
            $title = "New $rating-Star Review";
            $message = "$customerName left a $rating-star review.";
            createAndSendNotification($pdo, $funnel['user_id'], $title, $message, 'review', $reviewId, '/reviews', null);
        }
    } catch (Exception $e) {
        error_log('Push notification failed: ' . $e->getMessage());
    }

    echo json_encode(['success' => true, 'message' => 'Review submitted securely']);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
