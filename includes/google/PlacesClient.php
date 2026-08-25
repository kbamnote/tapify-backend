<?php
/**
 * TAPIFY — Google Places API (New), server-side.
 *
 * This is the ONLY way to look at a business's Google listing without that
 * business having connected their account to us. GoogleBusinessClient needs an
 * OAuth token; a stranger who has just messaged the WhatsApp bot has none. So
 * the bot's score is built from what Places exposes publicly — which is roughly
 * what a customer searching for them can see, and no more.
 *
 * Deliberately independent of the OAuth stack (GoogleHttp/GoogleOAuth): this
 * authenticates with a plain server API key and must keep working even if a
 * customer's OAuth connection is broken or absent.
 *
 * KEY: GOOGLE_MAPS_SERVER_KEY — the same server key the CRM already uses for
 * Roads and Geocoding. It needs "Places API (New)" enabled in the GCP project;
 * a key restricted to Android apps will NOT work for these web-service calls.
 *
 * COST: these calls are billed per request, so every caller must go through the
 * cache in api/public/visibility-score.php rather than hitting this directly in
 * a loop.
 */
class PlacesClient
{
    const SEARCH_URL  = 'https://places.googleapis.com/v1/places:searchText';
    const DETAILS_URL = 'https://places.googleapis.com/v1/places/';
    const TIMEOUT     = 12;

    /** Only the fields the score actually reads — the field mask drives billing. */
    const SEARCH_FIELDS  = 'places.id,places.displayName,places.formattedAddress,places.businessStatus';
    const DETAILS_FIELDS = 'id,displayName,formattedAddress,rating,userRatingCount,photos,'
                         . 'regularOpeningHours,websiteUri,editorialSummary,primaryTypeDisplayName,'
                         . 'nationalPhoneNumber,businessStatus,googleMapsUri';

    /**
     * Everything above PLUS the review bodies, for building a website.
     *
     * Deliberately a SEPARATE mask. The field mask is what Google bills on, and
     * details() runs on every score check while detailsFull() runs once per site
     * build — asking for review text on the cheap path would put the cost on the
     * request we make most often. `rating` and `userRatingCount` are already in
     * the mask above, so `reviews` does not move this to a dearer tier; it is
     * the same class of Places data we are already paying for.
     */
    const SITE_FIELDS = self::DETAILS_FIELDS . ',reviews';

    /** @var string */
    private $key;

    public function __construct($key = null)
    {
        $this->key = $key ?: (getenv('GOOGLE_MAPS_SERVER_KEY') ?: '');
    }

    public function isConfigured(): bool
    {
        return $this->key !== '';
    }

    /**
     * Find candidate businesses by free text ("Galaxy Car Decor Nagpur").
     *
     * Returns AT MOST $limit candidates, each {place_id, name, address}. The bot
     * shows these for the owner to confirm — scoring the wrong shop loses the
     * lead outright, so we never auto-pick even when there is only one result.
     *
     * RETURNS NULL WHEN THE CALL ITSELF FAILED, and an empty array only when
     * Google genuinely had no match. Collapsing those two into one empty array
     * made the bot tell customers "I could not find your business on Google"
     * while the real problem was our own API key — blaming the customer for our
     * outage, and hiding the outage from us.
     */
    public function searchText(string $query, int $limit = 3): ?array
    {
        $query = trim($query);
        if ($query === '') return [];
        if (!$this->isConfigured()) return null;

        $body = [
            'textQuery'     => $query,
            'maxResultCount' => max(1, min(5, $limit)),
            // Bias to India. The bot is advertised there, and without this a
            // shop name that also exists abroad can win on global prominence.
            'regionCode'    => 'IN',
            'languageCode'  => 'en',
        ];

        $res = $this->post(self::SEARCH_URL, $body, self::SEARCH_FIELDS);
        if ($res === null) return null;          // the call failed, not "no match"
        $out = [];
        foreach (($res['places'] ?? []) as $p) {
            if (empty($p['id'])) continue;
            $out[] = [
                'place_id' => $p['id'],
                'name'     => $p['displayName']['text'] ?? '',
                'address'  => $p['formattedAddress'] ?? '',
                'status'   => $p['businessStatus'] ?? null,
            ];
            if (count($out) >= $limit) break;
        }
        return $out;
    }

