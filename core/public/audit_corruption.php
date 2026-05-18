<?php
define('LARAVEL_START', microtime(true));
require __DIR__ . '/../../core/vendor/autoload.php';
$app = require_once __DIR__ . '/../../core/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use App\Models\GeneralSetting;
use App\Models\Frontend;

echo "--- Checking GeneralSetting ---\n";
$gs = GeneralSetting::first();
if ($gs) {
    foreach ($gs->getAttributes() as $key => $value) {
        if (is_string($value) && strpos($value, '???') !== false) {
            echo "Found '???' in GeneralSetting column '$key': '$value'\n";
            // If it's cur_sym, we already fixed it in previous run, but let's be sure
            if ($key == 'cur_sym') {
                $gs->$key = '৳';
                echo "Fixed cur_sym to ৳\n";
            }
        }
    }
    $gs->save();
}

echo "\n--- Checking Frontend Sections ---\n";
$frontends = Frontend::all();
foreach ($frontends as $f) {
    $corrupted = false;
    $data = $f->data_values;
    if ($data) {
        $json = json_encode($data);
        if (strpos($json, '???') !== false) {
            echo "Found '???' in Frontend ID {$f->id} ({$f->data_keys})\n";
            // We can't automatically fix these as we don't know what they should be.
            // But we can report them.
            echo "Value: " . $json . "\n";
        }
    }
}
