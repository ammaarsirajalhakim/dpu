<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Filesystem\FilesystemServiceProvider;
use Illuminate\Translation\TranslationServiceProvider;
use Illuminate\View\ViewServiceProvider;

define('LARAVEL_START', microtime(true));

$storagePath = $_ENV['APP_STORAGE_PATH'] ?? $_SERVER['APP_STORAGE_PATH'] ?? '/tmp/storage';
$viewCompiledPath = $_ENV['VIEW_COMPILED_PATH'] ?? $_SERVER['VIEW_COMPILED_PATH'] ?? $storagePath . '/framework/views';
$bootstrapCachePath = $storagePath . '/bootstrap/cache';

foreach ([
    $storagePath,
    $storagePath . '/app',
    $storagePath . '/app/public',
    $storagePath . '/framework',
    $storagePath . '/framework/cache',
    $storagePath . '/framework/cache/data',
    $storagePath . '/framework/sessions',
    $viewCompiledPath,
    $storagePath . '/logs',
    $storagePath . '/bootstrap',
    $bootstrapCachePath,
] as $path) {
    if (! is_dir($path)) {
        mkdir($path, 0777, true);
    }
}

$runtimeEnv = [
    'VIEW_COMPILED_PATH' => $viewCompiledPath,
    'APP_PACKAGES_CACHE' => $bootstrapCachePath . '/packages.php',
    'APP_SERVICES_CACHE' => $bootstrapCachePath . '/services.php',
    'APP_CONFIG_CACHE' => $bootstrapCachePath . '/config.php',
    'APP_ROUTES_CACHE' => $bootstrapCachePath . '/routes.php',
    'APP_EVENTS_CACHE' => $bootstrapCachePath . '/events.php',
];

foreach ($runtimeEnv as $key => $value) {
    $_ENV[$key] = $_SERVER[$key] = $value;
    putenv($key . '=' . $value);
}

if (file_exists($maintenance = __DIR__ . '/../storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__ . '/../vendor/autoload.php';

/** @var Application $app */
$app = require_once __DIR__ . '/../bootstrap/app.php';

$app->useStoragePath($storagePath);

/*
|--------------------------------------------------------------------------
| Vercel Runtime Provider Fallback
|--------------------------------------------------------------------------
|
| Vercel serverless runtime kadang belum memuat binding view sebelum error
| handler mencoba render halaman error. Ini memastikan service view tersedia.
|
*/

$app->register(FilesystemServiceProvider::class);
$app->register(TranslationServiceProvider::class);
$app->register(ViewServiceProvider::class);

$app->handleRequest(Request::capture());