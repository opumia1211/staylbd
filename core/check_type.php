<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$b = App\Models\Frontend::where('data_keys', 'banner.element')->first();
echo "Type: " . gettype($b->data_values) . "\n";
if (is_array($b->data_values)) {
    echo "Image key: " . ($b->data_values['image'] ?? 'NOT FOUND') . "\n";
} else if (is_object($b->data_values)) {
    echo "Image key: " . ($b->data_values->image ?? 'NOT FOUND') . "\n";
}
