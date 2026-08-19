<?php
/**
 * GET  /api/google/gbp/posts.php   → { posts[], total, actions[] }
 * POST /api/google/gbp/posts.php   Body: { summary, action?, action_url?, image_url? }
 *
 * Google Posts live on the legacy v4 surface — the same "Google My Business
 * API" as reviews and media, so no separate enablement is needed beyond what
 * those already use.
 *
 * As with photos, Google fetches the image from image_url itself, so it has to
 * be publicly hosted first. The app uploads to Cloudinary and passes that link.
 */
require_once __DIR__ . '/_bootstrap.php';

gbp_run(function ($userId, $service) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = getInput();
        $res = $service->createPost(
            $userId,
            (string)($input['summary'] ?? ''),
            (string)($input['action'] ?? ''),
            (string)($input['action_url'] ?? ''),
            (string)($input['image_url'] ?? '')
        );
        sendSuccess('Posted to your Google listing', $res);
    }

    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
    sendSuccess('Posts', $service->listPosts($userId, $limit));
});
