<?php
define('LARAVEL_START', microtime(true));
require __DIR__ . '/../../core/vendor/autoload.php';
$app = require_once __DIR__ . '/../../core/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use Illuminate\Support\Facades\DB;

$templates = DB::table('notification_templates')->get();
foreach ($templates as $t) {
    if (str_contains($t->subj, '???') || str_contains($t->email_body, '???') || str_contains($t->sms_body, '???')) {
        echo "Template ID {$t->id} ({$t->name}) has corruption.\n";
    }
}
