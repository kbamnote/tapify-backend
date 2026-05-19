<?php
/**
 * TAPIFY - Delete Account
 * POST /backend/api/profile/delete.php
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') sendError('Only POST allowed', 405);

try {
    $pdo = getDB();
    $userId = getCurrentUserId();

    // Check if tables exist before deleting from them to avoid SQL errors
    $tables = [];
    $stmt = $pdo->query("SHOW TABLES");
    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
        $tables[] = $row[0];
    }

    $pdo->beginTransaction();

    // Clean up dependent data based on existing tables
    if (in_array('appointments', $tables)) {
        $stmt = $pdo->prepare("DELETE FROM appointments WHERE user_id = ?");
        $stmt->execute([$userId]);
    }

    if (in_array('inquiries', $tables)) {
        $stmt = $pdo->prepare("DELETE FROM inquiries WHERE user_id = ?");
        $stmt->execute([$userId]);
    }

    if (in_array('store_orders', $tables) && in_array('whatsapp_stores', $tables)) {
        $stmt = $pdo->prepare("DELETE FROM store_orders WHERE store_id IN (SELECT id FROM whatsapp_stores WHERE user_id = ?)");
        $stmt->execute([$userId]);
    }

    if (in_array('store_products', $tables) && in_array('whatsapp_stores', $tables)) {
        $stmt = $pdo->prepare("DELETE FROM store_products WHERE store_id IN (SELECT id FROM whatsapp_stores WHERE user_id = ?)");
        $stmt->execute([$userId]);
    }

    if (in_array('whatsapp_stores', $tables)) {
        $stmt = $pdo->prepare("DELETE FROM whatsapp_stores WHERE user_id = ?");
        $stmt->execute([$userId]);
    }

    if (in_array('vcards', $tables)) {
        $stmt = $pdo->prepare("DELETE FROM vcards WHERE user_id = ?");
        $stmt->execute([$userId]);
    }

    // Delete primary user record
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$userId]);

    $pdo->commit();
    sendSuccess('Account and all associated data deleted successfully');
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    sendError('Failed to delete account: ' . $e->getMessage(), 500);
}
