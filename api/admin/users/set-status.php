<?php
/**
 * TAPIFY - Admin: Activate / Deactivate a User
 * POST /backend/api/admin/users/set-status.php
 *
 * Body: { "user_id": <int>, "status": 0|1 }   (0 = deactivate, 1 = activate)
 *
 * Deactivated accounts (status = 0) are blocked at login (see api/login.php,
 * which rejects any user whose status != 1). Their existing public vCards/stores
 * are unaffected by this flag; this only gates the owner's dashboard login.
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../includes/functions.php';

requireAdmin(); // Strictly restricted to Admins

if ($_SERVER['REQUEST_METHOD'] !== 'POST') sendError('Only POST allowed', 405);

$input  = getInput();
$userId = $input['user_id'] ?? null;
$status = $input['status'] ?? null;

if (empty($userId) || !is_numeric($userId)) sendError('Valid user_id is required');
if ($status === null || !in_array((int)$status, [0, 1], true)) {
    sendError('status must be 0 (deactivate) or 1 (activate)');
}
$userId = (int)$userId;
$status = (int)$status;

// Guard: an admin must not deactivate their own account (would lock themselves out).
if ($status === 0 && $userId === (int)getCurrentUserId()) {
    sendError('You cannot deactivate your own account.');
}

try {
    $pdo = getDB();

    $stmt = $pdo->prepare("SELECT id, name, email, role, status FROM users WHERE id = ? LIMIT 1");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();

    if (!$user) sendError('User not found', 404);

    // Idempotent: report success without a write when already in the target state.
    if ((int)$user['status'] === $status) {
        sendSuccess('User already ' . ($status ? 'active' : 'inactive'), [
            'user_id' => $userId,
            'status'  => $status,
        ]);
    }

    $stmt = $pdo->prepare("UPDATE users SET status = ? WHERE id = ?");
    $stmt->execute([$status, $userId]);

    $label = $user['email'] ?: ('user #' . $userId);
    sendSuccess(($status ? 'Activated ' : 'Deactivated ') . $label, [
        'user_id' => $userId,
        'status'  => $status,
    ]);
} catch (Exception $e) {
    sendError('Failed to update user status: ' . $e->getMessage(), 500);
}
