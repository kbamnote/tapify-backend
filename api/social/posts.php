<?php
/**
 * GET /api/social/posts.php?limit=30
 * Post history with per-target (per-account) status.
 */
require_once __DIR__ . '/_bootstrap.php';

social_run(function ($userId, $service) {
    $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 30;
    sendSuccess('Posts', ['posts' => $service->listPosts($userId, $limit)]);
});
