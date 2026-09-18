<?php
/**
 * TAPIFY - Public engagement tracker.
 *
 * Records what the CUSTOMER'S audience does — the other half of the Customer
 * Manager dashboard. ActivityTracker answers "what did the customer do in the
 * app"; this answers "did it work for them": how many people opened their card,
 * whether it was an NFC tap or a QR scan, how many visited the website they
 * built, how many scanned the Google review card and went on to leave a review,
 * how many tapped Call or WhatsApp.
 *
 * It is called from public pages that a stranger is waiting on, so:
 *   - nothing is written during the request. Events are queued and flushed by a
 *     shutdown function, after the page has been sent;
 *   - it never throws into a page and never prints;
 *   - bots, link-preview fetchers and the owner's own preview are dropped;
 *   - a repeat view of the same asset by the same visitor within VIEW_DEDUPE_MIN
 *     minutes is not counted again (a reload is not a new visit).
 *
 * Until run-engagement-migration.php has been run it silently does nothing.
 */
final class Engagement
{
    // How the audience arrived. `nfc` and `qr` come from the ?s= parameter that
    // Tapify writes into NFC chips and generated QR codes; the rest is inferred
    // from the referrer.
    public const SOURCES = ['nfc', 'qr', 'link', 'whatsapp', 'social', 'search', 'direct'];

    private const VIEW_DEDUPE_MIN = 30;
    private const RETENTION_MONTHS = 12;   // engagement_events
    private const VISITOR_DAYS = 90;       // engagement_visitors
    private const MAX_QUEUE = 20;

    /** @var array<int, array> */
    private static array $queue = [];
    private static bool $hooked = false;
    private static ?PDO $pdo = null;
    private static bool $failed = false;

    /**
     * Queue one thing that happened.
     *
     * @param int    $userId    the Tapify customer who owns the asset
     * @param string $assetType card|site|store|qr|review_card
     * @param string $event     view · tap_call · tap_whatsapp · scan · inquiry · …
     * @param array  $opts      source, label, visitorSalt, dedupe (bool), at
     */
    public static function record(int $userId, string $assetType, int $assetId, string $event, array $opts = []): void
    {
        try {
            // CLI means a cron job or a script, not a visitor — except in the
            // test harness, which defines TAPIFY_ENGAGEMENT_ALLOW_CLI.
            if ($userId <= 0 || $assetId <= 0
                || (PHP_SAPI === 'cli' && !defined('TAPIFY_ENGAGEMENT_ALLOW_CLI'))) {
                return;
            }
            if (self::isBot() || count(self::$queue) >= self::MAX_QUEUE) {
                return;
            }

            $ua = (string)($_SERVER['HTTP_USER_AGENT'] ?? '');
            self::$queue[] = [
                'userId'    => $userId,
                'assetType' => $assetType,
                'assetId'   => $assetId,
                'event'     => substr($event, 0, 30),
                'source'    => $opts['source'] ?? self::source(),
                'label'     => isset($opts['label']) && $opts['label'] !== '' ? substr((string)$opts['label'], 0, 80) : null,
                'device'    => self::device($ua),
                'os'        => self::os($ua),
                'browser'   => self::browser($ua),
                'visitor'   => self::visitor(),
                'referrer'  => self::referrer(),
                'dedupe'    => (bool)($opts['dedupe'] ?? ($event === 'view')),
                'at'        => $opts['at'] ?? gmdate('Y-m-d H:i:s'),
            ];

            if (!self::$hooked) {
                self::$hooked = true;
                register_shutdown_function([self::class, 'flush']);
            }
        } catch (Throwable $e) {
            self::fail($e);
        }
    }

    /** Asset type => [table, owner column] — for record()ing without an owner in hand. */
    private const OWNER_OF = [
        'card'        => ['vcards', 'user_id'],
        'site'        => ['sites', 'user_id'],
        'store'       => ['whatsapp_stores', 'user_id'],
        'qr'          => ['dynamic_qrs', 'user_id'],
        'review_card' => ['review_funnels', 'user_id'],
    ];

