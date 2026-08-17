<?php
/**
 * GET  /api/google/gbp/review-requests.php
 *      → { review_link, business_name, requests:[{id,customer_name,phone,channel,created_at}] }
 *
 * POST /api/google/gbp/review-requests.php
 *      Body: { name?, phone, channel? }  channel = whatsapp | sms | copy
 *      → { id, name, phone, channel, last_asked }
 *
 * This endpoint does NOT send anything. The owner's own WhatsApp or SMS app
 * sends the message; the app calls POST to record that they did. Sending from
 * our servers would put us in the business of bulk messaging on someone else's
 * behalf, and Google's review policy treats mass solicitation as abuse.
 */
require_once __DIR__ . '/_bootstrap.php';

gbp_run(function ($userId, $service) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = getInput();
        $res = $service->logReviewRequest(
            $userId,
            (string)($input['name'] ?? ''),
            (string)($input['phone'] ?? ''),
            (string)($input['channel'] ?? 'whatsapp')
        );
        sendSuccess('Review request logged', $res);
    }

    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 100;
    sendSuccess('Review requests', $service->reviewRequestContext($userId, $limit));
});
