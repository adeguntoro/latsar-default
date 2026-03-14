<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
// changed for cpanel shared hosting, the vendor directory is outside the public_html directory
require __DIR__.'/../laraapp/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
// change top level directory from laraapp to laravel for cpanel shared hosting
$app = require_once __DIR__.'/../laraapp/bootstrap/app.php';

// set the public path to this directory
// i have no idea why this is necessary, but without it, the app tries to use the public path as the base path, which causes all kinds of problems with things like config caching and storage paths
$app->bind('path.public', function() {
    return __DIR__;
});

$app->handleRequest(Request::capture());
