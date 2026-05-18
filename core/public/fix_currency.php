<?php
define('LARAVEL_START', microtime(true));
require __DIR__ . '/../../core/vendor/autoload.php';
$app = require_once __DIR__ . '/../../core/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use App\Models\GeneralSetting;
$gs = GeneralSetting::first();
if ($gs) {
    echo "Current Symbol: " . $gs->cur_sym . "\n";
    $gs->cur_sym = '৳';
    $gs->save();
    echo "Updated Symbol to: " . $gs->cur_sym . "\n";
} else {
    echo "GeneralSetting not found.\n";
}
