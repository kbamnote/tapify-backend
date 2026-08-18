<?php
/**
 * TAPIFY Google Business Profile — orchestration service.
 * The single entry point endpoints use: connection status, OAuth completion,
 * location selection, and reading/writing profile fields.
 */
class GoogleBusinessService
{
    /** @var PDO */
    private $db;
    /** @var GoogleBusinessRepo */
    private $repo;

    public function __construct(PDO $db)
    {
        $this->db   = $db;
        $this->repo = new GoogleBusinessRepo($db);
    }

    /** Create a one-time OAuth state for this user and return the consent URL. */
    public function buildConnectUrl($userId)
    {
        if (!GoogleOAuth::isConfigured()) {
            throw new GoogleException('Google Business Profile is not configured on the server yet.', 503,
                'GOOGLE_CLIENT_ID/SECRET empty');
        }
        $state = $this->repo->createState($userId);
        return GoogleOAuth::buildAuthUrl($state);
    }

    /** Handle the OAuth callback: validate state, store tokens, discover location. */
    public function completeOAuth($code, $state)
    {
        $userId = $this->repo->consumeState($state);
        if (!$userId) {
            throw new GoogleException('This sign-in link has expired. Please try connecting again.', 400,
                'invalid/expired oauth state');
        }

        $tokens = GoogleOAuth::exchangeCode($code);
        $access  = $tokens['access_token']  ?? null;
        $refresh = $tokens['refresh_token'] ?? null;
        if (!$access) {
            throw new GoogleException('Google sign-in failed. Please try again.', 502, 'no access_token from exchange');
        }
        $expiry = date('Y-m-d H:i:s', time() + (int) ($tokens['expires_in'] ?? 3600));
        $this->repo->upsertTokens($userId, $access, $refresh, $expiry, $tokens['scope'] ?? GOOGLE_BUSINESS_SCOPE);

        // Best-effort discovery of the first account + location. Never fail the
        // callback on this — the OAuth connection itself succeeded.
        try {
            $this->autoSelectFirstLocation($userId);
        } catch (Exception $e) {
            GoogleLogger::warn('discovery.failed', ['error' => $e->getMessage()]);
        }

        return $userId;
    }

    private function autoSelectFirstLocation($userId)
    {
        $client   = $this->client($userId);
        $accounts = $client->listAccounts();
        if (empty($accounts)) return;

        $account   = $accounts[0];
        $accountId = $account['name'] ?? null;      // "accounts/123"
        if (!$accountId) return;

        $locations = $client->listLocations($accountId);
        if (empty($locations)) {
            $this->repo->setLocation($userId, $accountId, $account['accountName'] ?? '', null, null);
            return;
        }
        $loc = $locations[0];
        $this->repo->setLocation($userId, $accountId, $account['accountName'] ?? '',
            $loc['name'] ?? null, $loc['title'] ?? '');
    }

    /** Connection status for the app. */
    public function getStatus($userId)
    {
        $conn = $this->repo->get($userId);
        return [
            'configured' => GoogleOAuth::isConfigured(),
            'connected'  => $conn !== null,
            'location'   => ($conn && $conn['location_id'])
                ? ['id' => $conn['location_id'], 'title' => $conn['location_title']]
                : null,
        ];
    }

    /** All locations under the connected account (for the picker). */
    public function listLocations($userId)
    {
        $conn = $this->requireConnection($userId);
        $client = $this->client($userId);
        $accountId = $conn['google_account_id'];
        if (!$accountId) {
            $accounts = $client->listAccounts();
            if (empty($accounts)) return [];
            $accountId = $accounts[0]['name'] ?? null;
        }
        $locations = $client->listLocations($accountId);
        return array_map(function ($l) {
            return ['id' => $l['name'] ?? '', 'title' => $l['title'] ?? '(untitled)'];
        }, $locations);
    }

    public function selectLocation($userId, $locationId, $title = null)
    {
        $conn = $this->requireConnection($userId);
        $this->repo->setLocation($userId, $conn['google_account_id'], $conn['account_name'], $locationId, $title);
    }

