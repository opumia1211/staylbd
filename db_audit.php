<?php
require 'core/vendor/autoload.php';
$app = require_once 'core/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

$tables = ['products', 'reviews', 'orders', 'users'];
$report = [];

foreach ($tables as $table) {
    if (Schema::hasTable($table)) {
        $columns = Schema::getColumnListing($table);
        $report[$table] = [
            'status' => 'OK',
            'columns' => $columns
        ];
        
        // Specific checks
        if ($table === 'reviews' && !in_array('product_id', $columns)) {
            $report[$table]['status'] = 'Error: missing product_id';
        }
        if ($table === 'products' && !in_array('today_deals', $columns)) {
            $report[$table]['status'] = 'Error: missing today_deals';
        }
    } else {
        $report[$table] = ['status' => 'Table NOT FOUND'];
    }
}

// Check Foreign Keys (MySQL specific)
$fks = DB::select("SELECT 
    TABLE_NAME, 
    COLUMN_NAME, 
    CONSTRAINT_NAME, 
    REFERENCED_TABLE_NAME, 
    REFERENCED_COLUMN_NAME
FROM
    INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE
    REFERENCED_TABLE_SCHEMA = '" . env('DB_DATABASE') . "' AND
    TABLE_NAME IN ('" . implode("','", $tables) . "')");

echo json_encode(['tables' => $report, 'fks' => $fks], JSON_PRETTY_PRINT);
