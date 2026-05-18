<?php
define('LARAVEL_START', microtime(true));
require __DIR__ . '/../../core/vendor/autoload.php';
$app = require_once __DIR__ . '/../../core/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use App\Models\GeneralSetting;
use Illuminate\Support\Facades\DB;

echo "--- Fixing GeneralSetting ---\n";
$gs = GeneralSetting::first();
if ($gs) {
    // Fix currency symbol (redundant but safe)
    $gs->cur_sym = '৳';
    
    // Fix email template footer
    if (strpos($gs->email_template, '???? 2021') !== false) {
        $gs->email_template = str_replace('???? 2021', '© 2021', $gs->email_template);
        echo "Fixed email_template footer.\n";
    }
    
    $gs->save();
}

echo "\n--- Fixing Notification Templates ---\n";
$templates = DB::table('notification_templates')->where('subj', 'LIKE', '%???%')->get();
foreach ($templates as $t) {
    $newSubj = str_replace('???', '-', $t->subj); // Replace with dash if unsure
    DB::table('notification_templates')->where('id', $t->id)->update(['subj' => $newSubj]);
    echo "Fixed notification template ID {$t->id}: {$newSubj}\n";
}

echo "\n--- Fixing Gateway Currencies ---\n";
DB::table('gateway_currencies')->where('symbol', 'LIKE', '%???%')->update(['symbol' => '৳']);
echo "Updated gateway currencies symbols to ৳ where corrupted.\n";

echo "\n--- Fixing Categories/Brands names (Temporary Cleanup) ---\n";
// This is more complex, but we can at least remove leading/trailing ??? or replace them
// For now, let's just report the count and do a simple replace if it's purely ???
$catCount = DB::table('categories')->where('name_bn', 'LIKE', '%???%')->count();
if ($catCount > 0) {
    echo "Found {$catCount} corrupted category names in Bengali. Replacing with English name as fallback.\n";
    $cats = DB::table('categories')->where('name_bn', 'LIKE', '%???%')->get();
    foreach ($cats as $c) {
        DB::table('categories')->where('id', $c->id)->update(['name_bn' => $c->name]);
    }
}

echo "\n--- Master Fix Completed ---\n";
