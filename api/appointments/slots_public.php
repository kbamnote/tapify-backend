<?php
/**
 * TAPIFY - Get Available Slots (Public)
 * GET /api/appointments/slots_public.php?vcard_id=1&date=YYYY-MM-DD
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

header('Content-Type: application/json');

$vcardId = (int)($_GET['vcard_id'] ?? 0);
$date = sanitize($_GET['date'] ?? '');

if (!$vcardId || empty($date)) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit;
}

try {
    $pdo = getDB();

    // Get all defined slots for this date
    $stmt = $pdo->prepare("SELECT time_slot FROM vcard_appointment_slots WHERE vcard_id = ? AND available_date = ? ORDER BY time_slot");
    $stmt->execute([$vcardId, $date]);
    $all_slots = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Get all booked slots for this date (excluding cancelled)
    $stmt = $pdo->prepare("SELECT appointment_time FROM vcard_appointments WHERE vcard_id = ? AND appointment_date = ? AND status != 'cancelled'");
    $stmt->execute([$vcardId, $date]);
    $booked_slots = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Filter out booked slots
    $available_slots = array_values(array_diff($all_slots, $booked_slots));

    echo json_encode(['success' => true, 'data' => $available_slots]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
