<?php
define('LARAVEL_START', microtime(true));
require __DIR__ . '/../../core/vendor/autoload.php';
$app = require_once __DIR__ . '/../../core/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use Illuminate\Support\Facades\DB;

echo "Checking for valid Bengali text...\n";

$categories = DB::table('categories')->get();
foreach ($categories as $c) {
    if (preg_match('/[\x{0980}-\x{09FF}]/u', $c->name_bn)) {
        echo "Category ID {$c->id} has valid Bengali: {$c->name_bn}\n";
    }
}

$products = DB::table('products')->get();
foreach ($products as $p) {
    if (preg_match('/[\x{0980}-\x{09FF}]/u', $p->name)) {
        echo "Product ID {$p->id} Name has valid Bengali: {$p->name}\n";
    }
    if (preg_match('/[\x{0980}-\x{09FF}]/u', $p->name_bn)) {
        echo "Product ID {$p->id} Name_BN has valid Bengali: {$p->name_bn}\n";
    }
}

echo "Scan complete.\n";