    /**
     * The public signals for one place, already normalised for VisibilityScore.
     *
     * NOTE ON PHOTOS: Places returns at most 10 photo references regardless of
     * how many the listing actually holds. `photos_total` is therefore a FLOOR,
     * and `photos_capped` says so — the score must not tell someone with 40
     * photos that they have 10, and the wording has to hedge at the cap.
     */
    public function details(string $placeId): ?array
    {
        $res = $this->fetchRaw($placeId);
        if (!$res || empty($res['id'])) return null;
        return $this->normalizeDetails($res);
    }

    /**
     * One raw Places Details response, shared by every reader below.
     *
     * $fields lets the caller choose how much to pay for: the default lean mask
     * for score checks, the wider SITE_FIELDS when a website is being built.
     */
    private function fetchRaw(string $placeId, ?string $fields = null): ?array
    {
        $placeId = trim($placeId);
        if ($placeId === '' || !$this->isConfigured()) return null;

        // The id already carries its own prefix in the New API ("places/ChIJ..."),
        // but callers hand us the bare id, so normalise either shape.
        $path = strpos($placeId, 'places/') === 0 ? substr($placeId, 7) : $placeId;
        return $this->get(self::DETAILS_URL . rawurlencode($path), $fields ?: self::DETAILS_FIELDS);
    }

    /** The score-shaped projection VisibilityScore reads. Kept byte-identical. */
    private function normalizeDetails(array $res): array
    {
        $photos = is_array($res['photos'] ?? null) ? count($res['photos']) : 0;
        $desc   = $res['editorialSummary']['text'] ?? '';

        return [
            'place_id'      => $res['id'],
            'name'          => $res['displayName']['text'] ?? '',
            'address'       => $res['formattedAddress'] ?? '',
            'maps_url'      => $res['googleMapsUri'] ?? null,
            'status'        => $res['businessStatus'] ?? null,

            // — signals VisibilityScore reads —
            'reviews_total'   => isset($res['userRatingCount']) ? (int)$res['userRatingCount'] : 0,
            'rating'          => isset($res['rating']) ? (float)$res['rating'] : 0.0,
            'photos_total'    => $photos,
            'photos_capped'   => $photos >= 10,
            'has_hours'       => !empty($res['regularOpeningHours']),
            'has_website'     => !empty($res['websiteUri']),
            'description_len' => mb_strlen((string)$desc),
            'has_category'    => !empty($res['primaryTypeDisplayName']['text']),
            'has_phone'       => !empty($res['nationalPhoneNumber']),
            'category'        => $res['primaryTypeDisplayName']['text'] ?? '',
            'website'         => $res['websiteUri'] ?? '',
        ];
    }

