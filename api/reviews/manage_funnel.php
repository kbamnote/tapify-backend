<?php
/**
 * TAPIFY - Manage Review Funnel
 * GET  : fetch funnel(s)
 *        - admin, no params      -> list ALL funnels with owner + counts
 *        - admin, ?user_id=X      -> that user's funnel
 *        - regular user           -> own funnel
 * POST : create/update a funnel
 *        - admin with user_id     -> create/update funnel FOR that client
 *        - otherwise              -> create/update own funnel
 */
// CORS, the OPTIONS preflight and the session are handled inside database.php,
// which echoes the request Origin and sets Allow-Credentials: true so that
// credentialed cross-origin requests (admin panel on www.* calling the API on
// app.*) are accepted.
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
    $isAdminUser = isStaffOrAdmin();
    $method = $_SERVER['REQUEST_METHOD'];

    if ($method === 'GET') {
        // Admin overview: every funnel with its owner and counts.
        if ($isAdminUser && !isset($_GET['user_id'])) {
            $stmt = $pdo->query("
                SELECT f.id, f.user_id, f.slug, f.google_review_url,
                       u.name AS owner_name, u.email AS owner_email,
                       (SELECT COUNT(*) FROM funnel_analytics a WHERE a.funnel_id = f.id AND a.event_type = 'scan') AS scans,
                       (SELECT COUNT(*) FROM funnel_analytics a WHERE a.funnel_id = f.id AND a.event_type = 'redirect') AS redirects,
                       (SELECT COUNT(*) FROM funnel_reviews r WHERE r.funnel_id = f.id) AS private_reviews
                FROM review_funnels f
                LEFT JOIN users u ON u.id = f.user_id
                ORDER BY f.id DESC
            ");
            echo json_encode(['success' => true, 'is_admin' => true, 'funnels' => $stmt->fetchAll()]);
            exit;
        }

        // A specific user's funnel (admin via ?user_id) or the caller's own.
        $targetUserId = ($isAdminUser && isset($_GET['user_id'])) ? (int)$_GET['user_id'] : $userId;
        $stmt = $pdo->prepare("SELECT id, user_id, slug, google_review_url FROM review_funnels WHERE user_id = ? LIMIT 1");
        $stmt->execute([$targetUserId]);
        $funnel = $stmt->fetch();
        echo json_encode(['success' => true, 'is_admin' => $isAdminUser, 'data' => $funnel ?: null]);
    }
    elseif ($method === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        $googleUrl = $input['google_review_url'] ?? '';
        if (empty($googleUrl)) {
            echo json_encode(['success' => false, 'message' => 'Google Review URL is required']);
            exit;
        }

        // Admins may create/update a funnel for a chosen client; everyone else
        // operates on their own funnel.
        $targetUserId = ($isAdminUser && !empty($input['user_id'])) ? (int)$input['user_id'] : $userId;

        if ($isAdminUser && !empty($input['user_id'])) {
            $chk = $pdo->prepare("SELECT id FROM users WHERE id = ?");
            $chk->execute([$targetUserId]);
            if (!$chk->fetch()) {
                echo json_encode(['success' => false, 'message' => 'Selected user does not exist']);
                exit;
            }
        }

        $stmt = $pdo->prepare("SELECT id, slug FROM review_funnels WHERE user_id = ?");
        $stmt->execute([$targetUserId]);
        $existing = $stmt->fetch();

        if ($existing) {
            // Keep the existing slug so printed QR codes don't break.
            $finalSlug = !empty($input['slug']) ? $input['slug'] : $existing['slug'];
            $stmt = $pdo->prepare("UPDATE review_funnels SET google_review_url = ?, slug = ? WHERE user_id = ?");
            $stmt->execute([$googleUrl, $finalSlug, $targetUserId]);
            $slug = $finalSlug;
        } else {
            $slug = !empty($input['slug']) ? $input['slug'] : ('review-' . uniqid());
            $stmt = $pdo->prepare("INSERT INTO review_funnels (user_id, slug, google_review_url) VALUES (?, ?, ?)");
            $stmt->execute([$targetUserId, $slug, $googleUrl]);
        }

        echo json_encode(['success' => true, 'message' => 'Funnel saved successfully', 'slug' => $slug, 'user_id' => $targetUserId]);
    }
} catch (Throwable $e) {
    echo json_encode(['success' => false, 'message' => 'System Error: ' . $e->getMessage()]);
}
