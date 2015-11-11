<?php namespace Core;

require __DIR__ . '/vendor/autoload.php';

function readEnvelopmentFile() {
    $env = parse_ini_file(__DIR__ . '/.env', true);
    if (false === $env) {
        die("Could not read environment configuration");
    }
    return $env;
}
$env = readEnvelopmentFile();

require __DIR__ . '/registry.php';

/**
 * spl_autoload_register
 */
spl_autoload_register(function($name) {
    $dirs = array_map('strtolower', explode('\\', $name));
    $base = array_shift($dirs);
    if ($base != 'core') {
        return;
    }

    $topName = array_pop($dirs);

    $basePath = __DIR__ . '/' . implode('/', $dirs);
    $filePath = "$basePath/$topName.php";

    if (file_exists($filePath)) {
        require_once $filePath;
        return;
    }

    $modulePath = "$basePath/$topName/$topName.php";
    if (file_exists($modulePath)) {
        require_once $modulePath;
    }
}, false);