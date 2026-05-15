<?php
/**
 * TAPIFY - Test Email
 * POST /backend/api/email/test.php
 * Sends test email to admin
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/email-helper.php';

requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') sendError('Only POST allowed', 405);

$input = getInput();
$customEmail = sanitize($input['email'] ?? '');

try {
    $config = require __DIR__ . '/../../config/email.php';

    // Check password
    if ($config['password'] === 'YOUR_EMAIL_PASSWORD_HERE' || empty($config['password'])) {
        sendError('Email password not configured. Please update backend/config/email.php with your email password.', 400);
    }

    $recipient = !empty($customEmail) ? $customEmail : $config['admin_email'];

    $sent = sendEmailNotification('test', [], $recipient);

    if ($sent) {
        sendSuccess("Test email sent to $recipient! Check your inbox.");
    } else {
        sendError('Failed to send. Check SMTP credentials in backend/config/email.php', 500);
    }
} catch (Exception $e) {
    sendError('Failed: ' . $e->getMessage(), 500);
}
