<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$tables = DB::select('SHOW TABLES');
$dbName = config('database.connections.mysql.database');
$property = "Tables_in_" . $dbName;

foreach ($tables as $table) {
    $tableName = $table->$property;
    DB::statement("OPTIMIZE TABLE $tableName");
    echo "Optimized: $tableName\n";
}

echo "\nDatabase Optimization Complete.\n";
