<?php

require __DIR__ . '/core/vendor/autoload.php';
$app = require_once __DIR__ . '/core/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->call('cache:clear');
echo "Cache cleared: " . $kernel->output() . "\n";

$kernel->call('config:clear');
echo "Config cleared: " . $kernel->output() . "\n";

$kernel->call('view:clear');
echo "Views cleared: " . $kernel->output() . "\n";

$kernel->call('route:clear');
echo "Routes cleared: " . $kernel->output() . "\n";

$kernel->call('optimize:clear');
echo "Optimize cleared: " . $kernel->output() . "\n";
