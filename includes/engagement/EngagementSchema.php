<?php
/**
 * TAPIFY - Schema for public engagement tracking.
 *
 * What the customer's OWN audience does: card views and NFC taps, website
 * visits, review-card scans, QR scans, and every tap that follows (call,
 * WhatsApp, save contact, directions...).
 *
 * Three tables, because the questions asked of this data are different:
 *   engagement_events    one row per thing that happened — the timeline
 *   engagement_daily     per day/asset/event/source totals — the charts
 *   engagement_visitors  one row per visitor per asset per day — the uniques
 *
 * Idempotent: safe to run any number of times. All timestamps are UTC, as
 * everywhere else in the backend.
 */
final class EngagementSchema
{
    /** @return string[] human-readable log of what was done */
    public static function migrate(PDO $pdo): array
    {
        $log = [];

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS engagement_events (
                id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                user_id     INT UNSIGNED    NOT NULL,
                asset_type  ENUM('card','site','store','qr','review_card') NOT NULL,
                asset_id    INT UNSIGNED    NOT NULL,
                event       VARCHAR(30)     NOT NULL,
                source      VARCHAR(12)     NOT NULL DEFAULT 'direct',
                label       VARCHAR(80)     DEFAULT NULL,
                device      VARCHAR(10)     DEFAULT NULL,
                os          VARCHAR(12)     DEFAULT NULL,
                browser     VARCHAR(20)     DEFAULT NULL,
                city        VARCHAR(60)     DEFAULT NULL,
                country     VARCHAR(60)     DEFAULT NULL,
                visitor     CHAR(16)        DEFAULT NULL,
                is_repeat   TINYINT(1)      NOT NULL DEFAULT 0,
                referrer    VARCHAR(120)    DEFAULT NULL,
                created_at  DATETIME        NOT NULL,
                PRIMARY KEY (id),
                KEY idx_user_time (user_id, created_at),
                KEY idx_user_event (user_id, event, created_at),
                KEY idx_asset (asset_type, asset_id, created_at),
                KEY idx_time (created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        $log[] = 'engagement_events ready';

        // Kept forever: it is what the Customer Manager's trends are drawn from,
        // and one row per day/asset/event/source stays small.
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS engagement_daily (
                user_id    INT UNSIGNED NOT NULL,
                asset_type ENUM('card','site','store','qr','review_card') NOT NULL,
                asset_id   INT UNSIGNED NOT NULL,
                event      VARCHAR(30)  NOT NULL,
                source     VARCHAR(12)  NOT NULL DEFAULT 'direct',
                day        DATE         NOT NULL,
                hits       INT UNSIGNED NOT NULL DEFAULT 0,
                visitors   INT UNSIGNED NOT NULL DEFAULT 0,
                PRIMARY KEY (user_id, asset_type, asset_id, event, source, day),
                KEY idx_user_day (user_id, day),
                KEY idx_day (day)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        $log[] = 'engagement_daily ready';

        // Purely to answer "how many different people", and to tell a repeat
        // visit from a first one. Pruned after 90 days.
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS engagement_visitors (
                user_id    INT UNSIGNED NOT NULL,
                asset_type ENUM('card','site','store','qr','review_card') NOT NULL,
                asset_id   INT UNSIGNED NOT NULL,
                day        DATE         NOT NULL,
                visitor    CHAR(16)     NOT NULL,
                seen_at    DATETIME     NOT NULL,
                hits       INT UNSIGNED NOT NULL DEFAULT 1,
                PRIMARY KEY (user_id, asset_type, asset_id, day, visitor),
                KEY idx_day (day)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        $log[] = 'engagement_visitors ready';

        return array_merge($log, self::backfill($pdo));
    }

    /**
     * Brings the history that Tapify already had into the new rollup: review-card
     * scans and redirects (funnel_analytics), dynamic QR scans (dynamic_qr_scans)
     * and website visits (site_views). Without this the Customer Manager's
     * dashboard would start at zero for customers who have been getting scans
     * for months.
     *
     * Only runs for an asset type with no rows yet, so re-running the migration
     * can never double-count. Per-visit detail (device, city, repeat visitor)
     * starts from the day tracking goes live; only the daily totals are restored.
     *
     * @return string[]
     */
    private static function backfill(PDO $pdo): array
    {
        $log = [];
        $has = static function (string $assetType) use ($pdo): bool {
            $st = $pdo->prepare('SELECT 1 FROM engagement_daily WHERE asset_type = ? LIMIT 1');
            $st->execute([$assetType]);
            return (bool)$st->fetchColumn();
        };
        $run = static function (string $assetType, string $what, string $sql) use ($pdo, $has, &$log): void {
            if ($has($assetType)) {
                $log[] = "$what: already present, left alone";
                return;
            }
            try {
                $n = $pdo->exec($sql);
                $log[] = "$what: " . (int)$n . ' day(s) of history restored';
            } catch (Throwable $e) {
                // The source table belongs to a feature that may not be installed.
                $log[] = "$what: skipped (" . $e->getMessage() . ')';
            }
        };

        $run('review_card', 'Google review card scans', "
            INSERT INTO engagement_daily (user_id, asset_type, asset_id, event, source, day, hits, visitors)
            SELECT f.user_id, 'review_card', a.funnel_id,
                   IF(a.event_type = 'scan', 'scan', 'redirect_google'), 'qr',
                   DATE(a.created_at), COUNT(*), COUNT(DISTINCT a.ip_address)
              FROM funnel_analytics a
              JOIN review_funnels f ON f.id = a.funnel_id
             GROUP BY f.user_id, a.funnel_id, a.event_type, DATE(a.created_at)
        ");

        $run('qr', 'Dynamic QR scans', "
            INSERT INTO engagement_daily (user_id, asset_type, asset_id, event, source, day, hits, visitors)
            SELECT q.user_id, 'qr', s.qr_id, 'scan', 'qr',
                   DATE(s.scanned_at), COUNT(*), COUNT(DISTINCT s.ip_address)
              FROM dynamic_qr_scans s
              JOIN dynamic_qrs q ON q.id = s.qr_id
             GROUP BY q.user_id, s.qr_id, DATE(s.scanned_at)
        ");

        $run('site', 'Website visits', "
            INSERT INTO engagement_daily (user_id, asset_type, asset_id, event, source, day, hits, visitors)
            SELECT s.user_id, 'site', v.site_id, 'view', 'direct', v.view_date, SUM(v.views), 0
              FROM site_views v
              JOIN sites s ON s.id = v.site_id
             GROUP BY s.user_id, v.site_id, v.view_date
        ");

        return $log;
    }
}