    /**
     * details() PLUS everything a website generator needs: photo media URLs,
     * the weekly hours lines, Google's editorial description and the listing's
     * own phone number. Still ONE Places call — the exact same request
     * details() makes; this simply refuses to throw the answer away.
     *
     * @return array{gmb_photos:array[],gmb_hours_lines:string[],gmb_description:string,gmb_phone:string,...} normalized details + gmb_* extras
     */
    public function detailsFull(string $placeId): ?array
    {
        $res = $this->fetchRaw($placeId, self::SITE_FIELDS);
        if (!$res || empty($res['id'])) return null;

        $out = $this->normalizeDetails($res);

        // Real Google reviews, for the site's testimonials. Only reviews with
        // actual text are useful — a bare 5-star rating with no words renders as
        // an empty quote card. Anonymised names are left as Google returns them.
        $out['gmb_reviews'] = [];
        foreach (array_slice(is_array($res['reviews'] ?? null) ? $res['reviews'] : [], 0, 12) as $rv) {
            $text = trim((string)($rv['originalText']['text'] ?? $rv['text']['text'] ?? ''));
            if ($text === '') continue;
            $out['gmb_reviews'][] = [
                'quote'  => mb_substr($text, 0, 600),
                'name'   => trim((string)($rv['authorAttribution']['displayName'] ?? '')) ?: 'Google review',
                'photo'  => trim((string)($rv['authorAttribution']['photoUri'] ?? '')),
                'rating' => isset($rv['rating']) ? (int)round((float)$rv['rating']) : 0,
            ];
        }

        // Public <img> tags cannot send an API-key header, so the key rides in
        // the URL — the documented way to serve Places media on a website.
        $out['gmb_photos'] = [];
        foreach (array_slice(is_array($res['photos'] ?? null) ? $res['photos'] : [], 0, 10) as $p) {
            if (empty($p['name'])) continue;
            $alt = trim((string)($p['authorAttributions'][0]['displayName'] ?? ''));
            $out['gmb_photos'][] = [
                'url' => 'https://places.googleapis.com/v1/' . $p['name']
                       . '/media?maxHeightPx=1200&maxWidthPx=1200&key=' . urlencode($this->key),
                'alt' => $alt !== '' ? $alt : null,
            ];
        }

        $out['gmb_hours_lines'] = is_array($res['regularOpeningHours']['weekdayDescriptions'] ?? null)
            ? $res['regularOpeningHours']['weekdayDescriptions']
            : [];
        $out['gmb_description'] = trim((string)($res['editorialSummary']['text'] ?? ''));
        $out['gmb_phone']       = trim((string)($res['nationalPhoneNumber'] ?? ''));

        return $out;
    }

