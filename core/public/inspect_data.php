<?php
define('LARAVEL_START', microtime(true));
require __DIR__ . '/../../core/vendor/autoload.php';
$app = require_once __DIR__ . '/../../core/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use Illuminate\Support\Facades\DB;

$category = DB::table('categories')->find(1);
echo "Category ID 1:\n";
print_r($category);

$product = DB::table('products')->find(4);
echo "\nProduct ID 4:\n";
print_r($product);
