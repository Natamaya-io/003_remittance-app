<?php

declare(strict_types=1);

use App\Core\App;
use App\Core\Database;
use App\Core\Env;

const BASE_PATH = __DIR__ . '/..';

require_once __DIR__ . '/Core/Env.php';
Env::load(BASE_PATH . '/.env');

spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    if (!str_starts_with($class, $prefix)) return;
    $relative = substr($class, strlen($prefix));
    $file = __DIR__ . '/' . str_replace('\\', '/', $relative) . '.php';
    if (is_file($file)) require_once $file;
});

require_once __DIR__ . '/helpers.php';

$config = require __DIR__ . '/config.php';
App::set('config', $config);
date_default_timezone_set($config['app']['timezone']);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_name('aplikasiRemmitance_session');
    session_start([
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax',
        'use_strict_mode' => true,
    ]);
}

if ($config['app']['debug']) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', '0');
}

App::set('db', Database::connect($config['database']));
