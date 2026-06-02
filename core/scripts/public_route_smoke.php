<?php
/**
 * Public storefront smoke test (no auth). Usage: C:\xampp\php\php.exe scripts/public_route_smoke.php
 */
$base = 'http://localhost/staylbd';
$paths = [
    '/',
    '/en/all/products',
    '/en/cart',
    '/en/contact',
    '/en/track-order',
    '/en/login',
];

$bad = [];
foreach ($paths as $path) {
    $ch = curl_init($base . $path);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_NOBODY => true,
    ]);
    curl_exec($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    echo sprintf("%s → %d\n", $path, $code);
    if ($code >= 500) {
        $bad[] = ['path' => $path, 'code' => $code];
    }
}

exit($bad ? 1 : 0);
