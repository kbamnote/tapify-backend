<?php
/**
 * TAPIFY Social Publishing — media upload to Cloudinary.
 * Platforms (esp. Instagram) require a PUBLIC media URL, which Cloudinary gives
 * us. Handles both images and videos (signed upload) and returns a normalized
 * { type, url } the providers can post.
 */
class MediaUploader
{
    /**
     * @param array $file a single $_FILES[...] entry
     * @return array ['type' => 'image'|'video', 'url' => string]
     * @throws SocialException
     */
    public static function upload($file)
    {
        if (empty($file['tmp_name'])) {
            throw new SocialException('No file was uploaded.', 422, 'empty tmp_name');
        }
        if (!defined('CLOUDINARY_CLOUD_NAME') || CLOUDINARY_CLOUD_NAME === '') {
            throw new SocialException('Media uploads are not configured.', 503, 'Cloudinary not configured');
        }

        $mime = (string) ($file['type'] ?? '');
        $isVideo = strpos($mime, 'video/') === 0;
        $resource = $isVideo ? 'video' : 'image';

        $cloud     = CLOUDINARY_CLOUD_NAME;
        $apiKey    = CLOUDINARY_API_KEY;
        $apiSecret = CLOUDINARY_API_SECRET;
        $timestamp = time();

        // Signed upload signature (params sorted, secret appended).
        $params = ['timestamp' => $timestamp];
        ksort($params);
        $signStr = '';
        foreach ($params as $k => $v) $signStr .= "$k=$v&";
        $signature = sha1(rtrim($signStr, '&') . $apiSecret);

        $url = "https://api.cloudinary.com/v1_1/{$cloud}/{$resource}/upload";
        $postData = [
            'file'      => new CURLFile($file['tmp_name'], $mime ?: 'application/octet-stream', $file['name'] ?? 'upload'),
            'api_key'   => $apiKey,
            'timestamp' => $timestamp,
            'signature' => $signature,
        ];

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $postData,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 120,   // videos can be large
        ]);
        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($response === false || $err) {
            throw new SocialException('Media upload failed. Please try again.', 502, "cloudinary curl: {$err}");
        }
        $result = json_decode($response, true);
        if (!isset($result['secure_url'])) {
            $msg = $result['error']['message'] ?? 'unknown cloudinary error';
            throw new SocialException('Media upload failed: ' . $msg, 502, $msg);
        }

        return ['type' => $isVideo ? 'video' : 'image', 'url' => $result['secure_url']];
    }
}
