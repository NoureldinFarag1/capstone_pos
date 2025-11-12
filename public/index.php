<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Temporary: suppress PHP 8.4 deprecation notices from third-party libs (e.g., voku/portable-ascii)
// until upstream packages fully adopt explicit nullable types. This does not hide errors.
if (PHP_VERSION_ID >= 80400) {
    error_reporting(error_reporting() & ~E_DEPRECATED & ~E_USER_DEPRECATED);
}

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
(require_once __DIR__.'/../bootstrap/app.php')
    ->handleRequest(Request::capture());
