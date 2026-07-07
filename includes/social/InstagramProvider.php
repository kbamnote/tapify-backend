<?php
/**
 * TAPIFY Social Publishing — Instagram (Business/Creator) provider.
 * Instagram connects through the SAME Meta OAuth as Facebook (see
 * FacebookProvider — it discovers the IG account linked to each Page), so this
 * provider only implements publish(). Publishing is the 2-step Content
 * Publishing flow: create a media container, then publish it.
 * https://developers.facebook.com/docs/instagram-api/guides/content-publishing
 */
class InstagramProvider implements SocialProviderInterface
{
    public function platform() { return 'instagram'; }

    public function isConfigured()
    {
        return defined('FACEBOOK_APP_ID') && FACEBOOK_APP_ID !== ''
            && defined('FACEBOOK_APP_SECRET') && FACEBOOK_APP_SECRET !== '';
    }

    private function graph()
    {
        return 'https://graph.facebook.com/' . (defined('FACEBOOK_GRAPH_VERSION') ? FACEBOOK_GRAPH_VERSION : 'v21.0');
    }

    // Instagram is authorized via the Facebook flow, so these are never used for connect.
    public function buildAuthUrl($state)
    {
        throw new SocialException('Connect Instagram by connecting Facebook.', 400, 'ig connect goes via facebook');
    }
    public function authorize($code)
    {
        throw new SocialException('Connect Instagram by connecting Facebook.', 400, 'ig authorize goes via facebook');
    }

    public function publish(array $connection, array $content)
    {
        $igUserId = $connection['account_id'];               // IG user id
        $token    = $connection['access_token'];             // Page token
        $caption  = (string) ($content['caption'] ?? '');
        $media    = is_array($content['media'] ?? null) ? $content['media'] : [];

        $images = array_values(array_filter($media, fn($m) => ($m['type'] ?? '') === 'image' && !empty($m['url'])));
        $videos = array_values(array_filter($media, fn($m) => ($m['type'] ?? '') === 'video' && !empty($m['url'])));

        if (!$images && !$videos) {
            throw new SocialException('Instagram posts need at least one photo or video.', 422, 'ig no media');
        }

        // Prefer images when present (single or carousel). v1 posts one media
        // kind per IG post; a video is only posted when there are no images.
        if ($images) {
            if (count($images) === 1) {
                $creationId = $this->createContainer($igUserId, $token, [
                    'image_url' => $images[0]['url'],
                    'caption'   => $caption,
                ]);
                return $this->publishContainer($igUserId, $token, $creationId);
            }

            // Carousel (2–10 images).
            $childIds = [];
            foreach (array_slice($images, 0, 10) as $img) {
                $childIds[] = $this->createContainer($igUserId, $token, [
                    'image_url'        => $img['url'],
                    'is_carousel_item' => 'true',
                ]);
            }
            $creationId = $this->createContainer($igUserId, $token, [
                'media_type' => 'CAROUSEL',
                'children'   => implode(',', $childIds),
                'caption'    => $caption,
            ]);
            return $this->publishContainer($igUserId, $token, $creationId);
        }

        // Videos only → Reel (first video).
        $creationId = $this->createContainer($igUserId, $token, [
            'media_type' => 'REELS',
            'video_url'  => $videos[0]['url'],
            'caption'    => $caption,
        ]);
        $this->waitForContainer($igUserId, $token, $creationId);
        return $this->publishContainer($igUserId, $token, $creationId);
    }

    private function createContainer($igUserId, $token, array $params)
    {
        $params['access_token'] = $token;
        $res = SocialHttp::postForm($this->graph() . '/' . $igUserId . '/media', $params);
        if (empty($res['id'])) {
            throw new SocialException('Instagram rejected the media. Please try a different file.', 502, 'no container id');
        }
        return $res['id'];
    }

    /** Poll a (video) container until it finishes processing. */
    private function waitForContainer($igUserId, $token, $creationId)
    {
        for ($i = 0; $i < 12; $i++) {          // up to ~36s
            $res = SocialHttp::get($this->graph() . '/' . $creationId . '?' . http_build_query([
                'fields'       => 'status_code',
                'access_token' => $token,
            ]));
            $status = $res['status_code'] ?? 'IN_PROGRESS';
            if ($status === 'FINISHED') return;
            if ($status === 'ERROR' || $status === 'EXPIRED') {
                throw new SocialException('Instagram could not process the video. Try a shorter/standard MP4.', 502,
                    "container status {$status}");
            }
            sleep(3);
        }
        throw new SocialException('Instagram is still processing the video. Please try again in a moment.', 504,
            'container processing timeout');
    }

    private function publishContainer($igUserId, $token, $creationId)
    {
        $res = SocialHttp::postForm($this->graph() . '/' . $igUserId . '/media_publish', [
            'creation_id'  => $creationId,
            'access_token' => $token,
        ]);
        $mediaId = $res['id'] ?? '';
        $url = null;
        if ($mediaId) {
            try {
                $perma = SocialHttp::get($this->graph() . '/' . $mediaId . '?' . http_build_query([
                    'fields'       => 'permalink',
                    'access_token' => $token,
                ]));
                $url = $perma['permalink'] ?? null;
            } catch (Exception $e) { /* permalink is best-effort */ }
        }
        return ['remote_post_id' => $mediaId, 'remote_url' => $url];
    }
}
