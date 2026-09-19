<?php
/**
 * TAPIFY - Turns raw activity events into something a Customer Manager can read.
 *
 * The events table answers "what happened" precisely and reads terribly:
 * forty rows of "open / tap / use" per visit. A manager about to phone a client
 * needs the opposite — a few sentences saying what that client actually did,
 * day by day, and whether the week as a whole was good or quiet.
 *
 * So this class is the only place that decides WORDING. Both the CRM web panel
 * and the CRM mobile app print what it returns, unchanged, so the two can never
 * drift into describing the same day differently.
 *
 * Everything here is pure: events in, sentences out. No database, no clock of
 * its own (the caller passes "now"), so the phrasing can be tested directly.
 *
 * Days are bucketed in IST, not UTC. Events are stored in UTC, and a visit at
 * 1 am in Nagpur belongs to that day as the customer lived it, not the previous
 * one as the database sees it.
 */
require_once __DIR__ . '/FeatureCatalog.php';

final class ActivityNarrator
{
    public const TZ = 'Asia/Kolkata';

    /** A gap longer than this means they put the phone down; it isn't time spent. */
    private const SESSION_GAP = 300;

    /**
     * How each action is written, keyed by its slug. Full sentence templates
     * rather than bare verbs, because "downloaded" + " in Designs" reads like a
     * database row; a template can say "Downloaded something from Designs".
     * %s is the feature's label, and a template is free to ignore it.
     */
    private const ACTION_PHRASES = [
        'save'              => 'Saved changes in %s',
        'save_draft'        => 'Saved changes in %s',
        'update'            => 'Updated something in %s',
        'create'            => 'Added something new in %s',
        'add'               => 'Added something new in %s',
        'publish'           => 'Published changes in %s',
        'delete'            => 'Deleted something in %s',
        'download'          => 'Downloaded something from %s',
        'download_qr'       => 'Downloaded their QR code',
        'share'             => 'Shared something from %s',
        'share_card'        => 'Shared their digital card',
        'share_post'        => 'Shared a post from %s',
        'send'              => 'Sent a message from %s',
        'broadcast'         => 'Sent a WhatsApp broadcast',
        'reply'             => 'Replied in %s',
        'mark_read'         => 'Marked something as read in %s',
        'call_lead'         => 'Called a lead from %s',
        'whatsapp_lead'     => 'Messaged a lead on WhatsApp',
        'call_customer'     => 'Called a customer from %s',
        'whatsapp_customer' => 'Messaged a customer on WhatsApp',
        'call_business'     => 'Called a business from %s',
        'reschedule'        => 'Rescheduled an appointment in %s',
    ];

    /**
     * @param array $events rows of [created_at (UTC 'Y-m-d H:i:s'), feature, action, kind, platform, detail]
     * @param string $nowUtc 'Y-m-d H:i:s' — passed in so tests aren't at the mercy of the clock
     * @return array{days: array, summary: array}
     */
    public static function build(array $events, string $nowUtc, int $windowDays): array
    {
        $tz = new DateTimeZone(self::TZ);
        $now = new DateTimeImmutable($nowUtc, new DateTimeZone('UTC'));
        $todayKey = $now->setTimezone($tz)->format('Y-m-d');

        // Oldest first: a day reads forwards, and "first thing they did" needs order.
        usort($events, fn($a, $b) => strcmp((string)$a['created_at'], (string)$b['created_at']));

        $byDay = [];
        foreach ($events as $e) {
            $local = (new DateTimeImmutable((string)$e['created_at'], new DateTimeZone('UTC')))->setTimezone($tz);
            $byDay[$local->format('Y-m-d')][] = ['at' => $local, 'row' => $e];
        }
        krsort($byDay); // newest day first — that's what a manager opens the page for

        $days = [];
        foreach ($byDay as $key => $items) {
            $days[] = self::describeDay($key, $items, $todayKey, $tz);
        }

        return [
            'days' => $days,
            'summary' => self::describePeriod($days, $windowDays, $now->setTimezone($tz)),
        ];
    }

