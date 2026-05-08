<?php
require 'core/vendor/autoload.php';
$app = require_once 'core/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Session: " . config('session.driver') . "\n";
echo "Cache: " . config('cache.default') . "\n";
echo "Queue: " . config('queue.default') . "\n";
