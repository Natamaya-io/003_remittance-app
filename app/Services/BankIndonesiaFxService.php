<?php

declare(strict_types=1);

namespace App\Services;

use DateTimeImmutable;
use DateTimeZone;
use DOMDocument;
use DOMElement;
use RuntimeException;
use Throwable;

final class BankIndonesiaFxService
{
    /**
     * Web service resmi Bank Indonesia.
     *
     * getSubKursLokal4:
     * mengambil seluruh Kurs Transaksi BI untuk tanggal tertentu.
     */
    private const ENDPOINT =
        'https://www.bi.go.id/biwebservice/'
        . 'wskursbi.asmx/getSubKursLokal4';

    /**
     * Cache diperiksa ulang setiap dua jam.
     *
     * Kurs BI hanya berubah satu kali setiap hari kerja,
     * tetapi interval dua jam membuat aplikasi mencoba lagi
     * ketika data hari berjalan belum diterbitkan.
     */
    private const CACHE_TTL_SECONDS = 7200;

    /**
     * Maksimal pencarian mundur ketika hari berjalan
     * belum memiliki kurs, misalnya Sabtu, Minggu,
     * atau hari libur.
     */
    private const MAX_LOOKBACK_DAYS = 10;

    /**
     * Jumlah histori yang disimpan untuk sparkline.
     */
    private const HISTORY_LIMIT = 14;

    private string $cacheFile;

    private DateTimeZone $timezone;

    public function __construct(
        ?string $cacheFile = null
    ) {
        $this->timezone = new DateTimeZone(
            'Asia/Jakarta'
        );

        $projectRoot = dirname(
            __DIR__,
            2
        );

        $this->cacheFile = $cacheFile
            ?? $projectRoot
            . '/storage/cache/bi_fx_daily.json';
    }

    /**
     * Mengambil kurs terbaru.
     *
     * @return array<string, mixed>
     */
    public function latest(
        bool $forceRefresh = false
    ): array {
        $oldCache = $this->readCache();

        if (
            !$forceRefresh
            && $this->isCacheFresh($oldCache)
        ) {
            $oldCache['cached'] = true;

            return $oldCache;
        }

        try {
            $today = new DateTimeImmutable(
                'today',
                $this->timezone
            );

            [
                $latestDate,
                $latestRows,
            ] = $this->findAvailableSnapshot(
                $today
            );

            $history = $this->extractHistory(
                $oldCache
            );

            /*
            |--------------------------------------------------------------------------
            | Ambil histori sebelumnya
            |--------------------------------------------------------------------------
            */

            if (
                !$this->historyHasPreviousDate(
                    $history,
                    $latestDate
                )
            ) {
                try {
                    $previousStart = (
                        new DateTimeImmutable(
                            $latestDate,
                            $this->timezone
                        )
                    )->modify('-1 day');

                    [
                        $previousDate,
                        $previousRows,
                    ] = $this->findAvailableSnapshot(
                        $previousStart
                    );

                    $history = $this->mergeSnapshotIntoHistory(
                        $history,
                        $previousDate,
                        $previousRows
                    );
                } catch (Throwable) {
                    /*
                    | Data sebelumnya hanya digunakan untuk
                    | persentase dan grafik. Kegagalan histori
                    | tidak boleh menggagalkan kurs terbaru.
                    */
                }
            }

            $history = $this->mergeSnapshotIntoHistory(
                $history,
                $latestDate,
                $latestRows
            );

            $rates = $this->buildRates(
                $latestDate,
                $latestRows,
                $history
            );

            $now = new DateTimeImmutable(
                'now',
                $this->timezone
            );

            $payload = [
                'base' => 'IDR',

                'provider' =>
                    'Bank Indonesia - Kurs Transaksi BI',

                'rate_date' => $latestDate,

                'updated_at' =>
                    $latestDate . ' 00:00:00',

                'fetched_at' =>
                    $now->format(DATE_ATOM),

                'cached' => false,

                'stale' => false,

                'rates' => $rates,

                /*
                | Digunakan untuk membangun grafik dari
                | hari ke hari.
                */
                'history_by_currency' => $history,
            ];

            $this->writeCache(
                $payload
            );

            return $payload;
        } catch (Throwable $exception) {
            /*
            |--------------------------------------------------------------------------
            | Gunakan cache lama jika provider sedang tidak tersedia
            |--------------------------------------------------------------------------
            */

            if ($oldCache !== []) {
                $oldCache['cached'] = true;
                $oldCache['stale'] = true;

                $oldCache['warning'] =
                    'Data Bank Indonesia tidak dapat '
                    . 'diperbarui. Sistem menampilkan '
                    . 'cache terakhir.';

                return $oldCache;
            }

            throw new RuntimeException(
                'Kurs Bank Indonesia gagal diambil: '
                . $exception->getMessage(),
                0,
                $exception
            );
        }
    }

