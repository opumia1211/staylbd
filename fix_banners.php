<?php
require 'core/vendor/autoload.php';
$app = require_once 'core/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$banners = \App\Models\Frontend::where('data_keys', 'banner.element')->get();
foreach ($banners as $b) {
    $dv = $b->data_values;
    if (is_object($dv)) {
        $dv->is_active = 1;
        $b->data_values = $dv;
        $b->save();
    } elseif (is_array($dv)) {
        $dv['is_active'] = 1;
        $b->data_values = $dv;
        $b->save();
    }
}
echo "Done";
