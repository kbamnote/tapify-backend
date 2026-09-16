<?php
/**
 * TAPIFY - CRM bridge: a customer's activity, newest first.
 * GET /api/crm/client-timeline.php?user_id=123&before_id=0&limit=50[&feature=designs]
 * Header: X-CRM-API-Key
 *
 * Page with `nextBeforeId` from the previous response. Covers the last 12
 * months — older events are pruned; the per-feature totals keep the full history.
 */
require_once __DIR__ . '/_bridge.php';
crm_bridge_auth();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendError('Only GET allowed', 405);
}
$userId = (int)($_GET['user_id'] ?? 0);
if ($userId <= 0) {
    sendError('user_id is required', 400);
}
$beforeId = max(0, (int)($_GET['before_id'] ?? 0));
$limit = min(200, max(1, (int)($_GET['limit'] ?? 50)));
$feature = (string)($_GET['feature'] ?? '');

try {
    $pdo = crm_pdo();

    $sql = 'SELECT id, feature, action, kind, platform, app_version, detail, created_at
              FROM user_activity_events WHERE user_id = ?';
    $params = [$userId];
    if ($beforeId > 0) {
        $sql .= ' AND id < ?';
        $params[] = $beforeId;
    }
    if ($feature !== '' && FeatureCatalog::exists($feature)) {
        $sql .= ' AND feature = ?';
        $params[] = $feature;
    }
    $sql .= " ORDER BY id DESC LIMIT $limit";

    $st = $pdo->prepare($sql);
    $st->execute($params);
    $catalog = FeatureCatalog::all();

    $events = [];
    foreach ($st as $r) {
        $events[] = [
            'id' => (int)$r['id'],
            'feature' => $r['feature'],
            'featureLabel' => $catalog[$r['feature']]['label'] ?? $r['feature'],
            'action' => $r['action'],
            'kind' => $r['kind'],
            'platform' => $r['platform'],
            'appVersion' => $r['app_version'],
            'detail' => $r['detail'],
            'at' => crm_iso($r['created_at']),
        ];
    }

    sendSuccess('OK', [
        'events' => $events,
        'nextBeforeId' => count($events) === $limit ? end($events)['id'] : null,
    ]);
} catch (Throwable $e) {
    // Before the activity migration there is simply no history yet.
    error_log('crm/client-timeline: ' . $e->getMessage());
    sendSuccess('OK', ['events' => [], 'nextBeforeId' => null]);
}
