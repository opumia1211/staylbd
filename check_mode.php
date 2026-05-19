<?php
require 'core/vendor/autoload.php';
$app = require_once 'core/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

foreach (\App\Models\Frontend::where('data_keys', 'like', '%scrollbar%')->get() as $row) {
    $vals = (array) $row->data_values;
    echo "ID: {$row->id} | Position: " . ($vals['position'] ?? 'N/A') . " | Container Mode: " . ($vals['container_mode'] ?? 'N/A') . "\n";
}
