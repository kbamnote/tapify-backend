<?php
/**
 * TAPIFY — Google Business Profile health score.
 *
 * Turns a connected listing into a score out of 100 plus an itemised list of
 * what is costing points and which AI tool fixes each one.
 *
 * THE SCORE IS DETERMINISTIC ON PURPOSE. It is rules over the listing's own
 * fields, not an AI judgement. A customer who changes nothing must see the same
 * number tomorrow — a score that drifts on its own is worse than no score,
 * because the first time it drops for no reason they stop believing all of it.
 * The AI writes the advice copy; the arithmetic lives here and is explainable
 * line by line.
 *
 * Every rule maps to something the customer can actually act on from inside
 * Tapify, so the list is never "your score is 62, good luck".
 */
class ProfileScore
{
    /** Google's own guidance is ~750 chars max; below this reads as a stub. */
    const DESCRIPTION_GOOD = 250;
    const DESCRIPTION_MIN  = 100;

    /**
     * @param array $fields FieldMap::toApp() output
     * @return array {score, max, band, summary, items[], earned, possible}
     */
    public static function compute(array $fields): array
    {
        $items = [];

        // ── Business name (10) ────────────────────────────────────────────────
        $name = trim((string)($fields['business_name'] ?? ''));
        $items[] = self::item(
            'business_name', 'Business name', 10, $name !== '' ? 10 : 0,
            $name !== ''
                ? 'Your listing has a business name.'
                : 'Your listing has no business name. Nothing else matters until this is set.',
            null, 'google_business'
        );

        // ── Description (25) — the biggest single lever, and AI can write it ──
        $desc = trim((string)($fields['description'] ?? ''));
        $len  = mb_strlen($desc);
        if ($len === 0) {
            $items[] = self::item('description', 'Business description', 25, 0,
                'No description. This is the largest single gap in your profile and the one that most affects how you appear in search.',
                'business-description', 'ai_tool');
        } elseif ($len < self::DESCRIPTION_MIN) {
            $items[] = self::item('description', 'Business description', 25, 8,
                "Your description is only {$len} characters. Under " . self::DESCRIPTION_MIN . ' it reads as a placeholder to both customers and Google.',
                'business-description', 'ai_tool');
        } elseif ($len < self::DESCRIPTION_GOOD) {
            $items[] = self::item('description', 'Business description', 25, 18,
                "Your description is {$len} characters. Around " . self::DESCRIPTION_GOOD . ' gives room to name your services and your area, which is what search actually reads.',
                'business-description', 'ai_tool');
        } else {
            $items[] = self::item('description', 'Business description', 25, 25,
                'Your description is a good length and gives search something to work with.',
                null, null);
        }

        // ── Category (15) ─────────────────────────────────────────────────────
        $cat = trim((string)($fields['primary_category'] ?? ''));
        $items[] = self::item(
            'primary_category', 'Primary category', 15, $cat !== '' ? 15 : 0,
            $cat !== ''
                ? "Listed under \"{$cat}\"."
                : 'No primary category. Google uses this to decide which searches you appear in at all, so an empty category is close to being invisible.',
            null, 'google_business'
        );

        // ── Phone (12) ────────────────────────────────────────────────────────
        $phone = trim((string)($fields['phone'] ?? ''));
        $items[] = self::item(
            'phone', 'Phone number', 12, $phone !== '' ? 12 : 0,
            $phone !== ''
                ? 'Customers can call you straight from search results.'
                : 'No phone number. The call button is the single most used action on a local listing.',
            null, 'google_business'
        );

        // ── Website (12) ──────────────────────────────────────────────────────
        $site = trim((string)($fields['website'] ?? ''));
        $items[] = self::item(
            'website', 'Website link', 12, $site !== '' ? 12 : 0,
            $site !== ''
                ? 'Your listing links to your website.'
                : 'No website linked. If you have a Tapify site, point this at it — the link itself is a ranking signal.',
            null, 'google_business'
        );

        // ── Address or service area (13) ──────────────────────────────────────
        // A service-area business deliberately hides its street address; Google
        // considers that listing complete. Scoring it as a missing address would
        // be both wrong and unfixable, so either satisfies this.
        $addr = trim((string)($fields['address'] ?? ''));
        $isSab = !empty($fields['is_service_area']);
        $items[] = self::item(
            'address',
            $isSab ? 'Service area' : 'Business address',
            13, $addr !== '' ? 13 : 0,
            $addr !== ''
                ? ($isSab
                    ? 'Your service area is set, so you appear in local searches across the places you cover.'
                    : 'Your address is on the listing, so you can appear in "near me" searches.')
                : 'No address or service area set. Without either you are largely excluded from local and "near me" results.',
            null, 'google_business'
        );

        // ── Opening hours (13) ────────────────────────────────────────────────
        $hours = $fields['hours'] ?? '';
        $hasHours = is_array($hours) ? count($hours) > 0 : trim((string)$hours) !== '';
        $items[] = self::item(
            'hours', 'Opening hours', 13, $hasHours ? 13 : 0,
            $hasHours
                ? 'Your opening hours are set, so Google can show Open or Closed.'
                : 'No opening hours. Google shows nothing rather than "Open now", and customers assume you are shut.',
            null, 'google_business'
        );

        $earned   = array_sum(array_column($items, 'earned'));
        $possible = array_sum(array_column($items, 'points'));
        $score    = $possible > 0 ? (int)round($earned / $possible * 100) : 0;

        return [
            'score'    => $score,
            'max'      => 100,
            'earned'   => $earned,
            'possible' => $possible,
            'band'     => self::band($score),
            'summary'  => self::summary($score, $items),
            'items'    => $items,
        ];
    }

    /**
     * @param string      $tool  aiTools key that generates a fix, or null
     * @param string|null $where 'ai_tool' | 'google_business' | null when already done
     */
    private static function item($key, $label, $points, $earned, $hint, $tool, $where): array
    {
        return [
            'key'    => $key,
            'label'  => $label,
            'points' => $points,
            'earned' => $earned,
            'status' => $earned >= $points ? 'good' : ($earned > 0 ? 'partial' : 'missing'),
            'hint'   => $hint,
            'tool'   => $tool,    // which AI tool writes this for them
            'fix_in' => $where,   // where the customer goes to fix it
        ];
    }

    private static function band($score): string
    {
        if ($score >= 90) return 'excellent';
        if ($score >= 75) return 'good';
        if ($score >= 50) return 'needs work';
        return 'poor';
    }

    private static function summary($score, array $items): string
    {
        $missing = array_values(array_filter($items, fn($i) => $i['status'] !== 'good'));
        if (!$missing) return 'Your profile is complete. Keep it current and add posts and photos regularly.';

        usort($missing, fn($a, $b) => ($b['points'] - $b['earned']) <=> ($a['points'] - $a['earned']));
        $top = array_slice(array_column($missing, 'label'), 0, 2);
        $gain = array_sum(array_map(fn($i) => $i['points'] - $i['earned'], $missing));

        return count($top) === 1
            ? "Fixing your {$top[0]} would take you to 100."
            : 'Start with ' . strtolower($top[0]) . ' and ' . strtolower($top[1])
              . " — there are {$gain} points on the table in total.";
    }
}
