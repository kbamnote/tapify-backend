<?php
/**
 * TAPIFY - Manage Review Funnel
 * Method: GET (Fetch), POST (Create/Update)
 */
// CORS, the OPTIONS preflight and the session are handled inside database.php,
// which echoes the request Origin and sets Allow-Credentials: true so that
// credentialed cross-origin POSTs (admin panel on www.* calling the API on
// app.*) are accepted. Setting Allow-Origin: * here would break those.
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

try {
    $pdo = getDB();
    $userId = getCurrentUserId();
    $method = $_SERVER['REQUEST_METHOD'];

    if ($method === 'GET') {
        $stmt = $pdo->prepare("SELECT id, slug, google_review_url FROM review_funnels WHERE user_id = ? LIMIT 1");
        $stmt->execute([$userId]);
        $funnel = $stmt->fetch();

        echo json_encode(['success' => true, 'data' => $funnel ?: null]);
    } 
    elseif ($method === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        $googleUrl = $input['google_review_url'] ?? '';
        $slug = $input['slug'] ?? ''; // They can provide a custom slug, or we generate one

        if (empty($googleUrl)) {
            echo json_encode(['success' => false, 'message' => 'Google Review URL is required']);
            exit;
        }

        // Check if funnel already exists for user
        $stmt = $pdo->prepare("SELECT id, slug FROM review_funnels WHERE user_id = ?");
        $stmt->execute([$userId]);
        $existing = $stmt->fetch();

        if ($existing) {
            // Keep the existing slug so printed QR codes don't break
            $finalSlug = !empty($input['slug']) ? $input['slug'] : $existing['slug'];
            
            $stmt = $pdo->prepare("UPDATE review_funnels SET google_review_url = ?, slug = ? WHERE user_id = ?");
            $stmt->execute([$googleUrl, $finalSlug, $userId]);
            $slug = $finalSlug;
        } else {
            if (empty($slug)) {
                $slug = 'review-' . uniqid();
            }
            $stmt = $pdo->prepare("INSERT INTO review_funnels (user_id, slug, google_review_url) VALUES (?, ?, ?)");
            $stmt->execute([$userId, $slug, $googleUrl]);
        }

        echo json_encode(['success' => true, 'message' => 'Funnel saved successfully', 'slug' => $slug]);
    }
} catch (Throwable $e) {
    echo json_encode(['success' => false, 'message' => 'System Error: ' . $e->getMessage()]);
}
