<?php
/**
 * GET /api/sites/schema.php
 *
 * Serves the builder contract (site schema + section manifests + themes +
 * industries) in ONE payload. The web builder, the mobile app and the Next.js
 * renderer all boot from this — it is what lets a new section appear in every
 * editor without shipping any client code.
 *
 * Public + cacheable: it contains no user data, only the contract.
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../builder/lib/SchemaRegistry.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'GET') sendError('Only GET allowed', 405);

try {
    $bundle = SchemaRegistry::bundle();

    // Cheap cache validator so editors don't re-download the registry constantly.
    $etag = '"' . md5(json_encode($bundle)) . '"';
    header('Cache-Control: public, max-age=300');
    header("ETag: $etag");
    if (($_SERVER['HTTP_IF_NONE_MATCH'] ?? '') === $etag) {
        http_response_code(304);
        exit;
    }

    // Optional: only the sections relevant to one industry.
    $industry = trim($_GET['industry'] ?? '');
    if ($industry !== '') {
        $bundle['suggestedSections'] = SchemaRegistry::sectionsForIndustry($industry);
    }

    sendSuccess('Builder schema', $bundle);
} catch (Exception $e) {
    sendError('Failed to load schema: ' . $e->getMessage(), 500);
}
