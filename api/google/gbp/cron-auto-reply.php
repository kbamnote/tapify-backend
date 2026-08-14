<?php
/**
 * GET /api/google/gbp/cron-auto-reply.php?secret=...
 *
 * Answers new Google reviews for customers who have opted in.
 *
 * Deliberately conservative, because every reply is published publicly under the
 * customer's own business name:
 *   - opt-in only, off by default
 *   - only reviews at or above the customer's star floor (default 4), so a
 *     complaint is never answered by a machine before a human has read it
 *   - never touches a review that already has a reply, from us or from them
 *   - one reply per review, recorded, so a re-run cannot double-post
 *   - a review with no text is skipped: there is nothing to reply to, and a
 *     generic response to a bare star rating reads worse than silence
 *
 * Point a scheduler at this every 15-30 minutes. Guarded by GBP_CRON_SECRET,
 * the same pattern as the social publishing cron.
 */

require_once __DIR__ . '/../../../config/database.php';
ini_set('display_errors', '0');
require_once __DIR__ . '/../../../includes/functions.php';
require_once __DIR__ . '/../../../includes/google/autoload.php';
require_once __DIR__ . '/../../../includes/ai/autoload.php';

header('Content-Type: application/json');

$secret = defined('GBP_CRON_SECRET') ? GBP_CRON_SECRET : (getenv('GBP_CRON_SECRET') ?: '');
if ($secret === '' || !hash_equals($secret, (string)($_GET['secret'] ?? ''))) {
    sendError('Forbidden', 403);
}

/** Cap per run so one busy account cannot consume the whole window. */
const MAX_PER_USER = 5;

try {
    $db      = getDB();
    $repo    = new GoogleBusinessRepo($db);
    $service = new GoogleBusinessService($db);
    $ai      = new AiService($db);

    $replied = 0; $skipped = 0; $failed = 0; $usersDone = 0;

    foreach ($repo->autoReplyUsers() as $row) {
        $userId   = (int)$row['user_id'];
        $minStars = max(1, min(5, (int)$row['auto_reply_min_stars']));
        $usersDone++;

        try {
            $data = $service->listReviews($userId, 50);
        } catch (Exception $e) {
            // A revoked token or an unenabled API must not stop everyone else.
            GoogleLogger::warn('autoreply.list_failed', ['user' => $userId]);
            $failed++;
            continue;
        }

        $done = 0;
        foreach ($data['reviews'] as $review) {
            if ($done >= MAX_PER_USER) break;

            if (!empty($review['reply']))              { $skipped++; continue; }  // already answered
            if ((int)$review['stars'] < $minStars)     { $skipped++; continue; }  // below their floor
            if (trim((string)$review['comment']) === ''){ $skipped++; continue; } // stars only, nothing to answer
            if ($repo->hasReplied($review['id']))      { $skipped++; continue; }  // we already did

            try {
                $out = $ai->generate($userId, 'review-reply', [
                    'review'        => $review['comment'],
                    'business_name' => $data['location_title'] ?? '',
                ]);
                // The tool returns four tones; "friendly" is the right register
                // for a positive review, which is all auto-reply ever handles.
                $result = $out['result'] ?? [];
                $text   = trim((string)($result['friendly'] ?? $result['professional'] ?? $result['short'] ?? ''));
                if ($text === '') { $failed++; continue; }

                $service->replyToReview($userId, $review['id'], $text, 'auto');
                $repo->recordReply($userId, $review['id'], $review['stars'], $text, 'auto');
                $replied++; $done++;
            } catch (Exception $e) {
                GoogleLogger::warn('autoreply.reply_failed', [
                    'user' => $userId, 'review' => substr($review['id'], -20),
                ]);
                $failed++;
            }
        }
    }

    sendSuccess('Auto-reply run complete', [
        'users'   => $usersDone,
        'replied' => $replied,
        'skipped' => $skipped,
        'failed'  => $failed,
    ]);

} catch (Exception $e) {
    GoogleLogger::error('autoreply.run_failed', ['error' => $e->getMessage()]);
    sendError('Auto-reply run failed.', 500);
}
