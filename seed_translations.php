<?php
require 'core/vendor/autoload.php';
$app = require_once 'core/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Product;
use App\Models\Category;

$products = Product::all();
foreach($products as $p) {
    $p->name_bn = 'বাংলা ' . $p->name;
    $p->summary_bn = 'এই পণ্যের সংক্ষিপ্ত বিবরণ';
    $p->description_bn = 'বিস্তারিত বাংলা বিবরণ এখানে থাকবে। এটি একটি পরীক্ষামূলক অনুবাদ।';
    $p->name_hi = 'हिंदी ' . $p->name;
    $p->name_ar = 'عربي ' . $p->name;
    $p->save();
}

$categories = Category::all();
foreach($categories as $c) {
    $c->name_bn = 'বাংলা ' . $c->name;
    $c->name_hi = 'हिंदी ' . $c->name;
    $c->name_ar = 'عربي ' . $c->name;
    $c->save();
}

echo "Seeded translations for " . $products->count() . " products and " . $categories->count() . " categories.\n";
