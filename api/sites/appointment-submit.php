<?php
/**
 * POST /api/sites/appointment-submit.php   (PUBLIC)
 *
 * An appointment requested from a PUBLISHED builder site's Appointment section.
 * Stored in site_appointments, which the dashboard's Appointments page merges
 * with the vCard appointments.
 *
 * Anonymous visitor input, so: the site must be published, fields are validated
 * and capped, the time is normalised from 12-hour to 24-hour, and there is a
 * per-IP hourly rate limit.
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../builder/lib/SiteRepo.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') sendError('Only POST allowed', 405);

$in    = getInput();
$slug  = strtolower(trim((string)($in['site'] ?? '')));
$name  = trim((string)($in['name'] ?? ''));
$phone = trim((string)($in['phone'] ?? ''));
$date  = trim((string)($in['date'] ?? ''));
$time  = trim((string)($in['time'] ?? ''));

if ($slug === '')  sendError('This form is not configured correctly.');
if ($name === '')  sendError('Please enter your name.');
if ($phone === '') sendError('Please enter your phone number.');
if ($date === '')  sendError('Please choose a date.');
if ($time === '')  sendError('Please choose a time.');

// --- date must be a real, non-past day ---
$d = DateTime::createFromFormat('Y-m-d', $date);
if (!$d || $d->format('Y-m-d') !== $date) sendError('Please choose a valid date.');
if ($d < new DateTime('today')) sendError('Please choose a date in the future.');

// --- time arrives as "09:30 AM"; store as 24-hour TIME ---
$ts = strtotime($time);
if ($ts === false) sendError('Please choose a valid time.');
$time24 = date('H:i:s', $ts);

try {
    $site = SiteRepo::findBySlug($slug);
    if (!$site || ($site['status'] ?? '') === 'disabled') sendError('This website is not available.', 404);
    $published = SiteRepo::getPublished($site);
    if (!$published) sendError('This website is not published yet.', 400);

    $db = getDB();

    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    if ($ip !== '') {
        $st = $db->prepare(
            "SELECT COUNT(*) FROM site_appointments
              WHERE site_id = ? AND ip_address = ? AND created_at > (NOW() - INTERVAL 1 HOUR)"
        );
        $st->execute([(int)$site['id'], $ip]);
        if ((int)$st->fetchColumn() >= 10) sendError('Too many requests from this device. Please try again later.', 429);
    }

    // Don't double-book the same slot for this site.
    $st = $db->prepare(
        "SELECT id FROM site_appointments
          WHERE site_id = ? AND appointment_date = ? AND appointment_time = ? AND status != 'cancelled'"
    );
    $st->execute([(int)$site['id'], $date, $time24]);
    if ($st->fetchColumn()) sendError('That time has just been taken. Please pick another slot.');

    $st = $db->prepare(
        "INSERT INTO site_appointments
           (site_id, customer_name, customer_email, customer_phone, service_name,
            appointment_date, appointment_time, customer_notes, status, is_read, ip_address)
         VALUES (?,?,?,?,?,?,?,?,'pending',0,?)"
    );
    $st->execute([
        (int)$site['id'],
        mb_substr($name, 0, 150),
        mb_substr(trim((string)($in['email'] ?? '')), 0, 150),
        mb_substr($phone, 0, 30),
        mb_substr(trim((string)($in['service'] ?? '')), 0, 255),
        $date,
        $time24,
        mb_substr(trim((string)($in['notes'] ?? '')), 0, 2000),
        $ip,
    ]);

    // === WhatsApp (silent failure) — customer reminder + business alert ===
    // Reuses the approved 'appointment_reminder' and 'new_appointment_alert'
    // templates (same as the vCard appointment flow).
    try {
        require_once __DIR__ . '/../../includes/whatsapp-helper.php';

        // 1) confirmation to the customer
        if ($phone !== '') {
            sendWhatsAppTemplate($phone, 'appointment_reminder', [$name, $date, $time]);
        }
        // 2) new-appointment alert to the site's own business number
        $biz = $published['doc']['business'] ?? [];
        $bizPhone = trim((string)($biz['whatsapp'] ?? $biz['phone'] ?? ''));
        if ($bizPhone !== '') {
            sendWhatsAppTemplate($bizPhone, 'new_appointment_alert', [$name, ($phone !== '' ? $phone : 'Not provided'), $date, $time]);
        }
    } catch (Exception $e) {
        error_log('site appointment WhatsApp failed: ' . $e->getMessage());
    }

    sendSuccess('Appointment requested', ['id' => (int)$db->lastInsertId()]);
} catch (Exception $e) {
    error_log('site appointment-submit: ' . $e->getMessage());
    sendError('Could not send the request right now.', 500);
}
