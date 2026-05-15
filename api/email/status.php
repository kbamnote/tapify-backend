<?php
/**
 * TAPIFY - Email Config Status
 * GET /backend/api/email/status.php
 * Returns whether email is configured (without revealing password)
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
requireAuth();

try {
    $config = require __DIR__ . '/../../config/email.php';

    $isConfigured = $config['password'] !== 'YOUR_EMAIL_PASSWORD_HERE' && !empty($config['password']);

    sendSuccess('Status loaded', [
        'configured' => $isConfigured,
        'host' => $config['host'],
        'port' => $config['port'],
        'username' => $config['username'],
        'from_email' => $config['from_email'],
        'from_name' => $config['from_name'],
        'admin_email' => $config['admin_email'],
        'notifications_enabled' => (bool)$config['notifications_enabled'],
        'notify_new_inquiry' => (bool)$config['notify_new_inquiry'],
        'notify_new_appointment' => (bool)$config['notify_new_appointment'],
        'notify_new_order' => (bool)$config['notify_new_order'],
        'send_inquiry_confirmation' => (bool)$config['send_inquiry_confirmation'],
        'send_appointment_confirmation' => (bool)$config['send_appointment_confirmation']
    ]);
} catch (Exception $e) {
    sendError('Failed: ' . $e->getMessage(), 500);
}
