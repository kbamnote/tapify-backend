<?php
/**
 * TAPIFY - Public Appointment Submission (with Email Notifications)
 * POST /appointment-submit.php
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') sendError('Only POST allowed', 405);

$input = getInput();

$vcardId = (int)($input['vcard_id'] ?? 0);
$customerName = sanitize($input['name'] ?? '');
$customerEmail = sanitize($input['email'] ?? '');
$customerPhone = sanitize($input['phone'] ?? '');
$serviceName = sanitize($input['service'] ?? '');
$appointmentDate = sanitize($input['date'] ?? '');
$appointmentTime = sanitize($input['time'] ?? '');
$customerNotes = sanitize($input['notes'] ?? '');

if (!$vcardId) sendError('Invalid vCard');
if (empty($customerName)) sendError('Name required');
if (empty($customerPhone)) sendError('Phone required');
if (empty($appointmentDate)) sendError('Date required');
if (empty($appointmentTime)) sendError('Time required');

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $appointmentDate)) sendError('Invalid date');
// Removed strict time format check to allow ranges like "10:00 AM - 12:00 PM"

if (strtotime($appointmentDate) < strtotime(date('Y-m-d'))) {
    sendError('Cannot book for past dates');
}

try {
    $pdo = getDB();

    // 1. Check if slot falls within weekly schedule availability
    $dayOfWeek = (int)date('w', strtotime($appointmentDate));
    $stmt = $pdo->prepare("SELECT start_time, end_time FROM vcard_weekly_schedule WHERE vcard_id = ? AND day_of_week = ?");
    $stmt->execute([$vcardId, $dayOfWeek]);
    $ranges = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $isValidTime = false;
    $requestedTS = strtotime($appointmentTime);
    foreach ($ranges as $range) {
        $startTS = strtotime($range['start_time']);
        $endTS = strtotime($range['end_time']);
        // A slot is valid if its start time >= schedule start and its end time (start + 30 mins) <= schedule end
        if ($requestedTS >= $startTS && ($requestedTS + 1800) <= $endTS) {
            $isValidTime = true;
            break;
        }
    }
    
    if (!$isValidTime) {
        sendError('The selected time slot is not available in the schedule');
    }

    // Check if slot is already booked by another customer
    $stmt = $pdo->prepare("SELECT id FROM vcard_appointments WHERE vcard_id = ? AND appointment_date = ? AND appointment_time = ? AND status != 'cancelled'");
    $stmt->execute([$vcardId, $appointmentDate, $appointmentTime]);
    if ($stmt->fetch()) {
        sendError('The selected time slot has already been booked');
    }

    // Get vCard info for email
    $stmt = $pdo->prepare("SELECT id, vcard_name FROM vcards WHERE id = ? AND status = 1 LIMIT 1");
    $stmt->execute([$vcardId]);
    $vcard = $stmt->fetch();
    if (!$vcard) sendError('vCard not found');

    $stmt = $pdo->prepare("INSERT INTO vcard_appointments
        (vcard_id, customer_name, customer_email, customer_phone, service_name,
         appointment_date, appointment_time, customer_notes, status, is_read)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', 0)");
    $stmt->execute([
        $vcardId, $customerName, $customerEmail, $customerPhone, $serviceName,
        $appointmentDate, $appointmentTime, $customerNotes
    ]);
    $appointmentId = $pdo->lastInsertId();

    // === EMAIL NOTIFICATIONS ===
    try {
        if (file_exists(__DIR__ . '/includes/email-helper.php')) {
            require_once __DIR__ . '/includes/email-helper.php';

            $emailData = [
                'vcard_name' => $vcard['vcard_name'],
                'customer_name' => $customerName,
                'customer_email' => $customerEmail,
                'customer_phone' => $customerPhone,
                'service_name' => $serviceName,
                'appointment_date' => $appointmentDate,
                'appointment_time' => $appointmentTime,
                'customer_notes' => $customerNotes
            ];

            // Notify admin
            sendEmailNotification('new_appointment', $emailData);

            // Confirmation to customer
            sendEmailNotification('appointment_confirmation', $emailData);
        }
    } catch (Exception $e) {
        error_log('Email notification failed: ' . $e->getMessage());
    }

    // === PUSH NOTIFICATIONS ===
    try {
        // Fetch user ID for the vCard owner
        $stmtUser = $pdo->prepare("SELECT user_id FROM vcards WHERE id = ?");
        $stmtUser->execute([$vcardId]);
        $owner = $stmtUser->fetch();
        if ($owner && file_exists(__DIR__ . '/includes/notifications.php')) {
            require_once __DIR__ . '/includes/notifications.php';
            $title = "New Appointment Booked";
            $message = "You received a new appointment from $customerName for $appointmentTime on $appointmentDate.";
            createAndSendNotification($pdo, $owner['user_id'], $title, $message, 'appointment', $appointmentId, '/appointments', null);
        }
    } catch (Exception $e) {
        error_log('Push notification failed: ' . $e->getMessage());
    }

    sendSuccess('Appointment booked successfully', ['id' => $appointmentId]);

} catch (Exception $e) {
    sendError('Failed: ' . $e->getMessage(), 500);
}
