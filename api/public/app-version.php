<?php
/**
 * TAPIFY - Public App Version / Update Check
 * GET /api/public/app-version.php?platform=android
 *
 * The mobile app calls this on launch, compares the installed version against
 * `latest_version`, and shows an "Update Available" popup when a newer build is live.
 *
 * ── ON EACH PLAY STORE RELEASE: bump $LATEST_VERSION below to the new version. ──
 * Keep it equal to the app's app.json "version" that you just published. Users on
 * an older version will then see the update popup; users already on it will not.
 *
 * No auth (public). No DB dependency so it can never error out and block the app.
 */
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') { http_response_code(200); exit; }

// ─────────────── EDIT THESE ON EACH RELEASE ───────────────
$LATEST_VERSION  = '1.1.10';   // latest version currently live on the Play Store
$MIN_VERSION     = '1.0.0';    // anyone below this is FORCED to update (blocking popup)
$FORCE           = false;      // set true to force ALL older users to update immediately
$MESSAGE         = 'A new version of Tapify is available with new features and improvements. Update now for the best experience.';
$ANDROID_PACKAGE = 'com.kbamnote.tapifapp';
// ───────────────────────────────────────────────────────────

echo json_encode([
    'success' => true,
    'data' => [
        'latest_version'  => $LATEST_VERSION,
        'min_version'     => $MIN_VERSION,
        'force'           => (bool)$FORCE,
        'message'         => $MESSAGE,
        'android_package' => $ANDROID_PACKAGE,
        'android_url'     => 'https://play.google.com/store/apps/details?id=' . $ANDROID_PACKAGE,
    ],
]);
