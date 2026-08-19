<?php
/**
 * TAPIFY Google Business Profile — API client.
 * Wraps the Account Management + Business Information APIs and transparently
 * refreshes the access token when it's near expiry.
 */
class GoogleBusinessClient
{
    const ACCOUNTS_BASE = 'https://mybusinessaccountmanagement.googleapis.com/v1';
    const INFO_BASE     = 'https://mybusinessbusinessinformation.googleapis.com/v1';
    // Reviews were never migrated off the legacy v4 surface — there is no v1
    // equivalent. It is a SEPARATE API in Google Cloud Console ("Google My
    // Business API") and must be enabled there, even though the OAuth scope
    // (business.manage) already covers it. Review calls 404 until it is on.
    const REVIEWS_BASE  = 'https://mybusiness.googleapis.com/v4';
    // Two more separately-enabled APIs. Same OAuth scope, same 403 until each is
    // switched on in Cloud Console. Both address a location as a bare
    // "locations/456" — no account prefix, unlike v4.
    const PERF_BASE     = 'https://businessprofileperformance.googleapis.com/v1';
    const QANDA_BASE    = 'https://mybusinessqanda.googleapis.com/v1';

    /** @var PDO */
    private $db;
    /** @var GoogleBusinessRepo */
    private $repo;
    /** @var array connection row */
    private $conn;

    public function __construct(PDO $db, GoogleBusinessRepo $repo, array $connection)
    {
        $this->db   = $db;
        $this->repo = $repo;
        $this->conn = $connection;
    }

    /** Valid access token, refreshing via the refresh_token when expired. */
    private function accessToken()
    {
        $expiry = $this->conn['token_expiry'] ? strtotime($this->conn['token_expiry']) : 0;
        // refresh if missing or within 60s of expiry
        if (empty($this->conn['access_token']) || $expiry === 0 || $expiry <= (time() + 60)) {
            if (empty($this->conn['refresh_token'])) {
                throw new GoogleException('Your Google connection expired. Please reconnect Google Business Profile.', 401,
                    'no refresh_token to renew access');
            }
            $tok = GoogleOAuth::refresh($this->conn['refresh_token']);
            $access = $tok['access_token'] ?? null;
            if (!$access) {
                throw new GoogleException('Your Google connection expired. Please reconnect Google Business Profile.', 401,
                    'refresh returned no access_token');
            }
            $newExpiry = date('Y-m-d H:i:s', time() + (int) ($tok['expires_in'] ?? 3600));
            $this->repo->setAccessToken($this->conn['user_id'], $access, $newExpiry);
            $this->conn['access_token'] = $access;
            $this->conn['token_expiry'] = $newExpiry;
        }
        return $this->conn['access_token'];
    }

    /** All GBP accounts the user manages. */
    public function listAccounts()
    {
        $res = GoogleHttp::get(self::ACCOUNTS_BASE . '/accounts', $this->accessToken());
        return $res['accounts'] ?? [];
    }

    /** Locations under an account (accountName like "accounts/123"). */
    public function listLocations($accountName)
    {
        $readMask = 'name,title,storefrontAddress';
        $url = self::INFO_BASE . '/' . $accountName . '/locations?pageSize=100&readMask=' . rawurlencode($readMask);
        $res = GoogleHttp::get($url, $this->accessToken());
        return $res['locations'] ?? [];
    }

    /** Full detail for one location (locationName like "locations/456"). */
    public function getLocation($locationName, $readMask)
    {
        $url = self::INFO_BASE . '/' . $locationName . '?readMask=' . rawurlencode($readMask);
        return GoogleHttp::get($url, $this->accessToken());
    }

    /** PATCH selected fields on a location. */
    public function patchLocation($locationName, array $updateMask, array $body)
    {
        $url = self::INFO_BASE . '/' . $locationName . '?updateMask=' . rawurlencode(implode(',', $updateMask));
        return GoogleHttp::patch($url, $this->accessToken(), $body);
    }

    /* ------------------------------------------------------------- reviews */

    /**
     * Reviews for a location, newest first.
     *
     * The v4 path needs BOTH the account and the location — unlike the v1 APIs,
     * a bare "locations/456" is not addressable here.
     *
     * @param string $accountName  "accounts/123"
     * @param string $locationName "locations/456"
     */
    public function listReviews($accountName, $locationName, $pageSize = 50)
    {
        $pageSize = max(1, min(50, (int)$pageSize));
        $url = self::REVIEWS_BASE . '/' . $accountName . '/' . $locationName
             . '/reviews?orderBy=' . rawurlencode('updateTime desc') . '&pageSize=' . $pageSize;
        $res = GoogleHttp::get($url, $this->accessToken());
        return [
            'reviews'      => $res['reviews'] ?? [],
            'total'        => (int)($res['totalReviewCount'] ?? 0),
            'average'      => isset($res['averageRating']) ? (float)$res['averageRating'] : null,
        ];
    }

