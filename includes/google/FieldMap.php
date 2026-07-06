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
            $body['profile'] = ['description' => (string) $input['description']];
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