    /** Current editable + display fields, read live from Google. */
    public function getFields($userId)
    {
        $conn = $this->requireConnection($userId);
        if (empty($conn['location_id'])) {
            throw new GoogleException('No Google Business location is linked yet.', 404, 'location_id empty');
        }
        $client = $this->client($userId);
        $loc = $client->getLocation($conn['location_id'], FieldMap::readMask());
        return FieldMap::toApp($loc);
    }

    /** Write editable fields back to Google, return the refreshed field set. */
    public function updateFields($userId, array $input)
    {
        $conn = $this->requireConnection($userId);
        if (empty($conn['location_id'])) {
            throw new GoogleException('No Google Business location is linked yet.', 404, 'location_id empty');
        }

        // Keep only editable fields that were sent.
        $clean = [];
        foreach (FieldMap::editableFields() as $f) {
            if (array_key_exists($f, $input)) {
                $clean[$f] = is_string($input[$f]) ? trim($input[$f]) : $input[$f];
            }
        }
        if (!$clean) {
            throw new GoogleException('Nothing to update.', 422, 'no editable fields provided');
        }

        list($mask, $body) = FieldMap::buildPatch($clean);
        $client = $this->client($userId);
        $client->patchLocation($conn['location_id'], $mask, $body);

        // Return fresh state so the app reflects exactly what Google stored.
        $loc = $client->getLocation($conn['location_id'], FieldMap::readMask());
        return FieldMap::toApp($loc);
    }

    /* ------------------------------------------------------- profile score */

    /**
     * Health score for the connected listing, plus the movement since the last
     * time it was computed.
     *
     * Recording every computation is what makes the number worth returning to:
     * "78, up from 54" is progress, "78" is trivia. We only write a new row when
     * the score actually changed, so opening the screen five times in a day does
     * not manufacture a flat history.
     */
    public function getScore($userId)
    {
        $fields = $this->getFields($userId);

        // Three more signals, each on a separately-enabled Google API. Any of
        // them can fail — API not switched on, transient error — and each one
        // that does passes null, which drops its item from the score rather than
        // reporting a zero the customer cannot explain or fix.
        $signals = ['photos' => null, 'services' => null, 'attributes' => null];

        try { $signals['photos'] = $this->countPhotos($userId); } catch (Exception $e) { }

        // Services come from the location read we already did — no extra call,
        // and no reason for it to be absent unless the field itself was.
        if (array_key_exists('services', $fields) && is_array($fields['services'])) {
            $signals['services'] = count($fields['services']);
        }

        try {
            $attrs = $this->listAttributes($userId);
            $signals['attributes'] = (int)($attrs['set'] ?? 0);
        } catch (Exception $e) { }

        $result = ProfileScore::compute($fields, $signals);

        $conn     = $this->repo->get($userId);
        $previous = $this->repo->lastScore($userId);

        $result['previous'] = $previous ? (int)$previous['score'] : null;
        $result['delta']    = $previous ? $result['score'] - (int)$previous['score'] : null;
        $result['since']    = $previous['created_at'] ?? null;

        if (!$previous || (int)$previous['score'] !== $result['score']) {
            $this->repo->recordScore($userId, $conn['location_id'] ?? null, $result['score'], $result);
        }
        return $result;
    }

    /* ----------------------------------------------------------- performance */

    /**
     * Google's performance data lags real time. Asking for "up to today" returns
     * empty trailing days, which would make every current period look like a
     * collapse against a complete previous one. Ending the window here instead
     * is the difference between a useful trend and an alarming lie.
     */
    const PERF_LAG_DAYS = 3;

    /** Metric → label, and whether it is a headline number or part of Views. */
    const PERF_METRICS = [
        'CALL_CLICKS'                        => ['label' => 'Calls',        'group' => 'calls'],
        'BUSINESS_DIRECTION_REQUESTS'        => ['label' => 'Directions',   'group' => 'directions'],
        'WEBSITE_CLICKS'                     => ['label' => 'Website taps', 'group' => 'website'],
        'BUSINESS_CONVERSATIONS'             => ['label' => 'Messages',     'group' => 'messages'],
        'BUSINESS_IMPRESSIONS_MOBILE_SEARCH' => ['label' => 'Search views', 'group' => 'search'],
        'BUSINESS_IMPRESSIONS_DESKTOP_SEARCH'=> ['label' => 'Search views', 'group' => 'search'],
        'BUSINESS_IMPRESSIONS_MOBILE_MAPS'   => ['label' => 'Maps views',   'group' => 'maps'],
        'BUSINESS_IMPRESSIONS_DESKTOP_MAPS'  => ['label' => 'Maps views',   'group' => 'maps'],
    ];

