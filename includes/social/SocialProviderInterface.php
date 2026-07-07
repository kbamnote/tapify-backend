<?php
/**
 * TAPIFY Social Publishing — provider contract.
 *
 * Business logic (SocialService) depends only on this interface, never on
 * Facebook/Instagram/LinkedIn specifics. Add a platform = implement this +
 * register it in SocialProviderFactory. Nothing else changes.
 */
interface SocialProviderInterface
{
    /** Short platform id stored on connections, e.g. "facebook". */
    public function platform();

    /** True only when this platform's app credentials are configured. */
    public function isConfigured();

    /** OAuth consent URL the user is sent to (state binds it to the user). */
    public function buildAuthUrl($state);

    /**
     * Exchange the OAuth code and return the list of connectable accounts.
     * One authorization can yield several (e.g. multiple FB Pages + IG accounts).
     *
     * @return array[] each: [
     *   'platform'       => string,   // may differ from provider (FB auth → IG connection)
     *   'account_id'     => string,
     *   'account_name'   => string,
     *   'account_avatar' => string|null,
     *   'access_token'   => string,
     *   'refresh_token'  => string|null,
     *   'token_expiry'   => 'Y-m-d H:i:s'|null,
     *   'scope'          => string|null,
     *   'extra'          => array,     // platform extras (ig_user_id, urn, page_id…)
     * ]
     */
    public function authorize($code);

    /**
     * Publish one post to one connected account.
     *
     * @param array $connection  a social_connections row
     * @param array $content     ['caption' => string, 'media' => [['type'=>'image|video','url'=>...]]]
     * @return array ['remote_post_id' => string, 'remote_url' => string|null]
     * @throws SocialException
     */
    public function publish(array $connection, array $content);
}
