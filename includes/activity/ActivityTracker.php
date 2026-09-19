<?php
/**
 * TAPIFY - Customer activity tracker.
 *
 * Records what customers (users.role = 'user') do, for the Sales CRM's Customer
 * Manager dashboard:
 *
 *   - "last active", and whether that was the app (Android/iOS) or the website
 *   - every successful write to a feature's endpoint  → kind 'use'
 *   - every screen the Tapify app opens               → kind 'open'  (api/activity/track.php)
 *   - app sessions and logins                         → kind 'session'
 *
 * It runs on EVERY authenticated API request (booted from config/database.php),
 * so it is built to be invisible:
 *   - it never writes output and never throws — every path is caught;
 *   - it opens its own DB connection, only when there is something to write, and
 *     never calls getDB(), which die()s with a JSON error on failure and would
 *     corrupt a response that already succeeded;
 *   - "last active" is written at most once per TOUCH_EVERY per session;
 *   - until run-activity-migration.php has been run it silently does nothing.
 */
require_once __DIR__ . '/FeatureCatalog.php';

final class ActivityTracker
{
    private const TOUCH_EVERY = 300;         // seconds between last-active writes per session
    private const OPEN_EVERY = 600;          // seconds between "opened <feature>" per feature per session
    private const RETENTION_MONTHS = 12;
    private const MAX_BATCH = 100;           // app events accepted per request
    private const MAX_EVENT_AGE = 7 * 86400; // older queued app events are dropped
    private const TRACKED_ROLE = 'user';

    private static bool $booted = false;
    private static bool $touchDue = false;
    private static ?string $openFeature = null; // feature this GET counts as opening
    private static ?PDO $pdo = null;
    private static bool $failed = false;

    // ───── request lifecycle ─────

    /** Called from config/database.php, after the session has started. */
    public static function boot(): void
    {
        if (self::$booted || PHP_SAPI === 'cli') {
            return;
        }
        self::$booted = true;

        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        if ($method === 'OPTIONS' || $method === 'HEAD') {
            return;
        }

        // Decide NOW whether last-active is due. The session is saved by a
        // shutdown function registered before ours (session_set_save_handler
        // with register_shutdown = true), so a $_SESSION write made at shutdown
        // would never be stored and the throttle would never hold.
        if (self::isTrackedCustomer()) {
            $now = time();
            if ($now - (int)($_SESSION['_activity_touched_at'] ?? 0) >= self::TOUCH_EVERY) {
                $_SESSION['_activity_touched_at'] = $now;
                self::$touchDue = true;
            }

            // Reading a feature's data counts as opening it. Without this, a
            // customer who only looks — at their inquiries, their reviews, their
            // website's orders — leaves no trace at all on the web, and the
            // Customer Manager sees "never used" for a feature they live in.
            // Decided here, at request start, for the same reason as above:
            // a $_SESSION write made at shutdown would never be stored.
            // Only for the website: the app reports the screens it opens itself,
            // and counting its API reads as well would double every open.
            if ($method === 'GET' && !self::client()['app']) {
                $path = self::apiPath();
                $feature = $path === null ? null : FeatureCatalog::resolveRead($path);
                if ($feature !== null && self::claimOpen($feature, $_SESSION, $now)) {
                    self::$openFeature = $feature;
                }
            }
        }

        register_shutdown_function([self::class, 'onShutdown']);
    }

    /**
     * Whether this read counts as opening the feature, remembering it in the
     * session if so. Opening a screen loads several endpoints, and customers
     * refresh — without the throttle, one visit to Inquiries would look like
     * twenty. Pure (the session is passed in) so it can be tested directly.
     */
    public static function claimOpen(string $feature, array &$session, int $now): bool
    {
        if ($now - (int)($session['_activity_opens'][$feature] ?? 0) < self::OPEN_EVERY) {
            return false;
        }
        $session['_activity_opens'][$feature] = $now;
        // The map is per session and features are few, but a long-lived session
        // shouldn't grow forever if the catalogue changes.
        if (count($session['_activity_opens']) > 60) {
            $session['_activity_opens'] = [$feature => $now];
        }
        return true;
    }

