<?php
/**
 * TAPIFY - Email Notification Helper
 * Easy way to send notifications throughout the app
 *
 * Usage:
 *   require_once __DIR__ . '/email-helper.php';
 *   sendEmailNotification('new_inquiry', $data);
 */

require_once __DIR__ . '/mailer.php';
require_once __DIR__ . '/email-templates.php';

/**
 * Send email notification
 *
 * @param string $type new_inquiry, inquiry_confirmation, new_appointment,
 *                     appointment_confirmation, new_order, test
 * @param array $data Data for the email template
 * @param string $to Recipient email (optional - uses admin_email by default)
 * @return bool Success
 */
function sendEmailNotification($type, $data = [], $to = null) {
    try {
        $config = require __DIR__ . '/../config/email.php';

        // Check if notifications enabled
        if (empty($config['notifications_enabled'])) {
            return false;
        }

        // Validate password is set
        if ($config['password'] === 'YOUR_EMAIL_PASSWORD_HERE' || empty($config['password'])) {
            error_log('Tapify Email: Password not configured in backend/config/email.php');
            return false;
        }

        // Generate template
        $email = null;
        $recipient = $to;
        $recipientName = '';

        switch ($type) {
            case 'new_inquiry':
                if (empty($config['notify_new_inquiry'])) return false;
                $email = EmailTemplates::newInquiryAdmin($data);
                $recipient = $recipient ?: $config['admin_email'];
                $recipientName = $config['admin_name'];
                break;

            case 'inquiry_confirmation':
                if (empty($config['send_inquiry_confirmation'])) return false;
                if (empty($data['email'])) return false;
                $email = EmailTemplates::inquiryConfirmation($data);
                $recipient = $data['email'];
                $recipientName = $data['name'] ?? '';
                break;

            case 'new_appointment':
                if (empty($config['notify_new_appointment'])) return false;
                $email = EmailTemplates::newAppointmentAdmin($data);
                $recipient = $recipient ?: $config['admin_email'];
                $recipientName = $config['admin_name'];
                break;

            case 'appointment_confirmation':
                if (empty($config['send_appointment_confirmation'])) return false;
                if (empty($data['customer_email'])) return false;
                $email = EmailTemplates::appointmentConfirmation($data);
                $recipient = $data['customer_email'];
                $recipientName = $data['customer_name'] ?? '';
                break;

            case 'new_order':
                if (empty($config['notify_new_order'])) return false;
                $email = EmailTemplates::newOrderAdmin($data);
                $recipient = $recipient ?: $config['admin_email'];
                $recipientName = $config['admin_name'];
                break;

            case 'test':
                $email = EmailTemplates::testEmail();
                $recipient = $recipient ?: $config['admin_email'];
                $recipientName = $config['admin_name'];
                break;

            default:
                return false;
        }

        if (!$email || !$recipient) return false;

        // Send via SMTP
        $mailer = new TapifyMailer($config);
        return $mailer->send($recipient, $email['subject'], $email['html'], '', $recipientName);

    } catch (Exception $e) {
        error_log('Tapify Email Helper: ' . $e->getMessage());
        return false;
    }
}
