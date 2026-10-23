<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Router PHP Development Server
|--------------------------------------------------------------------------
|
| File statis seperti CSS, JavaScript, gambar, dan font akan dilayani
| langsung. URL aplikasi lainnya diteruskan ke public/index.php.
|
*/

$requestPath = parse_url(
    $_SERVER['REQUEST_URI'] ?? '/',
    PHP_URL_PATH
);

$requestPath = is_string($requestPath)
    ? urldecode($requestPath)
    : '/';

$legacyPrefix = '/AplikasiRemmitance/public';
if (str_starts_with($requestPath, $legacyPrefix)) {
    $requestPath = substr($requestPath, strlen($legacyPrefix)) ?: '/';
}

$publicDirectory = __DIR__;

$requestedFile = realpath(
    $publicDirectory
    . DIRECTORY_SEPARATOR
    . ltrim(
        str_replace(
            '/',
            DIRECTORY_SEPARATOR,
            $requestPath
        ),
        DIRECTORY_SEPARATOR
    )
);

/*
|--------------------------------------------------------------------------
| Layani file statis yang benar-benar tersedia
|--------------------------------------------------------------------------
*/

if (
    $requestPath !== '/'
    && $requestedFile !== false
    && is_file($requestedFile)
    && str_starts_with(
        $requestedFile,
        realpath($publicDirectory)
    )
) {
    return false;
}

/*
|--------------------------------------------------------------------------
| Teruskan seluruh route aplikasi ke front controller
|--------------------------------------------------------------------------
*/

require $publicDirectory
    . DIRECTORY_SEPARATOR
    . 'index.php';