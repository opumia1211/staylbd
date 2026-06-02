<?php

$root = dirname(__DIR__);
require $root . '/vendor/autoload.php';
$app = require_once $root . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tests = [
    [\App\Http\Controllers\Admin\PaymentGatewayHubController::class, 'index'],
    [\App\Http\Controllers\Admin\OrderManagementHubController::class, 'index'],
    [\App\Http\Controllers\Admin\OrderChannelController::class, 'index'],
    [\App\Http\Controllers\Admin\OrderImportExportController::class, 'index'],
    [\App\Http\Controllers\Admin\OrderChannelController::class, 'create'],
    [\App\Http\Controllers\Admin\OrderAutomationController::class, 'index'],
];

foreach ($tests as [$class, $method]) {
    $label = class_basename($class) . '@' . $method;
    try {
        $response = app()->make($class)->{$method}();
        $ok = $response instanceof \Illuminate\View\View || $response instanceof \Illuminate\Http\RedirectResponse;
        echo $ok ? "[OK] {$label}\n" : "[?] {$label} returned " . get_class($response) . "\n";
    } catch (\Throwable $e) {
        echo "[FAIL] {$label}: {$e->getMessage()}\n";
    }
}

echo "Done.\n";
