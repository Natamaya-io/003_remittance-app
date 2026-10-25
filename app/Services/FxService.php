<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\App;
use DateTimeImmutable;
use DateTimeZone;
use DOMDocument;
use DOMElement;
use PDO;
use RuntimeException;
use Throwable;

final class FxService
{
    /*
    |--------------------------------------------------------------------------
    | Konfigurasi Bank Indonesia
    |--------------------------------------------------------------------------
    */

    private const PROVIDER =
        'Bank Indonesia - Kurs Transaksi BI';

    private const ENDPOINT =
        'https://www.bi.go.id/biwebservice/wskursbi.asmx/getSubKursLokal4';

    /*
     * Cache 30 menit.
     */
    private const CACHE_TTL_SECONDS = 1800;

    /*
     * Jumlah hari ke belakang untuk grafik historis.
     */
    private const LOOKBACK_DAYS = 20;

    /*
     * Mata uang default.
     */
    private const DEFAULT_QUOTES = [
        'USD',
        'EUR',
        'SGD',
        'AUD',
        'GBP',
        'JPY',
        'CNY',
        'HKD',
        'MYR',
        'THB',
        'KRW',
        'CHF',
        'CAD',
        'NZD',
        'SAR',
        'AED',
    ];

    private string $cacheFile;

    private DateTimeZone $timezone;


    /*
    |--------------------------------------------------------------------------
    | Constructor
    |--------------------------------------------------------------------------
    */

