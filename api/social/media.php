<?php
/**
 * POST /api/social/media.php   (multipart/form-data, field name: "file")
 * Uploads one image/video to Cloudinary and returns a public { type, url } the
 * composer attaches to a post.
 */
require_once __DIR__ . '/_bootstrap.php';

social_run(function ($userId, $service) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendError('Method not allowed', 405);
    }
    if (empty($_FILES['file'])) {
        sendError('No file provided (use form field "file").', 422);
    }
    $media = MediaUploader::upload($_FILES['file']);
    sendSuccess('Uploaded', $media);   // { type, url }
});
