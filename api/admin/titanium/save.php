<?php
/**
 * TAPIFY - Admin: Create or update a titanium member
 * POST /api/admin/titanium/save.php
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

$input          = getInput();
$userId         = (int)($input['user_id']         ?? 0);
$cardHolderName = sanitize($input['card_holder_name'] ?? '');
$cardNumber     = sanitize($input['card_number']      ?? '');
$expiryDate     = sanitize($input['expiry_date']      ?? '');
$isActive       = isset($input['is_active']) ? (int)(bool)$input['is_active'] : 1;

if (!$userId) sendError('user_id is required');

// Upsert
$stmt = $pdo->prepare("SELECT id FROM titanium_members WHERE user_id = ?");
$stmt->execute([$userId]);
$existing = $stmt->fetch();

if ($existing) {
    $stmt = $pdo->prepare("
        UPDATE titanium_members
        SET card_holder_name = ?,
            card_number      = ?,
            expiry_date      = ?,
            is_active        = ?
        WHERE user_id = ?
    ");
    $stmt->execute([$cardHolderName ?: null, $cardNumber ?: null, $expiryDate ?: null, $isActive, $userId]);
} else {
    $stmt = $pdo->prepare("
        INSERT INTO titanium_members (user_id, card_holder_name, card_number, expiry_date, is_active)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([$userId, $cardHolderName ?: null, $cardNumber ?: null, $expiryDate ?: null, $isActive]);
}

sendSuccess('Titanium member saved successfully');
