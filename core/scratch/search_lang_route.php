<?php
// Scratch script to search for route('lang') and other occurrences of lang route in views/controllers.

$dir = realpath(__DIR__ . '/../');
echo "Searching in: $dir\n";

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
$matches = [];

foreach ($iterator as $file) {
    if ($file->isDir()) continue;
    $filePath = $file->getPathname();
    
    // Ignore vendor, storage, bootstrap, node_modules
    if (str_contains($filePath, 'vendor') || 
        str_contains($filePath, 'storage') || 
        str_contains($filePath, 'bootstrap') || 
        str_contains($filePath, 'node_modules') || 
        str_contains($filePath, '.git')) {
        continue;
    }
    
    $ext = pathinfo($filePath, PATHINFO_EXTENSION);
    if ($ext !== 'php') continue;
    
    $content = file_get_contents($filePath);
    
    // Look for route('lang' or route("lang" or 'lang' in routes or controllers
    if (preg_match('/route\(\s*[\'"]lang[\'"]/', $content) || preg_match('/to_route\(\s*[\'"]lang[\'"]/', $content)) {
        $lines = explode("\n", $content);
        foreach ($lines as $idx => $line) {
            if (str_contains($line, 'lang')) {
                $matches[] = [
                    'file' => str_replace($dir, '', $filePath),
                    'line' => $idx + 1,
                    'content' => trim($line)
                ];
            }
        }
    }
}

echo "Matches found: " . count($matches) . "\n";
foreach ($matches as $m) {
    echo "File: {$m['file']}:{$m['line']}\n  Code: {$m['content']}\n\n";
}
