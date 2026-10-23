<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| FX BOARD ALIAS V13
|--------------------------------------------------------------------------
*/

$targetFile = __DIR__
    . '/fx-idr-board.php';

if (!is_file($targetFile)) {
    throw new RuntimeException(
        'File fx-idr-board.php tidak ditemukan.'
    );
}

require $targetFile;