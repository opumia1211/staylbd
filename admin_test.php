<?php
require 'core/vendor/autoload.php';
$app = require_once 'core/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$admin = \App\Models\Admin::first();
if ($admin) {
    echo "Admin username: " . $admin->username . "\n";
    $admin->password = \Illuminate\Support\Facades\Hash::make('admin123');
    $admin->save();
    echo "Password reset to: admin123\n";
} else {
    echo "No admin found.\n";
}

$f = fopen('core/storage/logs/laravel.log', 'r');
if ($f) {
    $lines = [];
    while (($line = fgets($f)) !== false) {
        if (strpos($line, '.ERROR:') !== false) {
            $lines[] = trim($line);
        }
    }
    fclose($f);
    echo "\nLast 3 errors:\n";
    foreach (array_slice($lines, -3) as $err) {
        echo $err . "\n";
    }
}