    /** Runs after the endpoint has finished and its response has been written. */
    public static function onShutdown(): void
    {
        try {
            // Re-checked here: login sets the session during the request, logout clears it.
            if (!self::isTrackedCustomer()) {
                return;
            }
            $path = self::apiPath();
            if ($path === null || $path === 'activity/track.php') {
                return; // not an API call, or the app batch endpoint (it records its own events)
            }

            $userId = (int)$_SESSION['user_id'];
            $client = self::client();
            $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

            if ($path === 'login.php') {
                if (self::succeeded()) {
                    self::recordEvent($userId, 'app', 'login', 'session', $client, null, null);
                    self::touch($userId, $client);
                }
                return;
            }

            // Only the Tapify app registers a push token, so a successful
            // registration is proof the app is installed — even on app builds
            // too old to send the client header.
            if ($path === 'notifications/update-token.php') {
                if (self::succeeded()) {
                    if (!$client['app']) {
                        $client = ['platform' => 'unknown', 'app' => true, 'version' => null];
                    }
                    self::touch($userId, $client);
                }
                return;
            }

            $write = null;
            if (!in_array($method, ['GET', 'HEAD', 'OPTIONS'], true) && self::succeeded()) {
                $write = FeatureCatalog::resolveWrite($path, $_GET, self::requestBody());
            }
            if ($write !== null) {
                // One timestamp for both, so the event and the running total agree.
                $at = gmdate('Y-m-d H:i:s');
                // Prefer WHAT they saved over WHICH endpoint saved it: a design's
                // title is what a Customer Manager can talk to the client about,
                // the path tells them nothing. Falls back to the path.
                $detail = $write['subject'] ?? $path;
                self::recordEvent($userId, $write['feature'], $write['action'], 'use', $client, $detail, $at);
                self::bumpUsage($userId, $write['feature'], 'use', $at, $at, 1);
            }

            // A GET that returned successfully: they opened the feature and saw it.
            $opened = self::$openFeature !== null && self::succeeded();
            if ($opened) {
                $at = gmdate('Y-m-d H:i:s');
                self::recordEvent($userId, self::$openFeature, 'open', 'open', $client, $path, $at);
                self::bumpUsage($userId, self::$openFeature, 'open', $at, $at, 1);
            }

            if (self::$touchDue || $write !== null || $opened) {
                self::touch($userId, $client);
            }
        } catch (Throwable $e) {
            self::fail($e);
        }
    }

    // ───── app batch ingest (api/activity/track.php) ─────

