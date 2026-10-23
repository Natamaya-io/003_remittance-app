<?php

declare(strict_types=1);

use App\Controllers\ReportController;
use App\Services\ReportImportService;

require_once dirname(__DIR__)
    . '/app/bootstrap.php';

/*
|--------------------------------------------------------------------------
| Reset OPcache
|--------------------------------------------------------------------------
*/

$opcacheReset = false;

if (function_exists('opcache_reset')) {
    $opcacheReset = opcache_reset();
}

/*
|--------------------------------------------------------------------------
| Bersihkan pesan dan input lama dari session
|--------------------------------------------------------------------------
*/

$oldFlash = $_SESSION['_flash'] ?? [];
$oldInput = $_SESSION['_old'] ?? [];

unset(
    $_SESSION['_flash'],
    $_SESSION['_old']
);

if (session_status() === PHP_SESSION_ACTIVE) {
    session_regenerate_id(true);
}

/*
|--------------------------------------------------------------------------
| Pemeriksaan class yang benar-benar dimuat Apache
|--------------------------------------------------------------------------
*/

$controllerReflection = new ReflectionClass(
    ReportController::class
);

$serviceReflection = new ReflectionClass(
    ReportImportService::class
);

$controllerFile = (string) (
    $controllerReflection->getFileName()
    ?: ''
);

$serviceFile = (string) (
    $serviceReflection->getFileName()
    ?: ''
);

$controllerSource = $controllerFile !== ''
    && is_file($controllerFile)
        ? (string) file_get_contents(
            $controllerFile
        )
        : '';

$serviceSource = $serviceFile !== ''
    && is_file($serviceFile)
        ? (string) file_get_contents(
            $serviceFile
        )
        : '';

$oldMessage =
    'Nama tim wajib diisi manual.';

$result = [
    'status' => 'Runtime berhasil diperiksa',

    'project_root' =>
        dirname(__DIR__),

    'controller_file' =>
        $controllerFile,

    'controller_modified_at' =>
        $controllerFile !== ''
        && is_file($controllerFile)
            ? date(
                'Y-m-d H:i:s',
                (int) filemtime(
                    $controllerFile
                )
            )
            : null,

    'service_file' =>
        $serviceFile,

    'service_modified_at' =>
        $serviceFile !== ''
        && is_file($serviceFile)
            ? date(
                'Y-m-d H:i:s',
                (int) filemtime(
                    $serviceFile
                )
            )
            : null,

    'controller_contains_old_message' =>
        str_contains(
            $controllerSource,
            $oldMessage
        ),

    'service_contains_old_message' =>
        str_contains(
            $serviceSource,
            $oldMessage
        ),

    'controller_reads_team_name_post' =>
        str_contains(
            $controllerSource,
            "\$_POST['team_name']"
        ),

    'controller_reads_client_name_post' =>
        str_contains(
            $controllerSource,
            "\$_POST['client_name']"
        ),

    'service_uses_client_as_team' =>
        str_contains(
            $serviceSource,
            '$rowTeam = $clientName'
        )
        || str_contains(
            $serviceSource,
            "'team_label' => \$clientName"
        ),

    'opcache_reset' =>
        $opcacheReset,

    'removed_flash_messages' =>
        $oldFlash,

    'removed_old_input' =>
        $oldInput,

    'php_version' =>
        PHP_VERSION,

    'session_id' =>
        session_id(),
];

header(
    'Content-Type: application/json; charset=utf-8'
);

header(
    'Cache-Control: no-store, no-cache, '
    . 'must-revalidate, max-age=0'
);

echo json_encode(
    $result,
    JSON_PRETTY_PRINT
    | JSON_UNESCAPED_UNICODE
    | JSON_UNESCAPED_SLASHES
    | JSON_THROW_ON_ERROR
);

exit;