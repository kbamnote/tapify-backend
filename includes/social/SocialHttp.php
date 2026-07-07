<?php
/**
 * TAPIFY Social Publishing — cURL helper for platform REST calls.
 * Supports JSON + form bodies, bearer auth and header maps; maps failures to
 * SocialException with a user-safe message.
 */
class SocialHttp
{
    /** GET (optional bearer) → decoded array. */
    public static function get($url, $accessToken = null, array $headers = [])
    {
        return self::send('GET', $url, null, false, $accessToken, $headers);
    }

    /** POST JSON → decoded array. */
    public static function postJson($url, array $body, $accessToken = null, array $headers = [])
    {
        return self::send('POST', $url, $body, true, $accessToken, $headers);
    }

    /** POST application/x-www-form-urlencoded → decoded array. */
    public static function postForm($url, array $fields, array $headers = [])
    {
        return self::send('POST', $url, $fields, false, null, $headers, true);
    }

    private static function send($method, $url, $body, $json, $accessToken, array $headers, $form = false)
    {
        $ch = curl_init();
        $hdr = ['Accept: application/json'];
        foreach ($headers as $h) $hdr[] = $h;
        if ($accessToken) $hdr[] = 'Authorization: Bearer ' . $accessToken;

        $opts = [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLOPT_TIMEOUT        => 60,   // video posts can be slow
            CURLOPT_CONNECTTIMEOUT => 15,
        ];
        if ($method !== 'GET' && $body !== null) {
            if ($form) {
                $opts[CURLOPT_POSTFIELDS] = http_build_query($body);
                $hdr[] = 'Content-Type: application/x-www-form-urlencoded';
            } elseif ($json) {
                $opts[CURLOPT_POSTFIELDS] = json_encode($body);
                $hdr[] = 'Content-Type: application/json';
            } else {
                $opts[CURLOPT_POSTFIELDS] = http_build_query($body);
            }
        }
        $opts[CURLOPT_HTTPHEADER] = $hdr;
        curl_setopt_array($ch, $opts);

        $raw  = curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err  = curl_error($ch);
        curl_close($ch);

        if ($raw === false) {
            throw new SocialException('Could not reach the network. Please try again.', 504, "curl: {$err}");
        }
        $decoded = $raw === '' ? [] : json_decode($raw, true);
        if (!is_array($decoded)) {
            throw new SocialException('Unexpected response from the platform.', 502,
                'non-JSON: ' . substr((string) $raw, 0, 300));
        }
        if ($code >= 400) {
            $msg = self::extractError($decoded);
            SocialLogger::error('http.error', ['status' => $code, 'url' => self::redact($url), 'msg' => $msg]);
            throw new SocialException(self::safeForStatus($code), $code, "HTTP {$code}: {$msg}");
        }
        return $decoded;
    }

    private static function extractError(array $d)
    {
        if (isset($d['error']['message'])) return $d['error']['message'];          // Meta
        if (isset($d['error_description'])) return $d['error_description'];         // OAuth
        if (isset($d['message'])) return is_string($d['message']) ? $d['message'] : json_encode($d['message']); // LinkedIn
        if (isset($d['error']) && is_string($d['error'])) return $d['error'];
        return 'platform error';
    }

    private static function safeForStatus($code)
    {
        if ($code === 401) return 'Your connection to this account expired. Please reconnect it.';
        if ($code === 403) return 'The platform denied this action. The account may lack permission, or app review is pending.';
        if ($code === 429) return 'The platform is rate-limiting posts. Please try again shortly.';
        return 'Posting to the platform failed. Please try again.';
    }

    private static function redact($url)
    {
        return preg_replace('/(access_token|client_secret|key)=[^&]+/', '$1=REDACTED', $url);
    }
}
