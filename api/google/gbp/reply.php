<?php
/**
 * POST /api/google/gbp/reply.php
 * Body: { review_id: "accounts/1/locations/2/reviews/3", comment: "..." }
 *
 * Posts the owner's reply to a Google review. Replying again to the same review
 * edits the existing reply — Google allows only one per review.
 */
require_once __DIR__ . '/_bootstrap.php';

gbp_run(function ($userId, $service) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendError('Method not allowed', 405);
    }
    $input = getInput();
    $res = $service->replyToReview(
        $userId,
        (string)($input['review_id'] ?? ''),
        (string)($input['comment'] ?? ''),
        'manual'
    );
    sendSuccess('Reply posted to Google', $res);
});