    /**
     * Stores a batch of events queued by the Tapify app.
     *
     * Each event: { id?, type: 'screen'|'action'|'app_open', screen?, feature?,
     *               action?, at?: epoch ms }
     *
     * `id` is generated by the app; a retried batch whose first attempt did
     * reach the server is de-duplicated on it rather than counted twice.
     *
     * @return array{accepted: int, duplicates: int, skipped: int}
     */
    public static function ingestAppBatch(int $userId, string $role, array $events): array
    {
        $result = ['accepted' => 0, 'duplicates' => 0, 'skipped' => 0];
        if ($role !== self::TRACKED_ROLE) {
            $result['skipped'] = count($events);
            return $result; // staff/admin testing the app: acknowledged, not recorded
        }

        $pdo = self::db();
        if ($pdo === null) {
            throw new RuntimeException('Activity storage unavailable');
        }

        $client = self::client();
        if (!$client['app']) {
            // This endpoint is only called by the app; trust that over a missing header.
            $client = ['platform' => $client['platform'] === 'web' ? 'unknown' : $client['platform'], 'app' => true, 'version' => $client['version']];
        }

        $now = time();
        $totals = []; // "feature|kind" => [first, last, count]

        $insert = $pdo->prepare(
            'INSERT IGNORE INTO user_activity_events
                (user_id, feature, action, kind, platform, app_version, detail, client_event_id, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );

        $pdo->beginTransaction();
        try {
            foreach (array_slice($events, 0, self::MAX_BATCH) as $ev) {
                $parsed = is_array($ev) ? self::parseAppEvent($ev, $now) : null;
                if ($parsed === null) {
                    $result['skipped']++;
                    continue;
                }
                [$feature, $action, $kind, $detail, $at, $clientId] = $parsed;

                $insert->execute([
                    $userId, $feature, $action, $kind, $client['platform'],
                    $client['version'], $detail, $clientId, $at,
                ]);
                if ($insert->rowCount() === 0) {
                    $result['duplicates']++;
                    continue;
                }
                $result['accepted']++;

                // Sessions roll up as opens of the pseudo-feature "app"; taps
                // are counted separately, so "opened it" and "pressed something
                // in it" stay distinguishable on the dashboard.
                $usageKind = in_array($kind, ['use', 'tap'], true) ? $kind : 'open';
                $key = "$feature|$usageKind";
                if (!isset($totals[$key])) {
                    $totals[$key] = [$at, $at, 0];
                }
                $totals[$key][0] = min($totals[$key][0], $at);
                $totals[$key][1] = max($totals[$key][1], $at);
                $totals[$key][2]++;
            }
            $result['skipped'] += max(0, count($events) - self::MAX_BATCH);

            foreach ($totals as $key => [$first, $last, $count]) {
                [$feature, $usageKind] = explode('|', $key);
                self::bumpUsage($userId, $feature, $usageKind, $first, $last, $count);
            }
            $pdo->commit();
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }

        self::touch($userId, $client);

        // Retention, done opportunistically so no cron is needed.
        if (random_int(1, 200) === 1) {
            self::pruneOld();
        }

        return $result;
    }

    /** @return array{0:string,1:string,2:string,3:?string,4:string,5:?string}|null */
    private static function parseAppEvent(array $ev, int $now): ?array
    {
        $type = (string)($ev['type'] ?? '');

        $atSec = isset($ev['at']) && is_numeric($ev['at']) ? (int)floor(((float)$ev['at']) / 1000) : $now;
        if ($atSec > $now + 300) {
            $atSec = $now; // phone clock ahead
        }
        if ($atSec < $now - self::MAX_EVENT_AGE) {
            return null; // too stale to be worth recording
        }
        $at = gmdate('Y-m-d H:i:s', $atSec);

        $clientId = null;
        if (isset($ev['id']) && is_string($ev['id']) && preg_match('/^[A-Za-z0-9_-]{8,40}$/', $ev['id'])) {
            $clientId = $ev['id'];
        }

        if ($type === 'screen') {
            $screen = (string)($ev['screen'] ?? '');
            if ($screen === '') {
                return null;
            }
            // A screen the catalogue doesn't know is still something the
            // customer opened. Dropping it was how a new screen could go
            // missing for months without anyone noticing — it is recorded
            // against "Other screens" with its own name kept in detail.
            $feature = FeatureCatalog::featureForScreen($screen) ?? FeatureCatalog::OTHER;
            return [$feature, 'open', 'open', substr($screen, 0, 120), $at, $clientId];
        }
        if ($type === 'tap') {
            $screen = (string)($ev['screen'] ?? '');
            $label = trim((string)($ev['label'] ?? ''));
            if ($label === '') {
                return null;
            }
            $feature = ($screen !== '' ? FeatureCatalog::featureForScreen($screen) : null) ?? FeatureCatalog::OTHER;
            $action = FeatureCatalog::slug($label);
            if ($action === '') {
                $action = 'tap';
            }
            // The label is kept verbatim in detail — "Save changes" reads better
            // on a timeline than "save_changes" — while action stays a slug so
            // taps on the same button group together.
            return [$feature, $action, 'tap', substr($label, 0, 120), $at, $clientId];
        }
        if ($type === 'action') {
            $feature = (string)($ev['feature'] ?? '');
            $action = FeatureCatalog::slug((string)($ev['action'] ?? ''));
            if (!FeatureCatalog::exists($feature) || $action === '') {
                return null;
            }
            // What it was done to — the design that was shared. Only the app can
            // know this for something that never reaches the server.
            $label = trim((string)($ev['label'] ?? ''));
            $label = $label === '' ? null : mb_substr($label, 0, 100);
            return [$feature, $action, 'use', $label, $at, $clientId];
        }
        if ($type === 'app_open') {
            return ['app', 'app_open', 'session', null, $at, $clientId];
        }
        return null;
    }

    // ───── writes ─────

    private static function recordEvent(int $userId, string $feature, string $action, string $kind, array $client, ?string $detail, ?string $at): void
    {
        $pdo = self::db();
        if ($pdo === null) {
            return;
        }
        $pdo->prepare(
            'INSERT INTO user_activity_events
                (user_id, feature, action, kind, platform, app_version, detail, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, COALESCE(?, UTC_TIMESTAMP()))'
        )->execute([
            $userId, $feature, $action, $kind, $client['platform'], $client['version'],
            $detail === null ? null : substr($detail, 0, 120), $at,
        ]);
    }

    /**
     * Adds to the running totals. First/last are compared, not overwritten,
     * because queued app events can arrive out of order and older than what's
     * already stored.
     */
    private static function bumpUsage(int $userId, string $feature, string $kind, string $first, string $last, int $count): void
    {
        $pdo = self::db();
        if ($pdo === null || $count < 1) {
            return;
        }
        $columns = [
            'use'  => ['first_used_at', 'last_used_at', 'use_count'],
            'tap'  => ['first_tapped_at', 'last_tapped_at', 'tap_count'],
            'open' => ['first_opened_at', 'last_opened_at', 'open_count'],
        ];
        [$f, $l, $c] = $columns[$kind] ?? $columns['open'];

        $pdo->prepare(
            "INSERT INTO user_feature_usage (user_id, feature, $f, $l, $c)
             VALUES (?, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE
                $f = IF($f IS NULL OR VALUES($f) < $f, VALUES($f), $f),
                $l = IF($l IS NULL OR VALUES($l) > $l, VALUES($l), $l),
                $c = $c + VALUES($c)"
        )->execute([$userId, $feature, $first, $last, $count]);
    }

    /**
     * Last active + app/web. `updated_at = updated_at` stops the column's
     * ON UPDATE CURRENT_TIMESTAMP from firing: being active is not an edit to
     * the account, and anything that reads updated_at must not see it change.
     */
    private static function touch(int $userId, array $client): void
    {
        $pdo = self::db();
        if ($pdo === null) {
            return;
        }
        $sql = 'UPDATE users SET updated_at = updated_at, last_active_at = UTC_TIMESTAMP(), last_platform = ?';
        $params = [$client['platform']];
        if ($client['app']) {
            $sql .= ', app_first_seen_at = COALESCE(app_first_seen_at, UTC_TIMESTAMP()),
                      app_last_seen_at = UTC_TIMESTAMP()';
            if ($client['platform'] !== 'unknown') {
                $sql .= ', app_platform = ?';
                $params[] = $client['platform'];
            }
            if ($client['version'] !== null) {
                $sql .= ', app_version = ?';
                $params[] = $client['version'];
            }
        }
        $sql .= ' WHERE id = ?';
        $params[] = $userId;
        $pdo->prepare($sql)->execute($params);
    }

    public static function pruneOld(): void
    {
        try {
            $pdo = self::db();
            if ($pdo === null) {
                return;
            }
            $pdo->exec(
                'DELETE FROM user_activity_events
                  WHERE created_at < UTC_TIMESTAMP() - INTERVAL ' . self::RETENTION_MONTHS . ' MONTH
                  LIMIT 5000'
            );
        } catch (Throwable $e) {
            self::fail($e);
        }
    }

    // ───── request inspection ─────

    private static function isTrackedCustomer(): bool
    {
        return !empty($_SESSION['user_id']) && ($_SESSION['user_role'] ?? '') === self::TRACKED_ROLE;
    }

    /** "designs/save.php" for /api/designs/save.php (or /backend/api/…), else null. */
    private static function apiPath(): ?string
    {
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
        if (!is_string($uri)) {
            return null;
        }
        $pos = strpos($uri, '/api/');
        return $pos === false ? null : substr($uri, $pos + 5);
    }

    /**
     * Where the request came from.
     *
     * Current app builds send  X-Tapify-Client: app;android;1.1.17
     * Older builds send nothing, but React Native's networking still identifies
     * itself — OkHttp on Android, CFNetwork on iOS — while every browser says
     * "Mozilla".
     *
     * @return array{platform: string, app: bool, version: ?string}
     */
    private static function client(): array
    {
        $header = (string)($_SERVER['HTTP_X_TAPIFY_CLIENT'] ?? '');
        if ($header !== '') {
            $parts = explode(';', $header);
            if (($parts[0] ?? '') === 'app') {
                $platform = in_array($parts[1] ?? '', ['android', 'ios'], true) ? $parts[1] : 'unknown';
                $version = preg_replace('/[^0-9A-Za-z.\-]/', '', (string)($parts[2] ?? ''));
                return ['platform' => $platform, 'app' => true, 'version' => $version !== '' ? substr($version, 0, 20) : null];
            }
        }

        $ua = (string)($_SERVER['HTTP_USER_AGENT'] ?? '');
        if (stripos($ua, 'okhttp') !== false) {
            return ['platform' => 'android', 'app' => true, 'version' => null];
        }
        if (stripos($ua, 'Mozilla') === false && stripos($ua, 'CFNetwork') !== false) {
            return ['platform' => 'ios', 'app' => true, 'version' => null];
        }
        return ['platform' => $ua !== '' ? 'web' : 'unknown', 'app' => false, 'version' => null];
    }

    private static function succeeded(): bool
    {
        $code = http_response_code();
        if ($code !== false && ($code < 200 || $code >= 300)) {
            return false;
        }
        // emitJson() records the envelope's `success` flag: many endpoints
        // reply 200 with { success: false } for a refused action.
        return ($GLOBALS['__tapify_json_success'] ?? null) !== false;
    }

    /** JSON body (or form fields), read only for small requests. */
    private static function requestBody(): array
    {
        if ((int)($_SERVER['CONTENT_LENGTH'] ?? 0) > 100000) {
            return [];
        }
        $raw = file_get_contents('php://input');
        $decoded = is_string($raw) && $raw !== '' ? json_decode($raw, true) : null;
        if (is_array($decoded)) {
            return $decoded;
        }
        return is_array($_POST) ? $_POST : [];
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
            // Same connection options as getDB(), minus its die() on failure.
            self::$pdo = new PDO("mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4", $user, $pass, [
                PDO::ATTR_TIMEOUT => 5,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
            ]);
        } catch (Throwable $e) {
            self::$failed = true;
            error_log('ActivityTracker: no DB connection: ' . $e->getMessage());
        }
        return self::$pdo;
    }

    private static function fail(Throwable $e): void
    {
        // 42S02 = table missing, 42S22 = column missing: the migration hasn't
        // been run yet. Expected until then — don't flood the log.
        $state = $e instanceof PDOException ? (string)($e->errorInfo[0] ?? $e->getCode()) : '';
        if ($state !== '42S02' && $state !== '42S22') {
            error_log('ActivityTracker: ' . $e->getMessage());
        }
    }
}
