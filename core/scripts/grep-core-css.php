<?php
$css = file_get_contents(dirname(__DIR__) . '/../assets/admin-ui/vendor/css/core.css');
foreach (['layout-without-menu', 'layout-navbar-full.layout-horizontal', 'navbar-height'] as $p) {
    $i = stripos($css, $p);
    if ($i !== false) {
        echo "=== $p ===\n" . substr($css, max(0, $i - 50), 350) . "\n\n";
    }
}
