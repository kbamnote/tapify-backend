<?php
/**
 * TAPIFY Social Publishing — module loader (no Composer autoload).
 * Endpoints require config/database.php + functions.php, then this file.
 */
$__s_dir = __DIR__;

require_once $__s_dir . '/SocialException.php';
require_once $__s_dir . '/SocialLogger.php';
require_once $__s_dir . '/SocialHttp.php';
require_once $__s_dir . '/SocialProviderInterface.php';
require_once $__s_dir . '/FacebookProvider.php';
require_once $__s_dir . '/InstagramProvider.php';
require_once $__s_dir . '/SocialProviderFactory.php';
require_once $__s_dir . '/SocialRepo.php';
require_once $__s_dir . '/MediaUploader.php';
require_once $__s_dir . '/SocialService.php';

unset($__s_dir);
