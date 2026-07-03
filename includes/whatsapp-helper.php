<?php
/**
 * ====================================================
 * TAPIFY - WhatsApp Cloud API helper
 * ====================================================
 * Sends pre-approved WhatsApp message templates via the Tapify number
 * (Meta Cloud API). Reads WHATSAPP_PHONE_ID + WHATSAPP_ACCESS_TOKEN from
 * config/database.php (which sources them from environment variables).
 *
 * Designed for silent failure: callers wrap in try/catch and it never throws,
 * so a WhatsApp hiccup can't block an inquiry/appointment from being saved.
 */

if (!function_exists('wa_normalize_phone')) {
    /** Digits only; prepend country code 91 for bare 10-digit India numbers. */
    function wa_normalize_phone($p) {
        $d = preg_replace('/\D/', '', (string)$p);
        if (strlen($d) === 10) $d = '91' . $d;
        return $d;
    }
}

if (!function_exists('wa_business_phone')) {
    /**
     * Build a sendable WhatsApp number from a vCard's stored phone + country code.
     * Uses the country code only when the phone is a bare 10-digit local number,
     * so a phone already saved with its country code isn't double-prefixed.
     * Falls back to India (91) when a 10-digit number has no country code.
     *
     * @return string digits only, or '' when there is no usable phone
     */
    function wa_business_phone($phone, $countryCode = '') {
        $digits = preg_replace('/\D/', '', (string)$phone);
        if ($digits === '') return '';
        if (strlen($digits) === 10) {
            $cc = preg_replace('/\D/', '', (string)$countryCode);
            $digits = ($cc !== '' ? $cc : '91') . $digits;
        }
        return $digits;
    }
}

if (!function_exists('sendWhatsAppTemplate')) {
    /**
     * Send an approved WhatsApp template to one recipient.
     *
     * @param string $to        recipient phone (any format; normalized here)
     * @param string $template  approved template name (e.g. 'welcome', 'appointment_reminder')
     * @param array  $params    body params in order for {{1}}, {{2}}, ...
     * @param string $lang      template language code (default 'en')
     * @return bool  true on HTTP 2xx, false otherwise (always logged, never throws)
     */
    function sendWhatsAppTemplate($to, $template, $params = [], $lang = 'en') {
        $phoneId = defined('WHATSAPP_PHONE_ID') ? WHATSAPP_PHONE_ID : (getenv('WHATSAPP_PHONE_ID') ?: '');
        $token   = defined('WHATSAPP_ACCESS_TOKEN') ? WHATSAPP_ACCESS_TOKEN : (getenv('WHATSAPP_ACCESS_TOKEN') ?: '');
        $to      = wa_normalize_phone($to);

        if (empty($phoneId) || empty($token) || $to === '') {
            error_log('WhatsApp: not configured or no recipient — skipping template "' . $template . '"');
            return false;
        }

        $components = [];
        if (!empty($params)) {
            $bodyParams = array_map(function ($v) {
                return ['type' => 'text', 'text' => (string)$v];
            }, array_values($params));
            $components[] = ['type' => 'body', 'parameters' => $bodyParams];
        }

        $payload = [
            'messaging_product' => 'whatsapp',
            'to'                => $to,
            'type'              => 'template',
            'template'          => [
                'name'       => $template,
                'language'   => ['code' => $lang],
                'components' => $components,
            ],
        ];

        $url = 'https://graph.facebook.com/v20.0/' . $phoneId . '/messages';
        $ch  = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $token,
                'Content-Type: application/json',
            ],
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_TIMEOUT        => 15,
        ]);
        $resp = curl_exec($ch);
        $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err  = curl_error($ch);
        curl_close($ch);

        if ($resp === false || $code < 200 || $code >= 300) {
            error_log('WhatsApp send failed (HTTP ' . $code . ') for "' . $template . '": ' . ($err !== '' ? $err : $resp));
            return false;
        }
        return true;
    }
}
