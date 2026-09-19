<?php
/**
 * TAPIFY - Shared setup for the read-only CRM bridge endpoints
 * (clients, client, client-timeline, client-inquiries, feature-catalog).
 *
 * These are called server-to-server by the Sales CRM for its Customer Manager
 * dashboard and return customers' full details, so the key check is strict:
 *
 *   - CRM_API_KEY must be set and at least 24 characters. There is NO fallback
 *     key — create-user.php falls back to a default that sits in this public
 *     repository, which would expose every customer's details here.
 *   - compared with hash_equals() to avoid timing leaks.
 */
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/activity/FeatureCatalog.php';

// config/database.php starts a session for every request, and the DB session
// handler writes a row even when it's empty. A server-to-server sync has no
// session to keep, so drop it rather than leave a row behind on every call.
if (session_status() === PHP_SESSION_ACTIVE) {
    session_abort();
}

function crm_bridge_auth(): void
{
    $expected = (string)(getenv('CRM_API_KEY') ?: '');
    if (strlen($expected) < 24) {
        sendError('CRM bridge is not configured', 503);
    }
    $given = (string)($_SERVER['HTTP_X_CRM_API_KEY'] ?? '');
    if ($given === '' || !hash_equals($expected, $given)) {
        sendError('Unauthorized', 401);
    }
}

/** DB connection with TIMESTAMP columns returned in UTC, so every time can carry a 'Z'. */
function crm_pdo(): PDO
{
    $pdo = getDB();
    $pdo->exec("SET time_zone = '+00:00'");
    return $pdo;
}

/** "2026-09-16 14:05:40" (UTC) → "2026-09-16T14:05:40Z" */
function crm_iso($value): ?string
{
    if ($value === null || $value === '' || $value === '0000-00-00 00:00:00') {
        return null;
    }
    return str_replace(' ', 'T', substr((string)$value, 0, 19)) . 'Z';
}

/**
 * Runs one section of a response. Sections read tables that only exist once
 * their feature's migration has run, so a missing table empties that section
 * instead of failing the whole response.
 */
function crm_section(callable $fn, $fallback = null)
{
    try {
        return $fn();
    } catch (Throwable $e) {
        error_log('crm bridge section skipped: ' . $e->getMessage());
        return $fallback;
    }
}

/** "?,?,?" for an IN clause. */
function crm_placeholders(array $ids): string
{
    return implode(',', array_fill(0, count($ids), '?'));
}

/**
 * Per-feature usage for a set of customers: running totals (forever) plus
 * opens/uses in the last 7 and 30 days (from the event log).
 *
 * @return array<int, array<string, array>> userId => feature => usage
 */