    /**
     * How the listing actually performed: calls, direction requests, website
     * taps and views, this period against the one before it.
     *
     * Both windows are fetched in ONE request covering twice the range, then
     * split here. Two requests would double the quota cost for the same data.
     *
     * @param int $days length of each window (7, 30 or 90 in the app)
     */
    public function getInsights($userId, $days = 30)
    {
        $conn = $this->requireConnection($userId);
        if (empty($conn['location_id'])) {
            throw new GoogleException('No Google Business location is linked yet.', 404, 'location empty');
        }
        $days = max(7, min(90, (int)$days));

        $end       = strtotime('-' . self::PERF_LAG_DAYS . ' days', time());
        $start     = strtotime('-' . ($days - 1) . ' days', $end);
        $prevEnd   = strtotime('-1 day', $start);
        $prevStart = strtotime('-' . ($days - 1) . ' days', $prevEnd);

        $raw = $this->client($userId)->fetchDailyMetrics(
            $conn['location_id'],
            array_keys(self::PERF_METRICS),
            self::ymd($prevStart),
            self::ymd($end)
        );

        // Sum each metric into its group, for each window separately.
        $current = $previous = [];
        $series  = [];   // group => [ ['date'=>, 'value'=>], … ] over the CURRENT window
        foreach (self::PERF_METRICS as $metric => $meta) {
            $g = $meta['group'];
            $current[$g]  = $current[$g]  ?? 0;
            $previous[$g] = $previous[$g] ?? 0;
            foreach ($raw[$metric] ?? [] as $date => $value) {
                $t = strtotime($date);
                if ($t >= $start && $t <= $end)             $current[$g]  += $value;
                elseif ($t >= $prevStart && $t <= $prevEnd) $previous[$g] += $value;
            }
        }

        // Daily series for the chart: views are the sum of all four impression
        // metrics, the rest are one metric each.
        for ($t = $start; $t <= $end; $t = strtotime('+1 day', $t)) {
            $d = date('Y-m-d', $t);
            foreach (['calls' => ['CALL_CLICKS'],
                      'directions' => ['BUSINESS_DIRECTION_REQUESTS'],
                      'website' => ['WEBSITE_CLICKS'],
                      'views' => ['BUSINESS_IMPRESSIONS_MOBILE_SEARCH', 'BUSINESS_IMPRESSIONS_DESKTOP_SEARCH',
                                  'BUSINESS_IMPRESSIONS_MOBILE_MAPS', 'BUSINESS_IMPRESSIONS_DESKTOP_MAPS']] as $g => $ms) {
                $v = 0;
                foreach ($ms as $m) $v += $raw[$m][$d] ?? 0;
                $series[$g][] = ['date' => $d, 'value' => $v];
            }
        }

        $current['views']  = ($current['search'] ?? 0)  + ($current['maps'] ?? 0);
        $previous['views'] = ($previous['search'] ?? 0) + ($previous['maps'] ?? 0);

        $cards = [];
        foreach ([
            ['views',      'Views',           'How many people saw your listing on Search and Maps.'],
            ['calls',      'Calls',           'Taps on the call button, straight from your listing.'],
            ['directions', 'Direction taps',  'People who asked Maps how to get to you.'],
            ['website',    'Website taps',    'People who opened your website from the listing.'],
            ['messages',   'Messages',        'Chats started from your listing.'],
        ] as list($key, $label, $blurb)) {
            $now = (int)($current[$key] ?? 0);
            $was = (int)($previous[$key] ?? 0);
            $cards[] = [
                'key'       => $key,
                'label'     => $label,
                'blurb'     => $blurb,
                'value'     => $now,
                'previous'  => $was,
                // No percentage from a zero base — "+∞%" helps nobody. The app
                // shows the raw change instead.
                'delta_pct' => $was > 0 ? (int)round((($now - $was) / $was) * 100) : null,
                'delta'     => $now - $was,
            ];
        }

        return [
            'days'       => $days,
            'range'      => ['start' => date('Y-m-d', $start), 'end' => date('Y-m-d', $end)],
            'prev_range' => ['start' => date('Y-m-d', $prevStart), 'end' => date('Y-m-d', $prevEnd)],
            'lag_days'   => self::PERF_LAG_DAYS,
            'split'      => ['search' => (int)($current['search'] ?? 0), 'maps' => (int)($current['maps'] ?? 0)],
            'cards'      => $cards,
            'series'     => $series,
        ];
    }

