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
                'opens7d' => 0, 'opens30d' => 0, 'uses7d' => 0, 'uses30d' => 0,
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
                   SUM(kind = 'use')                                               AS uses30
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
