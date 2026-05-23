<?php
$dirs = [
    __DIR__ . '/core/storage/framework/views',
    __DIR__ . '/core/storage/framework/cache/data',
    __DIR__ . '/core/bootstrap/cache'
];

foreach ($dirs as $dir) {
    if (is_dir($dir)) {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $fileinfo) {
            $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
            if ($fileinfo->getFilename() !== '.gitignore') {
                @$todo($fileinfo->getRealPath());
            }
        }
        echo "Cleared $dir <br>\n";
    }
}
echo "Done!";
