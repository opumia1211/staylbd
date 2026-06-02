<?php

/**
 * Smoke test: Payment & Order hub routes (CLI).
 * Usage: php scripts/admin_payment_orders_smoke.php
 */

$root = dirname(__DIR__);
require $root . '/vendor/autoload.php';
$app = require_once $root . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$routes = [
    'admin.payment.gateways.hub',
    'admin.payment.analytics',
    'admin.orders.hub',
    'admin.orders.fulfillment',
    'admin.orders.automation.index',
    'admin.orders.channels.index',
    'admin.orders.channels.create',
    'admin.orders.import-export',
    'admin.orders.export',
    'api.order-channel.webhook',
];

$ok = 0;
$fail = 0;
foreach ($routes as $name) {
    try {
        $url = route($name, $name === 'api.order-channel.webhook' ? ['token' => 'test'] : []);
        echo "[OK] {$name} => {$url}\n";
        $ok++;
    } catch (\Throwable $e) {
        echo "[FAIL] {$name}: {$e->getMessage()}\n";
        $fail++;
    }
}

echo "\nRoutes: {$ok} ok, {$fail} failed\n";
echo Schema::hasTable('order_channels') ? "order_channels table: yes\n" : "order_channels table: no\n";
exit($fail > 0 ? 1 : 0);
