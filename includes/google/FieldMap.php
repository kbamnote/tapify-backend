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
        // serviceArea matters: a business that delivers to customers rather than
        // receiving them hides its street address, and Google then omits
        // storefrontAddress entirely. Without asking for serviceArea such a
        // listing looks addressless to us even though it is complete on Google.
        // metadata carries newReviewUri — the short "write a review" link Google
        // generates for this listing. It is the only supported way to get it;
        // building one from the place ID is a fallback, not a substitute.
        return 'name,title,profile,phoneNumbers,websiteUri,categories,storefrontAddress,serviceArea,regularHours,metadata,serviceItems';
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
            // The category's opaque id ("categories/gcid:dentist"). Needed to look
            // up Google's suggested service types, and required on every
            // free-form service we write back.
            'primary_category_id' => $loc['categories']['primaryCategory']['name'] ?? '',
            'services'         => self::servicesToApp($loc['serviceItems'] ?? []),
            'address'          => self::formatAddress($loc['storefrontAddress'] ?? null)
                                  ?: self::formatServiceArea($loc['serviceArea'] ?? null),
            // City on its own, because `address` is a flattened comma-joined string
            // and the AI Growth tools need the city as a discrete value. Splitting
            // the joined string on the client guesses wrong the moment a listing has
            // one address line, or none.
            'city'             => $loc['storefrontAddress']['locality'] ?? self::firstServiceArea($loc['serviceArea'] ?? null),
            // True when the business hides its address and serves customers at
            // their location. Such a listing is complete WITHOUT a street address,
            // so nothing downstream should treat it as a gap to be fixed.
            'is_service_area'  => empty($loc['storefrontAddress']) && !empty($loc['serviceArea']),
            'hours'            => self::formatHours($loc['regularHours'] ?? null),
            // Links Google publishes for this listing. Not fields — you cannot
            // write them — but the review link is what the whole Request a
            // Review feature sends, so it travels with the rest of the listing.
            'review_link'      => self::reviewLink($loc['metadata'] ?? null),
            'maps_link'        => $loc['metadata']['mapsUri'] ?? '',
        ];
    }

    /**
     * The "write a review" URL for this listing.
     *
     * Google hands us a short one in metadata.newReviewUri. When it is absent —
     * some listings, and any response fetched before `metadata` was added to the
     * readMask — fall back to composing the documented long form from the place
     * ID. Returns '' when neither is available, and callers must treat that as
     * "cannot ask for reviews yet" rather than sending a broken link.
     */
    private static function reviewLink($metadata)
    {
        if (!is_array($metadata)) return '';
        if (!empty($metadata['newReviewUri'])) return $metadata['newReviewUri'];
        if (!empty($metadata['placeId'])) {
            return 'https://search.google.com/local/writereview?placeid=' . rawurlencode($metadata['placeId']);
        }
        return '';
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

    /* --------------------------------------------------------------- services */

    /**
     * Google's serviceItems → a flat list the app can render and edit.
     *
     * Two shapes exist and they are not interchangeable. A *structured* item
     * points at one of Google's predefined service types for the category and
     * carries no name of its own — the name lives in Google's taxonomy. A
     * *free-form* item carries its own label. Both may have a price. The app
     * gets one shape with a `type` discriminator so it never has to know this.
     */
    public static function servicesToApp($items)
    {
        if (!is_array($items)) return [];
        $out = [];
        foreach ($items as $it) {
            if (!empty($it['structuredServiceItem'])) {
                $s = $it['structuredServiceItem'];
                $out[] = [
                    'type'            => 'structured',
                    'service_type_id' => $s['serviceTypeId'] ?? '',
                    'name'            => '',   // resolved from the category's service types
                    'description'     => $s['description'] ?? '',
                    'price'           => self::priceToApp($it['price'] ?? null),
                ];
            } elseif (!empty($it['freeFormServiceItem'])) {
                $f = $it['freeFormServiceItem'];
                $out[] = [
                    'type'        => 'free',
                    'category'    => $f['category'] ?? '',
                    'name'        => $f['label']['displayName'] ?? '',
                    'description' => $f['label']['description'] ?? '',
                    'price'       => self::priceToApp($it['price'] ?? null),
                ];
            }
        }
        return $out;
    }

    /**
     * App list → Google serviceItems.
     *
     * @param string $categoryId "categories/gcid:dentist", stamped onto every
     *                           free-form item because Google requires it and the
     *                           app has no reason to carry it per row.
     */
    public static function servicesToGoogle(array $services, $categoryId, $currency = 'INR')
    {
        $out = [];
        foreach ($services as $s) {
            $name  = trim((string)($s['name'] ?? ''));
            $desc  = trim((string)($s['description'] ?? ''));
            $typeId = trim((string)($s['service_type_id'] ?? ''));
            $item  = [];

            if ($typeId !== '') {
                $item['structuredServiceItem'] = array_filter([
                    'serviceTypeId' => $typeId,
                    'description'   => $desc,
                ], fn($v) => $v !== '');
            } elseif ($name !== '') {
                if ($categoryId === '') continue;   // free-form without a category is rejected
                $item['freeFormServiceItem'] = [
                    'category' => $categoryId,
                    'label'    => array_filter([
                        'displayName' => $name,
                        'description' => $desc,
                        'languageCode' => 'en',
                    ], fn($v) => $v !== ''),
                ];
            } else {
                continue;   // neither a type nor a name: nothing to write
            }

            $price = self::priceToGoogle($s['price'] ?? null, $currency);
            if ($price !== null) $item['price'] = $price;
            $out[] = $item;
        }
        return $out;
    }

    /** Google Money → a plain number the app can put in a text field. */
    private static function priceToApp($price)
    {
        if (!is_array($price) || !isset($price['units']) && !isset($price['nanos'])) return null;
        $units = (float)($price['units'] ?? 0);
        $nanos = (float)($price['nanos'] ?? 0) / 1000000000;
        return round($units + $nanos, 2);
    }

    /** A number the customer typed → Google Money. */
    private static function priceToGoogle($value, $currency)
    {
        if ($value === null || $value === '' || !is_numeric($value)) return null;
        $amount = (float)$value;
        if ($amount <= 0) return null;
        $units = (int)floor($amount);
        return [
            'currencyCode' => $currency,
            'units'        => (string)$units,
            'nanos'        => (int)round(($amount - $units) * 1000000000),
        ];
    }

    /**
     * Service-area businesses list the places they serve instead of a street
     * address. Render those as the location line so the app has something true
     * to show rather than an empty field.
     */
    private static function formatServiceArea($area)
    {
        if (!is_array($area)) return '';
        $names = [];
        foreach ($area['places']['placeInfos'] ?? [] as $p) {
            if (!empty($p['placeName'])) $names[] = $p['placeName'];
        }
        if (!$names) return '';
        $shown = array_slice($names, 0, 3);
        $more  = count($names) - count($shown);
        return 'Serves ' . implode(', ', $shown) . ($more > 0 ? " and {$more} more" : '');
    }

    /** First served place, used as the city when there is no storefront. */
    private static function firstServiceArea($area)
    {
        if (!is_array($area)) return '';
        $first = $area['places']['placeInfos'][0]['placeName'] ?? '';
        // "Nagpur, Maharashtra, India" → "Nagpur"
        return trim(explode(',', (string)$first)[0]);
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
