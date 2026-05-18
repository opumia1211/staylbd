<?php
define('LARAVEL_START', microtime(true));
require __DIR__ . '/../../core/vendor/autoload.php';
$app = require_once __DIR__ . '/../../core/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "--- Cleaning ANY question mark sequence from DB ---\n";

$tables = ['products', 'categories', 'subcategories', 'brands'];
$cleaned = 0;

foreach ($tables as $table) {
    if (!Schema::hasTable($table)) continue;
    
    $cols = Schema::getColumnListing($table);
    $targetCols = array_intersect($cols, ['name', 'name_bn', 'description', 'summary', 'key_features']);
    
    foreach ($targetCols as $col) {
        $rows = DB::table($table)->where($col, 'LIKE', '%?%')->get();
        foreach ($rows as $row) {
            $val = $row->$col;
            // Only clean if it has multiple question marks, to not break normal sentences that end with ?
            if (preg_match('/\?\?+/', $val)) {
                $clean = preg_replace('/\s*\?\?+\s*/', '', $val);
                // clean up trailing hyphens
                $clean = trim($clean, ' -');
                if ($clean === '') $clean = 'Item ' . $row->id;
                
                DB::table($table)->where('id', $row->id)->update([$col => $clean]);
                echo "Cleaned $table ID {$row->id} col $col: '$val' -> '$clean'\n";
                $cleaned++;
            }
        }
    }
}

echo "Cleanup complete. Fixed $cleaned fields.\n";
