<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../../config/database.php';
ini_set('display_errors', 0); // Override database.php config for JSON APIs
require_once __DIR__ . '/../../../includes/functions.php';

requireDesignerOrAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Method not allowed', 405);
}

if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    $uploadError = isset($_FILES['image']['error']) ? $_FILES['image']['error'] : 'No file uploaded';
    sendError('Image file is required. Upload error: ' . $uploadError, 400);
}

$url = null;

// Try Cloudinary first
try {
    if (defined('CLOUDINARY_CLOUD_NAME') && defined('CLOUDINARY_API_KEY')) {
        $result = uploadToCloudinary($_FILES['image']);
        if ($result && !empty($result['url'])) {
            $url = $result['url'];
        } elseif ($result && !empty($result['secure_url'])) {
            $url = $result['secure_url'];
        }
    }
} catch (Throwable $e) {
    // Cloudinary failed — will fall back to local upload below
    $url = null;
}

// Fallback to local upload if Cloudinary failed
if (!$url) {
    try {
        $localResult = uploadFile($_FILES['image']);
        if ($localResult && !empty($localResult['url'])) {
            $url = $localResult['url'];
        } elseif (is_string($localResult)) {
            $url = $localResult;
        }
    } catch (Throwable $e) {
        sendError('Image upload failed: ' . $e->getMessage(), 500);
    }
}

if (!$url) {
    sendError('Image upload failed. Could not obtain a valid URL.', 500);
}

sendSuccess('Image uploaded', ['url' => $url]);
