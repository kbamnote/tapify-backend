<?php
/**
 * TAPIFY Google Business Profile — module loader (no Composer autoload here).
 * Endpoints require config/database.php + functions.php, then this file.
 */
$__g_dir = __DIR__;

require_once $__g_dir . '/GoogleException.php';
require_once $__g_dir . '/GoogleLogger.php';
require_once $__g_dir . '/GoogleHttp.php';
require_once $__g_dir . '/GoogleOAuth.php';
require_once $__g_dir . '/GoogleBusinessRepo.php';
require_once $__g_dir . '/GoogleBusinessClient.php';
require_once $__g_dir . '/FieldMap.php';
require_once $__g_dir . '/GoogleBusinessService.php';

unset($__g_dir);
