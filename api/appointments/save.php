<?php
/**
 * TAPIFY - Save Appointment (Admin manual create/edit)
 * POST /backend/api/appointments/save.php
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') sendError('Only POST allowed', 405);

$input = getInput();
$id = (int)($input['id'] ?? 0);
$vcardId = (int)($input['vcard_id'] ?? 0);
$customerName = sanitize($input['customer_name'] ?? '');
$customerEmail = sanitize($input['customer_email'] ?? '');
$customerPhone = sanitize($input['customer_phone'] ?? '');
$serviceName = sanitize($input['service_name'] ?? '');
$appointmentDate = sanitize($input['appointment_date'] ?? '');
$appointmentTime = sanitize($input['appointment_time'] ?? '');
$durationMinutes = (int)($input['duration_minutes'] ?? 30);
$customerNotes = $input['customer_notes'] ?? '';
$adminNotes = $input['admin_notes'] ?? '';

if (!$vcardId) sendError('vCard required');
if (empty($customerName)) sendError('Customer name required');
if (empty($customerPhone)) sendError('Phone required');
if (empty($appointmentDate)) sendError('Date required');
if (empty($appointmentTime)) sendError('Time required');

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $appointmentDate)) sendError('Invalid date format');
if (!preg_match('/^\d{2}:\d{2}/', $appointmentTime)) sendError('Invalid time format');

try {
    $pdo = getDB();
    $userId = getCurrentUserId();

    // Verify vCard ownership
    $stmt = $pdo->prepare("SELECT id FROM vcards WHERE id = ? AND user_id = ? LIMIT 1");
    $stmt->execute([$vcardId, $userId]);
    if (!$stmt->fetch()) sendError('Access denied', 403);

    if ($id > 0) {
        // Update - verify ownership
        $stmt = $pdo->prepare("
            SELECT a.id FROM vcard_appointments a
            JOIN vcards v ON v.id = a.vcard_id
            WHERE a.id = ? AND v.user_id = ? LIMIT 1
        ");
        $stmt->execute([$id, $userId]);
        if (!$stmt->fetch()) sendError('Access denied', 403);

        $stmt = $pdo->prepare("UPDATE vcard_appointments
            SET vcard_id = ?, customer_name = ?, customer_email = ?, customer_phone = ?,
                service_name = ?, appointment_date = ?, appointment_time = ?,
                duration_minutes = ?, customer_notes = ?, admin_notes = ?
            WHERE id = ?");
        $stmt->execute([
            $vcardId, $customerName, $customerEmail, $customerPhone,
            $serviceName, $appointmentDate, $appointmentTime,
            $durationMinutes, $customerNotes, $adminNotes, $id
        ]);
        sendSuccess('Appointment updated', ['id' => $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO vcard_appointments
            (vcard_id, customer_name, customer_email, customer_phone, service_name,
             appointment_date, appointment_time, duration_minutes, customer_notes, admin_notes, is_read)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)");
        $stmt->execute([
            $vcardId, $customerName, $customerEmail, $customerPhone, $serviceName,
            $appointmentDate, $appointmentTime, $durationMinutes, $customerNotes, $adminNotes
        ]);
        sendSuccess('Appointment created', ['id' => $pdo->lastInsertId()]);
    }
} catch (Exception $e) {
    sendError('Failed: ' . $e->getMessage(), 500);
}
