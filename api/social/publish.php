<?php
/**
 * POST /api/social/publish.php
 * Body: {
 *   caption: string,
 *   media: [{ type:'image'|'video', url:'https://...' }],
 *   connection_ids: [1,2,...],
 *   scheduled_at?: 'YYYY-MM-DD HH:MM:SS'   // omit / null = post now
 * }
 * Publishes now (fan-out to each account) or schedules for later.
 */
require_once __DIR__ . '/_bootstrap.php';

social_run(function ($userId, $service) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendError('Method not allowed', 405);
    }
    $input = getInput();

    $caption       = (string) ($input['caption'] ?? '');
    $media         = is_array($input['media'] ?? null) ? $input['media'] : [];
    $connectionIds = is_array($input['connection_ids'] ?? null) ? $input['connection_ids'] : [];
    $scheduledAt   = !empty($input['scheduled_at']) ? (string) $input['scheduled_at'] : null;

    $result = $service->publish($userId, $caption, $media, $connectionIds, $scheduledAt);
    sendSuccess($result['status'] === 'scheduled' ? 'Scheduled' : 'Published', $result);
});