    /**
     * Turn a pasted Google Maps link into something searchable.
     *
     * The bot invites this ("you can also paste your Google Maps link"), and a
     * shortened maps.app.goo.gl URL handed straight to Places as a text query
     * matches nothing — which is what customers were seeing. Following the
     * redirect gives a full /maps/place/<Name>/@lat,lng URL, and the name out
     * of that is a far better query than anything they would have typed.
     *
     * Costs no Places quota: it is a plain redirect fetch, not an API call.
     *
     * @return string|null the business name, or null if this was not a Maps link
     */
    public function resolveMapsLink(string $text): ?string
    {
        $text = trim($text);
        if (!preg_match('~https?://\S+~i', $text, $m)) return null;
        $url = $m[0];
        if (!preg_match('~(google\.[a-z.]+/maps|maps\.app\.goo\.gl|goo\.gl/maps)~i', $url)) return null;

        $final = $url;
        // Follow up to 5 hops by hand: CURLOPT_FOLLOWLOCATION is disabled on
        // some hosts with open_basedir set, and silently not following would
        // look exactly like a bad link.
        for ($i = 0; $i < 5; $i++) {
            $ch = curl_init($final);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HEADER         => true,
                CURLOPT_NOBODY         => true,
                CURLOPT_FOLLOWLOCATION => false,
                CURLOPT_TIMEOUT        => 10,
                CURLOPT_USERAGENT      => 'Mozilla/5.0',
            ]);
            $head = curl_exec($ch);
            $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if ($head === false) break;
            if ($code >= 300 && $code < 400 && preg_match('~^Location:\s*(\S+)~mi', $head, $lm)) {
                $final = trim($lm[1]);
                continue;
            }
            break;
        }

        // .../maps/place/The+Business+Name/@21.1,79.1,17z/...
        if (preg_match('~/maps/place/([^/@?]+)~', $final, $pm)) {
            $name = urldecode(str_replace('+', ' ', $pm[1]));
            $name = trim(preg_replace('/\s+/', ' ', $name));
            if ($name !== '') return $name;
        }
        // ...?q=Business+Name  /  ...&query=Business+Name
        if (preg_match('~[?&](?:q|query)=([^&]+)~', $final, $qm)) {
            $name = trim(urldecode(str_replace('+', ' ', $qm[1])));
            if ($name !== '' && !preg_match('~^-?\d+\.\d+,~', $name)) return $name;
        }
        return null;
    }

    /* --------------------------------------------------------- spend guard */
    /*
     * The daily ceiling lives HERE, not in one endpoint, because more than one
     * caller spends money: the bot's lookups AND the weekly follow-up job,
     * which re-scores every lead it chases. The counter started life as two
     * private functions inside visibility-score.php, which meant the follow-up
     * job spent freely past the cap — exactly the path most likely to run away,
     * since it loops without anyone watching.
     */

    public static function ensureUsageTable(PDO $db): void
    {
        try {
            $db->exec(
                "CREATE TABLE IF NOT EXISTS place_api_usage (
                   day   DATE NOT NULL PRIMARY KEY,
                   calls INT  NOT NULL DEFAULT 0
                 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
            );
        } catch (Exception $e) {
            error_log('[PLACES] usage table setup failed: ' . $e->getMessage());
        }
    }

    /** False once today's Places calls have hit VISIBILITY_DAILY_CAP. */
    public static function spendAllowed(PDO $db): bool
    {
        $cap = (int)(getenv('VISIBILITY_DAILY_CAP') ?: 2000);
        if ($cap <= 0) return true;                 // 0 disables the ceiling
        try {
            $st = $db->prepare("SELECT calls FROM place_api_usage WHERE day = CURDATE()");
            $st->execute();
            return (int)$st->fetchColumn() < $cap;
        } catch (Exception $e) {
            // If the counter cannot be read we allow the call: blocking every
            // lookup because a counter table is unavailable would be worse than
            // the bill, and the 7-day cache absorbs most traffic anyway.
            return true;
        }
    }

    public static function countCall(PDO $db): void
    {
        try {
            $db->exec(
                "INSERT INTO place_api_usage (day, calls) VALUES (CURDATE(), 1)
                 ON DUPLICATE KEY UPDATE calls = calls + 1"
            );
        } catch (Exception $e) {
            error_log('[PLACES] usage count failed: ' . $e->getMessage());
        }
    }

    /** Today's spend, for the stats endpoint. */
    public static function callsToday(PDO $db): int
    {
        try {
            $st = $db->prepare("SELECT calls FROM place_api_usage WHERE day = CURDATE()");
            $st->execute();
            return (int)$st->fetchColumn();
        } catch (Exception $e) { return 0; }
    }

    /* ----------------------------------------------------------- transport */

    private function post(string $url, array $body, string $fields): ?array
    {
        return $this->call($url, $fields, json_encode($body));
    }

    private function get(string $url, string $fields): ?array
    {
        return $this->call($url, $fields, null);
    }

    private function call(string $url, string $fields, $payload): ?array
    {
        $ch = curl_init($url);
        $headers = [
            'X-Goog-Api-Key: ' . $this->key,
            'X-Goog-FieldMask: ' . $fields,
        ];
        if ($payload !== null) {
            $headers[] = 'Content-Type: application/json';
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, self::TIMEOUT);

        $raw  = curl_exec($ch);
        $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err  = curl_error($ch);
        curl_close($ch);

        if ($raw === false) {
            self::log('places.transport', ['error' => $err]);
            return null;
        }
        $data = json_decode($raw, true);
        if ($code < 200 || $code >= 300) {
            // Logged, never thrown: a Places outage must degrade the bot to
            // "could not check right now", not break the conversation.
            self::log('places.error', [
                'status'  => $code,
                'message' => $data['error']['message'] ?? substr((string)$raw, 0, 200),
            ]);
            return null;
        }
        return is_array($data) ? $data : null;
    }

    private static function log(string $event, array $ctx): void
    {
        if (class_exists('GoogleLogger')) { GoogleLogger::error($event, $ctx); return; }
        error_log('[PLACES] ' . $event . ' ' . json_encode($ctx));
    }
}