    /** One day, in sentences. */
    private static function describeDay(string $key, array $items, string $todayKey, DateTimeZone $tz): array
    {
        $catalog = FeatureCatalog::all();
        $date = new DateTimeImmutable($key, $tz);

        $sessions = 0;
        $opened = [];      // feature => times
        $did = [];         // "feature|action" => times
        $pressed = [];     // button caption => times
        $platforms = [];
        $first = null; $last = null; $prev = null; $engaged = 0;

        foreach ($items as $it) {
            $r = $it['row'];
            $at = $it['at'];
            $first = $first ?? $at;
            $last = $at;
            if ($prev !== null) {
                $gap = $at->getTimestamp() - $prev->getTimestamp();
                if ($gap > 0 && $gap <= self::SESSION_GAP) $engaged += $gap;
            }
            $prev = $at;

            $platform = (string)($r['platform'] ?? '');
            if ($platform !== '' && $platform !== 'unknown') $platforms[$platform] = true;

            $feature = (string)$r['feature'];
            switch ((string)$r['kind']) {
                case 'session':
                    if (($r['action'] ?? '') === 'app_open') $sessions++;
                    break;
                case 'open':
                    $label = $catalog[$feature]['label'] ?? $feature;
                    // An unmapped screen is named by the screen itself, which is
                    // more use to a manager than the words "Other screens".
                    if ($feature === FeatureCatalog::OTHER && !empty($r['detail'])) {
                        $label = self::humanScreen((string)$r['detail']);
                    }
                    $opened[$label] = ($opened[$label] ?? 0) + 1;
                    break;
                case 'use':
                    $k = $feature . '|' . (string)$r['action'];
                    $did[$k] = ($did[$k] ?? 0) + 1;
                    break;
                case 'tap':
                    $caption = trim((string)($r['detail'] ?? ''));
                    if ($caption !== '') $pressed[$caption] = ($pressed[$caption] ?? 0) + 1;
                    break;
            }
        }

        $lines = [];

        // Where they went.
        if ($opened) {
            arsort($opened);
            $names = array_keys($opened);
            $lines[] = count($names) === 1
                ? 'Looked at ' . $names[0]
                : 'Looked at ' . self::joinList($names);
        }

        // What they actually changed — the part that matters most, so it is
        // phrased as a full sentence rather than a count.
        foreach ($did as $k => $count) {
            [$feature, $action] = explode('|', $k, 2);
            $lines[] = self::describeAction($feature, $action, $count, $catalog);
        }

        // Which buttons they pressed. Capped: a manager wants the gist, and a
        // busy day can hold a hundred taps.
        if ($pressed) {
            arsort($pressed);
            $names = array_slice(array_keys($pressed), 0, 4);
            $more = count($pressed) - count($names);
            $lines[] = 'Pressed ' . self::joinList(array_map(fn($n) => '“' . $n . '”', $names))
                     . ($more > 0 ? ' and ' . $more . ' more' : '');
        }

        if (!$lines) {
            $lines[] = 'Opened the app but did not go anywhere';
        }

        $minutes = (int)round($engaged / 60);
        $where = isset($platforms['android']) || isset($platforms['ios'])
            ? (isset($platforms['web']) ? 'the app and the website' : 'the app')
            : 'the website';

        $visits = $sessions > 0 ? $sessions : 1;
        $headline = 'Used ' . $where . ' ' . self::times($visits)
            . ($minutes >= 1 ? ', about ' . $minutes . ' ' . ($minutes === 1 ? 'minute' : 'minutes') . ' in total' : '');

        return [
            'date'     => $key,
            'label'    => self::dayLabel($key, $todayKey, $date),
            'headline' => $headline,
            'lines'    => $lines,
            'from'     => $first ? $first->format('g:i a') : null,
            'to'       => $last ? $last->format('g:i a') : null,
            'visits'   => $visits,
            'minutes'  => $minutes,
            'changes'  => array_sum($did),
            'screens'  => array_sum($opened),
            'taps'     => array_sum($pressed),
            'platform' => isset($platforms['ios']) ? 'iPhone' : (isset($platforms['android']) ? 'Android' : 'Website'),
        ];
    }

