<?php
$file = dirname(__DIR__) . '/database/staylbd_wintersm.sql';
if (!file_exists($file)) {
    die("File not found: $file\n");
}

$content = file_get_contents($file);
$pos = strpos($content, 'INSERT INTO `brands`');
if ($pos === false) {
    die("INSERT INTO brands not found in SQL file.\n");
}

echo "Found brand data in SQL file:\n";
echo substr($content, $pos, 2000);
