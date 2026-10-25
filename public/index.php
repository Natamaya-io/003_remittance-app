<?php

declare(strict_types=1);

use App\Core\Router;

try {
    /*
    |--------------------------------------------------------------------------
    | Bootstrap aplikasi
    |--------------------------------------------------------------------------
    */

    require_once dirname(__DIR__)
        . '/app/bootstrap.php';

    /*
    |--------------------------------------------------------------------------
    | Kompatibilitas Nama Klien sebagai Nama Tim
    |--------------------------------------------------------------------------
    |
    | Beberapa versi ReportController masih membaca:
    |
    | $_POST['team_name']
    |
    | Sementara form terbaru hanya meminta pengguna mengisi:
    |
    | $_POST['client_name']
    |
    | Normalisasi dilakukan sebelum Router memanggil controller sehingga:
    |
    | client_name = PARK
    | team_name   = PARK
    |
    | Excel tidak wajib mempunyai kolom TEAM.
    |
    */

    $requestMethod = strtoupper(
        (string) (
            $_SERVER['REQUEST_METHOD']
            ?? 'GET'
        )
    );

    $requestUri = (string) (
        $_SERVER['REQUEST_URI']
        ?? '/'
    );

    $requestPath = parse_url(
        $requestUri,
        PHP_URL_PATH
    );

    if (!is_string($requestPath)) {
        $requestPath = '/';
    }

    $normalizedPath = rtrim(
        $requestPath,
        '/'
    );

    $isReportStoreRequest =
        $requestMethod === 'POST'
        && str_ends_with(
            $normalizedPath,
            '/reports'
        );

    if ($isReportStoreRequest) {
        $clientName = trim(
            (string) (
                $_POST['client_name']
                ?? ''
            )
        );

        $teamName = trim(
            (string) (
                $_POST['team_name']
                ?? ''
            )
        );

        /*
         * Nama klien menjadi nama tim.
         */
        if (
            $clientName !== ''
            && $teamName === ''
        ) {
            $_POST['team_name'] =
                $clientName;

            $teamName = $clientName;
        }

        /*
         * Kompatibilitas apabila form versi lama hanya
         * mengirim team_name.
         */
        if (
            $teamName !== ''
            && $clientName === ''
        ) {
            $_POST['client_name'] =
                $teamName;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Rapikan flash message dari source lama
    |--------------------------------------------------------------------------
    |
    | Pesan lama dapat masih tersimpan setelah redirect.
    | Teksnya diperbarui agar sesuai dengan alur terbaru.
    |
    */

    if (
        isset($_SESSION['_flash'])
        && is_array(
            $_SESSION['_flash']
        )
    ) {
        foreach (
            $_SESSION['_flash']
            as $index => $flash
        ) {
            if (!is_array($flash)) {
                continue;
            }

            $message = trim(
                (string) (
                    $flash['message']
                    ?? ''
                )
            );

            if (
                $message
                === 'Nama tim wajib diisi manual.'
            ) {
                $_SESSION['_flash'][$index]['message'] =
                    'Nama klien wajib diisi. '
                    . 'Nama klien otomatis digunakan '
                    . 'sebagai nama tim report.';
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Muat Router
    |--------------------------------------------------------------------------
    */

    $router = require dirname(__DIR__)
        . '/app/routes.php';

    /*
    |--------------------------------------------------------------------------
    | Validasi Router
    |--------------------------------------------------------------------------
    */

    if (!$router instanceof Router) {
        throw new RuntimeException(
            'app/routes.php harus mengembalikan '
            . 'objek App\Core\Router. '
            . 'Pastikan baris terakhir file tersebut '
            . 'adalah: return $router;'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Jalankan Router
    |--------------------------------------------------------------------------
    */

    $router->dispatch();
} catch (Throwable $exception) {
    /*
    |--------------------------------------------------------------------------
    | Simpan error ke log
    |--------------------------------------------------------------------------
    */

    $basePath = dirname(__DIR__);
    $logDirectory =
        $basePath
        . '/storage/logs';

    if (!is_dir($logDirectory)) {
        @mkdir(
            $logDirectory,
            0775,
            true
        );
    }

    $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'CLI';
    $requestUri = $_SERVER['REQUEST_URI'] ?? '-';
    $logMessage =
        '['
        . date('Y-m-d H:i:s')
        . '] '
        . $requestMethod . ' ' . $requestUri . PHP_EOL
        . get_class($exception)
        . ': '
        . $exception->getMessage()
        . ' in '
        . $exception->getFile()
        . ':'
        . $exception->getLine()
        . PHP_EOL
        . $exception->getTraceAsString()
        . PHP_EOL
        . str_repeat('-', 80)
        . PHP_EOL;

    @file_put_contents(
        $logDirectory
        . '/app.log',
        $logMessage,
        FILE_APPEND
    );

    http_response_code(500);

    /*
    |--------------------------------------------------------------------------
    | Periksa mode debug
    |--------------------------------------------------------------------------
    */

    $debug = false;

    try {
        $debug =
            class_exists(
                'App\Core\App'
            )
            && (bool) App\Core\App::config(
                'app.debug',
                false
            );
    } catch (Throwable) {
        $debug = false;
    }

    header(
        'Content-Type: text/html; charset=utf-8'
    );

    echo '<!doctype html>';
    echo '<html lang="id">';
    echo '<head>';
    echo '<meta charset="utf-8">';
    echo '<meta name="viewport" '
        . 'content="width=device-width,initial-scale=1">';
    echo '<title>Kesalahan Sistem</title>';

    echo '<style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 40px 20px;
            font-family:
                Arial,
                Helvetica,
                sans-serif;
            background:
                linear-gradient(
                    135deg,
                    #f4f7fb,
                    #edf4fc
                );
            color: #132238;
        }

        .error-box {
            width: min(900px, 100%);
            margin: 0 auto;
            padding: 30px;
            border: 1px solid #dce6f1;
            border-radius: 20px;
            background: #ffffff;
            box-shadow:
                0 18px 45px
                rgba(25, 45, 75, .12);
        }

        h1 {
            margin-top: 0;
            color: #10213b;
        }

        code,
        pre {
            display: block;
            overflow: auto;
            padding: 12px;
            border-radius: 10px;
            background: #f1f5f9;
            white-space: pre-wrap;
        }

        .muted {
            color: #847488;
        }
    </style>';

    echo '<link rel="stylesheet" href="' . e(asset('css/theme-clean-global.css')) . '">
</head>';
    echo '<body>';
    echo '<div class="error-box">';

    echo '<h1>Terjadi kesalahan pada aplikasi</h1>';

    echo '<p>';
    echo 'Detail teknis sudah disimpan pada';
    echo '<code>storage/logs/app.log</code>';
    echo '.</p>';

    if ($debug) {
        echo '<h3>Detail debug</h3>';

        echo '<pre>';
        echo htmlspecialchars(
            $exception->getMessage()
            . PHP_EOL
            . $exception->getFile()
            . ':'
            . $exception->getLine(),
            ENT_QUOTES,
            'UTF-8'
        );
        echo '</pre>';
    } else {
        echo '<p class="muted">';
        echo 'Aktifkan ';
        echo '<code>APP_DEBUG=true</code>';
        echo ' pada file ';
        echo '<code>.env</code>';
        echo ' untuk melihat detail ketika ';
        echo 'pengembangan lokal.';
        echo '</p>';
    }

    echo '</div>';
    echo '</body>';
    echo '</html>';
}