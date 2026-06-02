<?php
/**
 * Smoke-test admin GET routes (unauthenticated → expect login redirect, not 500).
 * Usage: C:\xampp\php\php.exe scripts/admin_route_smoke.php
 */

$base = 'http://localhost/staylbd/sajaladminopu';

$paths = [
    '/dashboard',
    '/frontend/ticker',
    '/frontend/scrollbar',
    '/frontend/banner',
    '/frontend/middle-banner',
    '/frontend/bottom-banner',
    '/frontend/footer',
    '/frontend/social_icon',
    '/frontend/homepage-sections',
    '/frontend/quickorder',
    '/product',
    '/order',
    '/payment-gateways',
    '/users',
    '/report/transaction',
];

$bad = [];
foreach ($paths as $path) {
    $url = $base . $path;
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_HEADER => true,
        CURLOPT_NOBODY => true,
    ]);
    curl_exec($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($code >= 500) {
        $bad[] = ['path' => $path, 'code' => $code];
    }
    echo sprintf("%s → %d\n", $path, $code);
}

if ($bad) {
    echo "\nFAILED (5xx):\n";
    foreach ($bad as $b) {
        echo "  {$b['path']} → {$b['code']}\n";
    }
    exit(1);
}

echo "\nOK: no 5xx on sampled admin paths.\n";
exit(0);
