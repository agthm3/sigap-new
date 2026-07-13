<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../sigap_new/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../sigap_new/vendor/autoload.php';

// Bootstrap Laravel...
$app = require_once __DIR__.'/../sigap_new/bootstrap/app.php';

// ----------------------------------------------------------------------
// TAMBAHAN UNTUK SHARED HOSTING:
// Beritahu Laravel bahwa folder public sekarang ada di public_html
// ----------------------------------------------------------------------
$app->usePublicPath(__DIR__);

// Handle the request...
$app->handleRequest(Request::capture());