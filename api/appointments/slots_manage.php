<?php
/**
 * TAPIFY - Manage Availability Slots (Admin)
 * GET /api/appointments/slots_manage.php?vcard_id=1&date=YYYY-MM-DD
 * POST /api/appointments/slots_manage.php (action=add, vcard_id, date, time_slot)
 * POST /api/appointments/slots_manage.php (action=delete, id, vcard_id)
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
requireAuth();

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$userId = getCurrentUserId();
$pdo = getDB();

try {
    if ($method === 'GET') {
        $vcardId = (int)($_GET['vcard_id'] ?? 0);
        $date = sanitize($_GET['date'] ?? '');
        
        // Verify ownership
        $stmt = $pdo->prepare("SELECT id FROM vcards WHERE id = ? AND user_id = ?");
        $stmt->execute([$vcardId, $userId]);
        if (!$stmt->fetch()) sendError('Access denied', 403);

        $query = "SELECT * FROM vcard_appointment_slots WHERE vcard_id = ?";
        $params = [$vcardId];
        
        if (!empty($date)) {
            $query .= " AND available_date = ?";
            $params[] = $date;
        }
        $query .= " ORDER BY available_date DESC, time_slot ASC";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        sendSuccess('Slots fetched', $stmt->fetchAll());
    } 
    else if ($method === 'POST') {
        $input = getInput();
        $action = $input['action'] ?? '';
        $vcardId = (int)($input['vcard_id'] ?? 0);
        
        // Verify ownership
        $stmt = $pdo->prepare("SELECT id FROM vcards WHERE id = ? AND user_id = ?");
        $stmt->execute([$vcardId, $userId]);
        if (!$stmt->fetch()) sendError('Access denied', 403);

        if ($action === 'add') {
            $date = sanitize($input['date'] ?? '');
            $timeSlot = sanitize($input['time_slot'] ?? '');
            
            if (empty($date) || empty($timeSlot)) sendError('Date and time slot are required');
            
            // Check if already exists
            $stmt = $pdo->prepare("SELECT id FROM vcard_appointment_slots WHERE vcard_id = ? AND available_date = ? AND time_slot = ?");
            $stmt->execute([$vcardId, $date, $timeSlot]);
            if ($stmt->fetch()) sendError('This time slot already exists for this date');
            
            $stmt = $pdo->prepare("INSERT INTO vcard_appointment_slots (vcard_id, available_date, time_slot) VALUES (?, ?, ?)");
            $stmt->execute([$vcardId, $date, $timeSlot]);
            sendSuccess('Slot added', ['id' => $pdo->lastInsertId()]);
        } 
        else if ($action === 'delete') {
            $id = (int)($input['id'] ?? 0);
            
            $stmt = $pdo->prepare("DELETE FROM vcard_appointment_slots WHERE id = ? AND vcard_id = ?");
            $stmt->execute([$id, $vcardId]);
            sendSuccess('Slot deleted');
        }
        else {
            sendError('Invalid action');
        }
    }
} catch (Exception $e) {
    sendError('Server error: ' . $e->getMessage(), 500);
}
