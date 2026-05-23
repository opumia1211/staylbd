<?php

require __DIR__ . '/core/vendor/autoload.php';
$app = require_once __DIR__ . '/core/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

use App\Models\Product;

$products = Product::with(['category', 'brand', 'subcategory'])->get();

foreach ($products as $p) {
    echo "ID: " . $p->id . "\n";
    echo "Name: " . $p->name . "\n";
    echo "Status (Active): " . $p->status . "\n";
    echo "Category: " . ($p->category ? $p->category->name . " (Active: " . $p->category->status . ")" : "NULL") . "\n";
    echo "Brand: " . ($p->brand ? $p->brand->name . " (Active: " . $p->brand->status . ")" : "NULL") . "\n";
    echo "Subcategory: " . ($p->subcategory ? $p->subcategory->name . " (Active: " . $p->subcategory->status . ")" : "NULL") . "\n";
    
    // Check if available scope works
    $isAvailable = Product::available()->where('id', $p->id)->exists();
    echo "Is Available Scope OK: " . ($isAvailable ? "YES" : "NO") . "\n";
    echo "---------------------------------\n";
}
