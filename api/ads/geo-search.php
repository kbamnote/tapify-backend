<?php
/**
 * GET /api/ads/geo-search.php?q=Mumbai
 * Autocomplete for the boost screen's location picker. Returns Meta ad
 * geo-locations (cities/regions/…) matching the query; the app sends the chosen
 * row's `key` back in targeting.cities[].key when launching a boost.
 */
require_once __DIR__ . '/_bootstrap.php';

ads_run(function ($userId, $ads) {
    $q = isset($_GET['q']) ? (string) $_GET['q'] : '';
    sendSuccess('ok', ['results' => $ads->searchGeo($q)]);
});
