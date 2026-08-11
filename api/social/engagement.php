<?php
/**
 * GET /api/social/engagement.php
 *
 * Likes / comments / shares for the posts this user published through Tapify,
 * plus a rolled-up total. Facebook Pages are read with pages_read_engagement,
 * Instagram with instagram_basic.
 *
 * Stale rows are refreshed a few at a time inside the request (see
 * SocialService::getEngagement) — everything else is served from cache, so the
 * cost of this endpoint does not grow with the customer's post count.
 */
require_once __DIR__ . '/_bootstrap.php';

social_run(function ($userId, $service) {
    sendSuccess('Engagement', $service->getEngagement($userId));
});