    /**
     * Create or replace the owner's reply to a review.
     *
     * PUT is intentional: Google treats the reply as a single sub-resource, so
     * replying twice edits the existing reply rather than adding a second one.
     * There is no way to have two replies on one review.
     *
     * @param string $reviewName full name: "accounts/1/locations/2/reviews/3"
     */
    public function replyToReview($reviewName, $comment)
    {
        $url = self::REVIEWS_BASE . '/' . $reviewName . '/reply';
        return GoogleHttp::put($url, $this->accessToken(), ['comment' => (string)$comment]);
    }

    /* --------------------------------------------------------------- media */

    /** Photos on the listing. Same v4 API as reviews. */
    public function listMedia($accountName, $locationName, $pageSize = 100)
    {
        $pageSize = max(1, min(2500, (int)$pageSize));
        $url = self::REVIEWS_BASE . '/' . $accountName . '/' . $locationName . '/media?pageSize=' . $pageSize;
        $res = GoogleHttp::get($url, $this->accessToken());
        return [
            'items' => $res['mediaItems'] ?? [],
            'total' => (int)($res['totalMediaItemCount'] ?? count($res['mediaItems'] ?? [])),
        ];
    }

    /**
     * Add a photo to the listing from a publicly reachable URL.
     *
     * Google fetches the image itself, so the URL must be public and stay up
     * long enough for that fetch — which is why we push to Cloudinary first
     * rather than trying to stream bytes from the phone.
     *
     * @param string $category LOGO|COVER|EXTERIOR|INTERIOR|PRODUCT|AT_WORK|TEAM|ADDITIONAL…
     */
    public function uploadPhoto($accountName, $locationName, $sourceUrl, $category = 'ADDITIONAL')
    {
        $url = self::REVIEWS_BASE . '/' . $accountName . '/' . $locationName . '/media';
        return GoogleHttp::postJson($url, $this->accessToken(), [
            'mediaFormat'   => 'PHOTO',
            'locationAssociation' => ['category' => $category],
            'sourceUrl'     => $sourceUrl,
        ]);
    }

    /* --------------------------------------------------------- performance */

    /**
     * Daily counts for several metrics over one date range, in a single call.
     *
     * Dates are passed as separate y/m/d query parameters because that is how
     * Google's REST transcoding exposes the nested Date message — there is no
     * "2026-01-31" form.
     *
     * @param string $locationName "locations/456"
     * @param array  $metrics      DailyMetric enum names
     * @param array  $start        ['y'=>,'m'=>,'d'=>]
     * @param array  $end          ['y'=>,'m'=>,'d'=>] inclusive
     * @return array metric name => [ 'YYYY-MM-DD' => int ]
     */
    public function fetchDailyMetrics($locationName, array $metrics, array $start, array $end)
    {
        $q = [];
        foreach ($metrics as $m) $q[] = 'dailyMetrics=' . rawurlencode($m);
        foreach ([['start_date', $start], ['end_date', $end]] as list($k, $d)) {
            $q[] = "dailyRange.{$k}.year="  . (int)$d['y'];
            $q[] = "dailyRange.{$k}.month=" . (int)$d['m'];
            $q[] = "dailyRange.{$k}.day="   . (int)$d['d'];
        }
        $url = self::PERF_BASE . '/' . $locationName . ':fetchMultiDailyMetricsTimeSeries?' . implode('&', $q);
        $res = GoogleHttp::get($url, $this->accessToken());

        // Flatten Google's four levels of nesting into metric => date => count.
        // Days with no activity are omitted from the response entirely, so the
        // caller must treat a missing date as zero rather than as missing data.
        $out = [];
        foreach ($res['multiDailyMetricTimeSeries'] ?? [] as $group) {
            foreach ($group['dailyMetricTimeSeries'] ?? [] as $series) {
                $metric = $series['dailyMetric'] ?? '';
                if ($metric === '') continue;
                $byDate = [];
                foreach ($series['timeSeries']['datedValues'] ?? [] as $dv) {
                    $dt = $dv['date'] ?? [];
                    if (empty($dt['year'])) continue;
                    $key = sprintf('%04d-%02d-%02d', $dt['year'], $dt['month'] ?? 1, $dt['day'] ?? 1);
                    $byDate[$key] = (int)($dv['value'] ?? 0);
                }
                $out[$metric] = $byDate;
            }
        }
        return $out;
    }

    /* ------------------------------------------------------------------ Q&A */

    /**
     * Questions on the listing, most recently active first.
     *
     * BOTH page limits are 10, not a guessable larger number: Google documents
     * "the default and maximum pageSize values are 10" and the same for
     * answersPerQuestion. Asking for more is a 400, so the clamp is the
     * contract rather than a nicety.
     */
    const QANDA_MAX_PAGE = 10;

