<?php
/**
 * TAPIFY - Save Public WhatsApp Store Order
 * POST /backend/api/public/store-order.php
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

header('Content-Type: application/json');

// Allow CORS for public stores
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Only POST allowed', 405);
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    sendError('Invalid JSON data');
}

$storeId = (int)($input['store_id'] ?? 0);
$customerName = trim($input['customer_name'] ?? '');
$customerPhone = trim($input['customer_phone'] ?? '');
$subtotal = (float)($input['subtotal'] ?? 0);
$deliveryCharge = (float)($input['delivery_charge'] ?? 0);
$totalAmount = (float)($input['total_amount'] ?? 0);
$items = is_array($input['items'] ?? null) ? json_encode($input['items']) : '[]';

if (!$storeId || empty($customerName) || empty($customerPhone)) {
    sendError('Missing required fields (Store ID, Name, Phone)');
}

try {
    $pdo = getDB();
    
    // Verify store exists
    $stmt = $pdo->prepare("SELECT id FROM whatsapp_stores WHERE id = ?");
    $stmt->execute([$storeId]);
    if (!$stmt->fetch()) {
        sendError('Invalid store');
    }

    $sql = "INSERT INTO whatsapp_store_orders 
            (store_id, customer_name, customer_phone, items, subtotal, delivery_charge, total_amount, status, is_read, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', 0, NOW())";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $storeId,
        $customerName,
        $customerPhone,
        $items,
        $subtotal,
        $deliveryCharge,
        $totalAmount
    ]);

    sendSuccess('Order saved successfully', ['order_id' => $pdo->lastInsertId()]);

} catch (Exception $e) {
    sendError('Failed to save order: ' . $e->getMessage(), 500);
}