    public function __construct()
    {
        $root = defined('BASE_PATH')
            ? (string) BASE_PATH
            : dirname(__DIR__, 2);

        $this->cacheFile =
            $root
            . '/storage/cache/fx_bi_daily.json';

        $this->timezone =
            new DateTimeZone(
                'Asia/Jakarta'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Ambil Kurs
    |--------------------------------------------------------------------------
    |
    | $force = true
    | digunakan ketika tombol Sinkron ditekan.
    |
    */

    /**
     * @param array<int, string>|null $quotes
     * @return array<string, mixed>
     */
    public function getRates(
        ?string $base = 'IDR',
        ?array $quotes = null,
        bool $force = false
    ): array {
        $base = strtoupper(
            trim(
                (string) $base
            )
        );

        /*
         * Sistem ini menggunakan IDR
         * sebagai mata uang dasar.
         */
        if ($base !== 'IDR') {
            throw new RuntimeException(
                'Kurs BI pada aplikasi ini hanya mendukung basis IDR.'
            );
        }

        /*
         * Normalisasi daftar currency.
         */
        $quotes = $this->normalizeQuotes(
            $quotes
            ?: (array) App::config(
                'fx.quotes',
                self::DEFAULT_QUOTES
            )
        );

        if ($quotes === []) {
            $quotes =
                self::DEFAULT_QUOTES;
        }

        /*
         * Baca cache terlebih dahulu.
         */
        $cache =
            $this->readCache();

        /*
         * Kalau bukan force refresh
         * dan cache masih fresh,
         * gunakan cache.
         */
        if (
            !$force
            && $this->cacheFresh(
                $cache
            )
        ) {
            return $this->filterPayload(
                $cache,
                $quotes,
                true
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Ambil data langsung dari BI
        |--------------------------------------------------------------------------
        */

        try {
            $series =
                $this->fetchSeries(
                    $quotes
                );

            if (
                (
                    $series['latest_date']
                    ?? ''
                ) === ''
                ||
                (
                    $series['current']
                    ?? []
                ) === []
            ) {
                throw new RuntimeException(
                    'Data kurs BI terbaru tidak ditemukan.'
                );
            }

            /*
             * Bentuk data siap frontend.
             */
            $rows =
                $this->buildRows(
                    $base,
                    $quotes,
                    (string) $series['latest_date'],
                    (array) $series['current'],
                    (array) $series['previous'],
                    (array) $series['history']
                );

            if ($rows === []) {
                throw new RuntimeException(
                    'Mata uang yang diminta tidak tersedia pada data BI.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Simpan snapshot ke database
            |--------------------------------------------------------------------------
            */

            $warning = null;

            try {
                $this->persistSnapshot(
                    $base,
                    (string) $series['latest_date'],
                    (array) $series['current']
                );
            } catch (
                Throwable $databaseError
            ) {
                /*
                 * Data kurs tetap ditampilkan walaupun
                 * penyimpanan DB gagal.
                 */
                $warning =
                    'Data BI berhasil diambil, tetapi gagal disimpan ke database: '
                    . $databaseError->getMessage();
            }

            $now =
                new DateTimeImmutable(
                    'now',
                    $this->timezone
                );

            /*
            |--------------------------------------------------------------------------
            | Payload final
            |--------------------------------------------------------------------------
            */

            $payload = [
                'base' =>
                    $base,

                'provider' =>
                    self::PROVIDER,

                /*
                 * false = hasil fetch baru.
                 */
                'cached' =>
                    false,

                /*
                 * false = bukan data lama.
                 */
                'stale' =>
                    false,

                /*
                 * warning DB bila ada.
                 */
                'error' =>
                    $warning,

                'rate_date' =>
                    (string) $series['latest_date'],

                'updated_at' =>
                    (string) $series['latest_date']
                    . ' 00:00:00',

                'fetched_at' =>
                    $now->format(
                        DATE_ATOM
                    ),

                'generated_at' =>
                    $now->format(
                        DATE_ATOM
                    ),

                'rates' =>
                    $rows,
            ];

            /*
             * Simpan cache terbaru.
             */
            try {
                $this->writeCache(
                    $payload
                );
            } catch (Throwable) {
                /*
                 * Cache gagal tidak boleh
                 * menggagalkan response.
                 */
            }

            return $payload;
        } catch (Throwable $error) {

            /*
            |--------------------------------------------------------------------------
            | FALLBACK 1: Cache
            |--------------------------------------------------------------------------
            */

            if (
                $this->isBiPayload(
                    $cache
                )
            ) {
                $cache['cached'] =
                    true;

                $cache['stale'] =
                    true;

                $cache['error'] =
                    'Update BI gagal. Cache BI terakhir digunakan. '
                    . $error->getMessage();

                return $this->filterPayload(
                    $cache,
                    $quotes,
                    true
                );
            }

            /*
            |--------------------------------------------------------------------------
            | FALLBACK 2: Database
            |--------------------------------------------------------------------------
            */

            $fallback =
                $this->databaseFallback(
                    $base,
                    $quotes
                );

            if (
                (
                    $fallback['rates']
                    ?? []
                ) !== []
            ) {
                $fallback['cached'] =
                    true;

                $fallback['stale'] =
                    true;

                $fallback['error'] =
                    'Update BI gagal. Data BI terakhir dari database digunakan. '
                    . $error->getMessage();

                return $fallback;
            }

            /*
            |--------------------------------------------------------------------------
            | Tidak ada cache dan database
            |--------------------------------------------------------------------------
            */

            throw new RuntimeException(
                'Kurs Bank Indonesia tidak dapat diambil: '
                . $error->getMessage(),
                0,
                $error
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Normalize Currency
    |--------------------------------------------------------------------------
    */

    /**
     * @param array<int, mixed> $quotes
     * @return array<int, string>
     */
    private function normalizeQuotes(
        array $quotes
    ): array {
        $normalized = [];

        foreach ($quotes as $quote) {
            $code =
                strtoupper(
                    trim(
                        (string) $quote
                    )
                );

            /*
             * Hanya menerima 3 huruf.
             */
            if (
                preg_match(
                    '/^[A-Z]{3}$/',
                    $code
                ) !== 1
            ) {
                continue;
            }

            /*
             * IDR adalah base.
             */
            if ($code === 'IDR') {
                continue;
            }

            $normalized[$code] =
                true;
        }

        return array_keys(
            $normalized
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Ambil Data Historis
    |--------------------------------------------------------------------------
    */

    /**
     * @param array<int, string> $quotes
     * @return array<string, mixed>
     */
    private function fetchSeries(
        array $quotes
    ): array {
        $cursor =
            new DateTimeImmutable(
                'today',
                $this->timezone
            );

        $history = [];
        $snapshots = [];

        foreach ($quotes as $quote) {
            $history[$quote] = [];
        }

        /*
         * Simpan error terakhir.
         */
        $lastError = null;

        /*
         * Batasi kegagalan berturut-turut agar
         * tidak menunggu terlalu lama jika BI down.
         */
        $consecutiveErrors = 0;

        for (
            $i = 0;
            $i < self::LOOKBACK_DAYS;
            $i++
        ) {
            /*
             * 1 = Senin
             * 5 = Jumat
             */
            $dayNumber =
                (int) $cursor->format(
                    'N'
                );

            /*
             * Abaikan Sabtu dan Minggu.
             */
            if ($dayNumber > 5) {
                $cursor =
                    $cursor->modify(
                        '-1 day'
                    );

                continue;
            }

            $date =
                $cursor->format(
                    'Y-m-d'
                );

            try {
                $snapshot =
                    $this->fetchSnapshot(
                        $date
                    );

                /*
                 * Request berhasil.
                 */
                $consecutiveErrors = 0;

                if ($snapshot !== []) {
                    $snapshots[$date] =
                        $snapshot;

                    foreach (
                        $quotes
                        as $quote
                    ) {
                        if (
                            !isset(
                                $snapshot[$quote]
                            )
                        ) {
                            continue;
                        }

                        $mid =
                            (float) (
                                $snapshot[$quote]['mid']
                                ?? 0
                            );

                        if ($mid <= 0) {
                            continue;
                        }

                        $history[$quote][] = [
                            'value' =>
                                $mid,

                            'at' =>
                                $date
                                . ' 00:00:00',
                        ];
                    }
                }
            } catch (Throwable $error) {
                /*
                 * Satu tanggal gagal tidak langsung
                 * membatalkan seluruh sinkronisasi.
                 */
                $lastError =
                    $error;

                $consecutiveErrors++;

                /*
                 * Bila BI benar-benar tidak bisa
                 * diakses beberapa kali berturut-turut,
                 * hentikan agar request tidak terlalu lama.
                 */
                if (
                    $consecutiveErrors >= 3
                    && $snapshots === []
                ) {
                    break;
                }
            }

            $cursor =
                $cursor->modify(
                    '-1 day'
                );
        }

        /*
         * Tidak ada data sama sekali.
         */
        if ($snapshots === []) {
            if (
                $lastError
                instanceof Throwable
            ) {
                throw new RuntimeException(
                    'Tidak berhasil mengambil data kurs BI: '
                    . $lastError->getMessage(),
                    0,
                    $lastError
                );
            }

            return [
                'latest_date' =>
                    '',

                'current' =>
                    [],

                'previous' =>
                    [],

                'history' =>
                    $history,
            ];
        }

        /*
         * Urutkan tanggal terbaru.
         */
        krsort(
            $snapshots
        );

        $dates =
            array_keys(
                $snapshots
            );

        $latestDate =
            (string) (
                $dates[0]
                ?? ''
            );

        $previousDate =
            (string) (
                $dates[1]
                ?? $latestDate
            );

        /*
         * Urutkan grafik lama -> baru.
         */
        foreach (
            $history
            as &$points
        ) {
            usort(
                $points,
                static function (
                    array $a,
                    array $b
                ): int {
                    return strcmp(
                        (string) (
                            $a['at']
                            ?? ''
                        ),
                        (string) (
                            $b['at']
                            ?? ''
                        )
                    );
                }
            );
        }

        unset($points);

        return [
            'latest_date' =>
                $latestDate,

            'current' =>
                $snapshots[$latestDate]
                ?? [],

            'previous' =>
                $snapshots[$previousDate]
                ?? [],

            'history' =>
                $history,
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Fetch Satu Tanggal
    |--------------------------------------------------------------------------
    */

    /**
     * @return array<string, array<string, float|string>>
     */
    private function fetchSnapshot(
        string $date
    ): array {
        $xml =
            $this->requestXml(
                self::ENDPOINT,
                [
                    'startdate' =>
                        $date,
                ]
            );

        return $this->parseXml(
            $xml
        );
    }


    /*
    |--------------------------------------------------------------------------
    | HTTP Request ke Bank Indonesia
    |--------------------------------------------------------------------------
    */

    /**
     * @param array<string, string> $fields
     */
    private function requestXml(
        string $url,
        array $fields
    ): string {
        /*
         * Pastikan extension cURL aktif.
         */
        if (
            !function_exists(
                'curl_init'
            )
        ) {
            throw new RuntimeException(
                'Ekstensi cURL belum aktif pada PHP.'
            );
        }

        /*
         * Inisialisasi handle.
         */
        $curl =
            curl_init(
                $url
            );

        if ($curl === false) {
            throw new RuntimeException(
                'Gagal menginisialisasi koneksi cURL.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | cURL Options
        |--------------------------------------------------------------------------
        */

        $optionsSet =
            curl_setopt_array(
                $curl,
                [
                    CURLOPT_POST =>
                        true,

                    CURLOPT_POSTFIELDS =>
                        http_build_query(
                            $fields
                        ),

                    CURLOPT_RETURNTRANSFER =>
                        true,

                    CURLOPT_FOLLOWLOCATION =>
                        true,

                    CURLOPT_MAXREDIRS =>
                        5,

                    CURLOPT_CONNECTTIMEOUT =>
                        10,

                    CURLOPT_TIMEOUT =>
                        30,

                    CURLOPT_ENCODING =>
                        '',

                    CURLOPT_SSL_VERIFYPEER =>
                        true,

                    CURLOPT_SSL_VERIFYHOST =>
                        2,

                    CURLOPT_USERAGENT =>
                        'RemitGo/22.1',

                    CURLOPT_HTTPHEADER => [
                        'Accept: application/xml, text/xml',
                        'Content-Type: application/x-www-form-urlencoded',
                        'Cache-Control: no-cache',
                    ],
                ]
            );

        if ($optionsSet === false) {
            unset($curl);

            throw new RuntimeException(
                'Konfigurasi request cURL gagal.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Execute
        |--------------------------------------------------------------------------
        */

        $response =
            curl_exec(
                $curl
            );

        /*
         * WAJIB diambil sebelum unset.
         */
        $statusCode =
            (int) curl_getinfo(
                $curl,
                CURLINFO_HTTP_CODE
            );

        $curlError =
            curl_error(
                $curl
            );

        /*
         * PHP 8+ tidak memerlukan curl_close().
         */
        unset($curl);

        /*
        |--------------------------------------------------------------------------
        | cURL Failure
        |--------------------------------------------------------------------------
        */

        if ($response === false) {
            throw new RuntimeException(
                'Koneksi ke Bank Indonesia gagal'
                . (
                    $curlError !== ''
                        ? ': '
                            . $curlError
                        : '.'
                )
            );
        }

        /*
        |--------------------------------------------------------------------------
        | HTTP Failure
        |--------------------------------------------------------------------------
        */

        if (
            $statusCode < 200
            ||
            $statusCode >= 300
        ) {
            throw new RuntimeException(
                'Bank Indonesia mengembalikan HTTP '
                . $statusCode
                . '.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Empty Response
        |--------------------------------------------------------------------------
        */

        $xml =
            trim(
                (string) $response
            );

        if ($xml === '') {
            throw new RuntimeException(
                'Respons Bank Indonesia kosong.'
            );
        }

        return $xml;
    }


    /*
    |--------------------------------------------------------------------------
    | Parse XML Bank Indonesia
    |--------------------------------------------------------------------------
    */

    /**
     * @return array<string, array<string, float|string>>
     */
    private function parseXml(
        string $xml
    ): array {
        $previousSetting =
            libxml_use_internal_errors(
                true
            );

        try {
            $doc =
                new DOMDocument();

            $loaded =
                $doc->loadXML(
                    $xml,
                    LIBXML_NOCDATA
                    | LIBXML_NOBLANKS
                    | LIBXML_NONET
                );

            if (!$loaded) {
                throw new RuntimeException(
                    'XML Bank Indonesia tidak valid.'
                );
            }

            $result = [];

            foreach (
                $doc->getElementsByTagName(
                    'Table'
                )
                as $table
            ) {
                if (
                    !$table
                    instanceof DOMElement
                ) {
                    continue;
                }

                /*
                 * Currency code.
                 */
                $currency =
                    strtoupper(
                        trim(
                            $this->nodeValue(
                                $table,
                                'mts_subkurslokal'
                            )
                        )
                    );

                /*
                 * Unit nilai.
                 *
                 * Contoh:
                 * JPY kadang menggunakan unit tertentu.
                 */
                $factor =
                    $this->toFloat(
                        $this->nodeValue(
                            $table,
                            'nil_subkurslokal'
                        )
                    );

                /*
                 * Kurs beli.
                 */
                $buyRaw =
                    $this->toFloat(
                        $this->nodeValue(
                            $table,
                            'beli_subkurslokal'
                        )
                    );

                /*
                 * Kurs jual.
                 */
                $sellRaw =
                    $this->toFloat(
                        $this->nodeValue(
                            $table,
                            'jual_subkurslokal'
                        )
                    );

                if (
                    preg_match(
                        '/^[A-Z]{3}$/',
                        $currency
                    ) !== 1
                ) {
                    continue;
                }

                if (
                    $buyRaw <= 0
                    ||
                    $sellRaw <= 0
                ) {
                    continue;
                }

                /*
                 * Default factor = 1.
                 */
                if ($factor <= 0) {
                    $factor =
                        1.0;
                }

                /*
                 * Nilai per 1 unit currency.
                 */
                $buy =
                    $buyRaw
                    / $factor;

                $sell =
                    $sellRaw
                    / $factor;

                /*
                 * Mid rate.
                 */
                $mid =
                    (
                        $buy
                        + $sell
                    )
                    / 2;

                $result[$currency] = [
                    'currency' =>
                        $currency,

                    'factor' =>
                        $factor,

                    'buy' =>
                        $buy,

                    'sell' =>
                        $sell,

                    'mid' =>
                        $mid,
                ];
            }

            return $result;
        } finally {
            libxml_clear_errors();

            libxml_use_internal_errors(
                $previousSetting
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Ambil isi node XML
    |--------------------------------------------------------------------------
    */

    private function nodeValue(
        DOMElement $parent,
        string $name
    ): string {
        $nodes =
            $parent->getElementsByTagName(
                $name
            );

        if (
            $nodes->length === 0
        ) {
            return '';
        }

        $node =
            $nodes->item(0);

        if ($node === null) {
            return '';
        }

        return trim(
            (string)
            $node->textContent
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Konversi angka BI
    |--------------------------------------------------------------------------
    */

    private function toFloat(
        string $rawValue
    ): float {
        $value =
            preg_replace(
                '/[^0-9,\.\-]/u',
                '',
                trim(
                    $rawValue
                )
            ) ?? '';

        if ($value === '') {
            return 0.0;
        }

        $hasComma =
            str_contains(
                $value,
                ','
            );

        $hasDot =
            str_contains(
                $value,
                '.'
            );

        /*
         * Contoh format:
         *
         * 17.919,00
         * 17,919.00
         */
        if (
            $hasComma
            &&
            $hasDot
        ) {
            $lastComma =
                strrpos(
                    $value,
                    ','
                );

            $lastDot =
                strrpos(
                    $value,
                    '.'
                );

            if (
                $lastComma !== false
                &&
                $lastDot !== false
                &&
                $lastComma > $lastDot
            ) {
                /*
                 * Indonesia:
                 * 17.919,00
                 */
                $value =
                    str_replace(
                        '.',
                        '',
                        $value
                    );

                $value =
                    str_replace(
                        ',',
                        '.',
                        $value
                    );
            } else {
                /*
                 * English:
                 * 17,919.00
                 */
                $value =
                    str_replace(
                        ',',
                        '',
                        $value
                    );
            }
        } elseif ($hasComma) {
            /*
             * 17919,00
             */
            $value =
                str_replace(
                    ',',
                    '.',
                    $value
                );
        }

        return (float) $value;
    }


    /*
    |--------------------------------------------------------------------------
    | Bentuk Data Currency
    |--------------------------------------------------------------------------
    */

    /**
     * @param array<int, string> $quotes
     * @param array<string, array<string, float|string>> $current
     * @param array<string, array<string, float|string>> $previous
     * @param array<string, array<int, array<string, mixed>>> $history
     * @return array<int, array<string, mixed>>
     */
    private function buildRows(
        string $base,
        array $quotes,
        string $date,
        array $current,
        array $previous,
        array $history
    ): array {
        $rows = [];

        $fetchedAt =
            (
                new DateTimeImmutable(
                    'now',
                    $this->timezone
                )
            )->format(
                'Y-m-d H:i:s'
            );

        foreach (
            $quotes
            as $quote
        ) {
            if (
                !isset(
                    $current[$quote]
                )
            ) {
                continue;
            }

            $currentRow =
                $current[$quote];

            $mid =
                (float) (
                    $currentRow['mid']
                    ?? 0
                );

            if ($mid <= 0) {
                continue;
            }

            $buy =
                (float) (
                    $currentRow['buy']
                    ?? 0
                );

            $sell =
                (float) (
                    $currentRow['sell']
                    ?? 0
                );

            $previousMid =
                (float) (
                    $previous[$quote]['mid']
                    ?? $mid
                );

            /*
             * Persentase perubahan.
             */
            $changePercent =
                $previousMid > 0
                    ? (
                        (
                            $mid
                            - $previousMid
                        )
                        / $previousMid
                    ) * 100
                    : 0.0;

            /*
             * Arah perubahan.
             */
            $direction =
                'flat';

            if (
                $changePercent
                > 0.000001
            ) {
                $direction =
                    'up';
            } elseif (
                $changePercent
                < -0.000001
            ) {
                $direction =
                    'down';
            }

            /*
             * Data chart.
             */
            $trend =
                $history[$quote]
                ?? [];

            if ($trend === []) {
                $trend = [
                    [
                        'value' =>
                            $previousMid,

                        'at' =>
                            '',
                    ],

                    [
                        'value' =>
                            $mid,

                        'at' =>
                            $date
                            . ' 00:00:00',
                    ],
                ];
            }

            /*
             * rate =
             * nilai currency terhadap 1 IDR.
             *
             * display_rate =
             * nilai IDR per currency.
             */
            $rawRate =
                1 / $mid;

            $rows[] = [
                'base_currency' =>
                    $base,

                'quote_currency' =>
                    $quote,

                'rate' =>
                    $rawRate,

                'display_rate' =>
                    $mid,

                'buy_rate' =>
                    $buy,

                'sell_rate' =>
                    $sell,

                'provider' =>
                    self::PROVIDER,

                'rate_date' =>
                    $date
                    . ' 00:00:00',

                'fetched_at' =>
                    $fetchedAt,

                'previous_display_rate' =>
                    $previousMid,

                'change_percent' =>
                    $changePercent,

                'direction' =>
                    $direction,

                'trend' =>
                    $trend,
            ];
        }

        return $rows;
    }


    /*
    |--------------------------------------------------------------------------
    | Simpan Snapshot
    |--------------------------------------------------------------------------
    */

    /**
     * @param array<string, array<string, float|string>> $snapshot
     */
    private function persistSnapshot(
        string $base,
        string $date,
        array $snapshot
    ): void {
        $db =
            App::db();

        $upsert =
            $db->prepare(
                '
                INSERT INTO fx_rates
                (
                    base_currency,
                    quote_currency,
                    rate,
                    provider,
                    rate_date,
                    fetched_at
                )
                VALUES
                (
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    NOW()
                )

                ON DUPLICATE KEY UPDATE
                    rate = VALUES(rate),
                    provider = VALUES(provider),
                    rate_date = VALUES(rate_date),
                    fetched_at = NOW()
                '
            );

        foreach (
            $snapshot
            as $currency => $row
        ) {
            $currency =
                strtoupper(
                    trim(
                        (string) $currency
                    )
                );

            $mid =
                (float) (
                    $row['mid']
                    ?? 0
                );

            if (
                $currency === ''
                ||
                $mid <= 0
            ) {
                continue;
            }

            $rawRate =
                1 / $mid;

            $upsert->execute([
                $base,
                $currency,
                $rawRate,
                self::PROVIDER,
                $date
                . ' 00:00:00',
            ]);

            $this->persistHistory(
                $base,
                $currency,
                $rawRate,
                $date
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Simpan History
    |--------------------------------------------------------------------------
    */

    private function persistHistory(
        string $base,
        string $quote,
        float $rawRate,
        string $date
    ): void {
        try {
            $db =
                App::db();

            /*
             * Cari record hari yang sama.
             */
            $check =
                $db->prepare(
                    '
                    SELECT id

                    FROM fx_rate_history

                    WHERE
                        base_currency = ?
                        AND quote_currency = ?
                        AND provider = ?
                        AND DATE(rate_date) = ?

                    ORDER BY id DESC

                    LIMIT 1
                    '
                );

            $check->execute([
                $base,
                $quote,
                self::PROVIDER,
                $date,
            ]);

            $historyId =
                $check->fetchColumn();

            /*
             * Update record existing.
             */
            if (
                $historyId !== false
            ) {
                $update =
                    $db->prepare(
                        '
                        UPDATE fx_rate_history

                        SET
                            rate = ?,
                            fetched_at = NOW()

                        WHERE id = ?
                        '
                    );

                $update->execute([
                    $rawRate,
                    (int) $historyId,
                ]);

                return;
            }

            /*
             * Insert history baru.
             */
            $insert =
                $db->prepare(
                    '
                    INSERT INTO fx_rate_history
                    (
                        base_currency,
                        quote_currency,
                        rate,
                        provider,
                        rate_date,
                        fetched_at
                    )
                    VALUES
                    (
                        ?,
                        ?,
                        ?,
                        ?,
                        ?,
                        NOW()
                    )
                    '
                );

            $insert->execute([
                $base,
                $quote,
                $rawRate,
                self::PROVIDER,
                $date
                . ' 00:00:00',
            ]);
        } catch (Throwable) {
            /*
             * Histori bersifat sekunder.
             *
             * Jika gagal, data utama tidak
             * boleh ikut gagal.
             */
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Read Cache
    |--------------------------------------------------------------------------
    */

    /**
     * @return array<string, mixed>
     */
    private function readCache(): array
    {
        if (
            !is_file(
                $this->cacheFile
            )
        ) {
            return [];
        }

        $json =
            @file_get_contents(
                $this->cacheFile
            );

        if (
            !is_string($json)
            ||
            trim($json) === ''
        ) {
            return [];
        }

        try {
            $decoded =
                json_decode(
                    $json,
                    true,
                    512,
                    JSON_THROW_ON_ERROR
                );
        } catch (Throwable) {
            return [];
        }

        if (
            !is_array(
                $decoded
            )
        ) {
            return [];
        }

        return $this->isBiPayload(
            $decoded
        )
            ? $decoded
            : [];
    }


    /*
    |--------------------------------------------------------------------------
    | Write Cache
    |--------------------------------------------------------------------------
    */

    /**
     * @param array<string, mixed> $payload
     */
    private function writeCache(
        array $payload
    ): void {
        $dir =
            dirname(
                $this->cacheFile
            );

        /*
         * Buat folder bila belum ada.
         */
        if (
            !is_dir($dir)
        ) {
            $created =
                @mkdir(
                    $dir,
                    0775,
                    true
                );

            if (
                !$created
                &&
                !is_dir($dir)
            ) {
                throw new RuntimeException(
                    'Folder cache kurs tidak dapat dibuat.'
                );
            }
        }

        $json =
            json_encode(
                $payload,
                JSON_PRETTY_PRINT
                | JSON_UNESCAPED_UNICODE
                | JSON_UNESCAPED_SLASHES
                | JSON_THROW_ON_ERROR
            );

        $tmp =
            $this->cacheFile
            . '.tmp';

        $written =
            @file_put_contents(
                $tmp,
                $json,
                LOCK_EX
            );

        if ($written === false) {
            throw new RuntimeException(
                'Cache kurs BI tidak dapat ditulis.'
            );
        }

        /*
         * Windows kadang tidak mau rename
         * jika target sudah ada.
         */
        if (
            is_file(
                $this->cacheFile
            )
        ) {
            @unlink(
                $this->cacheFile
            );
        }

        if (
            !@rename(
                $tmp,
                $this->cacheFile
            )
        ) {
            @unlink(
                $tmp
            );

            throw new RuntimeException(
                'Cache kurs BI tidak dapat disimpan.'
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Cache Fresh
    |--------------------------------------------------------------------------
    */

    /**
     * @param array<string, mixed> $cache
     */
    private function cacheFresh(
        array $cache
    ): bool {
        if (
            !$this->isBiPayload(
                $cache
            )
        ) {
            return false;
        }

        $date =
            (string) (
                $cache['fetched_at']
                ??
                $cache['generated_at']
                ??
                ''
            );

        if ($date === '') {
            return false;
        }

        $timestamp =
            strtotime(
                $date
            );

        if ($timestamp === false) {
            return false;
        }

        return (
            time()
            - $timestamp
        ) < self::CACHE_TTL_SECONDS;
    }


    /*
    |--------------------------------------------------------------------------
    | Validasi Cache BI
    |--------------------------------------------------------------------------
    */

    /**
     * @param array<string, mixed> $payload
     */
    private function isBiPayload(
        array $payload
    ): bool {
        return trim(
            (string) (
                $payload['provider']
                ?? ''
            )
        ) === self::PROVIDER;
    }


    /*
    |--------------------------------------------------------------------------
    | Filter Payload
    |--------------------------------------------------------------------------
    */

    /**
     * @param array<string, mixed> $payload
     * @param array<int, string> $quotes
     * @return array<string, mixed>
     */
    private function filterPayload(
        array $payload,
        array $quotes,
        bool $cached
    ): array {
        if (
            !$this->isBiPayload(
                $payload
            )
        ) {
            return $this->emptyPayload(
                (string) (
                    $payload['base']
                    ?? 'IDR'
                )
            );
        }

        $indexed = [];

        foreach (
            (array) (
                $payload['rates']
                ?? []
            )
            as $row
        ) {
            if (
                !is_array(
                    $row
                )
            ) {
                continue;
            }

            $code =
                strtoupper(
                    trim(
                        (string) (
                            $row['quote_currency']
                            ?? ''
                        )
                    )
                );

            if ($code !== '') {
                $indexed[$code] =
                    $row;
            }
        }

        $rates = [];

        foreach (
            $quotes
            as $quote
        ) {
            if (
                isset(
                    $indexed[$quote]
                )
            ) {
                $rates[] =
                    $indexed[$quote];
            }
        }

        $payload['rates'] =
            $rates;

        $payload['cached'] =
            $cached;

        $payload['stale'] =
            (bool) (
                $payload['stale']
                ?? false
            );

        $payload['error'] =
            $payload['error']
            ?? null;

        return $payload;
    }


    /*
    |--------------------------------------------------------------------------
    | Database Fallback
    |--------------------------------------------------------------------------
    */

    /**
     * @param array<int, string> $quotes
     * @return array<string, mixed>
     */
    private function databaseFallback(
        string $base,
        array $quotes
    ): array {
        if ($quotes === []) {
            return $this->emptyPayload(
                $base
            );
        }

        try {
            $placeholders =
                implode(
                    ',',
                    array_fill(
                        0,
                        count($quotes),
                        '?'
                    )
                );

            $sql =
                '
                SELECT
                    base_currency,
                    quote_currency,
                    rate,
                    provider,
                    rate_date,
                    fetched_at

                FROM fx_rates

                WHERE
                    base_currency = ?
                    AND quote_currency IN ('
                    . $placeholders .
                    ')
                    AND provider = ?

                ORDER BY
                    rate_date DESC,
                    fetched_at DESC
                ';

            $stmt =
                App::db()->prepare(
                    $sql
                );

            $stmt->execute(
                array_merge(
                    [$base],
                    $quotes,
                    [self::PROVIDER]
                )
            );

            $indexed = [];

            foreach (
                $stmt->fetchAll(
                    PDO::FETCH_ASSOC
                )
                as $row
            ) {
                $code =
                    strtoupper(
                        trim(
                            (string) (
                                $row['quote_currency']
                                ?? ''
                            )
                        )
                    );

                $rawRate =
                    (float) (
                        $row['rate']
                        ?? 0
                    );

                /*
                 * Hanya ambil record terbaru
                 * untuk setiap currency.
                 */
                if (
                    $code === ''
                    ||
                    $rawRate <= 0
                    ||
                    isset(
                        $indexed[$code]
                    )
                ) {
                    continue;
                }

                $displayRate =
                    1 / $rawRate;

                $indexed[$code] = [
                    'base_currency' =>
                        $base,

                    'quote_currency' =>
                        $code,

                    'rate' =>
                        $rawRate,

                    'display_rate' =>
                        $displayRate,

                    'buy_rate' =>
                        0.0,

                    'sell_rate' =>
                        0.0,

                    'provider' =>
                        self::PROVIDER,

                    'rate_date' =>
                        (string) (
                            $row['rate_date']
                            ?? ''
                        ),

                    'fetched_at' =>
                        (string) (
                            $row['fetched_at']
                            ?? ''
                        ),

                    'previous_display_rate' =>
                        $displayRate,

                    'change_percent' =>
                        0.0,

                    'direction' =>
                        'flat',

                    'trend' => [
                        [
                            'value' =>
                                $displayRate,

                            'at' =>
                                (string) (
                                    $row['rate_date']
                                    ?? ''
                                ),
                        ],
                    ],
                ];
            }

            $rates = [];

            foreach (
                $quotes
                as $quote
            ) {
                if (
                    isset(
                        $indexed[$quote]
                    )
                ) {
                    $rates[] =
                        $indexed[$quote];
                }
            }

            $first =
                $rates[0]
                ?? [];

            return [
                'base' =>
                    $base,

                'rates' =>
                    $rates,

                'provider' =>
                    self::PROVIDER,

                'cached' =>
                    true,

                'stale' =>
                    true,

                'error' =>
                    null,

                'rate_date' =>
                    substr(
                        (string) (
                            $first['rate_date']
                            ?? ''
                        ),
                        0,
                        10
                    ),

                'updated_at' =>
                    (string) (
                        $first['rate_date']
                        ?? ''
                    ),

                'fetched_at' =>
                    (string) (
                        $first['fetched_at']
                        ?? ''
                    ),

                'generated_at' =>
                    (
                        new DateTimeImmutable(
                            'now',
                            $this->timezone
                        )
                    )->format(
                        DATE_ATOM
                    ),
            ];
        } catch (Throwable) {
            return $this->emptyPayload(
                $base
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Empty Payload
    |--------------------------------------------------------------------------
    */

    /**
     * @return array<string, mixed>
     */
    private function emptyPayload(
        string $base
    ): array {
        return [
            'base' =>
                $base,

            'rates' =>
                [],

            'provider' =>
                self::PROVIDER,

            'cached' =>
                false,

            'stale' =>
                true,

            'error' =>
                null,

            'rate_date' =>
                '',

            'updated_at' =>
                '',

            'fetched_at' =>
                '',

            'generated_at' =>
                (
                    new DateTimeImmutable(
                        'now',
                        $this->timezone
                    )
                )->format(
                    DATE_ATOM
                ),
        ];
    }
}