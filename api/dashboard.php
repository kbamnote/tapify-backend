<?php
/**
 * TAPIFY - Dashboard Stats API
 * GET /backend/api/dashboard.php
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

requireAuth();

try {
    $pdo = getDB();
    $userId = getCurrentUserId();

    // Total active vCards
    $stmt = $pdo->prepare("SELECT COUNT(*) AS total FROM vcards WHERE user_id = ? AND status = 1");
    $stmt->execute([$userId]);
    $activeVcards = (int)$stmt->fetch()['total'];

    // Total deactivated vCards
    $stmt = $pdo->prepare("SELECT COUNT(*) AS total FROM vcards WHERE user_id = ? AND status = 0");
    $stmt->execute([$userId]);
    $deactivatedVcards = (int)$stmt->fetch()['total'];

    // Today's inquiries
    $stmt = $pdo->prepare("
        SELECT COUNT(*) AS total
        FROM vcard_inquiries vi
        JOIN vcards v ON v.id = vi.vcard_id
        WHERE v.user_id = ? AND DATE(vi.created_at) = CURDATE()
    ");
    $stmt->execute([$userId]);
    $todayInquiries = (int)$stmt->fetch()['total'];

    // Total inquiries
    $stmt = $pdo->prepare("
        SELECT COUNT(*) AS total
        FROM vcard_inquiries vi
        JOIN vcards v ON v.id = vi.vcard_id
        WHERE v.user_id = ?
    ");
    $stmt->execute([$userId]);
    $totalInquiries = (int)$stmt->fetch()['total'];

    // Total views (sum of view_count)
    $stmt = $pdo->prepare("SELECT COALESCE(SUM(view_count), 0) AS total FROM vcards WHERE user_id = ?");
    $stmt->execute([$userId]);
    $totalViews = (int)$stmt->fetch()['total'];

    // Get user info
    $stmt = $pdo->prepare("SELECT name FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();

    sendSuccess('Dashboard loaded', [
        'user_name' => $user['name'] ?? 'User',
        'stats' => [
            'active_vcards' => $activeVcards,
            'deactivated_vcards' => $deactivatedVcards,
            'today_inquiries' => $todayInquiries,
            'total_inquiries' => $totalInquiries,
            'today_appointments' => 0, // Phase 4
            'whatsapp_stores' => 0,    // Phase 5
            'whatsapp_orders' => 0,    // Phase 5
            'pending_orders' => 0,     // Phase 5
            'total_views' => $totalViews
        ]
    ]);

} catch (Exception $e) {
    sendError('Failed: ' . $e->getMessage(), 500);
}