    /**
     * Mencari tanggal terbaru yang memiliki data.
     *
     * @return array{
     *     0: string,
     *     1: array<string, array<string, float|string>>
     * }
     */
    private function findAvailableSnapshot(
        DateTimeImmutable $startDate
    ): array {
        $cursor = $startDate;

        for (
            $attempt = 0;
            $attempt < self::MAX_LOOKBACK_DAYS;
            $attempt++
        ) {
            /*
            | Sabtu dan Minggu tidak memiliki publikasi baru.
            */
            $dayOfWeek = (int) $cursor->format('N');

            if ($dayOfWeek <= 5) {
                $date = $cursor->format('Y-m-d');

                $rows = $this->fetchForDate(
                    $date
                );

                if ($rows !== []) {
                    return [
                        $date,
                        $rows,
                    ];
                }
            }

            $cursor = $cursor->modify(
                '-1 day'
            );
        }

        throw new RuntimeException(
            'Data Kurs Transaksi BI tidak ditemukan '
            . 'dalam rentang pencarian.'
        );
    }

    /**
     * Mengambil data satu tanggal dari Bank Indonesia.
     *
     * @return array<string, array<string, float|string>>
     */
    private function fetchForDate(
        string $date
    ): array {
        $response = $this->postForm(
            self::ENDPOINT,
            [
                'startdate' => $date,
            ]
        );

        return $this->parseXml(
            $response
        );
    }

    /**
     * Mengirim HTTP POST form.
     *
     * @param array<string, string> $data
     */
    private function postForm(
        string $url,
        array $data
    ): string {
        if (!function_exists('curl_init')) {
            throw new RuntimeException(
                'Ekstensi PHP cURL belum aktif.'
            );
        }

        $curl = curl_init();

        if ($curl === false) {
            throw new RuntimeException(
                'Gagal menginisialisasi cURL.'
            );
        }

        curl_setopt_array(
            $curl,
            [
                CURLOPT_URL => $url,
                CURLOPT_POST => true,

                CURLOPT_POSTFIELDS =>
                    http_build_query($data),

                CURLOPT_RETURNTRANSFER => true,

                CURLOPT_FOLLOWLOCATION => true,

                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_TIMEOUT => 25,

                CURLOPT_HTTPHEADER => [
                    'Accept: application/xml',
                    'Content-Type: '
                    . 'application/x-www-form-urlencoded',
                    'User-Agent: RemitGo/1.0',
                ],

                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_SSL_VERIFYHOST => 2,

                CURLOPT_ENCODING => '',
            ]
        );

        $response = curl_exec(
            $curl
        );

        $curlError = curl_error(
            $curl
        );

        $httpStatus = (int) curl_getinfo(
            $curl,
            CURLINFO_HTTP_CODE
        );

       unset($curl);

        if ($response === false) {
            throw new RuntimeException(
                'Koneksi ke Bank Indonesia gagal: '
                . $curlError
            );
        }

        if (
            $httpStatus < 200
            || $httpStatus >= 300
        ) {
            throw new RuntimeException(
                'Bank Indonesia mengembalikan HTTP '
                . $httpStatus
            );
        }

        $body = trim(
            (string) $response
        );

        if ($body === '') {
            throw new RuntimeException(
                'Respons Bank Indonesia kosong.'
            );
        }

        return $body;
    }

