<?php
/**
 * GET /api/ads/page-posts.php?connection_id=XX
 * Recent published posts on the connected Page, to choose one to boost.
 */
require_once __DIR__ . '/_bootstrap.php';

ads_run(function ($userId, $ads) {
    $connectionId = (int) ($_GET['connection_id'] ?? 0);
    if ($connectionId <= 0) {
        sendError('connection_id is required', 422);
    }
    sendSuccess('Posts', ['posts' => $ads->pagePosts($userId, $connectionId)]);
});
