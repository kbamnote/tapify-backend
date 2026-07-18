<?php
/**
 * VercelDomains — makes a published builder site reachable at
 * <slug>.tapify.co.in.
 *
 * Why this exists
 * ---------------
 * Builder sites are served by Vercel, while `*.tapify.co.in` still points at
 * Railway for the existing vCards/stores. A MORE SPECIFIC DNS record wins over
 * the wildcard, so each published site needs exactly two things:
 *
 *   1. the domain attached to the Vercel project (so it answers for that host)
 *   2. a CNAME record `<slug>` -> Vercel in the tapify.co.in DNS zone
 *
 * Doing that by hand per customer is not viable, so publish.php calls this.
 * The existing wildcard record is never touched, so every current vCard keeps
 * resolving to Railway exactly as before.
 *
 * Failure policy
 * --------------
 * DNS setup must NEVER block publishing. If Vercel is unreachable or not
 * configured, the version is still published and this reports what happened so
 * the UI can say "live" vs "finishing setup" instead of lying to the customer.
 *
 * Configuration (all optional — absent = feature simply disabled):
 *   VERCEL_API_TOKEN   personal/team token with domain + project access
 *   VERCEL_PROJECT_ID  the tapify-sites project id (prj_...)
 *   VERCEL_TEAM_ID     only if the project lives under a team
 *   VERCEL_CNAME       defaults to cname.vercel-dns.com (Vercel's legacy target,
 *                      which they document as continuing to work)
 */

class VercelDomains
{
    private const API = 'https://api.vercel.com';

    public static function isConfigured(): bool
    {
        return self::token() !== '' && self::projectId() !== '';
    }

    private static function token(): string     { return trim((string)getenv('VERCEL_API_TOKEN')); }
    private static function projectId(): string { return trim((string)getenv('VERCEL_PROJECT_ID')); }
    private static function teamId(): string    { return trim((string)getenv('VERCEL_TEAM_ID')); }
    private static function cname(): string     { return trim((string)getenv('VERCEL_CNAME')) ?: 'cname.vercel-dns.com'; }
    private static function baseDomain(): string
    {
        return defined('PUBLIC_BASE_DOMAIN') ? PUBLIC_BASE_DOMAIN : 'tapify.co.in';
    }

    /** Append ?teamId=… when the project sits under a Vercel team. */
    private static function url(string $path): string
    {
        $team = self::teamId();
        if ($team === '') return self::API . $path;
        return self::API . $path . (strpos($path, '?') === false ? '?' : '&') . 'teamId=' . urlencode($team);
    }

    /**
     * @return array{status:int, body:array|null, error:string|null}
     */
    private static function call(string $method, string $path, ?array $payload = null): array
    {
        $ch = curl_init(self::url($path));
        $headers = [
            'Authorization: Bearer ' . self::token(),
            'Content-Type: application/json',
        ];
        $opts = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_CONNECTTIMEOUT => 8,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_CUSTOMREQUEST  => $method,
        ];
        if ($payload !== null) {
            $opts[CURLOPT_POSTFIELDS] = json_encode($payload);
        }
        curl_setopt_array($ch, $opts);

        $raw    = curl_exec($ch);
        $status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err    = curl_error($ch);
        curl_close($ch);

        if ($raw === false) {
            return ['status' => 0, 'body' => null, 'error' => $err ?: 'Request failed'];
        }
        $body = json_decode($raw, true);
        return ['status' => $status, 'body' => is_array($body) ? $body : null, 'error' => null];
    }

    /**
     * Make <slug>.<base domain> resolve to this Vercel project.
     *
     * Idempotent: re-publishing an existing site is a no-op rather than an error,
     * and an existing CNAME is left alone instead of being duplicated.
     *
     * @return array{
     *   configured:bool, ok:bool, host:string,
     *   domain:string, record:string, message:string
     * }
     */
    public static function ensureSiteDomain(string $slug): array
    {
        $slug = strtolower(trim($slug));
        $host = $slug . '.' . self::baseDomain();

        $result = [
            'configured' => self::isConfigured(),
            'ok'         => false,
            'host'       => $host,
            'domain'     => 'skipped',
            'record'     => 'skipped',
            'message'    => '',
        ];

        if (!preg_match('/^[a-z0-9](?:[a-z0-9-]{1,61}[a-z0-9])?$/', $slug)) {
            $result['message'] = 'Slug is not a valid DNS label.';
            return $result;
        }

        if (!self::isConfigured()) {
            // Not an error — the feature is simply not switched on yet.
            $result['message'] = 'Vercel is not configured; the address must be added manually.';
            return $result;
        }

        // --- 1. attach the domain to the project ---
        $res = self::call('POST', '/v10/projects/' . rawurlencode(self::projectId()) . '/domains', ['name' => $host]);
        $code = $res['body']['error']['code'] ?? null;

        if ($res['status'] >= 200 && $res['status'] < 300) {
            $result['domain'] = 'added';
        } elseif ($res['status'] === 409 || in_array($code, ['domain_already_in_use', 'domain_taken'], true)) {
            $result['domain'] = 'already';      // fine — re-publish
        } else {
            $result['message'] = 'Could not attach the address: '
                . ($res['body']['error']['message'] ?? $res['error'] ?? ('HTTP ' . $res['status']));
            return $result;
        }

        // --- 2. make sure the CNAME exists (don't duplicate it) ---
        $zone = self::baseDomain();
        $list = self::call('GET', '/v4/domains/' . rawurlencode($zone) . '/records?limit=200');

        if ($list['status'] >= 200 && $list['status'] < 300) {
            foreach (($list['body']['records'] ?? []) as $rec) {
                if (strtolower((string)($rec['name'] ?? '')) === $slug
                    && strtoupper((string)($rec['type'] ?? '')) === 'CNAME') {
                    $result['record'] = 'already';
                    $result['ok'] = true;
                    $result['message'] = 'Address is set up.';
                    return $result;
                }
            }
        }

        $add = self::call('POST', '/v2/domains/' . rawurlencode($zone) . '/records', [
            'name'  => $slug,
            'type'  => 'CNAME',
            'value' => self::cname(),
            'ttl'   => 60,
        ]);

        if ($add['status'] >= 200 && $add['status'] < 300) {
            $result['record'] = 'added';
            $result['ok'] = true;
            $result['message'] = 'Address created. It can take a few minutes to work worldwide.';
            return $result;
        }

        // The domain is attached but DNS isn't — surface that rather than
        // claiming the site is reachable.
        $result['message'] = 'Address attached but the DNS record failed: '
            . ($add['body']['error']['message'] ?? $add['error'] ?? ('HTTP ' . $add['status']));
        return $result;
    }
}
