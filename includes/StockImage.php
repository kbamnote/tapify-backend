<?php
/**
 * TAPIFY — a stock photo for a service the customer typed.
 *
 * When a shop owner says his services are "hair cut, colour, keratin", each card
 * should show a picture of THAT, not a generic salon shot. This looks the phrase
 * up on Pexels and returns a hosted image URL.
 *
 * KEY: PEXELS_API_KEY (free — pexels.com/api). Without it every call returns
 * null and the caller falls back to its own artwork, so an unset key degrades
 * the pictures rather than breaking the build.
 *
 * WHY THE QUERY IS NOT JUST THE SERVICE NAME. "Fillings" alone returns pastry
 * and cake; "Fillings dental clinic" returns a dentist. The business category is
 * appended precisely because short service words are ambiguous out of context.
 *
 * ACCURACY. This is an unreviewed automated search and it will occasionally be
 * wrong — over this project image search has offered a wind farm for solar, a
 * kitchen island for a bed, and a rival firm's branding on a shirt. Owners can
 * swap any picture in the app's Website Builder, and results are cached so the
 * same phrase always yields the same photo rather than changing under them.
 */
class StockImage
{
    const API      = 'https://api.pexels.com/v1/search';
    const TIMEOUT  = 8;
    const CACHE_TTL_DAYS = 90;

    private static $tableReady = false;

    private static function key(): string
    {
        return trim((string)(getenv('PEXELS_API_KEY') ?: ''));
    }

    public static function isConfigured(): bool
    {
        return self::key() !== '';
    }

    /** Cache table — same phrase must always give the same photo. */
    private static function ensureTable(PDO $db): void
    {
        if (self::$tableReady) return;
        $db->exec(
            "CREATE TABLE IF NOT EXISTS stock_image_cache (
               q          VARCHAR(190) NOT NULL PRIMARY KEY,
               url        TEXT NULL,
               created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
             ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        );
        self::$tableReady = true;
    }

    private static function norm(string $s): string
    {
        $s = mb_strtolower(trim($s));
        $s = preg_replace('/[^\p{L}\p{N} ]+/u', ' ', $s);
        return trim(preg_replace('/\s+/', ' ', (string)$s));
    }

    /**
     * A landscape photo for this service, or null.
     *
     * @param string $service what the owner typed, e.g. "keratin treatment"
     * @param string $context his category, e.g. "salon" — disambiguates
     */
    public static function forService(?PDO $db, string $service, string $context = ''): ?string
    {
        $service = self::norm($service);
        if ($service === '' || !self::isConfigured()) return null;

        $q = trim($service . ' ' . self::norm($context));
        $q = mb_substr($q, 0, 180);

        if ($db) {
            try {
                self::ensureTable($db);
                $st = $db->prepare(
                    'SELECT url FROM stock_image_cache
                      WHERE q = ? AND created_at > (NOW() - INTERVAL ? DAY) LIMIT 1'
                );
                $st->execute([$q, self::CACHE_TTL_DAYS]);
                $row = $st->fetch(PDO::FETCH_ASSOC);
                // A cached NULL is a remembered "no result" — do not re-ask.
                if ($row) return $row['url'] !== null && $row['url'] !== '' ? $row['url'] : null;
            } catch (Throwable $e) {
                error_log('[STOCK] cache read failed: ' . $e->getMessage());
            }
        }

        $url = self::fetch($q);

        if ($db) {
            try {
                $st = $db->prepare(
                    'INSERT INTO stock_image_cache (q, url) VALUES (?, ?)
                     ON DUPLICATE KEY UPDATE url = VALUES(url), created_at = CURRENT_TIMESTAMP'
                );
                $st->execute([$q, $url]);
            } catch (Throwable $e) {
                error_log('[STOCK] cache write failed: ' . $e->getMessage());
            }
        }
        return $url;
    }

    private static function fetch(string $q): ?string
    {
        $url = self::API . '?' . http_build_query([
            'query'       => $q,
            'per_page'    => 1,
            'orientation' => 'landscape',
        ]);
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => self::TIMEOUT,
            CURLOPT_HTTPHEADER     => ['Authorization: ' . self::key()],
        ]);
        $body = curl_exec($ch);
        $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err  = curl_error($ch);
        curl_close($ch);

        if ($err !== '' || $code !== 200) {
            error_log('[STOCK] "' . $q . '" -> HTTP ' . $code . ' ' . $err);
            return null;
        }
        $j = json_decode((string)$body, true);
        $p = $j['photos'][0]['src'] ?? null;
        if (!is_array($p)) return null;
        // `large` is ~940px wide — enough for a 176px card and a detail page,
        // without shipping a 4000px original to a phone.
        $out = $p['large'] ?? $p['landscape'] ?? $p['medium'] ?? $p['original'] ?? null;
        return is_string($out) && $out !== '' ? $out : null;
    }
}
