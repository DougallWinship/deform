<?php
// router.php
$request_uri = $_SERVER['REQUEST_URI'];

ini_set('error_log', 'tests/_output/php-error.log');
error_log("INI loaded: " . php_ini_loaded_file());
error_log("Xdebug loaded: " . (extension_loaded('xdebug') ? 'yes' : 'no'));
error_log(   "Xdebug mode: " . ini_get('xdebug.mode'));
error_log(   "Xdebug log: " . ini_get('xdebug.log'));
error_log("Session save path: " . session_save_path());
error_log("PHP binary: " . PHP_BINARY);

include_once __DIR__.DIRECTORY_SEPARATOR.'/index.php';