    private static function ymd($ts)
    {
        return ['y' => (int)date('Y', $ts), 'm' => (int)date('n', $ts), 'd' => (int)date('j', $ts)];
    }

    /* -------------------------------------------------------------- reviews */

    /** Reviews for the connected location, newest first. */
    public function listReviews($userId, $pageSize = 50)
    {
        $conn = $this->requireConnection($userId);
        if (empty($conn['location_id']) || empty($conn['google_account_id'])) {
            throw new GoogleException('No Google Business location is linked yet.', 404, 'location or account empty');
        }
        $res = $this->client($userId)->listReviews($conn['google_account_id'], $conn['location_id'], $pageSize);

        // Flatten to what the app needs. Google's shape is deeply nested and
        // changes between API versions; the app should not have to know it.
        $out = [];
        foreach ($res['reviews'] as $r) {
            $out[] = [
                'id'          => $r['name'] ?? ($r['reviewId'] ?? ''),
                'reviewer'    => $r['reviewer']['displayName'] ?? 'A Google user',
                'photo'       => $r['reviewer']['profilePhotoUrl'] ?? null,
                'stars'       => self::starsToInt($r['starRating'] ?? null),
                'comment'     => $r['comment'] ?? '',
                'created_at'  => $r['createTime'] ?? null,
                'updated_at'  => $r['updateTime'] ?? null,
                'reply'       => $r['reviewReply']['comment'] ?? null,
                'replied_at'  => $r['reviewReply']['updateTime'] ?? null,
            ];
        }
        return [
            'reviews'        => $out,
            'total'          => $res['total'],
            'average'        => $res['average'],
            'location_title' => $conn['location_title'] ?? '',
            'unanswered'     => count(array_filter($out, fn($r) => empty($r['reply']))),
            'auto_reply'   => [
                'enabled'   => (int)($conn['auto_reply_enabled'] ?? 0) === 1,
                'min_stars' => (int)($conn['auto_reply_min_stars'] ?? 4),
            ],
        ];
    }

    /** Post (or replace) the owner's reply to one review. */
    public function replyToReview($userId, $reviewId, $comment, $source = 'manual')
    {
        $conn = $this->requireConnection($userId);
        $comment = trim((string)$comment);
        if ($comment === '') {
            throw new GoogleException('A reply cannot be empty.', 422, 'empty comment');
        }
        // Google rejects anything over 4096 characters outright.
        if (mb_strlen($comment) > 4096) {
            throw new GoogleException('That reply is too long. Google allows up to 4096 characters.', 422, 'comment too long');
        }
        // The review id we hand the app is already the full resource name.
        if (strpos($reviewId, 'accounts/') !== 0) {
            throw new GoogleException('That review could not be identified.', 422, 'unexpected review id: ' . substr($reviewId, 0, 60));
        }

        $this->client($userId)->replyToReview($reviewId, $comment);
        $this->repo->recordReply($userId, $reviewId, null, $comment, $source);
        return ['review_id' => $reviewId, 'reply' => $comment];
    }

    /* --------------------------------------------------------------- photos */

    /** Photos currently on the listing. */
    public function listPhotos($userId)
    {
        list($conn, $client) = $this->locationClient($userId);
        $res = $client->listMedia($conn['google_account_id'], $conn['location_id']);

        $out = [];
        foreach ($res['items'] as $m) {
            if (($m['mediaFormat'] ?? '') !== 'PHOTO') continue;
            $out[] = [
                'id'       => $m['name'] ?? '',
                'url'      => $m['thumbnailUrl'] ?? ($m['googleUrl'] ?? ($m['sourceUrl'] ?? '')),
                'category' => $m['locationAssociation']['category'] ?? 'ADDITIONAL',
                'created'  => $m['createTime'] ?? null,
            ];
        }
        return ['photos' => $out, 'total' => count($out), 'categories' => self::PHOTO_CATEGORIES];
    }

