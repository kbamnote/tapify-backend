<?php
/**
 * TAPIFY Social Publishing — Facebook Pages provider.
 * OAuth requests IG scopes too (so connecting once covers Instagram later with
 * no re-auth). authorize() discovers the user's Pages; publish() posts text,
 * photos (single or multi) or a video to a Page's feed.
 * https://developers.facebook.com/docs/pages-api
 */
class FacebookProvider implements SocialProviderInterface
{
    // Includes IG scopes now so Instagram can be added without reconnecting.
    // pages_manage_ads maps to the Page ADVERTISE task and ads_management to ad
    // creation — both are needed so a customer's consent lets Tapify boost their
    // Page's posts (reseller ads model).
    const SCOPES = 'pages_show_list,pages_manage_posts,pages_read_engagement,pages_manage_ads,ads_management,business_management,instagram_basic,instagram_content_publish';

    public function platform() { return 'facebook'; }

    public function isConfigured()
    {
        return defined('FACEBOOK_APP_ID') && FACEBOOK_APP_ID !== ''
            && defined('FACEBOOK_APP_SECRET') && FACEBOOK_APP_SECRET !== '';
    }

    private function version() { return defined('FACEBOOK_GRAPH_VERSION') ? FACEBOOK_GRAPH_VERSION : 'v21.0'; }
    private function graph()   { return 'https://graph.facebook.com/' . $this->version(); }

    public function buildAuthUrl($state)
    {
        $params = [
            'client_id'     => FACEBOOK_APP_ID,
            'redirect_uri'  => SOCIAL_OAUTH_REDIRECT,
            'state'         => $state,
            'response_type' => 'code',
        ];
        // Facebook Login for Business: reference the saved Configuration (which
        // carries the permissions + assets). Classic Login: fall back to scope.
        if (defined('FACEBOOK_CONFIG_ID') && FACEBOOK_CONFIG_ID !== '') {
            $params['config_id'] = FACEBOOK_CONFIG_ID;
        } else {
            $params['scope'] = self::SCOPES;
        }
        return 'https://www.facebook.com/' . $this->version() . '/dialog/oauth?' . http_build_query($params);
    }

    public function authorize($code)
    {
        // 1) code -> short-lived user token
        $tok = SocialHttp::get($this->graph() . '/oauth/access_token?' . http_build_query([
            'client_id'     => FACEBOOK_APP_ID,
            'client_secret' => FACEBOOK_APP_SECRET,
            'redirect_uri'  => SOCIAL_OAUTH_REDIRECT,
            'code'          => $code,
        ]));
        $userToken = $tok['access_token'] ?? null;
        if (!$userToken) {
            throw new SocialException('Facebook sign-in failed. Please try again.', 502, 'no user access_token');
        }

        // 2) exchange for a long-lived user token (~60 days)
        $long = SocialHttp::get($this->graph() . '/oauth/access_token?' . http_build_query([
            'grant_type'        => 'fb_exchange_token',
            'client_id'         => FACEBOOK_APP_ID,
            'client_secret'     => FACEBOOK_APP_SECRET,
            'fb_exchange_token' => $userToken,
        ]));
        $longToken = $long['access_token'] ?? $userToken;
        $expiry = isset($long['expires_in']) ? date('Y-m-d H:i:s', time() + (int) $long['expires_in']) : null;

        // 3) list the Pages the user manages (page tokens are long-lived)
        $pagesRes = SocialHttp::get($this->graph() . '/me/accounts?' . http_build_query([
            'fields'       => 'id,name,access_token,picture{url}',
            'access_token' => $longToken,
            'limit'        => 100,
        ]));

        $connections = [];
        foreach (($pagesRes['data'] ?? []) as $page) {
            if (empty($page['id']) || empty($page['access_token'])) continue;
            $pageId    = $page['id'];
            $pageToken = $page['access_token'];

            $connections[] = [
                'platform'       => 'facebook',
                'account_id'     => $pageId,
                'account_name'   => $page['name'] ?? 'Facebook Page',
                'account_avatar' => $page['picture']['data']['url'] ?? null,
                'access_token'   => $pageToken,
                'refresh_token'  => null,
                'token_expiry'   => $expiry,
                'scope'          => self::SCOPES,
                'extra'          => ['page_id' => $pageId],
            ];

            // Also surface the Instagram Business account linked to this Page
            // (published to with the same Page token). Best-effort per page.
            try {
                $igRes = SocialHttp::get($this->graph() . '/' . $pageId . '?' . http_build_query([
                    'fields'       => 'instagram_business_account{id,username,profile_picture_url}',
                    'access_token' => $pageToken,
                ]));
                $ig = $igRes['instagram_business_account'] ?? null;
                if (!empty($ig['id'])) {
                    $connections[] = [
                        'platform'       => 'instagram',
                        'account_id'     => $ig['id'],
                        'account_name'   => isset($ig['username']) ? ('@' . $ig['username']) : 'Instagram',
                        'account_avatar' => $ig['profile_picture_url'] ?? null,
                        'access_token'   => $pageToken,
                        'refresh_token'  => null,
                        'token_expiry'   => $expiry,
                        'scope'          => self::SCOPES,
                        'extra'          => ['ig_user_id' => $ig['id'], 'page_id' => $pageId],
                    ];
                }
            } catch (Exception $e) {
                SocialLogger::warn('ig.discovery_skipped', ['page' => $pageId]);
            }
        }

        if (!$connections) {
            throw new SocialException('No Facebook Pages found on this account. You need a Page to post.', 404,
                'me/accounts returned no pages');
        }
        return $connections;
    }

