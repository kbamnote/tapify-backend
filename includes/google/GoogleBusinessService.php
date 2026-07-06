<?php
/**
 * TAPIFY Google Business Profile — orchestration service.
 * The single entry point endpoints use: connection status, OAuth completion,
 * location selection, and reading/writing profile fields.
 */
class GoogleBusinessService
{
    /** @var PDO */
    private $db;
    /** @var GoogleBusinessRepo */
    private $repo;

    public function __construct(PDO $db)
    {
        $this->db   = $db;
        $this->repo = new GoogleBusinessRepo($db);
    }

    /** Create a one-time OAuth state for this user and return the consent URL. */
    public function buildConnectUrl($userId)
    {
        if (!GoogleOAuth::isConfigured()) {
            throw new GoogleException('Google Business Profile is not configured on the server yet.', 503,
                'GOOGLE_CLIENT_ID/SECRET empty');
        }
        $state = $this->repo->createState($userId);
        return GoogleOAuth::buildAuthUrl($state);
    }

    /** Handle the OAuth callback: validate state, store tokens, discover location. */
    public function completeOAuth($code, $state)
    {
        $userId = $this->repo->consumeState($state);
        if (!$userId) {
            throw new GoogleException('This sign-in link has expired. Please try connecting again.', 400,
                'invalid/expired oauth state');
        }

        $tokens = GoogleOAuth::exchangeCode($code);
        $access  = $tokens['access_token']  ?? null;
        $refresh = $tokens['refresh_token'] ?? null;
        if (!$access) {
            throw new GoogleException('Google sign-in failed. Please try again.', 502, 'no access_token from exchange');
        }
        $expiry = date('Y-m-d H:i:s', time() + (int) ($tokens['expires_in'] ?? 3600));
        $this->repo->upsertTokens($userId, $access, $refresh, $expiry, $tokens['scope'] ?? GOOGLE_BUSINESS_SCOPE);

        // Best-effort discovery of the first account + location. Never fail the
        // callback on this — the OAuth connection itself succeeded.
        try {
            $this->autoSelectFirstLocation($userId);
        } catch (Exception $e) {
            GoogleLogger::warn('discovery.failed', ['error' => $e->getMessage()]);
        }

        return $userId;
    }

    private function autoSelectFirstLocation($userId)
    {
        $client   = $this->client($userId);
        $accounts = $client->listAccounts();
        if (empty($accounts)) return;

        $account   = $accounts[0];
        $accountId = $account['name'] ?? null;      // "accounts/123"
        if (!$accountId) return;

        $locations = $client->listLocations($accountId);
        if (empty($locations)) {
            $this->repo->setLocation($userId, $accountId, $account['accountName'] ?? '', null, null);
            return;
        }
        $loc = $locations[0];
        $this->repo->setLocation($userId, $accountId, $account['accountName'] ?? '',
            $loc['name'] ?? null, $loc['title'] ?? '');
    }

    /** Connection status for the app. */
    public function getStatus($userId)
    {
        $conn = $this->repo->get($userId);
        return [
            'configured' => GoogleOAuth::isConfigured(),
            'connected'  => $conn !== null,
            'location'   => ($conn && $conn['location_id'])
                ? ['id' => $conn['location_id'], 'title' => $conn['location_title']]
                : null,
        ];
    }

    /** All locations under the connected account (for the picker). */
    public function listLocations($userId)
    {
        $conn = $this->requireConnection($userId);
        $client = $this->client($userId);
        $accountId = $conn['google_account_id'];
        if (!$accountId) {
            $accounts = $client->listAccounts();
            if (empty($accounts)) return [];
            $accountId = $accounts[0]['name'] ?? null;
        }
        $locations = $client->listLocations($accountId);
        return array_map(function ($l) {
            return ['id' => $l['name'] ?? '', 'title' => $l['title'] ?? '(untitled)'];
        }, $locations);
    }

    public function selectLocation($userId, $locationId, $title = null)
    {
        $conn = $this->requireConnection($userId);
        $this->repo->setLocation($userId, $conn['google_account_id'], $conn['account_name'], $locationId, $title);
    }

    /** Current editable + display fields, read live from Google. */
    public function getFields($userId)
    {
        $conn = $this->requireConnection($userId);
        if (empty($conn['location_id'])) {
            throw new GoogleException('No Google Business location is linked yet.', 404, 'location_id empty');
        }
        $client = $this->client($userId);
        $loc = $client->getLocation($conn['location_id'], FieldMap::readMask());
        return FieldMap::toApp($loc);
    }

    /** Write editable fields back to Google, return the refreshed field set. */
    public function updateFields($userId, array $input)
    {
        $conn = $this->requireConnection($userId);
        if (empty($conn['location_id'])) {
            throw new GoogleException('No Google Business location is linked yet.', 404, 'location_id empty');
        }

        // Keep only editable fields that were sent.
        $clean = [];
        foreach (FieldMap::editableFields() as $f) {
            if (array_key_exists($f, $input)) {
                $clean[$f] = is_string($input[$f]) ? trim($input[$f]) : $input[$f];
            }
        }
        if (!$clean) {
            throw new GoogleException('Nothing to update.', 422, 'no editable fields provided');
        }

        list($mask, $body) = FieldMap::buildPatch($clean);
        $client = $this->client($userId);
        $client->patchLocation($conn['location_id'], $mask, $body);

        // Return fresh state so the app reflects exactly what Google stored.
        $loc = $client->getLocation($conn['location_id'], FieldMap::readMask());
        return FieldMap::toApp($loc);
    }

    public function disconnect($userId)
    {
        $this->repo->delete($userId);
    }

    // ── helpers ─────────────────────────────────────────────────────────────
    private function requireConnection($userId)
    {
        $conn = $this->repo->get($userId);
        if (!$conn) {
            throw new GoogleException('Google Business Profile is not connected.', 409, 'no connection row');
        }
        return $conn;
    }

    private function client($userId)
    {
        return new GoogleBusinessClient($this->db, $this->repo, $this->repo->get($userId));
    }
}
