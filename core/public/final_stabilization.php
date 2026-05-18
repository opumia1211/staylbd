<?php
define('LARAVEL_START', microtime(true));
require __DIR__ . '/../../core/vendor/autoload.php';
$app = require_once __DIR__ . '/../../core/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use Illuminate\Support\Facades\DB;

echo "--- Final Cleanup & Stabilization ---\n";

// 1. Fix General Settings Currency Symbol
DB::table('general_settings')->update(['cur_sym' => '৳']);
echo "Fixed currency symbol in general_settings.\n";

// 2. Clean up '???' from products
$products = DB::table('products')->where('name', 'LIKE', '%???%')->orWhere('name_bn', 'LIKE', '%???%')->get();
foreach ($products as $p) {
    $cleanName = str_replace('???', '', $p->name);
    $cleanNameBn = str_replace('???', '', $p->name_bn);
    
    // If name becomes empty, fallback to ID or something
    if (trim($cleanName) === '') $cleanName = "Product #" . $p->id;
    if (trim($cleanNameBn) === '') $cleanNameBn = $cleanName;
    
    DB::table('products')->where('id', $p->id)->update([
        'name' => trim($cleanName),
        'name_bn' => trim($cleanNameBn)
    ]);
}
echo "Cleaned up " . count($products) . " products with ??? artifacts.\n";

// 3. Clean up '???' from categories
$categories = DB::table('categories')->where('name', 'LIKE', '%???%')->orWhere('name_bn', 'LIKE', '%???%')->get();
foreach ($categories as $c) {
    $cleanName = str_replace('???', '', $c->name);
    $cleanNameBn = str_replace('???', '', $c->name_bn);
    if (trim($cleanName) === '') $cleanName = "Category #" . $c->id;
    if (trim($cleanNameBn) === '') $cleanNameBn = $cleanName;
    DB::table('categories')->where('id', $c->id)->update(['name' => trim($cleanName), 'name_bn' => trim($cleanNameBn)]);
}
echo "Cleaned up " . count($categories) . " categories with ??? artifacts.\n";

// 4. Clean up '???' from subcategories
$subs = DB::table('subcategories')->where('name', 'LIKE', '%???%')->get();
foreach ($subs as $s) {
    $cleanName = str_replace('???', '', $s->name);
    if (trim($cleanName) === '') $cleanName = "Subcategory #" . $s->id;
    DB::table('subcategories')->where('id', $s->id)->update(['name' => trim($cleanName)]);
}
echo "Cleaned up " . count($subs) . " subcategories with ??? artifacts.\n";

// 5. Clean up '???' from brands
$brands = DB::table('brands')->where('name', 'LIKE', '%???%')->get();
foreach ($brands as $b) {
    $cleanName = str_replace('???', '', $b->name);
    if (trim($cleanName) === '') $cleanName = "Brand #" . $b->id;
    DB::table('brands')->where('id', $b->id)->update(['name' => trim($cleanName)]);
}
echo "Cleaned up " . count($brands) . " brands with ??? artifacts.\n";

echo "Stabilization complete.\n";
