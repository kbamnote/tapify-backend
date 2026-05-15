<?php
/**
 * TAPIFY - Store Order Submission (with Email Notifications)
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Only POST allowed', 405);
}

$input = getInput();

$storeId = (int)($input['store_id'] ?? 0);
$customerName = sanitize($input['customer_name'] ?? '');
$customerPhone = sanitize($input['customer_phone'] ?? '');
$customerAddress = sanitize($input['customer_address'] ?? '');
$items = $input['items'] ?? [];
$subtotal = (float)($input['subtotal'] ?? 0);
$deliveryCharge = (float)($input['delivery_charge'] ?? 0);
$totalAmount = (float)($input['total_amount'] ?? 0);
$notes = sanitize($input['notes'] ?? '');

if (!$storeId) sendError('Invalid store');
if (empty($customerName)) sendError('Name required');
if (empty($customerPhone)) sendError('Phone required');
if (empty($items) || !is_array($items)) sendError('Cart is empty');

try {
    $pdo = getDB();

    // Get store info
    $stmt = $pdo->prepare("SELECT id, store_name FROM whatsapp_stores WHERE id = ? AND status = 1 LIMIT 1");
    $stmt->execute([$storeId]);
    $store = $stmt->fetch();
    if (!$store) sendError('Store not found');

    // Save order
    $stmt = $pdo->prepare("INSERT INTO whatsapp_store_orders
        (store_id, customer_name, customer_phone, customer_address, items,
         subtotal, delivery_charge, total_amount, notes, status, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())");
    $stmt->execute([
        $storeId, $customerName, $customerPhone, $customerAddress,
        json_encode($items), $subtotal, $deliveryCharge, $totalAmount, $notes
    ]);
    $orderId = $pdo->lastInsertId();

    $pdo->prepare("UPDATE whatsapp_stores SET order_count = order_count + 1 WHERE id = ?")->execute([$storeId]);

    // === EMAIL NOTIFICATION TO ADMIN ===
    try {
        if (file_exists(__DIR__ . '/includes/email-helper.php')) {
            require_once __DIR__ . '/includes/email-helper.php';

            sendEmailNotification('new_order', [
                'order_id' => $orderId,
                'store_name' => $store['store_name'],
                'customer_name' => $customerName,
                'customer_phone' => $customerPhone,
                'customer_address' => $customerAddress,
                'items' => $items,
                'total_amount' => $totalAmount
            ]);
        }
    } catch (Exception $e) {
        error_log('Email notification failed: ' . $e->getMessage());
    }

    sendSuccess('Order placed', ['order_id' => $orderId]);

} catch (Exception $e) {
    sendError('Failed: ' . $e->getMessage(), 500);
}
