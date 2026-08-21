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
$LATEST_VERSION  = '1.1.11';   // latest version currently live on the Play Store
$MIN_VERSION     = '1.0.0';    // anyone below this is FORCED to update (blocking popup)
$FORCE           = false;      // set true to force ALL older users to update immediately
$MESSAGE         = 'A new version of Tapify is available with new features and improvements. Update now for the best experience.';
$ANDROID_PACKAGE = 'com.kbamnote.tapifapp';

// iOS. The app is not on the App Store yet, so there is nowhere to send an
// iPhone user: leave $IOS_APP_ID empty and the app shows NO update prompt on
// iOS at all. Previously every platform was handed the Play Store link, which
// sent iPhone users to Google Play — broken, and an App Store rejection risk.
// ── ON THE FIRST APP STORE RELEASE: put the numeric Apple ID here (App Store
// Connect → App Information → "Apple ID", digits only) and set $LATEST_VERSION_IOS.
$IOS_APP_ID        = '';        // e.g. '6478912345'
$LATEST_VERSION_IOS = '';       // defaults to $LATEST_VERSION when left empty
// ───────────────────────────────────────────────────────────

$platform = strtolower(trim((string)($_GET['platform'] ?? 'android')));
$iosUrl   = $IOS_APP_ID !== '' ? 'https://apps.apple.com/app/id' . $IOS_APP_ID : '';

// `latest_version` is what the app compares against, so it must describe the
// platform that asked. iOS and Android review times differ, so the two builds
// drift apart and a single shared number would nag one platform to "update" to
// a version that is not published for it yet.
$latest = $LATEST_VERSION;
if ($platform === 'ios' && $LATEST_VERSION_IOS !== '') {
    $latest = $LATEST_VERSION_IOS;
}

echo json_encode([
    'success' => true,
    'data' => [
        'platform'        => $platform,
        'latest_version'  => $latest,
        'min_version'     => $MIN_VERSION,
        'force'           => (bool)$FORCE,
        'message'         => $MESSAGE,
        'android_package' => $ANDROID_PACKAGE,
        'android_url'     => 'https://play.google.com/store/apps/details?id=' . $ANDROID_PACKAGE,
        'ios_app_id'      => $IOS_APP_ID,
        'ios_url'         => $iosUrl,   // empty until the listing exists -> no iOS prompt
    ],
]);
