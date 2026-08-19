<?php
/**
 * TAPIFY — marketing score.
 *
 * WHY THIS EXISTS ALONGSIDE ProfileScore, not instead of it.
 *
 * ProfileScore measures COMPLETENESS: is the listing filled in? That question
 * gets answered once. Fill the fields, reach 100, and the screen is finished
 * forever — which is exactly the wrong shape for a product someone is meant to
 * keep opening.
 *
 * This measures ACTIVITY, and five of its items DECAY. Recent reviews, photo
 * recency, post recency, social posting and reply rate all fall on their own if
 * the business does nothing. That is not a trick to manufacture engagement: a
 * listing that has gone quiet for six months genuinely is weaker, and Google
 * weights recency in local ranking. A score that can only go up is a score
 * people stop checking.
 *
 * IT IS NOT RIGGED LOW. It does not need to be. A typical unmanaged business
 * scores 25-40 honestly, because almost nobody posts weekly, replies to reviews
 * or adds photos regularly. Deflating it deliberately would be worse than
 * pointless — a customer who does everything asked and still sees 45 concludes
 * the product does not work, which destroys the only thing the number is for.
 *
 * EVERY ITEM MAPS TO SOMETHING THE PRODUCT SHIPS. A gap the customer cannot act
 * on is the thing that makes a score feel arbitrary, so each one carries the
 * screen that fixes it.
 */
class MarketingScore
{
    /** A "recent" window, used by every decaying item. */
    const RECENT_DAYS = 90;

