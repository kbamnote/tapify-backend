<?php
/**
 * TAPIFY - Feature catalogue for customer activity tracking.
 *
 * The single place that decides what counts as "using a feature". Two inputs
 * are mapped here:
 *
 *   - app screens    → the Tapify app reports the raw screen name it opened
 *                      ("my-designs"), and this file turns it into a feature.
 *   - API endpoints  → a successful write (POST/PUT/PATCH/DELETE) to a feature's
 *                      endpoint counts as actually using it.
 *
 * Keeping the mapping server-side means the app stays dumb: adding a feature,
 * renaming one or regrouping screens never needs an app release.
 *
 * The Sales CRM reads this catalogue (api/crm/feature-catalog.php) for labels
 * and grouping, so a feature added here appears on the Customer Manager
 * dashboard automatically.
 */
final class FeatureCatalog
{
    /**
     * key => [
     *   'label'     => shown to customer managers,
     *   'group'     => section on the dashboard,
     *   'screens'   => Tapify app screen names that count as opening it,
     *   'endpoints' => API paths (relative to /api/, prefix match) whose
     *                  successful writes count as using it,
     * ]
     *
     * Endpoint prefixes are matched longest-first, so 'sites/inquiries.php'
     * wins over 'sites/'.
     */
    public const FEATURES = [
        // Pseudo-feature: app launches and logins, so "opened the app N times"
        // sits alongside the real features.
        'app' => [
            'label' => 'App', 'group' => 'Account',
            'screens' => [], 'endpoints' => [],
        ],
        'dashboard' => [
            'label' => 'Dashboard', 'group' => 'Account',
            'screens' => ['dashboard'], 'endpoints' => [],
        ],
        'digital_card' => [
            'label' => 'Digital Card', 'group' => 'Digital Card',
            'screens' => ['vcards-edit'],
            'endpoints' => [
                'vcards/', 'services/', 'service-categories/', 'service-items/', 'products/',
                'galleries/', 'blogs/', 'testimonials/', 'custom-links/', 'iframes/', 'instagram/',
                'business/',
            ],
        ],
        'inquiries' => [
            'label' => 'Card Inquiries', 'group' => 'Digital Card',
            'screens' => ['inquiries'], 'endpoints' => ['inquiries/'],
        ],
        'appointments' => [
            'label' => 'Appointments', 'group' => 'Digital Card',
            'screens' => ['appointments'], 'endpoints' => ['appointments/'],
        ],
        'dynamic_qr' => [
            'label' => 'Dynamic QR Codes', 'group' => 'Digital Card',
            'screens' => [], 'endpoints' => ['dynamic-qr/'],
        ],
        'designs' => [
            'label' => 'Designs', 'group' => 'Marketing',
            'screens' => ['my-designs', 'design-customize'], 'endpoints' => ['designs/'],
        ],
        'website_builder' => [
            'label' => 'Website Builder', 'group' => 'Website',
            'screens' => ['website-builder', 'site-editor'], 'endpoints' => ['sites/'],
        ],
        'website_inquiries' => [
            'label' => 'Website Inquiries', 'group' => 'Website',
            'screens' => ['website-inquiries'], 'endpoints' => ['sites/inquiries.php'],
        ],
        'website_orders' => [
            'label' => 'Website Orders', 'group' => 'Website',
            'screens' => ['website-orders'], 'endpoints' => ['sites/orders.php'],
        ],
        'website_appointments' => [
            'label' => 'Website Appointments', 'group' => 'Website',
            'screens' => ['website-appointments'],
            'endpoints' => ['sites/appointments.php', 'sites/slots_manage.php'],
        ],
        'website_feedback' => [
            'label' => 'Website Feedback & Reviews', 'group' => 'Website',
            'screens' => ['website-feedback'], 'endpoints' => ['sites/feedback.php', 'sites/reviews.php'],
        ],
        'whatsapp_inbox' => [
            'label' => 'WhatsApp Inbox', 'group' => 'WhatsApp',
            'screens' => ['whatsapp'], 'endpoints' => ['whatsapp/'],
        ],
        'whatsapp_broadcast' => [
            'label' => 'WhatsApp Broadcast', 'group' => 'WhatsApp',
            'screens' => ['whatsapp-broadcast'], 'endpoints' => [],
        ],
        'whatsapp_auto_replies' => [
            'label' => 'WhatsApp Auto-replies', 'group' => 'WhatsApp',
            'screens' => ['whatsapp-auto-replies'], 'endpoints' => [],
        ],
        'whatsapp_store' => [
            'label' => 'WhatsApp Store', 'group' => 'WhatsApp',
            'screens' => ['whatsapp-stores'],
            'endpoints' => ['stores/', 'store-products/', 'store-categories/'],
        ],
        'whatsapp_orders' => [
            'label' => 'Store Orders', 'group' => 'WhatsApp',
            'screens' => ['whatsapp-orders'], 'endpoints' => ['store-orders/'],
        ],
        'google_business' => [
            'label' => 'Google Business Profile', 'group' => 'Google',
            'screens' => ['google-business', 'business-insights', 'business-attributes', 'business-services'],
            'endpoints' => ['google/gbp/'],
        ],
        'google_reviews' => [
            'label' => 'Google Reviews', 'group' => 'Google',
            'screens' => ['google-reviews'],
            'endpoints' => ['google/gbp/reply.php', 'google/gbp/auto-reply.php'],
        ],
        'google_posts' => [
            'label' => 'Google Posts', 'group' => 'Google',
            'screens' => ['google-posts'], 'endpoints' => ['google/gbp/posts.php'],
        ],
        'google_questions' => [
            'label' => 'Google Q&A', 'group' => 'Google',
            'screens' => ['google-questions'], 'endpoints' => ['google/gbp/questions.php'],
        ],
        'review_requests' => [
            'label' => 'Review Requests', 'group' => 'Google',
            'screens' => ['request-review', 'reviews-funnel'],
            'endpoints' => ['google/gbp/review-requests.php', 'reviews/'],
        ],
        'social_posting' => [
            'label' => 'Social Media Posting', 'group' => 'Marketing',
            'screens' => ['social'], 'endpoints' => ['social/'],
        ],
        'ai_growth' => [
            'label' => 'AI Growth Center', 'group' => 'Marketing',
            'screens' => ['ai-growth'], 'endpoints' => ['ai/'],
        ],
        'boost_ads' => [
            'label' => 'Boost Ads', 'group' => 'Marketing',
            'screens' => ['boost-ads', 'ad-insights'], 'endpoints' => ['ads/'],
        ],
        'wallet' => [
            'label' => 'Wallet', 'group' => 'Account',
            'screens' => ['wallet'], 'endpoints' => ['wallet/'],
        ],
        'titanium' => [
            'label' => 'Titanium Membership', 'group' => 'Account',
            'screens' => ['titanium'], 'endpoints' => [],
        ],
        'business_directory' => [
            'label' => 'Business Directory', 'group' => 'Account',
            'screens' => ['businesses'], 'endpoints' => [],
        ],
        'notifications' => [
            'label' => 'Notifications', 'group' => 'Account',
            'screens' => ['notifications'], 'endpoints' => ['notifications/mark-read.php'],
        ],
        'profile' => [
            'label' => 'Profile & Settings', 'group' => 'Account',
            'screens' => ['profile', 'settings'], 'endpoints' => ['profile/'],
        ],
        // Anything the app reports that isn't mapped above — a screen added
        // since this file was last updated, or a button pressed on one. It is
        // recorded rather than dropped: the screen's real name is kept in the
        // event's detail, so nothing a customer does is ever lost, and a busy
        // "Other" here is the signal that this catalogue needs a new entry.
        self::OTHER => [
            'label' => 'Other screens', 'group' => 'Account',
            'screens' => [], 'endpoints' => [],
        ],
    ];

