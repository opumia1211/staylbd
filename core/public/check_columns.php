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

$results = DB::select("
    SELECT TABLE_NAME, COLUMN_NAME, CHARACTER_SET_NAME, COLLATION_NAME 
    FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = ? 
    AND (TABLE_NAME = 'categories' OR TABLE_NAME = 'products')
    AND DATA_TYPE IN ('varchar', 'text', 'longtext', 'mediumtext')
", [$database]);

foreach ($results as $row) {
    echo "{$row->TABLE_NAME}.{$row->COLUMN_NAME}: {$row->CHARACTER_SET_NAME} / {$row->COLLATION_NAME}\n";
}