    /**
     * Mengubah XML Bank Indonesia menjadi array.
     *
     * @return array<string, array<string, float|string>>
     */
    private function parseXml(
        string $xmlContent
    ): array {
        $previousState = libxml_use_internal_errors(
            true
        );

        try {
            $document = new DOMDocument();

            $loaded = $document->loadXML(
                $xmlContent,
                LIBXML_NOCDATA
                | LIBXML_NOBLANKS
                | LIBXML_NONET
            );

            if (!$loaded) {
                throw new RuntimeException(
                    'XML Bank Indonesia tidak valid.'
                );
            }

            $tableNodes = $document
                ->getElementsByTagName('Table');

            $rows = [];

            foreach ($tableNodes as $tableNode) {
                if (!$tableNode instanceof DOMElement) {
                    continue;
                }

                $currency = strtoupper(
                    trim(
                        $this->getNodeValue(
                            $tableNode,
                            'mts_subkurslokal'
                        )
                    )
                );

                if ($currency === '') {
                    continue;
                }

                $factor = $this->toFloat(
                    $this->getNodeValue(
                        $tableNode,
                        'nil_subkurslokal'
                    )
                );

                if ($factor <= 0) {
                    $factor = 1;
                }

                $sellRaw = $this->toFloat(
                    $this->getNodeValue(
                        $tableNode,
                        'jual_subkurslokal'
                    )
                );

                $buyRaw = $this->toFloat(
                    $this->getNodeValue(
                        $tableNode,
                        'beli_subkurslokal'
                    )
                );

                if (
                    $sellRaw <= 0
                    || $buyRaw <= 0
                ) {
                    continue;
                }

                /*
                | BI dapat menggunakan faktor 100,
                | contohnya JPY. Seluruh nilai dinormalisasi
                | menjadi nilai untuk 1 unit mata uang.
                */
                $sell = $sellRaw / $factor;
                $buy = $buyRaw / $factor;

                $mid = (
                    $sell + $buy
                ) / 2;

                $rows[$currency] = [
                    'currency' => $currency,
                    'factor' => $factor,
                    'sell' => $sell,
                    'buy' => $buy,
                    'mid' => $mid,
                ];
            }

            return $rows;
        } finally {
            libxml_clear_errors();

            libxml_use_internal_errors(
                $previousState
            );
        }
    }

    private function getNodeValue(
        DOMElement $parent,
        string $nodeName
    ): string {
        $nodes = $parent
            ->getElementsByTagName(
                $nodeName
            );

        if ($nodes->length === 0) {
            return '';
        }

        return trim(
            (string) $nodes
                ->item(0)
                ?->textContent
        );
    }

    private function toFloat(
        string $rawValue
    ): float {
        $value = trim(
            $rawValue
        );

        if ($value === '') {
            return 0.0;
        }

        $value = preg_replace(
            '/[^\d,\.\-]/u',
            '',
            $value
        ) ?? '';

        if ($value === '') {
            return 0.0;
        }

        $hasComma = str_contains(
            $value,
            ','
        );

        $hasDot = str_contains(
            $value,
            '.'
        );

        if ($hasComma && $hasDot) {
            $lastComma = strrpos(
                $value,
                ','
            );

            $lastDot = strrpos(
                $value,
                '.'
            );

            if (
                $lastComma !== false
                && $lastDot !== false
                && $lastComma > $lastDot
            ) {
                /*
                | Format Indonesia: 18.080,96
                */
                $value = str_replace(
                    '.',
                    '',
                    $value
                );

                $value = str_replace(
                    ',',
                    '.',
                    $value
                );
            } else {
                /*
                | Format internasional: 18,080.96
                */
                $value = str_replace(
                    ',',
                    '',
                    $value
                );
            }
        } elseif ($hasComma) {
            $value = str_replace(
                ',',
                '.',
                $value
            );
        }

        return (float) $value;
    }

