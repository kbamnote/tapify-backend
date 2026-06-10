<?php
/**
 * TAPIFY - Manage Weekly Schedule Slots (Admin)
 * GET /api/appointments/slots_manage.php?vcard_id=1
 * POST /api/appointments/slots_manage.php
 * Body: { action: 'save_week', vcard_id: 10, schedule: [ { day: 1, start: '09:00', end: '17:00' }, ... ] }
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
        
        // Verify ownership (admins may edit any vCard)
        if (!userCanEditVcard($pdo, $vcardId)) sendError('Access denied', 403);

        $stmt = $pdo->prepare("SELECT * FROM vcard_weekly_schedule WHERE vcard_id = ? ORDER BY day_of_week, start_time");
        $stmt->execute([$vcardId]);
        sendSuccess('Schedule fetched', $stmt->fetchAll());
    } 
    else if ($method === 'POST') {
        $input = getInput();
        $action = $input['action'] ?? '';
        $vcardId = (int)($input['vcard_id'] ?? 0);
        
        // Verify ownership (admins may edit any vCard)
        if (!userCanEditVcard($pdo, $vcardId)) sendError('Access denied', 403);

        if ($action === 'save_week') {
            $schedule = $input['schedule'] ?? [];
            if (!is_array($schedule)) sendError('Invalid schedule format');
            
            $pdo->beginTransaction();
            try {
                // Delete existing schedule
                $stmt = $pdo->prepare("DELETE FROM vcard_weekly_schedule WHERE vcard_id = ?");
                $stmt->execute([$vcardId]);
                
                // Insert new schedule
                $stmt = $pdo->prepare("INSERT INTO vcard_weekly_schedule (vcard_id, day_of_week, start_time, end_time) VALUES (?, ?, ?, ?)");
                foreach ($schedule as $slot) {
                    $day = (int)$slot['day'];
                    if ($day >= 0 && $day <= 6 && !empty($slot['start']) && !empty($slot['end'])) {
                        $startDb = date('H:i:s', strtotime($slot['start']));
                        $endDb = date('H:i:s', strtotime($slot['end']));
                        $stmt->execute([$vcardId, $day, $startDb, $endDb]);
                    }
                }
                
                $pdo->commit();
                sendSuccess('Schedule saved successfully');
            } catch (Exception $e) {
                $pdo->rollBack();
                sendError('Database error while saving schedule');
            }
        } 
        else {
            sendError('Invalid action');
        }
    }
} catch (Exception $e) {
    sendError('Server error: ' . $e->getMessage(), 500);
}
