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

    // Get vCard info for email + WhatsApp (phone = the card's own contact number)
    $stmt = $pdo->prepare("SELECT id, user_id, vcard_name, phone, phone_country_code FROM vcards WHERE id = ? AND status = 1 LIMIT 1");
    $stmt->execute([$vcardId]);
    $vcard = $stmt->fetch();
    if (!$vcard) sendError('vCard not found');

    // Optional file attachment (multipart submissions from Pro templates)
    $attachment = null;
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
        $up = uploadToCloudinary($_FILES['attachment']);
        if (empty($up['success'])) {
            $up = uploadFile($_FILES['attachment'], ['jpg','jpeg','png','gif','webp','pdf']);
        }
        if (!empty($up['success'])) {
            $attachment = $up['url'] ?? ($up['path'] ?? null);
        }
    }

    // Save inquiry (fall back to legacy column set if `attachment` doesn't exist yet)
    try {
        $stmt = $pdo->prepare("INSERT INTO vcard_inquiries (vcard_id, name, email, phone, message, attachment, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$vcardId, $name, $email, $phone, $message, $attachment]);
    } catch (Exception $eCol) {
        $stmt = $pdo->prepare("INSERT INTO vcard_inquiries (vcard_id, name, email, phone, message, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$vcardId, $name, $email, $phone, $message]);
    }
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

    // === PUSH NOTIFICATIONS ===
    try {
        if (isset($vcard['user_id']) && file_exists(__DIR__ . '/includes/notifications.php')) {
            require_once __DIR__ . '/includes/notifications.php';
            $title = "New Lead Received";
            $message = "$name has sent an inquiry via your vCard.";
            createAndSendNotification($pdo, $vcard['user_id'], $title, $message, 'lead', $inquiryId, '/leads', null);
        }
    } catch (Exception $e) {
        error_log('Push notification failed: ' . $e->getMessage());
    }

    // === WHATSAPP (silent failure) ===
    // Customer gets a confirmation; the business owner gets a new-lead alert on
    // the vCard's own contact number (the number set on the card).
    try {
        if (file_exists(__DIR__ . '/includes/whatsapp-helper.php')) {
            require_once __DIR__ . '/includes/whatsapp-helper.php';

            // 1) Confirmation to the customer (only if they left a phone)
            if (!empty($phone)) {
                sendWhatsAppTemplate($phone, 'welcome', [$name]);
            }

            // 2) New-lead alert to the vCard's own WhatsApp number.
            // NB: $message was reused for the push text above — re-read the
            // original inquiry text from $input for the alert body.
            $bizPhone = wa_business_phone($vcard['phone'] ?? '', $vcard['phone_country_code'] ?? '');
            if (!empty($bizPhone)) {
                $origMsg = trim(preg_replace('/\s+/', ' ', sanitize($input['message'] ?? '')));
                if ($origMsg === '') $origMsg = '(no message)';
                if (function_exists('mb_strlen') && mb_strlen($origMsg) > 300) {
                    $origMsg = mb_substr($origMsg, 0, 297) . '...';
                }
                $custPhone = ($phone !== '') ? $phone : 'Not provided';
                sendWhatsAppTemplate($bizPhone, 'new_inquiry_alert', [$name, $custPhone, $origMsg]);
            }
        }
    } catch (Exception $e) {
        error_log('WhatsApp inquiry notification failed: ' . $e->getMessage());
    }

    sendSuccess('Thank you! Your message has been sent.', [
        'inquiry_id' => $inquiryId
    ]);

} catch (Exception $e) {
    sendError('Failed to send: ' . $e->getMessage(), 500);
}