    /** Feature used when a screen name isn't in the catalogue. */
    public const OTHER = 'other';

    /**
     * WhatsApp's inbox, broadcasts and auto-replies all go through one proxy
     * endpoint; the `action` query parameter says which one was used.
     */
    private const WHATSAPP_PROXY_ACTIONS = [
        'send'      => 'whatsapp_inbox',
        'broadcast' => 'whatsapp_broadcast',
        'bot-save'  => 'whatsapp_auto_replies',
    ];

    /**
     * Endpoints that are never the logged-in customer using a feature: things
     * their own site visitors trigger, OAuth callbacks, cron jobs, uploads that
     * are always followed by a save (which is what counts), and paths handled
     * separately (login, push-token registration, the tracking endpoint itself).
     */
    // Matched against the start of the path. Kept separate from the substring
    // list on purpose: 'categories/' as a substring would also swallow
    // 'service-categories/' and 'store-categories/'.
    private const IGNORED_PREFIXES = [
        'crm/', 'activity/', 'admin/', 'email/', 'categories/', 'uploads/', 'storage/',
        'public/', 'login.php', 'logout.php', 'register.php', 'me.php', 'dashboard.php',
        'notifications/update-token.php',
    ];

    // Matched anywhere in the path.
    private const IGNORED_SUBSTRINGS = [
        '-submit.php', 'customer-', 'callback', 'cron', 'google-auth', 'slots_public',
        'public_', 'upload-image', 'upload-logo', 'geo-search', 'targeting-search', 'debug',
    ];

    public static function all(): array
    {
        return self::FEATURES;
    }

    public static function exists(string $feature): bool
    {
        return isset(self::FEATURES[$feature]);
    }

    /** Feature for an app screen name, or null if it isn't a tracked screen. */
    public static function featureForScreen(string $screen): ?string
    {
        static $index = null;
        if ($index === null) {
            $index = [];
            foreach (self::FEATURES as $key => $f) {
                foreach ($f['screens'] as $s) {
                    $index[$s] = $key;
                }
            }
        }
        return $index[$screen] ?? null;
    }

