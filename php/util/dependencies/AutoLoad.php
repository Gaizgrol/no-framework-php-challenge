<?php

declare(strict_types=1);

class AutoLoad
{
    private static $loaded = false;

    public static function all(string $baseDir)
    {
        if (self::$loaded) {
            return;
        }

        $root = $baseDir;
        $dir = new RecursiveDirectoryIterator($root);
        $iterator = new RecursiveIteratorIterator($dir);
        foreach ($iterator as $file) {
            $file = $file . '';
            if (!is_dir($file)) {
                require_once $file;
            }
        }
    }
}
