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
if (!preg_match('/^\d{2}:\d{2}/', $appointmentTime)) sendError('Invalid time');

if (strtotime($appointmentDate) < strtotime(date('Y-m-d'))) {
    sendError('Cannot book for past dates');
}

try {
    $pdo = getDB();

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

    sendSuccess('Appointment booked successfully', ['id' => $appointmentId]);

} catch (Exception $e) {
    sendError('Failed: ' . $e->getMessage(), 500);
}