    /**
     * @param array $s Signals. Any that is null omits its item entirely rather
     *                 than scoring zero — same rule as ProfileScore. `possible`
     *                 shrinks to match so the percentage stays honest when an
     *                 API is unavailable (Q&A is, pending Google's approval).
     *
     *   reviews_total, rating, reviews_recent, reviews_replied
     *   photos_total, photos_recent
     *   description_len, has_category, has_hours, services, attributes
     *   days_since_post, questions_unanswered, social_posts_30d, social_connected
     *   site_live, site_linked, site_pages, site_ordering
     */
    public static function compute(array $s): array
    {
        $g = function ($k) use ($s) {
            return array_key_exists($k, $s) && $s[$k] !== null ? $s[$k] : null;
        };
        $items = [];
        $add = function ($group, $key, $label, $points, $earned, $hint, $fixIn, $decays = false)
            use (&$items) {
            $items[] = [
                'group'  => $group,
                'key'    => $key,
                'label'  => $label,
                'points' => $points,
                'earned' => $earned,
                'status' => $earned >= $points ? 'good' : ($earned > 0 ? 'partial' : 'missing'),
                'hint'   => $hint,
                'fix_in' => $fixIn,
                'decays' => $decays,
            ];
        };

        /* ── Reputation: 30 ─────────────────────────────────────────────── */
        $n = $g('reviews_total');
        if ($n !== null) {
            $e = $n >= 50 ? 8 : ($n >= 20 ? 6 : ($n >= 10 ? 4 : ($n >= 1 ? 2 : 0)));
            $add('Reputation', 'review_volume', 'Number of reviews', 8, $e,
                $n == 0 ? 'No reviews yet. This is the single biggest thing customers look at before choosing you.'
                        : "{$n} reviews. Businesses that rank well locally usually carry 50 or more.",
                'request_review');
        }
        $r = $g('rating');
        if ($r !== null) {
            $e = $r >= 4.5 ? 6 : ($r >= 4.0 ? 4 : ($r >= 3.0 ? 2 : 0));
            $add('Reputation', 'rating', 'Average rating', 6, $e,
                $r > 0 ? sprintf('%.1f stars. Below 4.0 costs you customers who never call to find out why.', $r)
                       : 'No rating yet.',
                'request_review');
        }
        $rr = $g('reviews_recent');
        if ($rr !== null) {
            $e = $rr >= 10 ? 8 : ($rr >= 5 ? 6 : ($rr >= 2 ? 3 : 0));
            $add('Reputation', 'reviews_recent', 'Reviews in the last 90 days', 8, $e,
                $rr == 0 ? 'No reviews in three months. Recent reviews count for far more than old ones.'
                         : "{$rr} in the last 90 days. Asking a few customers a week keeps this healthy.",
                'request_review', true);
        }
        $rep = $g('reviews_replied');
        if ($rep !== null && $n !== null && $n > 0) {
            $pct = $rep / max(1, $n);
            $e = $pct >= 0.9 ? 8 : ($pct >= 0.6 ? 5 : ($pct >= 0.3 ? 3 : 0));
            $add('Reputation', 'reply_rate', 'Replies to reviews', 8, $e,
                sprintf('%d%% of your reviews have a reply. Answering every one — good and bad — is visible to everyone reading them.', round($pct * 100)),
                'google_reviews', true);
        }

        /* ── Photos: 15 ─────────────────────────────────────────────────── */
        $p = $g('photos_total');
        if ($p !== null) {
            $e = $p >= 20 ? 8 : ($p >= 10 ? 6 : ($p >= 4 ? 3 : 0));
            $add('Photos', 'photo_count', 'Photos on the listing', 8, $e,
                "{$p} photos. Listings with 20 or more get noticeably more calls and direction requests.",
                'google_business');
        }
        $pr = $g('photos_recent');
        if ($pr !== null) {
            $e = $pr >= 5 ? 7 : ($pr >= 2 ? 5 : ($pr >= 1 ? 3 : 0));
            $add('Photos', 'photos_recent', 'Photos added recently', 7, $e,
                $pr == 0 ? 'No new photos in three months. Recent photos carry more weight than old ones.'
                         : "{$pr} added in the last 90 days.",
                'google_business', true);
        }

        /* ── Listing depth: 20 ──────────────────────────────────────────── */
        $d = $g('description_len');
        if ($d !== null) {
            $e = $d >= 250 ? 8 : ($d >= 100 ? 5 : ($d > 0 ? 2 : 0));
            $add('Listing', 'description', 'Business description', 8, $e,
                $d == 0 ? 'No description. The biggest single gap on most listings.'
                        : "{$d} characters. Around 250 gives search something to work with.",
                'ai_tool');
        }
        foreach ([['has_category', 'Primary category', 3, 'google_business'],
                  ['has_hours',    'Opening hours',    3, 'google_business']] as $c) {
            $v = $g($c[0]);
            if ($v !== null) {
                $add('Listing', $c[0], $c[1], $c[2], $v ? $c[2] : 0,
                    $v ? 'Set.' : $c[1] . ' is not set, and Google uses it to decide which searches you appear in.',
                    $c[3]);
            }
        }
        $sv = $g('services');
        if ($sv !== null) {
            $add('Listing', 'services', 'Services listed', 3, $sv >= 3 ? 3 : ($sv >= 1 ? 1 : 0),
                $sv == 0 ? 'No services listed, so you only appear for your category rather than what you actually do.'
                         : "{$sv} services listed.", 'services');
        }
        $at = $g('attributes');
        if ($at !== null) {
            $add('Listing', 'attributes', 'Attributes set', 3, $at >= 5 ? 3 : ($at >= 1 ? 1 : 0),
                $at == 0 ? 'No attributes set — these are the filters customers narrow by.'
                         : "{$at} attributes set.", 'attributes');
        }

        /* ── Content activity: 20 ───────────────────────────────────────── */
        $dp = $g('days_since_post');
        if ($dp !== null) {
            // Posts expire after 7 days on the listing, so the window is tight.
            $e = $dp <= 7 ? 8 : ($dp <= 30 ? 5 : ($dp <= 90 ? 2 : 0));
            $add('Activity', 'posts', 'Google Posts', 8, $e,
                $dp >= 9999 ? 'You have never posted. A post a week keeps your listing active in search.'
                            : "Last post was {$dp} days ago. Posts drop off the listing after about a week.",
                'google_posts', true);
        }
        $q = $g('questions_unanswered');
        if ($q !== null) {
            $add('Activity', 'qanda', 'Questions answered', 4, $q == 0 ? 4 : ($q <= 2 ? 2 : 0),
                $q == 0 ? 'No unanswered questions.'
                        : "{$q} unanswered. Google shows whichever answer gets the most votes until you post your own.",
                'google_questions', true);
        }
        $sp = $g('social_posts_30d');
        if ($sp !== null) {
            $e = $sp >= 8 ? 8 : ($sp >= 4 ? 6 : ($sp >= 1 ? 3 : 0));
            $add('Activity', 'social', 'Social posting', 8, $e,
                $sp == 0 ? 'Nothing posted to social in a month.'
                         : "{$sp} posts in the last 30 days.",
                'social', true);
        }

        /* ── Web presence: 15 ───────────────────────────────────────────── */
        $live = $g('site_live');
        if ($live !== null) {
            $add('Website', 'site_live', 'Website published', 6, $live ? 6 : 0,
                $live ? 'Your website is live.' : 'No published website yet.', 'website');
        }
        $ln = $g('site_linked');
        if ($ln !== null) {
            $add('Website', 'site_linked', 'Linked from Google', 3, $ln ? 3 : 0,
                $ln ? 'Your listing links to your site.'
                    : 'Your Google listing has no website link — the link itself is a ranking signal.',
                'google_business');
        }
        $pg = $g('site_pages');
        if ($pg !== null) {
            $add('Website', 'site_pages', 'Site depth', 3, $pg >= 3 ? 3 : ($pg >= 1 ? 1 : 0),
                $pg >= 3 ? "{$pg} pages." : 'A single-page site gives search very little to index.', 'website');
        }
        $or = $g('site_ordering');
        if ($or !== null) {
            $add('Website', 'ordering', 'Online ordering', 3, $or ? 3 : 0,
                $or ? 'Customers can order from your site.'
                    : 'No online ordering yet — customers cannot buy without calling you.', 'website');
        }

        $earned   = array_sum(array_column($items, 'earned'));
        $possible = array_sum(array_column($items, 'points'));
        $score    = $possible > 0 ? (int)round($earned / $possible * 100) : 0;

        // Group rollups, so the gauge can be broken down without the app
        // re-deriving the arithmetic.
        $groups = [];
        foreach ($items as $it) {
            $k = $it['group'];
            if (!isset($groups[$k])) $groups[$k] = ['group' => $k, 'earned' => 0, 'points' => 0];
            $groups[$k]['earned'] += $it['earned'];
            $groups[$k]['points'] += $it['points'];
        }

        return [
            'score'    => $score,
            'max'      => 100,
            'earned'   => $earned,
            'possible' => $possible,
            'band'     => self::band($score),
            'summary'  => self::summary($score, $items),
            'groups'   => array_values($groups),
            'items'    => $items,
        ];
    }

    /** Bands the gauge colours itself from. */
    private static function band($score): string
    {
        if ($score >= 80) return 'strong';
        if ($score >= 60) return 'good';
        if ($score >= 35) return 'needs work';
        return 'weak';
    }

    private static function summary($score, array $items): string
    {
        $missing = array_values(array_filter($items, fn($i) => $i['status'] !== 'good'));
        if (!$missing) {
            return 'Everything we measure is in good shape. Keep posting and asking for reviews — this score falls if activity stops.';
        }
        usort($missing, fn($a, $b) => ($b['points'] - $b['earned']) <=> ($a['points'] - $a['earned']));
        $top  = array_slice(array_column($missing, 'label'), 0, 2);
        $gain = array_sum(array_map(fn($i) => $i['points'] - $i['earned'], $missing));
        return count($top) === 1
            ? "Fixing your {$top[0]} would take you to 100."
            : 'Start with ' . strtolower($top[0]) . ' and ' . strtolower($top[1])
              . " — there are {$gain} points on the table.";
    }
}