    /**
     * Feature + action for a successful write to $path (relative to /api/, e.g.
     * "designs/save.php"), or null if the request doesn't count as using one.
     *
     * @return array{feature: string, action: string, subject: ?string}|null
     */
    public static function resolveWrite(string $path, array $query = [], array $body = []): ?array
    {
        foreach (self::IGNORED_PREFIXES as $prefix) {
            if (strncmp($path, $prefix, strlen($prefix)) === 0) {
                return null;
            }
        }
        foreach (self::IGNORED_SUBSTRINGS as $needle) {
            if (strpos($path, $needle) !== false) {
                return null;
            }
        }

        $action = self::slug(basename($path, '.php'));

        if ($path === 'whatsapp/proxy.php') {
            $proxyAction = (string)($query['action'] ?? '');
            $feature = self::WHATSAPP_PROXY_ACTIONS[$proxyAction] ?? null;
            return $feature ? ['feature' => $feature, 'action' => self::slug($proxyAction), 'subject' => null] : null;
        }

        $feature = self::featureForEndpoint($path);
        if ($feature === null) {
            return null;
        }

        // Several endpoints take an `action` in the body ({id, action: "delete"})
        // — fold it in so the timeline says what was actually done.
        if (isset($body['action']) && is_string($body['action']) && $body['action'] !== '') {
            $action = self::slug($action . '_' . $body['action']);
        }

        return [
            'feature' => $feature,
            'action'  => $action,
            'subject' => self::subjectFor($feature, $query, $body),
        ];
    }

    /**
     * WHICH thing they saved — the design's title, the site's name — so the
     * report can say «Saved changes in Designs — "Diwali Offer Poster"» rather
     * than leaving a manager to guess.
     *
     * Deliberately narrow on BOTH sides. Only features whose content belongs to
     * the customer themselves are eligible, and only a fixed list of field
     * names is read. A request body can hold anything — an inquiry's message, a
     * customer's phone number — and none of that belongs in an analytics table
     * because a field name happened to look useful.
     */
    private const SUBJECT_FEATURES = [
        'designs', 'digital_card', 'website_builder', 'whatsapp_store',
        'dynamic_qr', 'social_posting', 'boost_ads',
    ];

    private const SUBJECT_KEYS = [
        'title', 'name', 'design_name', 'vcard_name', 'site_name',
        'store_name', 'product_name', 'label', 'slug',
    ];

    private static function subjectFor(string $feature, array $query, array $body): ?string
    {
        if (!in_array($feature, self::SUBJECT_FEATURES, true)) {
            return null;
        }
        foreach (self::SUBJECT_KEYS as $key) {
            foreach ([$body, $query] as $source) {
                $value = $source[$key] ?? null;
                if (!is_string($value)) {
                    continue;
                }
                // Tidy for a dashboard and a copied report. Whitespace is
                // collapsed FIRST so a newline becomes a space; stripping
                // control characters first would run the two words together.
                $value = preg_replace('/\s+/u', ' ', $value);
                $value = trim(preg_replace('/[\x00-\x1F\x7F]/u', '', $value));
                if ($value !== '') {
                    return mb_substr($value, 0, 100);
                }
            }
        }
        return null;
    }

    /**
     * Feature whose data a successful GET to $path returned, or null.
     *
     * Reading a feature's list is how customers spend most of their time — they
     * look far more than they save — so a read counts as OPENING the feature
     * (ActivityTracker throttles it per session). A write still counts as USING
     * it; the two are reported separately.
     */
    public static function resolveRead(string $path): ?string
    {
        foreach (self::IGNORED_PREFIXES as $prefix) {
            if (strncmp($path, $prefix, strlen($prefix)) === 0) {
                return null;
            }
        }
        foreach (self::IGNORED_SUBSTRINGS as $needle) {
            if (strpos($path, $needle) !== false) {
                return null;
            }
        }
        // The WhatsApp proxy's reads are inbox traffic whichever action they use.
        if ($path === 'whatsapp/proxy.php') {
            return 'whatsapp_inbox';
        }
        return self::featureForEndpoint($path);
    }

    private static function featureForEndpoint(string $path): ?string
    {
        static $prefixes = null;
        if ($prefixes === null) {
            $prefixes = [];
            foreach (self::FEATURES as $key => $f) {
                foreach ($f['endpoints'] as $p) {
                    $prefixes[$p] = $key;
                }
            }
            // Longest first, so the most specific endpoint wins.
            uksort($prefixes, fn($a, $b) => strlen($b) <=> strlen($a));
        }
        foreach ($prefixes as $prefix => $feature) {
            if (strncmp($path, $prefix, strlen($prefix)) === 0) {
                return $feature;
            }
        }
        return null;
    }

    /** "toggle-status" → "toggle_status"; bounded to the column width. */
    public static function slug(string $s): string
    {
        $s = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '_', $s));
        return substr(trim($s, '_'), 0, 40);
    }
}
