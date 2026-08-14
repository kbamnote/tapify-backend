<?php
/**
 * POST /api/google/gbp/auto-reply.php
 * Body: { enabled: bool, min_stars?: 1..5 }
 *
 * Opt-in automatic replying. Off by default, and the star floor defaults to 4:
 * praise is answered instantly, complaints wait for a human. Auto-replying to a
 * one-star review in the customer's own name, without them reading it, is how
 * this feature turns into a complaint about us.
 */
require_once __DIR__ . '/_bootstrap.php';

gbp_run(function ($userId, $service) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendError('Method not allowed', 405);
    }
    $input = getInput();
    $res = $service->setAutoReply(
        $userId,
        !empty($input['enabled']),
        $input['min_stars'] ?? 4
    );
    sendSuccess('Auto-reply settings saved', $res);
});
