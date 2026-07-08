<?php
/**
 * TAPIFY Meta Ads — module loader. Depends on the wallet module, so endpoints
 * require wallet/autoload.php BEFORE this file.
 */
$__ads_dir = __DIR__;

require_once $__ads_dir . '/AdsException.php';
require_once $__ads_dir . '/MetaAdsClient.php';
require_once $__ads_dir . '/AdsService.php';

unset($__ads_dir);
