<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Simulate serveBannerImage('en', 'banner_6a07a13adecf61778884922.jpg')
$localeOrFilename = 'en';
$filename = 'banner_6a07a13adecf61778884922.jpg';

$resolvedFilename = $filename;
if ($resolvedFilename === null) {
    $resolvedFilename = $localeOrFilename;
}
$resolvedFilename = basename((string) $resolvedFilename);

echo "Resolved Filename: " . $resolvedFilename . "\n";
echo "Regex match: " . (preg_match('/^[a-zA-Z0-9_.-]+$/', $resolvedFilename) ? 'YES' : 'NO') . "\n";

$base = \App\Services\BannerService::UPLOAD_BASE . '/' . \App\Services\BannerService::DESKTOP_DIR;
echo "Base: " . $base . "\n";

$paths = [
    'path 1' => base_path('../' . $base . '/' . $resolvedFilename),
    'path 2' => public_path($base . '/' . $resolvedFilename),
    'path 3' => base_path('../' . \App\Services\BannerService::UPLOAD_BASE . '/' . $resolvedFilename),
];

foreach ($paths as $name => $path) {
    echo $name . ": " . $path . "\n";
    echo "  file_exists: " . (file_exists($path) ? 'TRUE' : 'FALSE') . "\n";
}
