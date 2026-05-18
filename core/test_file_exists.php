<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$imageFile = 'banner_6a07a13adecf61778884922.jpg';

$paths = [
    'public_path/desktop' => public_path('assets/images/frontend/banner/desktop/' . $imageFile),
    'public_path/root' => public_path('assets/images/frontend/banner/' . $imageFile),
    'base_path/desktop' => base_path('../assets/images/frontend/banner/desktop/' . $imageFile),
    'base_path/root' => base_path('../assets/images/frontend/banner/' . $imageFile),
    'dirname/desktop' => dirname(base_path()) . '/assets/images/frontend/banner/desktop/' . $imageFile,
];

foreach ($paths as $name => $p) {
    echo $name . ":\n";
    echo "  Path: " . $p . "\n";
    echo "  file_exists: " . (file_exists($p) ? 'TRUE' : 'FALSE') . "\n";
    echo "  is_file: " . (is_file($p) ? 'TRUE' : 'FALSE') . "\n";
    echo "--------------------------------------------------\n";
}
