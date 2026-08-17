<?php
/**
 * TAPIFY Google Business Profile — maps between app-friendly fields and the
 * Business Information API "location" resource, and builds read/update masks.
 *
 * Editable (two-way): business_name, description, phone, website.
 * Read-only (display): primary_category, address, hours.
 */
class FieldMap
{
    /** App field names that can be written back to Google. */
    public static function editableFields()
    {
        return ['business_name', 'description', 'phone', 'website'];
    }

    /** readMask for GET location. */
    public static function readMask()
    {
        return 'name,title,profile,phoneNumbers,websiteUri,categories,storefrontAddress,regularHours';
    }

    /** Google location resource → flat, app-friendly array. */
    public static function toApp(array $loc)
    {
        return [
            // editable
            'business_name'    => $loc['title'] ?? '',
            'description'      => $loc['profile']['description'] ?? '',
            'phone'            => $loc['phoneNumbers']['primaryPhone'] ?? '',
            'website'          => $loc['websiteUri'] ?? '',
            // read-only display
            'primary_category' => $loc['categories']['primaryCategory']['displayName'] ?? '',
            'address'          => self::formatAddress($loc['storefrontAddress'] ?? null),
            // City on its own, because `address` is a flattened comma-joined string
            // and the AI Growth tools need the city as a discrete value. Splitting
            // the joined string on the client guesses wrong the moment a listing has
            // one address line, or none.
            'city'             => $loc['storefrontAddress']['locality'] ?? '',
            'hours'            => self::formatHours($loc['regularHours'] ?? null),
        ];
    }

    /**
     * Build [updateMask[], body] from app input, including only editable fields
     * that are actually present in the request.
     */
    public static function buildPatch(array $input)
    {
        $mask = [];
        $body = [];

        if (array_key_exists('business_name', $input)) {
            $mask[] = 'title';
            $body['title'] = (string) $input['business_name'];
        }
        if (array_key_exists('description', $input)) {
            $mask[] = 'profile.description';
            $body['profile'] = ['description' => self::sanitizeDescription($input['description'])];
        }
        if (array_key_exists('phone', $input)) {
            $mask[] = 'phoneNumbers.primaryPhone';
            $body['phoneNumbers'] = ['primaryPhone' => (string) $input['phone']];
        }
        if (array_key_exists('website', $input)) {
            $mask[] = 'websiteUri';
            $body['websiteUri'] = (string) $input['website'];
        }

        return [$mask, $body];
    }

    /** Google's hard limit on a Business Profile description. */
    const DESCRIPTION_MAX = 750;

    /**
     * Make a description acceptable to Google.
     *
     * Google rejects the whole PATCH with a bare "Request contains an invalid
     * argument" — no hint as to which rule was broken — for either of these:
     *   1. more than 750 characters
     *   2. any URL in the text
     *
     * AI-written descriptions routinely break both: they run long, and they
     * like to end with the business's website address. Rather than surface an
     * unactionable 400 to a customer who cannot edit the generated text from
     * the app, clean it here: strip the URLs, then trim to the limit at a
     * sentence boundary so it never ends mid-word.
     */
    public static function sanitizeDescription($text)
    {
        $text = (string) $text;

        // 1. URLs are categorically not allowed in a GBP description.
        $text = preg_replace('~\b(?:https?://|www\.)\S+~i', '', $text);
        // Bare domains that survive the above, e.g. "tapify.co.in".
        $text = preg_replace('~\b[a-z0-9-]+(?:\.[a-z0-9-]+)*\.(?:com|in|co\.in|org|net|io|co)\b~i', '', $text);

        // 2. Collapse the whitespace the removals leave behind.
        $text = preg_replace('/[ \t]{2,}/', ' ', $text);
        $text = preg_replace('/\s+([,.!?])/', '$1', $text);
        $text = trim(preg_replace('/\n{3,}/', "\n\n", $text));

        if (mb_strlen($text) <= self::DESCRIPTION_MAX) return $text;

        // 3. Trim to the limit, then back up to the last sentence end so the
        //    published text reads as finished rather than cut off.
        $cut = mb_substr($text, 0, self::DESCRIPTION_MAX);
        $lastStop = max(
            (int) mb_strrpos($cut, '. '),
            (int) mb_strrpos($cut, '! '),
            (int) mb_strrpos($cut, '? ')
        );
        // Only honour a sentence break in the last third, otherwise we would
        // throw away most of what the customer chose to publish.
        if ($lastStop > self::DESCRIPTION_MAX * 0.6) {
            return trim(mb_substr($cut, 0, $lastStop + 1));
        }
        $lastSpace = (int) mb_strrpos($cut, ' ');
        return trim($lastSpace > 0 ? mb_substr($cut, 0, $lastSpace) : $cut);
    }

    private static function formatAddress($addr)
    {
        if (!is_array($addr)) return '';
        $parts = [];
        if (!empty($addr['addressLines']) && is_array($addr['addressLines'])) {
            $parts = array_merge($parts, $addr['addressLines']);
        }
        foreach (['locality', 'administrativeArea', 'postalCode'] as $k) {
            if (!empty($addr[$k])) $parts[] = $addr[$k];
        }
        return implode(', ', array_filter($parts));
    }

    private static function formatHours($hours)
    {
        if (!is_array($hours) || empty($hours['periods'])) return '';
        $lines = [];
        foreach ($hours['periods'] as $p) {
            $day  = isset($p['openDay']) ? substr($p['openDay'], 0, 3) : '';
            $open = self::formatTod($p['openTime'] ?? null);
            $close = self::formatTod($p['closeTime'] ?? null);
            if ($day && $open && $close) {
                $lines[] = "{$day} {$open}–{$close}";
            }
        }
        return implode('  •  ', $lines);
    }

    private static function formatTod($tod)
    {
        if (is_string($tod)) return $tod;              // legacy "09:00"
        if (!is_array($tod)) return '';
        $h = str_pad((string) ($tod['hours'] ?? 0), 2, '0', STR_PAD_LEFT);
        $m = str_pad((string) ($tod['minutes'] ?? 0), 2, '0', STR_PAD_LEFT);
        return "{$h}:{$m}";
    }
}
