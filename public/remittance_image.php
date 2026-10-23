<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Remittance Login Illustration
|--------------------------------------------------------------------------
| File ini membaca gambar langsung dari filesystem.
| Dengan cara ini gambar tidak bergantung pada rewrite asset Apache.
|--------------------------------------------------------------------------
*/

$imagePath =
    __DIR__
    . DIRECTORY_SEPARATOR
    . 'assets'
    . DIRECTORY_SEPARATOR
    . 'img'
    . DIRECTORY_SEPARATOR
    . 'remittance-login-illustration.png';

if (!is_file($imagePath)) {

    http_response_code(404);

    header(
        'Content-Type: text/plain; charset=UTF-8'
    );

    echo "Gambar remittance tidak ditemukan.\n\n";
    echo "PHP mencari file di:\n";
    echo $imagePath;
    echo "\n\n";
    echo "Folder public saat ini:\n";
    echo __DIR__;

    exit;
}

if (!is_readable($imagePath)) {

    http_response_code(403);

    header(
        'Content-Type: text/plain; charset=UTF-8'
    );

    echo 'File ditemukan tetapi tidak dapat dibaca.';

    exit;
}

$fileSize =
    filesize($imagePath);

header(
    'Content-Type: image/png'
);

header(
    'Content-Length: '
    . $fileSize
);

header(
    'Cache-Control: public, max-age=86400'
);

header(
    'X-Content-Type-Options: nosniff'
);

readfile(
    $imagePath
);

exit;