    /** "Saved changes in Website Builder, twice" */
    private static function describeAction(string $feature, string $action, int $count, array $catalog): string
    {
        $label = $catalog[$feature]['label'] ?? $feature;
        $template = self::ACTION_PHRASES[$action] ?? null;
        if ($template === null) {
            // An action nobody has written wording for still has to read as
            // English — and naming it is more useful than hiding it.
            $words = trim(str_replace('_', ' ', $action));
            $template = ($words === '' ? 'Made a change' : ucfirst($words)) . ' in %s';
        }
        $sentence = sprintf($template, $label);
        return $count > 1 ? $sentence . ', ' . self::times($count) : $sentence;
    }

    /** The whole window: is this customer actually getting value out of Tapify? */
    private static function describePeriod(array $days, int $windowDays, DateTimeImmutable $nowLocal): array
    {
        $activeDays = count($days);
        $visits = array_sum(array_column($days, 'visits'));
        $changes = array_sum(array_column($days, 'changes'));
        $minutes = array_sum(array_column($days, 'minutes'));

        $period = $windowDays <= 1 ? 'today' : 'the last ' . $windowDays . ' days';

        if ($activeDays === 0) {
            return [
                'headline' => 'Has not used Tapify at all in ' . $period,
                'lines'    => ['Nothing recorded — worth a call to find out why'],
                'activeDays' => 0, 'visits' => 0, 'changes' => 0, 'minutes' => 0,
                'lastSeen' => null,
            ];
        }

        $lines = [];
        $lines[] = 'Used it on ' . $activeDays . ' of the last ' . $windowDays . ' days, '
                 . self::times($visits) . ' in total';
        $lines[] = $changes > 0
            ? 'Changed something ' . self::times($changes) . ' — they are actually working in it'
            : 'Only looked around — nothing was created or updated';

        $last = $days[0];
        $lastSeen = new DateTimeImmutable($last['date'], $nowLocal->getTimezone());
        $gap = (int)$nowLocal->setTime(0, 0)->diff($lastSeen->setTime(0, 0))->days;
        if ($gap === 0)      $lines[] = 'Last used it today';
        elseif ($gap === 1)  $lines[] = 'Last used it yesterday';
        else                 $lines[] = 'Last used it ' . $gap . ' days ago';

        $headline = $activeDays >= max(1, (int)round($windowDays * 0.5))
            ? 'Using Tapify regularly'
            : ($changes > 0 ? 'Using it occasionally' : 'Barely using it');

        return [
            'headline'   => $headline,
            'lines'      => $lines,
            'activeDays' => $activeDays,
            'visits'     => $visits,
            'changes'    => $changes,
            'minutes'    => $minutes,
            'lastSeen'   => $last['date'],
        ];
    }

    // ───── wording helpers ─────

    private static function dayLabel(string $key, string $todayKey, DateTimeImmutable $date): string
    {
        if ($key === $todayKey) return 'Today';
        $yesterday = (new DateTimeImmutable($todayKey, $date->getTimezone()))->modify('-1 day')->format('Y-m-d');
        if ($key === $yesterday) return 'Yesterday';
        return $date->format('l, j M');       // "Friday, 19 Sep"
    }

    private static function times(int $n): string
    {
        if ($n <= 1) return 'once';
        if ($n === 2) return 'twice';
        return $n . ' times';
    }

    /** "website-orders" -> "Website Orders" */
    private static function humanScreen(string $screen): string
    {
        return ucwords(str_replace(['-', '_'], ' ', $screen));
    }

    private static function joinList(array $items): string
    {
        $items = array_values($items);
        $n = count($items);
        if ($n === 0) return '';
        if ($n === 1) return $items[0];
        if ($n === 2) return $items[0] . ' and ' . $items[1];
        $lastOne = array_pop($items);
        return implode(', ', $items) . ' and ' . $lastOne;
    }
}