function crm_usage_for(PDO $pdo, array $userIds): array
{
    $out = [];
    if (!$userIds) {
        return $out;
    }
    $in = crm_placeholders($userIds);

    crm_section(function () use ($pdo, $userIds, $in, &$out) {
        $st = $pdo->prepare("SELECT * FROM user_feature_usage WHERE user_id IN ($in)");
        $st->execute($userIds);
        foreach ($st as $r) {
            $out[(int)$r['user_id']][$r['feature']] = [
                'openCount'     => (int)$r['open_count'],
                'firstOpenedAt' => crm_iso($r['first_opened_at']),
                'lastOpenedAt'  => crm_iso($r['last_opened_at']),
                'useCount'      => (int)$r['use_count'],
                'firstUsedAt'   => crm_iso($r['first_used_at']),
                'lastUsedAt'    => crm_iso($r['last_used_at']),
                // Buttons pressed inside the feature. Added later than the rest,
                // so a database that hasn't had the migration re-run yet has no
                // such columns — read defensively rather than 500 the dashboard.
                'tapCount'      => (int)($r['tap_count'] ?? 0),
                'firstTappedAt' => crm_iso($r['first_tapped_at'] ?? null),
                'lastTappedAt'  => crm_iso($r['last_tapped_at'] ?? null),
                'opens7d' => 0, 'opens30d' => 0, 'uses7d' => 0, 'uses30d' => 0,
                'taps7d' => 0, 'taps30d' => 0,
            ];
        }
    });

    crm_section(function () use ($pdo, $userIds, $in, &$out) {
        // An app launch counts as opening "app"; logins don't count as opens.
        $st = $pdo->prepare("
            SELECT user_id, feature,
                   SUM(is_open AND created_at >= UTC_TIMESTAMP() - INTERVAL 7 DAY) AS opens7,
                   SUM(is_open)                                                    AS opens30,
                   SUM(kind = 'use' AND created_at >= UTC_TIMESTAMP() - INTERVAL 7 DAY) AS uses7,
                   SUM(kind = 'use')                                               AS uses30,
                   SUM(kind = 'tap' AND created_at >= UTC_TIMESTAMP() - INTERVAL 7 DAY) AS taps7,
                   SUM(kind = 'tap')                                               AS taps30
              FROM (SELECT user_id, feature, kind, created_at,
                           (kind = 'open' OR (kind = 'session' AND action = 'app_open')) AS is_open
                      FROM user_activity_events
                     WHERE user_id IN ($in) AND created_at >= UTC_TIMESTAMP() - INTERVAL 30 DAY) e
             GROUP BY user_id, feature
        ");
        $st->execute($userIds);
        foreach ($st as $r) {
            $u = (int)$r['user_id'];
            if (!isset($out[$u][$r['feature']])) {
                continue; // only events newer than any total — can't happen, totals are written with events
            }
            $out[$u][$r['feature']]['opens7d']  = (int)$r['opens7'];
            $out[$u][$r['feature']]['opens30d'] = (int)$r['opens30'];
            $out[$u][$r['feature']]['uses7d']   = (int)$r['uses7'];
            $out[$u][$r['feature']]['uses30d']  = (int)$r['uses30'];
            $out[$u][$r['feature']]['taps7d']   = (int)$r['taps7'];
            $out[$u][$r['feature']]['taps30d']  = (int)$r['taps30'];
        }
    });

    return $out;
}

/**
 * What each event means to a Customer Manager. The dashboard doesn't want
 * fifteen counters, it wants five numbers that answer "is this working for
 * them" — so every event rolls up into one of these groups.
 */
const CRM_ENGAGEMENT_GROUPS = [
    'view'             => 'views',   // card opened, website page visited, store opened
    'scan'             => 'scans',   // QR code or Google review card scanned
    'inquiry'          => 'leads',
    'appointment'      => 'leads',
    'order'            => 'leads',
    'redirect_google'  => 'reviews', // sent on to Google to leave a review
    'review_submitted' => 'reviews',
];

/** Any tap the visitor made — Call, WhatsApp, Save Contact, directions… */
function crm_engagement_group(string $event): string
{
    if (strncmp($event, 'tap_', 4) === 0) {
        return 'taps';
    }
    return CRM_ENGAGEMENT_GROUPS[$event] ?? 'other';
}

/** An empty set of the five numbers, so a client with nothing still reports zeros. */
function crm_engagement_zero(): array
{
    return ['views' => 0, 'taps' => 0, 'scans' => 0, 'leads' => 0, 'reviews' => 0, 'other' => 0];
}

/**
 * What the customers' OWN audience did, for a set of customers: how many people
 * opened their card (and whether by NFC tap, QR scan or a shared link), visited
 * the website they built, scanned their Google review card, and what they
 * tapped afterwards.
 *
 * Read from the daily rollup, so the cost does not grow with traffic.
 *
 * @return array<int, array> userId => summary
 */
function crm_engagement_for(PDO $pdo, array $userIds): array
{
    $out = [];
    if (!$userIds) {
        return $out;
    }
    $in = crm_placeholders($userIds);

    crm_section(function () use ($pdo, $userIds, $in, &$out) {
        $st = $pdo->prepare("
            SELECT user_id, asset_type, event, source,
                   SUM(hits) AS total,
                   SUM(IF(day >= UTC_DATE() - INTERVAL 6  DAY, hits, 0)) AS d7,
                   SUM(IF(day >= UTC_DATE() - INTERVAL 29 DAY, hits, 0)) AS d30,
                   SUM(IF(day >= UTC_DATE() - INTERVAL 29 DAY, visitors, 0)) AS people30
              FROM engagement_daily
             WHERE user_id IN ($in)
             GROUP BY user_id, asset_type, event, source
        ");
        $st->execute($userIds);

        foreach ($st as $r) {
            $u = (int)$r['user_id'];
            if (!isset($out[$u])) {
                $out[$u] = [
                    'total'    => crm_engagement_zero(),
                    'd7'       => crm_engagement_zero(),
                    'd30'      => crm_engagement_zero(),
                    'people30' => 0,
                    'bySource' => [],   // how they arrived: nfc, qr, link, whatsapp, social, search, direct
                    'byEvent'  => [],   // every individual counter, for the detail page
                    'byAsset'  => [],   // card vs website vs store vs QR vs review card
                    'lastAt'   => null,
                ];
            }
            $group = crm_engagement_group($r['event']);

            // Card opens and website visits are different questions; keep them apart.
            $asset = $r['asset_type'];
            if (!isset($out[$u]['byAsset'][$asset])) {
                $out[$u]['byAsset'][$asset] = ['total' => crm_engagement_zero(), 'd30' => crm_engagement_zero()];
            }
            $out[$u]['byAsset'][$asset]['total'][$group] += (int)$r['total'];
            $out[$u]['byAsset'][$asset]['d30'][$group] += (int)$r['d30'];
            foreach (['total', 'd7', 'd30'] as $window) {
                $out[$u][$window][$group] += (int)$r[$window === 'total' ? 'total' : $window];
            }
            $out[$u]['people30'] += (int)$r['people30'];

            $event = $r['event'];
            $cur = $out[$u]['byEvent'][$event] ?? ['total' => 0, 'd7' => 0, 'd30' => 0];
            $cur['total'] += (int)$r['total'];
            $cur['d7']    += (int)$r['d7'];
            $cur['d30']   += (int)$r['d30'];
            $out[$u]['byEvent'][$event] = $cur;

            // Source is only meaningful for arriving somewhere, not for taps.
            if ($group === 'views' || $group === 'scans') {
                $src = $r['source'];
                $cur = $out[$u]['bySource'][$src] ?? ['total' => 0, 'd30' => 0];
                $cur['total'] += (int)$r['total'];
                $cur['d30']   += (int)$r['d30'];
                $out[$u]['bySource'][$src] = $cur;
            }
        }
    });

    crm_section(function () use ($pdo, $userIds, $in, &$out) {
        $st = $pdo->prepare("SELECT user_id, MAX(created_at) last_at FROM engagement_events
                              WHERE user_id IN ($in) GROUP BY user_id");
        $st->execute($userIds);
        foreach ($st as $r) {
            $u = (int)$r['user_id'];
            if (isset($out[$u])) {
                $out[$u]['lastAt'] = crm_iso($r['last_at']);
            }
        }
    });

    return $out;
}

/** The customer fields shared by the list and the detail endpoint. */
function crm_client_base(array $u): array
{
    return [
        'tapifyUserId'  => (int)$u['id'],
        'name'          => $u['name'],
        'email'         => $u['email'],
        'phone'         => $u['phone'],
        'active'        => (int)$u['status'] === 1,
        'emailVerified' => (int)$u['email_verified'] === 1,
        'signedUpAt'    => crm_iso($u['created_at']),
        'lastLoginAt'   => crm_iso($u['last_login']),
        'lastActiveAt'  => crm_iso($u['last_active_at'] ?? null),
        'lastPlatform'  => $u['last_platform'] ?? null,
        'app' => [
            'installed'   => !empty($u['app_first_seen_at']),
            'firstSeenAt' => crm_iso($u['app_first_seen_at'] ?? null),
            'lastSeenAt'  => crm_iso($u['app_last_seen_at'] ?? null),
            'platform'    => $u['app_platform'] ?? null,
            'version'     => $u['app_version'] ?? null,
        ],
    ];
}