    /**
     * Same as record(), for the callers that know the asset but not its owner
     * (an inquiry form knows the card id, not the customer behind it).
     * A lookup that finds nothing is simply not recorded.
     */
    public static function forAsset(PDO $pdo, string $assetType, int $assetId, string $event, array $opts = []): void
    {
        try {
            if (!isset(self::OWNER_OF[$assetType]) || $assetId <= 0) {
                return;
            }
            [$table, $ownerCol] = self::OWNER_OF[$assetType];
            $st = $pdo->prepare("SELECT $ownerCol FROM $table WHERE id = ? LIMIT 1");
            $st->execute([$assetId]);
            $userId = (int)$st->fetchColumn();
            $st->closeCursor();
            if ($userId > 0) {
                self::record($userId, $assetType, $assetId, $event, ['dedupe' => false] + $opts);
            }
        } catch (Throwable $e) {
            self::fail($e);
        }
    }

    /** Runs after the page has been sent. */
    public static function flush(): void
    {
        $queue = self::$queue;
        self::$queue = [];
        if (!$queue) {
            return;
        }
        try {
            $pdo = self::db();
            if ($pdo === null) {
                return;
            }
            // Long-running pages (a rendered site) can outlive the visitor's
            // interest; the write itself must not hold a connection open.
            @ignore_user_abort(true);

            $insertEvent = $pdo->prepare(
                'INSERT INTO engagement_events
                    (user_id, asset_type, asset_id, event, source, label, device, os, browser,
                     visitor, is_repeat, referrer, created_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
            );
            $insertVisitor = $pdo->prepare(
                'INSERT INTO engagement_visitors (user_id, asset_type, asset_id, day, visitor, seen_at, hits)
                 VALUES (?, ?, ?, ?, ?, ?, 1)
                 ON DUPLICATE KEY UPDATE hits = hits + 1, seen_at = VALUES(seen_at)'
            );
            $lastSeen = $pdo->prepare(
                'SELECT seen_at FROM engagement_visitors
                  WHERE user_id = ? AND asset_type = ? AND asset_id = ? AND day = ? AND visitor = ?'
            );
            $bumpDaily = $pdo->prepare(
                'INSERT INTO engagement_daily (user_id, asset_type, asset_id, event, source, day, hits, visitors)
                 VALUES (?, ?, ?, ?, ?, ?, 1, ?)
                 ON DUPLICATE KEY UPDATE hits = hits + 1, visitors = visitors + VALUES(visitors)'
            );

            foreach ($queue as $e) {
                $day = substr($e['at'], 0, 10);
                $isRepeat = 0;
                $isNewVisitor = 1;

                if ($e['visitor'] !== null) {
                    $lastSeen->execute([$e['userId'], $e['assetType'], $e['assetId'], $day, $e['visitor']]);
                    $seenAt = $lastSeen->fetchColumn();
                    $lastSeen->closeCursor();

                    if ($seenAt !== false && $seenAt !== null) {
                        $isRepeat = 1;
                        $isNewVisitor = 0;
                        // A reload or a second tap of the same card minutes apart
                        // is the same visit, not a new one.
                        if ($e['dedupe'] && (strtotime($e['at']) - strtotime($seenAt)) < self::VIEW_DEDUPE_MIN * 60) {
                            $insertVisitor->execute([
                                $e['userId'], $e['assetType'], $e['assetId'], $day, $e['visitor'], $e['at'],
                            ]);
                            continue;
                        }
                    }
                    $insertVisitor->execute([
                        $e['userId'], $e['assetType'], $e['assetId'], $day, $e['visitor'], $e['at'],
                    ]);
                } else {
                    $isNewVisitor = 0; // can't tell people apart without a visitor key
                }

                $insertEvent->execute([
                    $e['userId'], $e['assetType'], $e['assetId'], $e['event'], $e['source'], $e['label'],
                    $e['device'], $e['os'], $e['browser'], $e['visitor'], $isRepeat, $e['referrer'], $e['at'],
                ]);
                $bumpDaily->execute([
                    $e['userId'], $e['assetType'], $e['assetId'], $e['event'], $e['source'], $day, $isNewVisitor,
                ]);
            }

            // Retention, opportunistic so no cron is needed.
            if (random_int(1, 500) === 1) {
                self::pruneOld();
            }
        } catch (Throwable $e) {
            self::fail($e);
        }
    }

    public static function pruneOld(): void
    {
        try {
            $pdo = self::db();
            if ($pdo === null) {
                return;
            }
            $pdo->exec('DELETE FROM engagement_events
                         WHERE created_at < UTC_TIMESTAMP() - INTERVAL ' . self::RETENTION_MONTHS . ' MONTH
                         LIMIT 5000');
            $pdo->exec('DELETE FROM engagement_visitors
                         WHERE day < UTC_DATE() - INTERVAL ' . self::VISITOR_DAYS . ' DAY
                         LIMIT 5000');
        } catch (Throwable $e) {
            self::fail($e);
        }
    }

    // ───── request inspection ─────

    /**
     * Where the visitor came from.
     *
     * `?s=nfc` is written into the NFC chip, `?s=qr` into generated QR codes
     * (Engagement::taggedUrl()). Anything else is guessed from the referrer:
     * a WhatsApp/Instagram/Google click is worth telling apart from a stranger
     * tapping a card in person.
     */
    public static function source(): string
    {
        $s = strtolower(trim((string)($_GET['s'] ?? $_GET['src'] ?? '')));
        if ($s !== '') {
            if (in_array($s, self::SOURCES, true)) {
                return $s;
            }
            if ($s === 'card' || $s === 'tag') {
                return 'nfc';
            }
        }

        $ref = strtolower((string)($_SERVER['HTTP_REFERER'] ?? ''));
        if ($ref === '') {
            return 'direct';
        }
        $host = (string)(parse_url($ref, PHP_URL_HOST) ?: '');
        if ($host === '' || self::isOwnHost($host)) {
            return 'direct';
        }
        foreach ([
            'whatsapp' => ['whatsapp', 'wa.me'],
            'social'   => ['facebook', 'fb.', 'instagram', 'linkedin', 'twitter', 't.co', 'x.com', 'youtube', 'telegram', 'pinterest', 'snapchat'],
            'search'   => ['google', 'bing', 'duckduckgo', 'yahoo', 'ecosia', 'brave'],
        ] as $source => $needles) {
            foreach ($needles as $needle) {
                if (strpos($host, $needle) !== false) {
                    return $source;
                }
            }
        }
        return 'link';
    }

    /** Adds ?s=<source> to a public URL, for NFC chips and generated QR codes. */
    public static function taggedUrl(string $url, string $source): string
    {
        if (!in_array($source, self::SOURCES, true) || $url === '') {
            return $url;
        }
        return $url . (strpos($url, '?') === false ? '?' : '&') . 's=' . $source;
    }

    /**
     * A stable-but-anonymous key for one visitor: IP + user agent, hashed with
     * a daily salt, so the same person opening a card twice is recognised
     * without the IP address itself being stored.
     */
    private static function visitor(): ?string
    {
        $ip = self::ip();
        if ($ip === null) {
            return null;
        }
        $seed = $ip . '|' . ($_SERVER['HTTP_USER_AGENT'] ?? '') . '|' . gmdate('Y-m-d') . '|tapify-engagement';
        return substr(hash('sha256', $seed), 0, 16);
    }

    private static function ip(): ?string
    {
        // Railway/Vercel put the real client first in X-Forwarded-For.
        $fwd = (string)($_SERVER['HTTP_X_FORWARDED_FOR'] ?? '');
        if ($fwd !== '') {
            $first = trim(explode(',', $fwd)[0]);
            if (filter_var($first, FILTER_VALIDATE_IP)) {
                return $first;
            }
        }
        $ip = (string)($_SERVER['REMOTE_ADDR'] ?? '');
        return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : null;
    }

    private static function referrer(): ?string
    {
        $ref = (string)($_SERVER['HTTP_REFERER'] ?? '');
        if ($ref === '') {
            return null;
        }
        $host = (string)(parse_url($ref, PHP_URL_HOST) ?: '');
        return $host !== '' ? substr($host, 0, 120) : null;
    }

    private static function isOwnHost(string $host): bool
    {
        return strpos($host, 'tapify.co.in') !== false || strpos($host, 'tapifyworld.com') !== false;
    }

    /**
     * Anything that isn't a person: crawlers, and the link-preview fetchers that
     * WhatsApp/Facebook/Slack run when a card URL is pasted into a chat. Those
     * would otherwise show up as a view nobody made.
     */
    private static function isBot(): bool
    {
        $ua = strtolower((string)($_SERVER['HTTP_USER_AGENT'] ?? ''));
        if ($ua === '') {
            return true;
        }
        foreach ([
            'bot', 'crawl', 'spider', 'slurp', 'facebookexternalhit', 'whatsapp/', 'skypeuripreview',
            'telegrambot', 'twitterbot', 'linkedinbot', 'slackbot', 'discordbot', 'embedly', 'quora link',
            'preview', 'curl/', 'wget', 'python', 'axios', 'go-http', 'okhttp', 'headless', 'lighthouse',
            'pagespeed', 'gtmetrix', 'monitor', 'uptime', 'pingdom',
        ] as $needle) {
            if (strpos($ua, $needle) !== false) {
                return true;
            }
        }
        return false;
    }

    private static function device(string $ua): string
    {
        $ua = strtolower($ua);
        if (strpos($ua, 'ipad') !== false || strpos($ua, 'tablet') !== false) {
            return 'tablet';
        }
        return strpos($ua, 'mobi') !== false || strpos($ua, 'android') !== false ? 'mobile' : 'desktop';
    }

    private static function os(string $ua): ?string
    {
        foreach ([
            'android' => 'Android', 'iphone' => 'iOS', 'ipad' => 'iOS', 'ios' => 'iOS',
            'windows' => 'Windows', 'mac os' => 'macOS', 'linux' => 'Linux',
        ] as $needle => $name) {
            if (stripos($ua, $needle) !== false) {
                return $name;
            }
        }
        return null;
    }

    private static function browser(string $ua): ?string
    {
        // Order matters: Edge and Chrome both say "Chrome", Chrome says "Safari".
        foreach ([
            'edg' => 'Edge', 'opr' => 'Opera', 'samsungbrowser' => 'Samsung',
            'chrome' => 'Chrome', 'firefox' => 'Firefox', 'safari' => 'Safari',
        ] as $needle => $name) {
            if (stripos($ua, $needle) !== false) {
                return $name;
            }
        }
        return null;
    }

    // ───── infrastructure ─────

    /** Own connection, lazily opened. Null (and silent) if the DB is unreachable. */
    private static function db(): ?PDO
    {
        if (self::$pdo !== null || self::$failed) {
            return self::$pdo;
        }
        try {
            $host = getenv('DB_HOST') ?: getenv('MYSQLHOST') ?: 'localhost';
            $port = getenv('DB_PORT') ?: getenv('MYSQLPORT') ?: '3306';
            $name = getenv('DB_DATABASE') ?: getenv('MYSQLDATABASE');
            $user = getenv('DB_USERNAME') ?: getenv('MYSQLUSER');
            $pass = getenv('DB_PASSWORD') ?: getenv('MYSQLPASSWORD');
            // Same options as getDB(), minus its die() on failure: a tracking
            // problem may never break a page that has already been sent.
            self::$pdo = new PDO("mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4", $user, $pass, [
                PDO::ATTR_TIMEOUT => 5,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
            ]);
        } catch (Throwable $e) {
            self::$failed = true;
            error_log('Engagement: no DB connection: ' . $e->getMessage());
        }
        return self::$pdo;
    }

    private static function fail(Throwable $e): void
    {
        // 42S02 = table missing: the migration hasn't been run yet. Expected
        // until then — don't flood the log.
        $state = $e instanceof PDOException ? (string)($e->errorInfo[0] ?? $e->getCode()) : '';
        if ($state !== '42S02' && $state !== '42S22') {
            error_log('Engagement: ' . $e->getMessage());
        }
    }
}
