<?php
/**
 * TAPIFY - CRM bridge: the list of trackable features, for labels and grouping.
 * GET /api/crm/feature-catalog.php
 * Header: X-CRM-API-Key
 */
require_once __DIR__ . '/_bridge.php';
crm_bridge_auth();

$features = [];
foreach (FeatureCatalog::all() as $key => $f) {
    $features[] = ['key' => $key, 'label' => $f['label'], 'group' => $f['group']];
}
sendSuccess('OK', ['features' => $features]);
