<?php

use App\Http\Controllers\SiteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Bootstrap Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Request::capture());

$allowed = ['en', 'bn', 'hi', 'ar', 'ur', 'ru', 'zh', 'es', 'fr', 'de', 'pt', 'ja'];

echo "--- Localization Logic Test ---\n";

foreach ($allowed as $lang) {
    // Simulate request to /change/{lang} from /en/products
    $request = Request::create("/en/change/{$lang}", 'GET');
    $request->setLaravelSession(session());
    
    // Set previous URL
    url()->defaults(['locale' => 'en']);
    session()->put('_previous.url', url('/en/products'));
    
    $controller = app(SiteController::class);
    $response = $controller->changeLanguage($request, $lang);
    
    $target = $response->getTargetUrl();
    $expected = url("/{$lang}/products");
    
    // Since url() might be weird in CLI, let's just check the path
    $targetPath = parse_url($target, PHP_URL_PATH);
    $expectedPath = "/{$lang}/products";
    
    if (str_contains($targetPath, $expectedPath)) {
        echo "[OK] {$lang} -> {$targetPath}\n";
    } else {
        echo "[FAIL] {$lang} -> Got: {$targetPath}, Expected: {$expectedPath}\n";
    }
}

echo "--- End Test ---\n";
