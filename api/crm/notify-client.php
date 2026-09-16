<?php
/**
 * TAPIFY - CRM bridge: a Customer Manager nudges a customer inside the app.
 * POST /api/crm/notify-client.php
 * Header: X-CRM-API-Key
 * Body:   { user_id, title, message, feature? }
 *
 * Saved to the customer's in-app notification list AND pushed to their phone.
 * `feature` makes tapping the notification open that feature's screen, e.g.
 * "designs" → the Designs screen, via redirect_url "screen:my-designs".
 *
 * Returns whether it reached a phone: a customer who never installed the app
 * (or turned notifications off) only sees it the next time they open Tapify.
 */
require_once __DIR__ . '/_bridge.php';
require_once __DIR__ . '/../../includes/notifications.php';
crm_bridge_auth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Only POST allowed', 405);
}

$in = getInput();
$userId  = (int)($in['user_id'] ?? 0);
$title   = trim((string)($in['title'] ?? ''));
$message = trim((string)($in['message'] ?? ''));
$feature = (string)($in['feature'] ?? '');

if ($userId <= 0) {
    sendError('user_id is required', 400);
}
if ($title === '' || $message === '') {
    sendError('title and message are required', 400);
}
if (mb_strlen($title) > 120 || mb_strlen($message) > 500) {
    sendError('title must be at most 120 characters and message at most 500', 400);
}

try {
    $pdo = crm_pdo();

    $st = $pdo->prepare("SELECT id, fcm_token FROM users WHERE id = ? AND role = 'user'");
    $st->execute([$userId]);
    $customer = $st->fetch();
    if (!$customer) {
        sendError('Customer not found', 404);
    }

    $redirect = null;
    if ($feature !== '' && FeatureCatalog::exists($feature)) {
        $screens = FeatureCatalog::all()[$feature]['screens'];
        if ($screens) {
            $redirect = 'screen:' . $screens[0];
        }
    }

    $result = createAndSendNotification($pdo, $userId, $title, $message, 'crm_nudge', null, $redirect);

    sendSuccess('Sent', [
        'saved'  => (bool)$result['db'],
        'hasApp' => !empty($customer['fcm_token']),
        'pushed' => is_array($result['push']) && !empty($result['push']['ok']),
        'opens'  => $redirect,
    ]);
} catch (Throwable $e) {
    error_log('crm/notify-client: ' . $e->getMessage());
    sendError('Could not send the notification', 500);
}
