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
                kind            ENUM('open','use','session') NOT NULL,
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
                PRIMARY KEY (user_id, feature),
                KEY idx_feature (feature)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        $log[] = 'user_feature_usage ready';

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

        return $log;
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
