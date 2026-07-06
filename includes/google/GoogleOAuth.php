<?php
/**
 * TAPIFY Google Business Profile — OAuth 2.0 (authorization-code, server-side).
 * Builds the consent URL and exchanges/refreshes tokens. Client secret stays
 * on the server; the app never sees it.
 */
class GoogleOAuth
{
    const AUTH_URL  = 'https://accounts.google.com/o/oauth2/v2/auth';
    const TOKEN_URL = 'https://oauth2.googleapis.com/token';

    /** True only when OAuth credentials are configured. */
    public static function isConfigured()
    {
        return defined('GOOGLE_CLIENT_ID') && GOOGLE_CLIENT_ID !== ''
            && defined('GOOGLE_CLIENT_SECRET') && GOOGLE_CLIENT_SECRET !== '';
    }

    /** Consent URL the user is sent to (offline access → refresh token). */
    public static function buildAuthUrl($state)
    {
        $params = [
            'client_id'     => GOOGLE_CLIENT_ID,
            'redirect_uri'  => GOOGLE_OAUTH_REDIRECT,
            'response_type' => 'code',
            'scope'         => GOOGLE_BUSINESS_SCOPE,
            'access_type'   => 'offline',
            'include_granted_scopes' => 'true',
            'prompt'        => 'consent',   // force refresh_token on reconnect
            'state'         => $state,
        ];
        return self::AUTH_URL . '?' . http_build_query($params);
    }

    /** Exchange an authorization code → tokens array. */
    public static function exchangeCode($code)
    {
        return GoogleHttp::postForm(self::TOKEN_URL, [
            'code'          => $code,
            'client_id'     => GOOGLE_CLIENT_ID,
            'client_secret' => GOOGLE_CLIENT_SECRET,
            'redirect_uri'  => GOOGLE_OAUTH_REDIRECT,
            'grant_type'    => 'authorization_code',
        ]);
    }

    /** Use a refresh token to mint a fresh access token. */
    public static function refresh($refreshToken)
    {
        return GoogleHttp::postForm(self::TOKEN_URL, [
            'refresh_token' => $refreshToken,
            'client_id'     => GOOGLE_CLIENT_ID,
            'client_secret' => GOOGLE_CLIENT_SECRET,
            'grant_type'    => 'refresh_token',
        ]);
    }
}
