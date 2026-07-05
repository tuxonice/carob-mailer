<?php

declare(strict_types=1);

$projectRoot = __DIR__.'/..';

$assets = [
    'resources/plugins' => 'public/plugins',
    'resources/dist' => 'public/dist',
];

$exitCode = 0;

foreach ($assets as $source => $destination) {
    $sourcePath = $projectRoot.'/'.$source;
    $destinationPath = $projectRoot.'/'.$destination;

    if (! is_dir($sourcePath)) {
        fwrite(STDERR, "Source directory not found: {$source}\n");
        $exitCode = 1;

        continue;
    }

    if (is_dir($destinationPath)) {
        removeDirectory($destinationPath);
    }

    copyDirectory($sourcePath, $destinationPath);
    echo "Copied {$source} -> {$destination}\n";
}

exit($exitCode);

function copyDirectory(string $source, string $destination): void
{
    if (! is_dir($destination)) {
        mkdir($destination, 0755, true);
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($iterator as $item) {
        $target = $destination.'/'.$iterator->getSubPathName();

        if ($item->isDir()) {
            if (! is_dir($target)) {
                mkdir($target, 0755, true);
            }

            continue;
        }

        copy($item->getPathname(), $target);
    }
}

function removeDirectory(string $path): void
{
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );

    foreach ($iterator as $item) {
        if ($item->isDir()) {
            rmdir($item->getPathname());

            continue;
        }

        unlink($item->getPathname());
    }

    rmdir($path);
}