    /** Just the count, for the score. */
    public function countPhotos($userId)
    {
        list($conn, $client) = $this->locationClient($userId);
        $res = $client->listMedia($conn['google_account_id'], $conn['location_id']);
        $n = 0;
        foreach ($res['items'] as $m) if (($m['mediaFormat'] ?? '') === 'PHOTO') $n++;
        return $n;
    }

    /** Categories Google accepts for a location photo. */
    const PHOTO_CATEGORIES = [
        'COVER', 'PROFILE', 'LOGO', 'EXTERIOR', 'INTERIOR',
        'PRODUCT', 'AT_WORK', 'FOOD_AND_DRINK', 'MENU', 'TEAM', 'ADDITIONAL',
    ];

    /**
     * Add a photo to the listing.
     *
     * Google fetches the image from the URL itself, so it must already be
     * publicly hosted — the app uploads to Cloudinary first and passes that URL
     * here, reusing the same pipeline as website and social media uploads.
     */
    public function uploadPhoto($userId, $sourceUrl, $category = 'ADDITIONAL')
    {
        $sourceUrl = trim((string)$sourceUrl);
        if ($sourceUrl === '' || !preg_match('~^https://~i', $sourceUrl)) {
            throw new GoogleException('A photo must be uploaded before it can be sent to Google.', 422, 'bad sourceUrl');
        }
        $category = strtoupper(trim((string)$category));
        if (!in_array($category, self::PHOTO_CATEGORIES, true)) $category = 'ADDITIONAL';

        list($conn, $client) = $this->locationClient($userId);
        $res = $client->uploadPhoto($conn['google_account_id'], $conn['location_id'], $sourceUrl, $category);
        return ['id' => $res['name'] ?? '', 'category' => $category, 'url' => $sourceUrl];
    }

    /* ------------------------------------------------------------------ Q&A */

    /**
     * Questions on the listing, flattened, with the owner's own answer picked
     * out of the answer list.
     *
     * "Answered" here means answered BY THE BUSINESS. A question with three
     * replies from passing strangers is still unanswered as far as the owner is
     * concerned, and often worse than silence — Google shows the most upvoted
     * answer, whoever wrote it.
     */
    /** @param int $pageSize Google caps this at 10; the client clamps it. */
    public function listQuestions($userId, $pageSize = 10)
    {
        $conn = $this->requireConnection($userId);
        if (empty($conn['location_id'])) {
            throw new GoogleException('No Google Business location is linked yet.', 404, 'location empty');
        }
        $questions = $this->client($userId)->listQuestions($conn['location_id'], $pageSize);

        $out = []; $unanswered = 0;
        foreach ($questions as $q) {
            $mine = null;
            foreach ($q['topAnswers'] ?? [] as $a) {
                if (($a['author']['type'] ?? '') === 'MERCHANT') { $mine = $a; break; }
            }
            if (!$mine) $unanswered++;
            $out[] = [
                'id'           => $q['name'] ?? '',
                'text'         => $q['text'] ?? '',
                'author'       => $q['author']['displayName'] ?? 'A Google user',
                'is_mine'      => ($q['author']['type'] ?? '') === 'MERCHANT',
                'upvotes'      => (int)($q['upvoteCount'] ?? 0),
                'created_at'   => $q['createTime'] ?? null,
                'answer'       => $mine['text'] ?? null,
                'answer_count' => (int)($q['totalAnswerCount'] ?? count($q['topAnswers'] ?? [])),
            ];
        }
        return ['questions' => $out, 'total' => count($out), 'unanswered' => $unanswered];
    }

    /** Answer one question as the business. Replaces any previous answer. */
    public function answerQuestion($userId, $questionId, $text)
    {
        $this->requireConnection($userId);
        $text = trim((string)$text);
        if ($text === '')       throw new GoogleException('Write an answer first.', 422, 'empty answer');
        if ($questionId === '') throw new GoogleException('That question no longer exists.', 422, 'empty question id');
        $this->client($userId)->upsertAnswer($questionId, $text);
        return ['id' => $questionId, 'answer' => $text];
    }

