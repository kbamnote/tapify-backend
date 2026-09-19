<?php
/**
 * TAPIFY - Schema for customer activity tracking.
 *
 * Idempotent: safe to run any number of times. Written in PHP rather than a
 * .sql file because MySQL 8 has no `ADD COLUMN IF NOT EXISTS`, so the user
 * columns are added only after checking information_schema.
 *
 * All timestamps are UTC (written with UTC_TIMESTAMP()), matching the rest of
 * the backend's convention for data compared across PHP (IST) and MySQL (UTC).
 */
final class ActivitySchema
{
    /** @return string[] human-readable log of what was done */
    public static function migrate(PDO $pdo): array
    {
        $log = [];

        // One row per thing a customer did. Kept 12 months (see pruneOld()).
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS user_activity_events (
                id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                user_id         INT UNSIGNED    NOT NULL,
                feature         VARCHAR(40)     NOT NULL,
                action          VARCHAR(40)     NOT NULL,
                kind            ENUM('open','use','session','tap') NOT NULL,
                platform        VARCHAR(10)     NOT NULL DEFAULT 'unknown',
                app_version     VARCHAR(20)     DEFAULT NULL,
                detail          VARCHAR(120)    DEFAULT NULL,
                client_event_id VARCHAR(40)     DEFAULT NULL,
                created_at      DATETIME        NOT NULL,
                PRIMARY KEY (id),
                KEY idx_user_time (user_id, created_at),
                KEY idx_time (created_at),
                UNIQUE KEY uk_client_event (user_id, client_event_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        $log[] = 'user_activity_events ready';

        // Running totals per customer per feature — kept forever, so "used it,
        // when, how many times" is one row lookup rather than a scan of events.
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS user_feature_usage (
                user_id         INT UNSIGNED NOT NULL,
                feature         VARCHAR(40)  NOT NULL,
                first_opened_at DATETIME     DEFAULT NULL,
                last_opened_at  DATETIME     DEFAULT NULL,
                open_count      INT UNSIGNED NOT NULL DEFAULT 0,
                first_used_at   DATETIME     DEFAULT NULL,
                last_used_at    DATETIME     DEFAULT NULL,
                use_count       INT UNSIGNED NOT NULL DEFAULT 0,
                first_tapped_at DATETIME     DEFAULT NULL,
                last_tapped_at  DATETIME     DEFAULT NULL,
                tap_count       INT UNSIGNED NOT NULL DEFAULT 0,
                PRIMARY KEY (user_id, feature),
                KEY idx_feature (feature)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        $log[] = 'user_feature_usage ready';

        // 'tap' arrived later: every button press in the app, so a customer who
        // browses and taps but saves nothing is no longer invisible. MySQL in
        // strict mode REFUSES an unlisted enum value, so a table created before
        // this must be widened or every tap would be dropped at insert.
        $kindType = self::columnType($pdo, 'user_activity_events', 'kind');
        if ($kindType !== null && strpos($kindType, "'tap'") === false) {
            $pdo->exec("ALTER TABLE user_activity_events
                        MODIFY kind ENUM('open','use','session','tap') NOT NULL");
            $log[] = "user_activity_events.kind widened to include 'tap'";
        } else {
            $log[] = 'user_activity_events.kind already allows tap';
        }

        $usageColumns = [
            'first_tapped_at' => 'DATETIME DEFAULT NULL',
            'last_tapped_at'  => 'DATETIME DEFAULT NULL',
            'tap_count'       => 'INT UNSIGNED NOT NULL DEFAULT 0',
        ];
        foreach ($usageColumns as $name => $definition) {
            if (self::columnExists($pdo, 'user_feature_usage', $name)) {
                $log[] = "user_feature_usage.$name already present";
                continue;
            }
            $pdo->exec("ALTER TABLE user_feature_usage ADD COLUMN `$name` $definition");
            $log[] = "user_feature_usage.$name added";
        }

        $columns = [
            'last_active_at'    => 'DATETIME DEFAULT NULL',
            'last_platform'     => 'VARCHAR(10) DEFAULT NULL',
            'app_first_seen_at' => 'DATETIME DEFAULT NULL',
            'app_last_seen_at'  => 'DATETIME DEFAULT NULL',
            'app_platform'      => 'VARCHAR(10) DEFAULT NULL',
            'app_version'       => 'VARCHAR(20) DEFAULT NULL',
        ];
        foreach ($columns as $name => $definition) {
            if (self::columnExists($pdo, 'users', $name)) {
                $log[] = "users.$name already present";
                continue;
            }
            $pdo->exec("ALTER TABLE users ADD COLUMN `$name` $definition");
            $log[] = "users.$name added";
        }

        if (!self::indexExists($pdo, 'users', 'idx_last_active')) {
            $pdo->exec('ALTER TABLE users ADD INDEX idx_last_active (last_active_at)');
            $log[] = 'users.idx_last_active added';
        }

        $log[] = self::backfillAppSeen($pdo) . ' account(s) marked as having the app, from their recorded events';

        return $log;
    }

    /**
     * "Has the app" is normally set as the request arrives. An account can still
     * miss it — the flag was added after they installed, or their build predates
     * the app's client header and the write that would have set it happened
     * before this column existed. Any event already recorded from android or ios
     * is proof enough, so the flag is rebuilt from the events themselves.
     *
     * Only ever fills blanks (COALESCE / GREATEST), so it cannot move a date
     * that was recorded live, and re-running it changes nothing.
     */
    public static function backfillAppSeen(PDO $pdo): int
    {
        $sql = "
            UPDATE users u
              JOIN (SELECT user_id,
                           MIN(created_at) AS first_seen,
                           MAX(created_at) AS last_seen,
                           SUBSTRING_INDEX(GROUP_CONCAT(platform ORDER BY created_at DESC), ',', 1) AS platform
                      FROM user_activity_events
                     WHERE platform IN ('android','ios')
                     GROUP BY user_id) e ON e.user_id = u.id
               SET u.updated_at        = u.updated_at,
                   u.app_first_seen_at = LEAST(COALESCE(u.app_first_seen_at, e.first_seen), e.first_seen),
                   u.app_last_seen_at  = GREATEST(COALESCE(u.app_last_seen_at, e.last_seen), e.last_seen),
                   u.app_platform      = COALESCE(u.app_platform, e.platform),
                   u.last_active_at    = GREATEST(COALESCE(u.last_active_at, e.last_seen), e.last_seen)
             WHERE u.app_first_seen_at IS NULL
                OR u.app_last_seen_at IS NULL
                OR u.app_last_seen_at < e.last_seen
                OR u.app_first_seen_at > e.first_seen
        ";
        return (int)$pdo->exec($sql);
    }

    public static function isReady(PDO $pdo): bool
    {
        return self::columnExists($pdo, 'users', 'app_version')
            && self::tableExists($pdo, 'user_feature_usage');
    }

    private static function tableExists(PDO $pdo, string $table): bool
    {
        $st = $pdo->prepare(
            'SELECT 1 FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?'
        );
        $st->execute([$table]);
        return (bool)$st->fetchColumn();
    }

    private static function columnExists(PDO $pdo, string $table, string $column): bool
    {
        $st = $pdo->prepare(
            'SELECT 1 FROM information_schema.COLUMNS
              WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?'
        );
        $st->execute([$table, $column]);
        return (bool)$st->fetchColumn();
    }

    /** e.g. "enum('open','use','session')", or null if the column isn't there. */
    private static function columnType(PDO $pdo, string $table, string $column): ?string
    {
        $st = $pdo->prepare(
            'SELECT COLUMN_TYPE FROM information_schema.COLUMNS
              WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?'
        );
        $st->execute([$table, $column]);
        $type = $st->fetchColumn();
        return $type === false ? null : (string)$type;
    }

    private static function indexExists(PDO $pdo, string $table, string $index): bool
    {
        $st = $pdo->prepare(
            'SELECT 1 FROM information_schema.STATISTICS
              WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND INDEX_NAME = ?'
        );
        $st->execute([$table, $index]);
        return (bool)$st->fetchColumn();
    }
}
