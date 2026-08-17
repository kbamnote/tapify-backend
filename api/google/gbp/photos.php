<?php
/**
 * GET  /api/google/gbp/photos.php   → { photos[], total, categories[] }
 * POST /api/google/gbp/photos.php   Body: { source_url, category? }
 *
 * Photos live on the same legacy v4 API as reviews ("Google My Business API"),
 * which must be enabled on the Cloud project.
 *
 * Google fetches the image from source_url itself, so it has to be publicly
 * hosted already. The app uploads to Cloudinary through the existing media
 * pipeline and passes that URL here — we never stream bytes from the phone to
 * Google.
 */
require_once __DIR__ . '/_bootstrap.php';

gbp_run(function ($userId, $service) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = getInput();
        $res = $service->uploadPhoto(
            $userId,
            (string)($input['source_url'] ?? ''),
            (string)($input['category'] ?? 'ADDITIONAL')
        );
        sendSuccess('Photo added to your Google listing', $res);
    }

    sendSuccess('Photos', $service->listPhotos($userId));
});
