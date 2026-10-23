<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\BaseController;
use App\Services\FxService;
use Throwable;

final class FxController extends BaseController
{
    public function refresh(): void
    {
        try {
            $result = (new FxService())->getRates('IDR', null, true);

            $rates = is_array($result['rates'] ?? null)
                ? $result['rates']
                : [];

            if ($rates === []) {
                flash(
                    'warning',
                    'Bank Indonesia belum mengembalikan data kurs.'
                );
            } else {
                flash(
                    'success',
                    'Kurs BI berhasil diperbarui untuk tanggal '
                    . (string) ($result['rate_date'] ?? '-')
                    . '.'
                );
            }
        } catch (Throwable $e) {
            flash(
                'danger',
                'Pembaruan kurs BI gagal: ' . $e->getMessage()
            );
        }

        redirect('/');
    }

    public function publicJson(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');

        try {
            $force = in_array(
                strtolower(trim((string) ($_GET['refresh'] ?? ''))),
                ['1', 'true', 'yes', 'on'],
                true
            );

            $quotes = null;

            if (isset($_GET['quotes']) && trim((string) $_GET['quotes']) !== '') {
                $quotes = array_map(
                    'trim',
                    explode(',', (string) $_GET['quotes'])
                );
            }

            $result = (new FxService())->getRates('IDR', $quotes, $force);

            echo json_encode(
                $result,
                JSON_UNESCAPED_UNICODE
                    | JSON_UNESCAPED_SLASHES
                    | JSON_THROW_ON_ERROR
            );
        } catch (Throwable $e) {
            http_response_code(503);

            echo json_encode(
                [
                    'base' => 'IDR',
                    'rates' => [],
                    'provider' => 'Bank Indonesia - Kurs Transaksi BI',
                    'cached' => false,
                    'stale' => true,
                    'error' => $e->getMessage(),
                    'rate_date' => '',
                    'updated_at' => '',
                    'fetched_at' => '',
                    'generated_at' => date(DATE_ATOM),
                ],
                JSON_UNESCAPED_UNICODE
                    | JSON_UNESCAPED_SLASHES
                    | JSON_THROW_ON_ERROR
            );
        }

        exit;
    }
}