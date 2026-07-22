<?php
/**
 * /api/sites/media.php — the builder's media library.
 *
 * Documents never store a raw file URL. They store "media:<id>", and the
 * renderer turns that into GET media.php?id=<id>. That indirection is the whole
 * point: a file can be re-uploaded, moved, or swapped to a CDN without ever
 * rewriting a single site's JSON.
 *
 * Verbs:
 *   GET  ?id=<id>          Serve one asset. PUBLIC — published sites embed these,
 *                          so a visitor with no login must be able to load them.
 *   GET  ?mine=1           List the caller's library (auth). For the picker UI.
 *   POST (multipart)       Upload a file (auth). Returns { id, ref, url, … }.
 *   DELETE ?id=<id>        Remove an asset from the library (auth, owner only).
 *
 * Storage mirrors the rest of Tapify: Cloudinary when configured (persistent —
 * Railway's own filesystem is wiped on every deploy), local disk as a fallback.
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../builder/lib/SiteRepo.php';

$method = $_SERVER['REQUEST_METHOD'];
if ($method === 'OPTIONS') { http_response_code(204); exit; }

/** Uploads we accept. SVG is deliberately excluded: served from the API origin
 *  it would be a stored-XSS vector, and the builder gets icons from the icon
 *  field, not uploaded files. Raster images + PDF cover every real need. */
const MEDIA_ALLOWED = [
    'jpg'  => ['image/jpeg', 'image'],
    'jpeg' => ['image/jpeg', 'image'],
    'png'  => ['image/png',  'image'],
    'gif'  => ['image/gif',  'image'],
    'webp' => ['image/webp', 'image'],
    'pdf'  => ['application/pdf', 'pdf'],
];
const MEDIA_MAX_BYTES = 10 * 1024 * 1024; // 10 MB
const MEDIA_LOCAL_DIR = __DIR__ . '/../../uploads/sites';       // absolute
const MEDIA_LOCAL_WEB = '/uploads/sites';                       // public path prefix

if ($method === 'GET' && isset($_GET['id']))   { serveAsset((int)$_GET['id']); }
if ($method === 'GET' && isset($_GET['mine']))  { listLibrary(); }
if ($method === 'POST')                         { uploadAsset(); }
if ($method === 'DELETE')                       { deleteAsset(); }

sendError('Unsupported request', 400);


/** GET ?id=<id> — stream/redirect the bytes. No auth: this is what pages embed. */
function serveAsset(int $id): void
{
    if ($id <= 0) { http_response_code(400); exit; }

    $stmt = getDB()->prepare("SELECT path, mime FROM media_assets WHERE id = ? LIMIT 1");
    $stmt->execute([$id]);
    $asset = $stmt->fetch();
    if (!$asset) { http_response_code(404); exit; }

    $path = (string)$asset['path'];

    // Cloudinary / any absolute URL: bounce the browser straight there. A 302
    // (not 301) so a later re-upload that changes the target is picked up.
    if (preg_match('#^https?://#i', $path)) {
        header('Cache-Control: public, max-age=3600');
        header('Location: ' . $path, true, 302);
        exit;
    }

    // Local file: resolve and confirm it is still inside the uploads dir before
    // reading anything, so a tampered row can't turn this into arbitrary file read.
    $base = realpath(MEDIA_LOCAL_DIR);
    $full = realpath(__DIR__ . '/../../' . ltrim($path, '/'));
    if ($base === false || $full === false || strpos($full, $base) !== 0 || !is_file($full)) {
        http_response_code(404); exit;
    }

    $mime = $asset['mime'] ?: (function_exists('mime_content_type') ? mime_content_type($full) : 'application/octet-stream');
    header('Content-Type: ' . $mime);
    header('Content-Length: ' . filesize($full));
    header('Cache-Control: public, max-age=86400');
    header('X-Content-Type-Options: nosniff');
    readfile($full);
    exit;
}

/** GET ?mine=1 — the caller's library, newest first, for the picker. */
function listLibrary(): void
{
    requireAuth();
    $userId = getCurrentUserId();
    $siteId = isset($_GET['site_id']) && is_numeric($_GET['site_id']) ? (int)$_GET['site_id'] : null;

    $sql  = "SELECT id, site_id, kind, path, mime, bytes, width, height, alt, title, created_at
             FROM media_assets WHERE user_id = ?";
    $args = [$userId];
    if ($siteId !== null) { $sql .= " AND (site_id = ? OR site_id IS NULL)"; $args[] = $siteId; }
    $sql .= " ORDER BY created_at DESC LIMIT 200";

    $stmt = getDB()->prepare($sql);
    $stmt->execute($args);

    $items = array_map(function ($row) {
        $row['id']  = (int)$row['id'];
        $row['ref'] = 'media:' . $row['id'];
        $row['url'] = SITE_URL . '/api/sites/media.php?id=' . $row['id'];
        return $row;
    }, $stmt->fetchAll());

    sendSuccess('OK', ['items' => $items]);
}

