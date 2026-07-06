<?php
/**
 * TAPIFY Google Business Profile — thin cURL helper for Google REST calls.
 * Handles JSON + form bodies, bearer auth, and maps failures to GoogleException.
 */
class GoogleHttp
{
    /** GET with bearer token → decoded array. */
    public static function get($url, $accessToken)
    {
        return self::request('GET', $url, $accessToken, null, false);
    }

    /** PATCH JSON with bearer token → decoded array. */
    public static function patch($url, $accessToken, array $body)
    {
        return self::request('PATCH', $url, $accessToken, $body, true);
    }

    /** POST JSON with bearer token → decoded array. */
    public static function postJson($url, $accessToken, array $body)
    {
        return self::request('POST', $url, $accessToken, $body, true);
    }

    /** POST application/x-www-form-urlencoded (OAuth token endpoint) → decoded array. */
    public static function postForm($url, array $fields)
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query($fields),
            CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded'],
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
        ]);
        $raw  = curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err  = curl_error($ch);
        curl_close($ch);

        if ($raw === false) {
            throw new GoogleException('Could not reach Google. Please try again.', 504, "curl: {$err}");
        }
        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) {
            throw new GoogleException('Unexpected response from Google.', 502, 'non-JSON: ' . substr((string) $raw, 0, 300));
        }
        if ($code >= 400 || isset($decoded['error'])) {
            $msg = $decoded['error_description'] ?? ($decoded['error']['message'] ?? ($decoded['error'] ?? 'oauth error'));
            if (is_array($msg)) $msg = json_encode($msg);
            throw new GoogleException('Google sign-in failed. Please try connecting again.', $code ?: 400, "token endpoint: {$msg}");
        }
        return $decoded;
    }

    private static function request($method, $url, $accessToken, $body, $json)
    {
        $ch = curl_init();
        $headers = ['Authorization: Bearer ' . $accessToken, 'Accept: application/json'];
        $opts = [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
        ];
        if ($json && $body !== null) {
            $headers[] = 'Content-Type: application/json';
            $opts[CURLOPT_POSTFIELDS] = json_encode($body);
        }
        $opts[CURLOPT_HTTPHEADER] = $headers;
        curl_setopt_array($ch, $opts);

        $raw  = curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err  = curl_error($ch);
        curl_close($ch);

        if ($raw === false) {
            throw new GoogleException('Could not reach Google. Please try again.', 504, "curl: {$err}");
        }
        $decoded = $raw === '' ? [] : json_decode($raw, true);
        if (!is_array($decoded)) {
            throw new GoogleException('Unexpected response from Google.', 502, 'non-JSON: ' . substr((string) $raw, 0, 300));
        }
        if ($code >= 400) {
            $apiMsg = $decoded['error']['message'] ?? ($decoded['error'] ?? 'api error');
            if (is_array($apiMsg)) $apiMsg = json_encode($apiMsg);
            GoogleLogger::error('api.error', ['status' => $code, 'url' => self::redact($url), 'msg' => $apiMsg]);
            $safe = self::safeForStatus($code);
            throw new GoogleException($safe, $code, "HTTP {$code}: {$apiMsg}");
        }
        return $decoded;
    }

    private static function safeForStatus($code)
    {
        if ($code === 401) return 'Your Google connection expired. Please reconnect Google Business Profile.';
        if ($code === 403) return 'Google denied access. Your account may not have Business Profile API access yet.';
        if ($code === 404) return 'That Google Business Profile could not be found.';
        if ($code === 429) return 'Google is rate-limiting requests. Please try again shortly.';
        return 'Google Business Profile request failed. Please try again.';
    }

    private static function redact($url)
    {
        return preg_replace('/([?&])(access_token|key)=[^&]+/', '$1$2=REDACTED', $url);
    }
}