    /** How many FAQ pairs one publish run will post. Google throttles hard. */
    const FAQ_PUBLISH_MAX = 5;

    /**
     * Publish generated FAQs as owner-posted question/answer pairs.
     *
     * Two calls per pair — create the question, then answer it — and Google
     * rate-limits Q&A writes aggressively, so this is capped and reports
     * per-pair outcomes instead of failing the whole batch on one rejection.
     *
     * @param array $faqs [ ['question'=>, 'answer'=>], … ]
     */
    public function publishFaq($userId, array $faqs)
    {
        $conn = $this->requireConnection($userId);
        if (empty($conn['location_id'])) {
            throw new GoogleException('No Google Business location is linked yet.', 404, 'location empty');
        }
        $client = $this->client($userId);

        $posted = 0; $results = [];
        foreach (array_slice($faqs, 0, self::FAQ_PUBLISH_MAX) as $f) {
            $q = trim((string)($f['question'] ?? ''));
            $a = trim((string)($f['answer'] ?? ''));
            if ($q === '' || $a === '') continue;
            try {
                $created = $client->createQuestion($conn['location_id'], $q);
                $name = $created['name'] ?? '';
                if ($name === '') throw new GoogleException('Google did not return the new question.', 502, 'no name');
                $client->upsertAnswer($name, $a);
                $posted++;
                $results[] = ['question' => $q, 'ok' => true];
            } catch (Exception $e) {
                // One rejected pair must not discard the ones that worked.
                GoogleLogger::warn('faq.publish_failed', ['error' => $e->getMessage()]);
                $results[] = ['question' => $q, 'ok' => false, 'error' => 'Google rejected this one.'];
            }
        }
        return ['posted' => $posted, 'results' => $results];
    }

    /* ----------------------------------------------------------- attributes */

    /**
     * Attributes Google offers this listing, merged with what is set.
     *
     * Only BOOL attributes are returned as editable. Google also has ENUM, URL
     * and REPEATED_ENUM types, but those need bespoke inputs per attribute and
     * are a small minority of what matters; a yes/no list that works beats a
     * complete list that half-works. Non-BOOL values still count toward `set`
     * so the score does not understate a well-filled listing.
     */
    public function listAttributes($userId)
    {
        $conn = $this->requireConnection($userId);
        if (empty($conn['location_id'])) {
            throw new GoogleException('No Google Business location is linked yet.', 404, 'location empty');
        }
        $client = $this->client($userId);
        $available = $client->listAvailableAttributes($conn['location_id']);
        $current   = $client->getAttributes($conn['location_id']);

        // attributeId → current boolean, plus a count of everything set.
        $values = []; $setCount = 0;
        foreach ($current as $a) {
            $id = $a['name'] ?? '';
            if ($id === '') continue;
            if (isset($a['values'][0]) && is_bool($a['values'][0])) {
                $values[$id] = $a['values'][0];
                if ($a['values'][0]) $setCount++;
            } elseif (!empty($a['repeatedEnumValue']['setValues']) || !empty($a['uriValues']) || !empty($a['values'])) {
                $setCount++;
            }
        }

        $groups = [];
        foreach ($available as $meta) {
            if (($meta['valueType'] ?? '') !== 'BOOL') continue;
            if (!empty($meta['deprecated'])) continue;
            $id = $meta['parent'] ?? '';
            if ($id === '') continue;
            $group = $meta['groupDisplayName'] ?? 'Other';
            $groups[$group][] = [
                'id'    => $id,
                'label' => $meta['displayName'] ?? $id,
                'value' => $values[$id] ?? false,
            ];
        }

        // Stable order so toggles never jump between loads.
        ksort($groups);
        $out = [];
        foreach ($groups as $name => $items) {
            usort($items, fn($a, $b) => strcmp($a['label'], $b['label']));
            $out[] = ['group' => $name, 'items' => $items];
        }

        return ['groups' => $out, 'set' => $setCount, 'available' => count($available)];
    }

