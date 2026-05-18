<?php
define('LARAVEL_START', microtime(true));
require __DIR__ . '/../../core/vendor/autoload.php';
$app = require_once __DIR__ . '/../../core/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use Illuminate\Support\Facades\DB;

$carts = DB::table('abandoned_carts')->take(5)->get();
foreach ($carts as $c) {
    echo "Cart ID {$c->id}: {$c->cart_snapshot}\n\n";
}
