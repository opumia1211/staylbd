<?php
$base = 'http://localhost/staylbd/sajaladminopu';
$paths = [
    '/payment-gateways',
    '/finance',
    '/payment/analytics',
    '/gateway/automatic',
    '/gateway/manual',
    '/gateway/autopay',
    '/deposit',
    '/deposit/pending',
    '/shipping-method/cod',
];

foreach ($paths as $path) {
    $ch = curl_init($base . $path);
    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_FOLLOWLOCATION => false, CURLOPT_NOBODY => true, CURLOPT_TIMEOUT => 15]);
    curl_exec($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    echo "$path → $code\n";
}
