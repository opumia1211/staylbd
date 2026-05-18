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

$tables = DB::select('SHOW TABLES');
$dbName = env('DB_DATABASE');
$tableKey = "Tables_in_" . $dbName;

foreach ($tables as $table) {
    $tableName = $table->$tableKey;
    $columns = Schema::getColumnListing($tableName);
    
    foreach ($columns as $column) {
        try {
            $results = DB::table($tableName)
                ->where($column, 'LIKE', '%???%')
                ->get();
                
            if ($results->count() > 0) {
                echo "Found '???' in table '$tableName', column '$column':\n";
                foreach ($results as $row) {
                    echo "  - Row ID: " . ($row->id ?? 'N/A') . ", Value: " . substr((string)$row->$column, 0, 100) . "...\n";
                }
            }
        } catch (\Exception $e) {
            // Probably not a string column or other issue, skip
        }
    }
}
