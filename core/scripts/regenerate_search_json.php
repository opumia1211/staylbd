<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

App\Lib\SearchJsonGenerator::generate();
echo "search-horizontal.json & search-vertical.json updated\n";