    /**
     * @param array<string, mixed> $cache
     *
     * @return array<string, array<int, array{
     *     date: string,
     *     value: float
     * }>>
     */
    private function extractHistory(
        array $cache
    ): array {
        $history = $cache[
            'history_by_currency'
        ] ?? [];

        if (is_array($history)) {
            return $history;
        }

        return [];
    }

    /**
     * @param array<string, array<int, array{
     *     date: string,
     *     value: float
     * }>> $history
     */
    private function historyHasPreviousDate(
        array $history,
        string $latestDate
    ): bool {
        foreach ($history as $points) {
            if (!is_array($points)) {
                continue;
            }

            foreach ($points as $point) {
                if (!is_array($point)) {
                    continue;
                }

                $date = (string) (
                    $point['date']
                    ?? ''
                );

                if (
                    $date !== ''
                    && $date < $latestDate
                ) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param array<string, array<int, array{
     *     date: string,
     *     value: float
     * }>> $history
     *
     * @param array<string, array<string, float|string>> $rows
     *
     * @return array<string, array<int, array{
     *     date: string,
     *     value: float
     * }>>
     */
    private function mergeSnapshotIntoHistory(
        array $history,
        string $date,
        array $rows
    ): array {
        foreach ($rows as $currency => $row) {
            $mid = (float) (
                $row['mid']
                ?? 0
            );

            if ($mid <= 0) {
                continue;
            }

            $points = is_array(
                $history[$currency]
                ?? null
            )
                ? $history[$currency]
                : [];

            $updated = false;

            foreach ($points as &$point) {
                if (
                    is_array($point)
                    && (
                        $point['date']
                        ?? ''
                    ) === $date
                ) {
                    $point['value'] = $mid;

                    $updated = true;

                    break;
                }
            }

            unset($point);

            if (!$updated) {
                $points[] = [
                    'date' => $date,
                    'value' => $mid,
                ];
            }

            usort(
                $points,
                static function (
                    array $first,
                    array $second
                ): int {
                    return strcmp(
                        (string) (
                            $first['date']
                            ?? ''
                        ),
                        (string) (
                            $second['date']
                            ?? ''
                        )
                    );
                }
            );

            if (
                count($points)
                > self::HISTORY_LIMIT
            ) {
                $points = array_slice(
                    $points,
                    -self::HISTORY_LIMIT
                );
            }

            $history[$currency] = $points;
        }

        return $history;
    }

    /**
     * @param array<string, array<string, float|string>> $latestRows
     *
     * @param array<string, array<int, array{
     *     date: string,
     *     value: float
     * }>> $history
     *
     * @return array<int, array<string, mixed>>
     */
    private function buildRates(
        string $latestDate,
        array $latestRows,
        array $history
    ): array {
        $rates = [];

        foreach ($latestRows as $currency => $row) {
            $mid = (float) (
                $row['mid']
                ?? 0
            );

            $buy = (float) (
                $row['buy']
                ?? 0
            );

            $sell = (float) (
                $row['sell']
                ?? 0
            );

            if ($mid <= 0) {
                continue;
            }

            $points = is_array(
                $history[$currency]
                ?? null
            )
                ? $history[$currency]
                : [];

            $previousValue = 0.0;
            $trend = [];

            foreach ($points as $point) {
                if (!is_array($point)) {
                    continue;
                }

                $pointDate = (string) (
                    $point['date']
                    ?? ''
                );

                $pointValue = (float) (
                    $point['value']
                    ?? 0
                );

                if ($pointValue <= 0) {
                    continue;
                }

                $trend[] = [
                    'date' => $pointDate,
                    'value' => $pointValue,
                ];

                if ($pointDate < $latestDate) {
                    $previousValue = $pointValue;
                }
            }

            $changePercentage =
                $previousValue > 0
                    ? (
                        (
                            $mid
                            - $previousValue
                        )
                        / $previousValue
                    ) * 100
                    : 0.0;

            $direction = 'flat';

            if ($changePercentage > 0.000001) {
                $direction = 'up';
            } elseif (
                $changePercentage < -0.000001
            ) {
                $direction = 'down';
            }

            $rates[] = [
                'base_currency' => 'IDR',

                'quote_currency' => $currency,

                /*
                | Nilai tengah untuk satu unit mata uang.
                */
                'display_rate' => $mid,

                /*
                | Disediakan untuk kompatibilitas
                | dengan kode lama.
                */
                'rate' => $mid > 0
                    ? 1 / $mid
                    : 0,

                /*
                | Kurs resmi BI.
                */
                'buy_rate' => $buy,
                'sell_rate' => $sell,

                'change_percent' =>
                    $changePercentage,

                'direction' => $direction,

                'trend' => $trend,

                'rate_date' =>
                    $latestDate,

                'fetched_at' => (
                    new DateTimeImmutable(
                        'now',
                        $this->timezone
                    )
                )->format(DATE_ATOM),

                'provider' =>
                    'Bank Indonesia',
            ];
        }

        usort(
            $rates,
            static function (
                array $first,
                array $second
            ): int {
                $preferredOrder = [
                    'USD' => 1,
                    'EUR' => 2,
                    'SGD' => 3,
                    'AUD' => 4,
                    'GBP' => 5,
                    'JPY' => 6,
                    'CNY' => 7,
                    'AED' => 8,
                ];

                $firstCode = (string) (
                    $first['quote_currency']
                    ?? ''
                );

                $secondCode = (string) (
                    $second['quote_currency']
                    ?? ''
                );

                return (
                    $preferredOrder[$firstCode]
                    ?? 999
                ) <=> (
                    $preferredOrder[$secondCode]
                    ?? 999
                );
            }
        );

        return $rates;
    }

    /**
     * @return array<string, mixed>
     */
    private function readCache(): array
    {
        if (!is_file($this->cacheFile)) {
            return [];
        }

        $content = file_get_contents(
            $this->cacheFile
        );

        if (
            $content === false
            || trim($content) === ''
        ) {
            return [];
        }

        $decoded = json_decode(
            $content,
            true
        );

        return is_array($decoded)
            ? $decoded
            : [];
    }

    /**
     * @param array<string, mixed> $cache
     */
    private function isCacheFresh(
        array $cache
    ): bool {
        $fetchedAt = trim(
            (string) (
                $cache['fetched_at']
                ?? ''
            )
        );

        if ($fetchedAt === '') {
            return false;
        }

        $timestamp = strtotime(
            $fetchedAt
        );

        if ($timestamp === false) {
            return false;
        }

        return (
            time() - $timestamp
        ) < self::CACHE_TTL_SECONDS;
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function writeCache(
        array $payload
    ): void {
        $directory = dirname(
            $this->cacheFile
        );

        if (
            !is_dir($directory)
            && !mkdir(
                $directory,
                0775,
                true
            )
            && !is_dir($directory)
        ) {
            throw new RuntimeException(
                'Folder cache tidak dapat dibuat: '
                . $directory
            );
        }

        $json = json_encode(
            $payload,
            JSON_PRETTY_PRINT
            | JSON_UNESCAPED_UNICODE
            | JSON_UNESCAPED_SLASHES
            | JSON_THROW_ON_ERROR
        );

        $temporaryFile =
            $this->cacheFile
            . '.tmp';

        $written = file_put_contents(
            $temporaryFile,
            $json,
            LOCK_EX
        );

        if ($written === false) {
            throw new RuntimeException(
                'Cache kurs tidak dapat ditulis.'
            );
        }

        if (
            !rename(
                $temporaryFile,
                $this->cacheFile
            )
        ) {
            @unlink(
                $temporaryFile
            );

            throw new RuntimeException(
                'Cache kurs tidak dapat disimpan.'
            );
        }
    }
}