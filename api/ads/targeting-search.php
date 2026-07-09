<?php
/**
 * GET /api/ads/targeting-search.php?type=interest|language&q=fitness
 * Autocomplete for detailed targeting on the boost screen.
 *   - type=interest → [{ id, name, path, audience_size_* , ... }]  (send id back in targeting.interests[])
 *   - type=language → [{ key, name }]                              (send key back in targeting.locales[])
 */
require_once __DIR__ . '/_bootstrap.php';

ads_run(function ($userId, $ads) {
    $q = isset($_GET['q']) ? (string) $_GET['q'] : '';
    $type = isset($_GET['type']) ? (string) $_GET['type'] : 'interest';
    $results = $type === 'language' ? $ads->searchLocales($q) : $ads->searchInterests($q);
    sendSuccess('ok', ['results' => $results]);
});
