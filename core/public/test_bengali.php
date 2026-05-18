<?php
define('LARAVEL_START', microtime(true));
require __DIR__ . '/../../core/vendor/autoload.php';
$app = require_once __DIR__ . '/../../core/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use Illuminate\Support\Facades\DB;

$testString = "বাংলা টেস্ট String";
echo "Testing with string: $testString\n";

DB::table('categories')->where('id', 1)->update(['name_bn' => $testString]);

$result = DB::table('categories')->find(1);
echo "Result from DB: " . $result->name_bn . "\n";

if ($result->name_bn === $testString) {
    echo "SUCCESS: Bengali text preserved!\n";
} else {
    echo "FAILURE: Bengali text corrupted to: " . $result->name_bn . "\n";
    echo "Hex result: " . bin2hex($result->name_bn) . "\n";
}