    public function listQuestions($locationName, $pageSize = 10, $answersPerQuestion = 3)
    {
        $url = self::QANDA_BASE . '/' . $locationName . '/questions'
             . '?pageSize=' . max(1, min(self::QANDA_MAX_PAGE, (int)$pageSize))
             . '&answersPerQuestion=' . max(1, min(self::QANDA_MAX_PAGE, (int)$answersPerQuestion))
             . '&orderBy=' . rawurlencode('updateTime desc');
        $res = GoogleHttp::get($url, $this->accessToken());
        return $res['questions'] ?? [];
    }

    /**
     * Answer a question as the business.
     *
     * upsert, not create: a location may have exactly one answer from the owner,
     * and posting again edits it. Same shape of constraint as a review reply.
     *
     * @param string $questionName "locations/456/questions/789"
     */
    public function upsertAnswer($questionName, $text)
    {
        $url = self::QANDA_BASE . '/' . $questionName . '/answers:upsert';
        return GoogleHttp::postJson($url, $this->accessToken(), ['answer' => ['text' => (string)$text]]);
    }

    /** Post a question on your own listing — how an owner seeds an FAQ. */
    public function createQuestion($locationName, $text)
    {
        $url = self::QANDA_BASE . '/' . $locationName . '/questions';
        return GoogleHttp::postJson($url, $this->accessToken(), ['text' => (string)$text]);
    }

    /* --------------------------------------------------------- local posts */

    /**
     * Posts on the listing, newest first.
     *
     * Posts only ever existed on the legacy v4 surface — there is no v1
     * equivalent in Google's API directory — so this shares a host with reviews
     * and media, which is fortunate: it means posts are covered by the same
     * "Google My Business API" grant those already use.
     */
    public function listLocalPosts($accountName, $locationName, $pageSize = 20)
    {
        $url = self::REVIEWS_BASE . '/' . $accountName . '/' . $locationName
             . '/localPosts?pageSize=' . max(1, min(100, (int)$pageSize));
        $res = GoogleHttp::get($url, $this->accessToken());
        return $res['localPosts'] ?? [];
    }

    /**
     * Publish a post to the listing.
     *
     * topicType is the ONLY required field. name, createTime, updateTime,
     * searchUrl and state are output-only and must not be sent.
     *
     * @param array $post ['summary', 'topicType', 'callToAction' => ['actionType','url'],
     *                     'media' => [['mediaFormat'=>'PHOTO','sourceUrl'=>...]]]
     */
    public function createLocalPost($accountName, $locationName, array $post)
    {
        $url = self::REVIEWS_BASE . '/' . $accountName . '/' . $locationName . '/localPosts';
        return GoogleHttp::postJson($url, $this->accessToken(), $post);
    }

    /* ----------------------------------------------------------- attributes */

    /** Attribute values currently set on the listing. */
    public function getAttributes($locationName)
    {
        $res = GoogleHttp::get(self::INFO_BASE . '/' . $locationName . '/attributes', $this->accessToken());
        return $res['attributes'] ?? [];
    }

    /**
     * Every attribute Google offers this listing, with display names.
     *
     * Attribute ids are opaque ("has_wheelchair_accessible_entrance"), so the
     * metadata call is not optional — without it there is nothing to label a
     * toggle with.
     */
    public function listAvailableAttributes($locationName)
    {
        // ONLY `parent` may be sent here. Google's contract is explicit: "If
        // this field is set, categoryName, regionCode, languageCode and showAll
        // are not required and must not be set." Passing languageCode alongside
        // parent — which is what this did — returns a bare 400 "Request
        // contains an invalid argument" with no hint as to which argument.
        // Display names come back in the account's language regardless.
        $url = self::INFO_BASE . '/attributes?parent=' . rawurlencode($locationName) . '&pageSize=200';
        $res = GoogleHttp::get($url, $this->accessToken());
        return $res['attributeMetadata'] ?? [];
    }

    /**
     * Write attribute values.
     *
     * The update mask lists attribute ids, not field paths — an attribute left
     * out of the mask is untouched, and one named in the mask but absent from
     * the body is cleared.
     */
    public function updateAttributes($locationName, array $attributes, array $attributeIds)
    {
        $url = self::INFO_BASE . '/' . $locationName . '/attributes?updateMask='
             . rawurlencode(implode(',', $attributeIds));
        return GoogleHttp::patch($url, $this->accessToken(), [
            'name'       => $locationName . '/attributes',
            'attributes' => $attributes,
        ]);
    }

    /* ------------------------------------------------------------- services */

    /**
     * Service types Google predefines for a category, e.g. "Teeth whitening"
     * under Dentist. view=FULL is required — the default view omits them.
     *
     * @param string $categoryName "categories/gcid:dentist"
     */
    public function listServiceTypes($categoryName, $regionCode = 'IN', $languageCode = 'en')
    {
        $url = self::INFO_BASE . '/categories:batchGet?names=' . rawurlencode($categoryName)
             . '&view=FULL&regionCode=' . rawurlencode($regionCode)
             . '&languageCode=' . rawurlencode($languageCode);
        $res = GoogleHttp::get($url, $this->accessToken());
        return $res['categories'][0]['serviceTypes'] ?? [];
    }
}
