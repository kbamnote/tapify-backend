<?php
/**
 * TAPIFY — visibility score (the WhatsApp bot's score).
 *
 * WHY THIS IS NOT MarketingScore.
 *
 * MarketingScore reads 18 signals from a CONNECTED Google account. The person
 * the bot is talking to has connected nothing — we know only what Places API
 * exposes, which is roughly what a customer searching for them sees. So this
 * scores seven things, all of them visible from outside the listing.
 *
 * IT IS CALLED SOMETHING DIFFERENT ON PURPOSE. A business will score HIGHER
 * here than in the app, because the app can see problems this cannot — no
 * posts, unanswered questions, unreplied reviews. If both were called "your
 * score", watching 62 become 34 after installing would read as a bait-and
 * -switch. "Visibility score — what customers can see" and "marketing score"
 * are two honest measurements of two different things.
 *
 * THE GAP IS THE PITCH. `hidden` lists what cannot be seen from out here. That
 * list is the reason to install the app, so it is part of the result, not an
 * apology in a footnote.
 */
class VisibilityScore
{
    /**
     * @param array $s Signals from PlacesClient::details().
     *   reviews_total, rating, photos_total, photos_capped,
     *   has_hours, has_website, description_len, has_category
     *
     * A null signal drops its item and shrinks `possible`, exactly as
     * MarketingScore does, so an incomplete lookup never reads as a penalty.
     */
    public static function compute(array $s): array
    {
        $g = function ($k) use ($s) {
            return array_key_exists($k, $s) && $s[$k] !== null ? $s[$k] : null;
        };
        $items = [];
        $add = function ($group, $key, $label, $points, $earned, $hint) use (&$items) {
            $items[] = [
                'group'  => $group,
                'key'    => $key,
                'label'  => $label,
                'points' => $points,
                'earned' => $earned,
                'status' => $earned >= $points ? 'good' : ($earned > 0 ? 'partial' : 'missing'),
                'hint'   => $hint,
            ];
        };

        /* ── Reputation: 42 ─────────────────────────────────────────────── */
        $n = $g('reviews_total');
        if ($n !== null) {
            $e = $n >= 50 ? 24 : ($n >= 20 ? 18 : ($n >= 10 ? 12 : ($n >= 1 ? 6 : 0)));
            $add('Reputation', 'review_volume', 'Number of reviews', 24, $e,
                $n == 0
                    ? 'No reviews yet. This is the first thing anyone looks at before choosing you.'
                    : "{$n} reviews. Shops that rank well locally usually carry 50 or more.");
        }
        $r = $g('rating');
        if ($r !== null) {
            $e = $r >= 4.5 ? 18 : ($r >= 4.0 ? 13 : ($r >= 3.0 ? 6 : 0));
            $add('Reputation', 'rating', 'Star rating', 18, $e,
                $r > 0
                    ? sprintf('%.1f stars. Below 4.0 you lose customers who never call to find out why.', $r)
                    : 'No rating yet — nobody has reviewed you.');
        }

        /* ── Photos: 18 ─────────────────────────────────────────────────── */
        $p = $g('photos_total');
        if ($p !== null) {
            $capped = !empty($s['photos_capped']);
            $e = $p >= 10 ? 18 : ($p >= 5 ? 12 : ($p >= 1 ? 6 : 0));
            // Google only hands out 10 photo references however many the listing
            // holds, so at the cap we say "10 or more" — telling a shop with 40
            // photos that it has 10 is the kind of wrong that ends the chat.
            $add('Photos', 'photo_count', 'Photos on your listing', 18, $e,
                $p == 0
                    ? 'No photos at all. Listings with photos get far more calls and direction taps.'
                    : ($capped
                        ? '10 or more photos — good. Keep adding new ones; recent photos count for more.'
                        : "{$p} photos. Get above 10 and you will notice the difference in calls."));
        }

        /* ── Listing basics: 40 ─────────────────────────────────────────── */
        $h = $g('has_hours');
        if ($h !== null) {
            $add('Listing', 'hours', 'Opening hours', 12, $h ? 12 : 0,
                $h ? 'Your opening hours are set.'
                   : 'No opening hours. Google hides you from "open now" searches without them, and that is when people are actually looking.');
        }
        $w = $g('has_website');
        if ($w !== null) {
            $add('Listing', 'website', 'Website link', 14, $w ? 14 : 0,
                $w ? 'You have a website linked.'
                   : 'No website linked. That link is a free button on your listing and it is empty.');
        }
        $d = $g('description_len');
        if ($d !== null) {
            $e = $d >= 250 ? 10 : ($d >= 100 ? 7 : ($d > 0 ? 3 : 0));
            $add('Listing', 'description', 'Business description', 10, $e,
                $d == 0
                    ? 'No description. This is where you say what you actually do, in your own words.'
                    : "Description is {$d} characters. Around 250 gives Google enough to work with.");
        }
        $c = $g('has_category');
        if ($c !== null) {
            $add('Listing', 'category', 'Business category', 4, $c ? 4 : 0,
                $c ? 'Your category is set.'
                   : 'No category set. Google needs it to know which searches to show you in.');
        }

        $earned   = array_sum(array_column($items, 'earned'));
        $possible = array_sum(array_column($items, 'points'));
        $score    = $possible > 0 ? (int)round($earned / $possible * 100) : 0;

        $groups = [];
        foreach ($items as $it) {
            $k = $it['group'];
            if (!isset($groups[$k])) $groups[$k] = ['group' => $k, 'earned' => 0, 'points' => 0];
            $groups[$k]['earned'] += $it['earned'];
            $groups[$k]['points'] += $it['points'];
        }

        return [
            'kind'     => 'visibility',
            'score'    => $score,
            'max'      => 100,
            'earned'   => $earned,
            'possible' => $possible,
            'band'     => self::band($score),
            'summary'  => self::summary($score, $items),
            'groups'   => array_values($groups),
            'items'    => $items,
            'hidden'   => self::hidden(),
        ];
    }

    /** Same thresholds and words as MarketingScore, so the two never contradict. */
    private static function band($score): string
    {
        if ($score >= 80) return 'strong';
        if ($score >= 60) return 'good';
        if ($score >= 35) return 'needs work';
        return 'weak';
    }

    /**
     * What this score CANNOT see. Returned with every result because it is the
     * whole reason to connect the app, and because quoting a score without
     * saying what it missed would be dishonest.
     */
    private static function hidden(): array
    {
        return [
            'Whether you reply to your reviews',
            'Google Posts — whether you have posted in the last week',
            'Questions customers asked that nobody answered',
            'How recently you added photos',
            'Services and attributes on your listing',
        ];
    }

    private static function summary($score, array $items): string
    {
        $missing = array_values(array_filter($items, fn($i) => $i['status'] !== 'good'));
        if (!$missing) {
            return 'Everything a customer can see from outside looks good. The things that are '
                 . 'still costing you are the ones I cannot see from here.';
        }
        usort($missing, fn($a, $b) => ($b['points'] - $b['earned']) <=> ($a['points'] - $a['earned']));
        $gain = array_sum(array_map(fn($i) => $i['points'] - $i['earned'], $missing));
        $top  = array_slice(array_map(fn($i) => strtolower($i['label']), $missing), 0, 3);

        if (count($top) === 1) {
            return "Fix your {$top[0]} and you are at 100 on everything visible from outside.";
        }
        $last = array_pop($top);
        return 'Start with ' . implode(', ', $top) . " and {$last} — that is {$gain} points sitting on the table.";
    }
}
