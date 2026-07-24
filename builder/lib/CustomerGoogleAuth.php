<?php
/**
 * Helpers for "Sign in with Google" on published builder sites.
 *
 * The whole OAuth dance runs on the FIXED origin (app.tapify.co.in) so Google
 * needs only one redirect URI — this sidesteps the fact that Google forbids
 * wildcard JavaScript origins / redirect URIs, which every <slug>.tapify.co.in
 * would otherwise require. The site slug + post-login path ride along in a
 * signed, self-expiring `state` value (no server session needed).
 */
class CustomerGoogleAuth
{
    /** Google login is only offered when the OAuth client is configured. */
    public static function isConfigured(): bool
    {
        return defined('GOOGLE_LOGIN_CLIENT_ID') && GOOGLE_LOGIN_CLIENT_ID !== ''
            && defined('GOOGLE_LOGIN_CLIENT_SECRET') && GOOGLE_LOGIN_CLIENT_SECRET !== '';
    }

    /** URL-safe base64 (no padding) — used for both state and its signature. */
    private static function b64(string $raw): string
    {
        return rtrim(strtr(base64_encode($raw), '+/', '-_'), '=');
    }

    private static function unb64(string $s): string
    {
        return base64_decode(strtr($s, '-_', '+/')) ?: '';
    }

    /** Sign {site,next,exp} with the client secret so the callback can trust it. */
    public static function signState(string $slug, string $next): string
    {
        $payload = json_encode([
            'site' => $slug,
            'next' => $next,
            'exp'  => time() + 600,   // 10-minute window to complete consent
        ]);
        $body = self::b64($payload);
        $sig  = self::b64(hash_hmac('sha256', $body, GOOGLE_LOGIN_CLIENT_SECRET, true));
        return $body . '.' . $sig;
    }

    /** Returns ['site'=>..,'next'=>..] if the state is authentic and unexpired, else null. */
    public static function verifyState(string $state): ?array
    {
        $parts = explode('.', $state, 2);
        if (count($parts) !== 2) return null;
        [$body, $sig] = $parts;
        $expect = self::b64(hash_hmac('sha256', $body, GOOGLE_LOGIN_CLIENT_SECRET, true));
        if (!hash_equals($expect, $sig)) return null;
        $data = json_decode(self::unb64($body), true);
        if (!is_array($data) || ($data['exp'] ?? 0) < time()) return null;
        $site = strtolower(trim((string)($data['site'] ?? '')));
        if ($site === '') return null;
        $next = (string)($data['next'] ?? '');
        // Same-origin path only (blocks //evil.com and absolute URLs).
        if ($next === '' || !preg_match('#^/[^/]#', $next)) $next = '/account';
        return ['site' => $site, 'next' => $next];
    }

    /** Public URL of a customer site: https://<slug>.tapify.co.in */
    public static function publicSiteUrl(string $slug): string
    {
        $base = defined('PUBLIC_BASE_DOMAIN') ? PUBLIC_BASE_DOMAIN : 'tapify.co.in';
        return 'https://' . $slug . '.' . $base;
    }
}
