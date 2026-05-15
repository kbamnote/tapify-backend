<?php
/**
 * TAPIFY - Inquiry Form Submission (with Email Notifications)
 * POST /inquiry-submit.php (also via /inquiry route)
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Only POST method allowed', 405);
}

$input = getInput();

$vcardId = (int)($input['vcard_id'] ?? 0);
$name = sanitize($input['name'] ?? '');
$email = sanitize($input['email'] ?? '');
$phone = sanitize($input['phone'] ?? '');
$message = sanitize($input['message'] ?? '');

if (!$vcardId) sendError('Invalid vCard');
if (empty($name)) sendError('Name is required');
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) sendError('Valid email is required');
if (empty($message)) sendError('Message is required');
if (strlen($message) > 2000) sendError('Message too long (max 2000 characters)');
if (strlen($name) > 150) sendError('Name too long');

if (preg_match('/(http|https|www\.)/i', $name)) {
    sendError('Invalid name');
}

try {
    $pdo = getDB();

    // Get vCard info for email
    $stmt = $pdo->prepare("SELECT id, vcard_name FROM vcards WHERE id = ? AND status = 1 LIMIT 1");
    $stmt->execute([$vcardId]);
    $vcard = $stmt->fetch();
    if (!$vcard) sendError('vCard not found');

    // Save inquiry
    $stmt = $pdo->prepare("INSERT INTO vcard_inquiries (vcard_id, name, email, phone, message, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->execute([$vcardId, $name, $email, $phone, $message]);
    $inquiryId = $pdo->lastInsertId();

    // === EMAIL NOTIFICATIONS (silent failure) ===
    try {
        if (file_exists(__DIR__ . '/includes/email-helper.php')) {
            require_once __DIR__ . '/includes/email-helper.php';

            $emailData = [
                'vcard_name' => $vcard['vcard_name'],
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'message' => $message
            ];

            // Notify admin
            sendEmailNotification('new_inquiry', $emailData);

            // Confirmation to customer
            sendEmailNotification('inquiry_confirmation', $emailData);
        }
    } catch (Exception $e) {
        // Email failure shouldn't block inquiry save
        error_log('Email notification failed: ' . $e->getMessage());
    }

    sendSuccess('Thank you! Your message has been sent.', [
        'inquiry_id' => $inquiryId
    ]);

} catch (Exception $e) {
    sendError('Failed to send: ' . $e->getMessage(), 500);
}
