<?php
define('LARAVEL_START', microtime(true));
require __DIR__ . '/../../core/vendor/autoload.php';
$app = require_once __DIR__ . '/../../core/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use Illuminate\Support\Facades\DB;

$backupFile = dirname(__DIR__) . '/database/staylbd_wintersm.sql';
if (!file_exists($backupFile)) {
    die("Backup file not found.\n");
}

echo "Reading backup file...\n";
$content = file_get_contents($backupFile);

// Extract INSERT INTO products
$pos = strpos($content, 'INSERT INTO `products`');
if ($pos === false) {
    die("Product data not found in backup.\n");
}

$endPos = strpos($content, ';', $pos);
$insertStatement = substr($content, $pos, $endPos - $pos);

// Extract values
// Pattern: (id, category_id, ..., 'name', 'slug', ...)
// This is hard to parse with regex due to escaped quotes.
// I'll use a simpler approach: explode by "),("
$valuesPart = substr($insertStatement, strpos($insertStatement, 'VALUES') + 6);
$valuesPart = trim($valuesPart, " \n\r\t()");
$rows = explode("),\n(", $valuesPart);

echo "Found " . count($rows) . " products in backup.\n";

$restoredCount = 0;
foreach ($rows as $row) {
    // Row looks like: 1, 1, 1, 1, 'NAME', 'SLUG', ...
    // I need the first element (ID) and the 5th element (Name)
    // Using a more reliable parser for CSV-like SQL values
    $items = str_getcsv($row, ",", "'", "\\");
    
    $id = trim($items[0]);
    $name = $items[4]; // Based on CREATE TABLE structure: id, cat, sub, brand, name
    
    if (empty($name) || str_contains($name, '???')) {
        continue;
    }

    // Check if name has escaped unicode like \u09b8
    $name = preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/', function ($match) {
        return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE');
    }, $name);

    echo "Updating Product #{$id}: " . mb_substr($name, 0, 50) . "...\n";
    
    DB::table('products')->where('id', $id)->update(['name' => $name]);
    $restoredCount++;
}

echo "\nRestoration complete. Total products updated: {$restoredCount}\n";

// --- Restoring Subcategories ---
echo "\n--- Restoring Subcategories ---\n";
$pos = strpos($content, 'INSERT INTO `subcategories`');
if ($pos !== false) {
    $endPos = strpos($content, ';', $pos);
    $insertStatement = substr($content, $pos, $endPos - $pos);
    $valuesPart = substr($insertStatement, strpos($insertStatement, 'VALUES') + 6);
    $valuesPart = trim($valuesPart, " \n\r\t()");
    $rows = explode("),\n(", $valuesPart);
    
    foreach ($rows as $row) {
        $items = str_getcsv($row, ",", "'", "\\");
        $id = trim($items[0]);
        $name = $items[2]; // Based on CREATE TABLE: id, category_id, name
        
        $name = preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/', function ($match) {
            return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE');
        }, $name);
        
        echo "Updating Subcategory #{$id}: {$name}\n";
        DB::table('subcategories')->where('id', $id)->update(['name' => $name]);
    }
}

// --- Restoring Brands ---
echo "\n--- Restoring Brands ---\n";
$pos = strpos($content, 'INSERT INTO `brands`');
if ($pos !== false) {
    $endPos = strpos($content, ';', $pos);
    $insertStatement = substr($content, $pos, $endPos - $pos);
    $valuesPart = substr($insertStatement, strpos($insertStatement, 'VALUES') + 6);
    $valuesPart = trim($valuesPart, " \n\r\t()");
    $rows = explode("),\n(", $valuesPart);
    
    foreach ($rows as $row) {
        $items = str_getcsv($row, ",", "'", "\\");
        $id = trim($items[0]);
        $name = $items[1]; // Based on CREATE TABLE: id, name, ...
        
        $name = preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/', function ($match) {
            return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE');
        }, $name);
        
        echo "Updating Brand #{$id}: {$name}\n";
        DB::table('brands')->where('id', $id)->update(['name' => $name]);
    }
}

// Now for Categories
echo "\n--- Restoring Categories ---\n";
$pos = strpos($content, 'INSERT INTO `categories`');
if ($pos !== false) {
    $endPos = strpos($content, ';', $pos);
    $insertStatement = substr($content, $pos, $endPos - $pos);
    $valuesPart = substr($insertStatement, strpos($insertStatement, 'VALUES') + 6);
    $valuesPart = trim($valuesPart, " \n\r\t()");
    $rows = explode("),\n(", $valuesPart);
    
    foreach ($rows as $row) {
        $items = str_getcsv($row, ",", "'", "\\");
        $id = trim($items[0]);
        $name = $items[1];
        
        $name = preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/', function ($match) {
            return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE');
        }, $name);
        
        echo "Updating Category #{$id}: {$name}\n";
        DB::table('categories')->where('id', $id)->update(['name' => $name]);
    }
}