/** POST (multipart) — upload one file into the library. */
function uploadAsset(): void
{
    requireAuth();
    $userId = getCurrentUserId();

    if (!isset($_FILES['file']) || ($_FILES['file']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        sendError(uploadErrorMessage($_FILES['file']['error'] ?? UPLOAD_ERR_NO_FILE), 400);
    }
    $file = $_FILES['file'];

    if ($file['size'] <= 0)                 sendError('The file is empty.', 400);
    if ($file['size'] > MEDIA_MAX_BYTES)    sendError('File is too large. Maximum size is 10 MB.', 413);

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!isset(MEDIA_ALLOWED[$ext])) {
        sendError('Unsupported file type. Allowed: ' . implode(', ', array_keys(MEDIA_ALLOWED)) . '.', 415);
    }
    [$expectedMime, $kind] = MEDIA_ALLOWED[$ext];

    // Trust the file's actual bytes, not its name. A .png that isn't an image
    // (or an image whose real type differs from its extension) is rejected.
    $realMime = null;
    if (function_exists('finfo_open')) {
        $fi = finfo_open(FILEINFO_MIME_TYPE);
        $realMime = finfo_file($fi, $file['tmp_name']);
        finfo_close($fi);
    }
    if ($realMime && $realMime !== $expectedMime
        && !($expectedMime === 'image/jpeg' && in_array($realMime, ['image/jpeg', 'image/pjpeg'], true))) {
        sendError('The file contents do not match its extension.', 415);
    }

    // If this upload is scoped to a site, that site must belong to the caller.
    $siteId = null;
    if (!empty($_POST['site_id']) && is_numeric($_POST['site_id'])) {
        $site = SiteRepo::findById((int)$_POST['site_id']);
        if (!$site) sendError('Site not found', 404);
        if (!SiteRepo::ownedBy($site, $userId) && !isStaffOrAdmin()) sendError('Access denied', 403);
        $siteId = (int)$site['id'];
    }

    // Dimensions up front so the renderer can reserve space (no layout shift).
    $width = $height = null;
    if ($kind === 'image') {
        $dims = @getimagesize($file['tmp_name']);
        if ($dims) { $width = $dims[0] ?: null; $height = $dims[1] ?: null; }
    }

    // Persist the bytes: Cloudinary first, local disk as a fallback.
    $storedPath = null;
    if (defined('CLOUDINARY_CLOUD_NAME') && CLOUDINARY_CLOUD_NAME
        && defined('CLOUDINARY_API_KEY') && CLOUDINARY_API_KEY) {
        try {
            $res = uploadToCloudinary($file);
            if (!empty($res['success']) && !empty($res['url'])) $storedPath = $res['url'];
        } catch (Throwable $e) {
            $storedPath = null; // fall through to local
        }
    }
    if ($storedPath === null) {
        if (!is_dir(MEDIA_LOCAL_DIR) && !mkdir(MEDIA_LOCAL_DIR, 0755, true) && !is_dir(MEDIA_LOCAL_DIR)) {
            sendError('Could not prepare storage. Please try again.', 500);
        }
        $name = 'st_' . bin2hex(random_bytes(8)) . '.' . $ext;
        if (!move_uploaded_file($file['tmp_name'], MEDIA_LOCAL_DIR . '/' . $name)) {
            sendError('Failed to save the file. Please try again.', 500);
        }
        $storedPath = MEDIA_LOCAL_WEB . '/' . $name;
    }

    $alt   = isset($_POST['alt'])   ? mb_substr(trim((string)$_POST['alt']), 0, 255)   : null;
    $title = isset($_POST['title']) ? mb_substr(trim((string)$_POST['title']), 0, 255) : null;

    // Use ONE connection for the insert and lastInsertId(): getDB() opens a fresh
    // PDO each call, so a second getDB()->lastInsertId() would read a brand-new
    // connection and return 0 (the "media:0" bug).
    $db = getDB();
    $stmt = $db->prepare(
        "INSERT INTO media_assets (user_id, site_id, kind, path, mime, bytes, width, height, alt, title)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );
    $stmt->execute([$userId, $siteId, $kind, $storedPath, $expectedMime, (int)$file['size'], $width, $height, $alt, $title]);
    $id = (int)$db->lastInsertId();

    sendSuccess('Uploaded', [
        'id'     => $id,
        'ref'    => 'media:' . $id,      // this is what the document stores
        'url'    => SITE_URL . '/api/sites/media.php?id=' . $id,
        'kind'   => $kind,
        'mime'   => $expectedMime,
        'bytes'  => (int)$file['size'],
        'width'  => $width,
        'height' => $height,
        'alt'    => $alt,
        'title'  => $title,
    ]);
}

/** DELETE ?id=<id> — drop an asset from the library (owner or staff). */
function deleteAsset(): void
{
    requireAuth();
    $id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : 0;
    if ($id <= 0) sendError('id is required', 400);

    $stmt = getDB()->prepare("SELECT user_id, path FROM media_assets WHERE id = ? LIMIT 1");
    $stmt->execute([$id]);
    $asset = $stmt->fetch();
    if (!$asset) sendError('Not found', 404);

    if ((int)$asset['user_id'] !== (int)getCurrentUserId() && !isStaffOrAdmin()) {
        sendError('Access denied', 403);
    }

    // Remove the row first — that is what actually breaks the "media:<id>"
    // reference. A local file is best-effort unlinked; a Cloudinary asset is
    // left in place (destroying it needs the signed Admin API — a later cleanup
    // job can reconcile orphans). Callers should avoid deleting media a
    // published document still points at.
    getDB()->prepare("DELETE FROM media_assets WHERE id = ?")->execute([$id]);

    $path = (string)$asset['path'];
    if (!preg_match('#^https?://#i', $path)) {
        $base = realpath(MEDIA_LOCAL_DIR);
        $full = realpath(__DIR__ . '/../../' . ltrim($path, '/'));
        if ($base !== false && $full !== false && strpos($full, $base) === 0 && is_file($full)) {
            @unlink($full);
        }
    }

    sendSuccess('Deleted', ['id' => $id]);
}

/** Human-readable reason for a PHP upload failure. */
function uploadErrorMessage(int $code): string
{
    switch ($code) {
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE: return 'File is too large.';
        case UPLOAD_ERR_PARTIAL:   return 'The upload was interrupted. Please try again.';
        case UPLOAD_ERR_NO_FILE:   return 'No file was uploaded.';
        default:                   return 'The upload failed. Please try again.';
    }
}
