<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/app/bootstrap.php';

use App\Core\Auth;
use App\Services\FxService;

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

/**
 * Mengirim respons JSON dan menghentikan request.
 *
 * @param array<string, mixed> $payload
 */
function fx_live_json(array $payload, int $status = 200): never
{
    http_response_code($status);

    echo json_encode(
        $payload,
        JSON_UNESCAPED_UNICODE
        | JSON_UNESCAPED_SLASHES
        | JSON_PRESERVE_ZERO_FRACTION
        | JSON_THROW_ON_ERROR
    );

    exit;
}

if (!Auth::check()) {
    fx_live_json(
        [
            'base' => 'IDR',
            'rates' => [],
            'provider' => 'Bank Indonesia - Kurs Transaksi BI',
            'cached' => false,
            'stale' => true,
            'error' => 'Sesi login tidak tersedia.',
            'rate_date' => '',
            'updated_at' => '',
            'fetched_at' => '',
            'generated_at' => date(DATE_ATOM),
        ],
        401
    );
}

$forceRequested = in_array(
    strtolower(
        trim(
            (string) (
                $_GET['refresh']
                ?? $_GET['force']
                ?? ''
            )
        )
    ),
    [
        '1',
        'true',
        'yes',
        'on',
    ],
    true
);

$force =
    $forceRequested
    && Auth::can('fx.refresh');

$quotes = null;

$quotesParameter = trim(
    (string) (
        $_GET['quotes']
        ?? ''
    )
);

if ($quotesParameter !== '') {
    $quotes = array_values(
        array_filter(
            array_map(
                static fn (string $value): string =>
                    strtoupper(
                        trim($value)
                    ),
                explode(
                    ',',
                    $quotesParameter
                )
            ),
            static fn (string $value): bool =>
                preg_match(
                    '/^[A-Z]{3}$/',
                    $value
                ) === 1
        )
    );
}

try {
    $payload =
        (new FxService())->getRates(
            'IDR',
            $quotes,
            $force
        );

    fx_live_json(
        $payload
    );
} catch (\Throwable $exception) {
    fx_live_json(
        [
            'base' => 'IDR',
            'rates' => [],
            'provider' => 'Bank Indonesia - Kurs Transaksi BI',
            'cached' => false,
            'stale' => true,
            'error' => $exception->getMessage(),
            'rate_date' => '',
            'updated_at' => '',
            'fetched_at' => '',
            'generated_at' => date(DATE_ATOM),
        ],
        503
    );
}