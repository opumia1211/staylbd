<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$file = base_path('resources/views/admin/partials/sidenav.blade.php');
$content = file_get_contents($file);
preg_match_all("/route\\('([^']+)'/", $content, $matches);
$names = array_unique($matches[1]);
sort($names);

$missing = [];
foreach ($names as $name) {
    try {
        route($name);
    } catch (Throwable $e) {
        $missing[] = $name . ' — ' . $e->getMessage();
    }
}

echo 'Routes in sidenav: ' . count($names) . PHP_EOL;
if ($missing) {
    echo "MISSING (" . count($missing) . "):" . PHP_EOL;
    foreach ($missing as $line) {
        echo '  ' . $line . PHP_EOL;
    }
    exit(1);
}
echo "All sidenav routes resolve OK.\n";
exit(0);