    /**
     * Write attribute values.
     * @param array $changes attributeId => bool
     */
    public function setAttributes($userId, array $changes)
    {
        $conn = $this->requireConnection($userId);
        if (empty($conn['location_id'])) {
            throw new GoogleException('No Google Business location is linked yet.', 404, 'location empty');
        }
        $attributes = []; $ids = [];
        foreach ($changes as $id => $value) {
            $id = trim((string)$id);
            // Ids are Google-issued and go into an update mask, so anything that
            // is not one is dropped rather than passed through.
            if (!preg_match('~^attributes/[A-Za-z0-9_]+$~', $id)) continue;
            $ids[] = $id;
            $attributes[] = ['name' => $id, 'valueType' => 'BOOL', 'values' => [(bool)$value]];
        }
        if (!$ids) throw new GoogleException('Nothing to save.', 422, 'no valid attribute ids');

        $this->client($userId)->updateAttributes($conn['location_id'], $attributes, $ids);
        return $this->listAttributes($userId);
    }

    /* ------------------------------------------------------------- services */

    /** Services on the listing, plus the ones Google suggests for the category. */
    public function listServices($userId)
    {
        $fields     = $this->getFields($userId);
        $categoryId = $fields['primary_category_id'] ?? '';

        // Resolve structured items to a display name using Google's taxonomy,
        // otherwise the app shows a row with a blank title.
        $types = [];
        if ($categoryId !== '') {
            try {
                foreach ($this->client($userId)->listServiceTypes($categoryId) as $t) {
                    if (!empty($t['serviceTypeId'])) $types[$t['serviceTypeId']] = $t['displayName'] ?? $t['serviceTypeId'];
                }
            } catch (Exception $e) {
                GoogleLogger::warn('services.types_unavailable', ['category' => $categoryId]);
            }
        }

        $services = $fields['services'] ?? [];
        foreach ($services as &$s) {
            if ($s['type'] === 'structured' && $s['name'] === '') {
                $s['name'] = $types[$s['service_type_id']] ?? $s['service_type_id'];
            }
        }
        unset($s);

        $suggested = [];
        foreach ($types as $id => $label) $suggested[] = ['service_type_id' => $id, 'name' => $label];

        return [
            'services'    => $services,
            'suggested'   => $suggested,
            'category'    => $fields['primary_category'] ?? '',
            'category_id' => $categoryId,
        ];
    }

    /** Google's cap on the service list. */
    const SERVICES_MAX = 100;

    /**
     * Replace the service list.
     *
     * This is a whole-list write, not an append — Google has no per-item
     * endpoint. The app therefore sends the complete list every time, and
     * anything omitted is deleted.
     */
    public function setServices($userId, array $services)
    {
        $conn = $this->requireConnection($userId);
        if (empty($conn['location_id'])) {
            throw new GoogleException('No Google Business location is linked yet.', 404, 'location empty');
        }
        if (count($services) > self::SERVICES_MAX) {
            throw new GoogleException('Google allows at most ' . self::SERVICES_MAX . ' services.', 422, 'too many services');
        }
        $fields     = $this->getFields($userId);
        $categoryId = $fields['primary_category_id'] ?? '';

        $items = FieldMap::servicesToGoogle($services, $categoryId);
        $this->client($userId)->patchLocation($conn['location_id'], ['serviceItems'], ['serviceItems' => $items]);
        return $this->listServices($userId);
    }

    /* ------------------------------------------------- review requests (log) */

    /** India. Numbers are typed as 10 digits here far more often than not. */
    const DEFAULT_COUNTRY_CODE = '91';

    /**
     * Normalise a typed or picked phone number to digits with a country code.
     *
     * Contacts come back formatted every possible way ("+91 98765 43210",
     * "098765-43210", "9876543210") and wa.me accepts exactly one of them, so
     * this has to be done somewhere. Doing it on the server means the log is
     * consistent and the "already asked" check actually matches.
     *
     * @return string digits only, or '' when the input cannot be a phone number
     */
    public static function normalisePhone($raw)
    {
        $digits = preg_replace('/\D+/', '', (string)$raw);
        if ($digits === '') return '';

        // 00 as an international prefix → drop it.
        if (strlen($digits) > 12 && substr($digits, 0, 2) === '00') {
            $digits = substr($digits, 2);
        }
        // Domestic trunk prefix: 0XXXXXXXXXX → drop the 0.
        if (strlen($digits) === 11 && $digits[0] === '0') {
            $digits = substr($digits, 1);
        }
        // Bare national number → assume the default country.
        if (strlen($digits) === 10) {
            $digits = self::DEFAULT_COUNTRY_CODE . $digits;
        }
        // Anything under 10 digits is a short code or a typo, not a customer.
        return strlen($digits) >= 11 && strlen($digits) <= 15 ? $digits : '';
    }

