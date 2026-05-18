<?php
define('LARAVEL_START', microtime(true));
require __DIR__ . '/../../core/vendor/autoload.php';
$app = require_once __DIR__ . '/../../core/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use Illuminate\Support\Facades\DB;

$database = env('DB_DATABASE');

echo "Checking collation for database: $database\n\n";

$tables = DB::select("SELECT TABLE_NAME, TABLE_COLLATION FROM information_schema.TABLES WHERE TABLE_SCHEMA = ?", [$database]);

foreach ($tables as $table) {
    if ($table->TABLE_COLLATION && !str_contains($table->TABLE_COLLATION, 'utf8')) {
        echo "WARNING: Table '{$table->TABLE_NAME}' has non-UTF8 collation: {$table->TABLE_COLLATION}\n";
    }
    
    $columns = DB::select("SELECT COLUMN_NAME, COLLATION_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?", [$database, $table->TABLE_NAME]);
    
    foreach ($columns as $column) {
        if ($column->COLLATION_NAME && !str_contains($column->COLLATION_NAME, 'utf8')) {
            echo "  WARNING: Column '{$table->TABLE_NAME}.{$column->COLUMN_NAME}' has non-UTF8 collation: {$column->COLLATION_NAME}\n";
        }
    }
}

echo "\nScan complete.\n";
