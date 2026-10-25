<?php

declare(strict_types=1);

use App\Core\Env;

return [
    'app' => [
        'name' => Env::get('APP_NAME', 'RemitGo!'),
        'env' => Env::get('APP_ENV', 'local'),
        'debug' => Env::get('APP_DEBUG', false),
        'url' => Env::get('APP_URL', 'http://localhost/AplikasiRemmitance/public'),
        'timezone' => Env::get('APP_TIMEZONE', 'Asia/Jakarta'),
        'key' => Env::get('APP_KEY', 'change-me'),
    ],
    'database' => [
        'host' => Env::get('DB_HOST', '127.0.0.1'),
        'port' => Env::get('DB_PORT', '3306'),
        'database' => Env::get('DB_DATABASE', 'AplikasiRemmitance'),
        'username' => Env::get('DB_USERNAME', 'root'),
        'password' => Env::get('DB_PASSWORD', ''),
    ],
    'fx' => [
        'provider' => Env::get('FX_PROVIDER', 'bank-indonesia'),
        'api_key' => Env::get('FX_API_KEY', ''),
        'base' => Env::get('FX_BASE', 'IDR'),
        'quotes' => array_values(array_filter(array_map('trim', explode(',', (string) Env::get('FX_QUOTES', 'USD,EUR,SGD,AUD,GBP,JPY,CNY,HKD,MYR,THB,KRW,CHF,CAD,NZD,SAR,AED'))))),
        'cache_minutes' => (int) Env::get('FX_CACHE_MINUTES', 60),
        'spread_percent' => (float) Env::get('FX_SPREAD_PERCENT', 0.35),
    ],
    'upload' => [
        'max_mb' => (int) Env::get('UPLOAD_MAX_MB', 5),
    ],
];