    /**
     * Everything the Request a Review screen needs: the link to send, the
     * business name to sign the message with, and who has already been asked.
     */
    public function reviewRequestContext($userId, $limit = 100)
    {
        $conn = $this->requireConnection($userId);

        // The link comes from the live listing. If that call fails the screen is
        // still useful — the log renders — but sending is disabled rather than
        // sending a link we are guessing at.
        $link = '';
        $title = $conn['location_title'] ?? '';
        try {
            $fields = $this->getFields($userId);
            $link  = $fields['review_link'] ?? '';
            $title = $fields['business_name'] ?: $title;
        } catch (Exception $e) {
            GoogleLogger::warn('review_request.link_unavailable', ['user' => $userId]);
        }

        return [
            'review_link'   => $link,
            'business_name' => $title,
            'requests'      => $this->repo->reviewRequests($userId, $limit),
        ];
    }

    /**
     * Log a review request. The message itself is sent by the owner's own
     * WhatsApp or SMS app — we never send on their behalf, which keeps this
     * clear of bulk-messaging rules and of Google's prohibition on soliciting
     * reviews at scale.
     *
     * @return array the created row, plus last_asked when this number has been
     *               asked before, so the app can say so.
     */
    public function logReviewRequest($userId, $name, $phone, $channel = 'whatsapp')
    {
        $conn  = $this->requireConnection($userId);
        $phone = self::normalisePhone($phone);
        if ($phone === '') {
            throw new GoogleException('That does not look like a valid phone number.', 422, 'bad phone');
        }
        $channel = in_array($channel, ['whatsapp', 'sms', 'copy'], true) ? $channel : 'whatsapp';
        $name    = trim(mb_substr((string)$name, 0, 160));

        // Read before writing, so "last asked" describes the PREVIOUS ask rather
        // than the one being recorded right now.
        $previous = $this->repo->lastRequestTo($userId, $phone);

        $id = $this->repo->logReviewRequest($userId, $conn['location_id'] ?? null, $name, $phone, $channel);

        return [
            'id'         => $id,
            'name'       => $name,
            'phone'      => $phone,
            'channel'    => $channel,
            'last_asked' => $previous,
        ];
    }

    /**
     * Connection + client, asserting a location is linked.
     * The v4 endpoints need both the account and the location, and failing here
     * with a clear message beats a 404 from Google.
     */
    private function locationClient($userId)
    {
        $conn = $this->requireConnection($userId);
        if (empty($conn['location_id']) || empty($conn['google_account_id'])) {
            throw new GoogleException('No Google Business location is linked yet.', 404, 'location or account empty');
        }
        return [$conn, $this->client($userId)];
    }

    /** Turn on/off automatic replying, and the star floor it applies from. */
    public function setAutoReply($userId, $enabled, $minStars = 4)
    {
        $this->requireConnection($userId);
        $minStars = max(1, min(5, (int)$minStars));
        $this->repo->setAutoReply($userId, $enabled ? 1 : 0, $minStars);
        return ['enabled' => (bool)$enabled, 'min_stars' => $minStars];
    }

    /** "FIVE" → 5. Google sends an enum, not a number. */
    private static function starsToInt($enum)
    {
        $map = ['ONE' => 1, 'TWO' => 2, 'THREE' => 3, 'FOUR' => 4, 'FIVE' => 5];
        return $map[strtoupper((string)$enum)] ?? 0;
    }

    public function disconnect($userId)
    {
        $this->repo->delete($userId);
    }

    // ── helpers ─────────────────────────────────────────────────────────────
    private function requireConnection($userId)
    {
        $conn = $this->repo->get($userId);
        if (!$conn) {
            throw new GoogleException('Google Business Profile is not connected.', 409, 'no connection row');
        }
        return $conn;
    }

    private function client($userId)
    {
        return new GoogleBusinessClient($this->db, $this->repo, $this->repo->get($userId));
    }
}
