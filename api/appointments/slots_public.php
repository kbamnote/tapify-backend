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

    // 1. Determine day of week (0=Sun, 1=Mon, ..., 6=Sat)
    $dayOfWeek = (int)date('w', strtotime($date));

    // 2. Fetch schedule ranges for this day
    $stmt = $pdo->prepare("SELECT start_time, end_time FROM vcard_weekly_schedule WHERE vcard_id = ? AND day_of_week = ? ORDER BY start_time");
    $stmt->execute([$vcardId, $dayOfWeek]);
    $ranges = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 3. Generate 30-min slots
    $generated_slots = [];
    foreach ($ranges as $range) {
        $startTS = strtotime($range['start_time']);
        $endTS = strtotime($range['end_time']);
        
        while ($startTS + 1800 <= $endTS) {
            $generated_slots[] = date('H:i', $startTS); // 24-hour format
            $startTS += 1800; // Add 30 minutes
        }
    }
    
    // Sort and remove duplicates if overlapping ranges were created
    $generated_slots = array_unique($generated_slots);
    sort($generated_slots);

    // 4. Get booked slots for this date
    $stmt = $pdo->prepare("SELECT appointment_time FROM vcard_appointments WHERE vcard_id = ? AND appointment_date = ? AND status != 'cancelled'");
    $stmt->execute([$vcardId, $date]);
    
    $booked_slots = [];
    while ($row = $stmt->fetch()) {
        // appointment_time might be "10:00" or "10:00:00" or "10:00 AM". Let's parse it to H:i
        $booked_slots[] = date('H:i', strtotime($row['appointment_time']));
    }

    // 5. Filter out booked slots
    $available_slots = [];
    foreach ($generated_slots as $slot) {
        if (!in_array($slot, $booked_slots)) {
            $available_slots[] = [
                'value' => $slot,
                'label' => date('h:i A', strtotime($slot))
            ];
        }
    }

    echo json_encode(['success' => true, 'data' => $available_slots]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
