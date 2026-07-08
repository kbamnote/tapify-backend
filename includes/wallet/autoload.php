<?php
/**
 * TAPIFY Wallet — module loader (no Composer autoload).
 * Endpoints require config/database.php + functions.php, then this file.
 */
$__w_dir = __DIR__;

require_once $__w_dir . '/WalletException.php';
require_once $__w_dir . '/WalletService.php';
require_once $__w_dir . '/PaymentProviderInterface.php';
require_once $__w_dir . '/RazorpayProvider.php';
require_once $__w_dir . '/PaymentFactory.php';
require_once $__w_dir . '/TopupService.php';

unset($__w_dir);
