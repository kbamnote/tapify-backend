<?php
/**
 * TAPIFY - Admin: List all users with titanium status
 * GET /api/admin/titanium/list.php
 */
header('Content-Type: application/json');
ini_set('display_errors', 0);

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../includes/functions.php';

requireAuth();

$currentUserId = getCurrentUserId();
$pdo = getDB();

// Admin check
$stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
$stmt->execute([$currentUserId]);
$me = $stmt->fetch();
if (!$me || $me['role'] !== 'admin') {
    sendError('Unauthorized. Admin access required.', 403);
}

$search = sanitize($_GET['search'] ?? '');

$sql = "
    SELECT
        u.id,
        u.name,
        u.email,
        u.phone,
        u.avatar,
        t.id            AS titanium_id,
        t.card_holder_name,
        t.card_number,
        t.expiry_date,
        t.is_active     AS titanium_active
    FROM users u
    LEFT JOIN titanium_members t ON t.user_id = u.id
    WHERE u.role = 'user'
";
$params = [];

if (!empty($search)) {
    $sql .= " AND (u.name LIKE ? OR u.email LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$sql .= " ORDER BY t.is_active DESC, u.name ASC LIMIT 100";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$users = $stmt->fetchAll();

foreach ($users as &$u) {
    $u['titanium_active'] = $u['titanium_active'] !== null ? (bool)$u['titanium_active'] : false;
    $u['is_titanium']     = $u['titanium_id'] !== null;
    if ($u['avatar']) $u['avatar'] = imgUrl($u['avatar']);
}

sendSuccess('Users loaded', ['users' => $users]);