    public function publish(array $connection, array $content)
    {
        $pageId = $connection['account_id'];
        $token  = $connection['access_token'];
        $caption = (string) ($content['caption'] ?? '');
        $media   = is_array($content['media'] ?? null) ? $content['media'] : [];

        $images = array_values(array_filter($media, fn($m) => ($m['type'] ?? '') === 'image' && !empty($m['url'])));
        $videos = array_values(array_filter($media, fn($m) => ($m['type'] ?? '') === 'video' && !empty($m['url'])));

        // Video post (first video; FB videos are one per post).
        if ($videos) {
            $res = SocialHttp::postForm($this->graph() . '/' . $pageId . '/videos', [
                'file_url'     => $videos[0]['url'],
                'description'  => $caption,
                'access_token' => $token,
            ]);
            $id = $res['id'] ?? ($res['post_id'] ?? '');
            return ['remote_post_id' => $id, 'remote_url' => $id ? "https://www.facebook.com/{$id}" : null];
        }

        // Single photo.
        if (count($images) === 1) {
            $res = SocialHttp::postForm($this->graph() . '/' . $pageId . '/photos', [
                'url'          => $images[0]['url'],
                'caption'      => $caption,
                'access_token' => $token,
            ]);
            $id = $res['post_id'] ?? ($res['id'] ?? '');
            return ['remote_post_id' => $id, 'remote_url' => $id ? "https://www.facebook.com/{$id}" : null];
        }

        // Multiple photos: upload unpublished, then attach to one feed post.
        if (count($images) > 1) {
            $params = ['message' => $caption, 'access_token' => $token];
            $i = 0;
            foreach ($images as $img) {
                $up = SocialHttp::postForm($this->graph() . '/' . $pageId . '/photos', [
                    'url'          => $img['url'],
                    'published'    => 'false',
                    'access_token' => $token,
                ]);
                if (!empty($up['id'])) {
                    $params["attached_media[{$i}]"] = json_encode(['media_fbid' => $up['id']]);
                    $i++;
                }
            }
            $res = SocialHttp::postForm($this->graph() . '/' . $pageId . '/feed', $params);
            $id = $res['id'] ?? '';
            return ['remote_post_id' => $id, 'remote_url' => $id ? "https://www.facebook.com/{$id}" : null];
        }

        // Text only.
        $res = SocialHttp::postForm($this->graph() . '/' . $pageId . '/feed', [
            'message'      => $caption,
            'access_token' => $token,
        ]);
        $id = $res['id'] ?? '';
        return ['remote_post_id' => $id, 'remote_url' => $id ? "https://www.facebook.com/{$id}" : null];
    }
}
