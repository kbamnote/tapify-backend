<?php
/**
 * TAPIFY - Email Configuration
 * SMTP settings for sending emails
 *
 * IMPORTANT: After uploading, update DB_PASS below with your actual
 * email password from Hostinger panel.
 */

return [
    // === SMTP SETTINGS (Hostinger) ===
    'host' => 'smtp.hostinger.com',
    'port' => 465,
    'secure' => 'ssl',  // 'ssl' for port 465, 'tls' for port 587

    // === LOGIN CREDENTIALS ===
    'username' => 'info@tapify.in',
    'password' => 'YOUR_EMAIL_PASSWORD_HERE',  // ⚠️ UPDATE THIS in Hostinger File Manager!

    // === FROM ADDRESS ===
    'from_email' => 'info@tapify.in',
    'from_name' => 'Tapify',

    // === ADMIN NOTIFICATION EMAIL ===
    // Where notifications go when customers submit forms
    'admin_email' => 'info@tapify.in',
    'admin_name' => 'Tapify Admin',

    // === FEATURE TOGGLES ===
    'notifications_enabled' => true,
    'notify_new_inquiry' => true,
    'notify_new_appointment' => true,
    'notify_new_order' => true,

    // === CUSTOMER CONFIRMATIONS ===
    'send_inquiry_confirmation' => true,
    'send_appointment_confirmation' => true,
    'send_order_confirmation' => false,  // Customer already gets WhatsApp message

    // === DEBUG (set to true to log SMTP communication) ===
    'debug' => false
];
