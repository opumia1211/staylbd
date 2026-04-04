<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Check If The Application Is Under Maintenance
|--------------------------------------------------------------------------
|
| If the application is in maintenance / demo mode via the "down" command
| we will load this file so that any pre-rendered content can be shown
| instead of starting the framework, which could cause an exception.
|
*/

if (file_exists($maintenance = __DIR__ . '/core/storage/framework/maintenance.php')) {
    require $maintenance;
}

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| this application. We just need to utilize it! We'll simply require it
| into the script here so we don't need to manually load our classes.
|
*/

require __DIR__ . '/core/vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request using
| the application's HTTP kernel. Then, we will send the response back
| to this client's browser, allowing them to enjoy our application.
|
*/

$app = require_once __DIR__ . '/core/bootstrap/app.php';

/*
|--------------------------------------------------------------------------
| Subdirectory front controller (e.g. http://localhost/staylbd/)
|--------------------------------------------------------------------------
|
| Laravel's CompiledRouteCollection strips a trailing slash from REQUEST_URI.
| For /{subdir}/ that becomes /{subdir} with no "index.php" segment, Symfony
| then fails to derive baseUrl and pathInfo becomes /{subdir} instead of /,
| which breaks the home route (405: GET not supported, HEAD only).
|
| Point subdirectory-root requests at index.php in REQUEST_URI so routing
| matches the same as when the URI already includes index.php.
|
*/
if (isset($_SERVER['SCRIPT_NAME'], $_SERVER['REQUEST_URI'])) {
    $scriptName = str_replace('\\', '/', (string) $_SERVER['SCRIPT_NAME']);
    if (str_ends_with($scriptName, '/index.php')) {
        $dir = dirname($scriptName);
        if ($dir !== '/' && $dir !== '.' && $dir !== '') {
            $path = (string) parse_url((string) $_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $query = parse_url((string) $_SERVER['REQUEST_URI'], PHP_URL_QUERY);
            if ($path === $dir || $path === $dir . '/') {
                $_SERVER['REQUEST_URI'] = $scriptName;
                if (is_string($query) && $query !== '') {
                    $_SERVER['REQUEST_URI'] .= '?' . $query;
                }
            }
        }
    }
}

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
