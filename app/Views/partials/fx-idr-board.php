<?php

declare(strict_types=1);

use App\Core\App;

$fxData = (isset($fx) && is_array($fx))
    ? $fx
    : [];

$fxBase = strtoupper(
    trim(
        (string) (
            $fxData['base']
            ?? 'IDR'
        )
    )
);

$fxProvider = trim(
    (string) (
        $fxData['provider']
        ?? 'Bank Indonesia - Kurs Transaksi BI'
    )
);

$rawRates = is_array(
    $fxData['rates']
    ?? null
)
    ? $fxData['rates']
    : [];

$fxBoardId = isset($fxBoardId)
    ? trim((string) $fxBoardId)
    : 'fxDashboardBoard';

$fxBoardContext = isset($fxBoardContext)
    ? trim((string) $fxBoardContext)
    : 'dashboard';

$fxRateLimit = max(
    0,
    (int) (
        $fxRateLimit
        ?? 0
    )
);

$fxShowToolbar =
    (bool) (
        $fxShowToolbar
        ?? true
    );

$fxShowActions =
    (bool) (
        $fxShowActions
        ?? true
    );

$fxShowRefresh =
    (bool) (
        $fxShowRefresh
        ?? true
    );

$fxShowSync =
    (bool) (
        $fxShowSync
        ?? true
    );

$fxShowFooter =
    (bool) (
        $fxShowFooter
        ?? true
    );

$fxShowBuySell =
    (bool) (
        $fxShowBuySell
        ?? true
    );

$fxPanelKicker = trim(
    (string) (
        $fxPanelKicker
        ?? ''
    )
);

$fxPanelTitle = trim(
    (string) (
        $fxPanelTitle
        ?? 'Kurs Referensi IDR'
    )
);

$fxPanelDescription = trim(
    (string) (
        $fxPanelDescription
        ?? 'Kurs Transaksi Bank Indonesia harian dengan histori dan detail per mata uang.'
    )
);


/*
|--------------------------------------------------------------------------
| REQUESTED QUOTES
|--------------------------------------------------------------------------
|
| Variabel $fxRequestedQuotes bersifat opsional.
|
| get_defined_vars() digunakan supaya Intelephense tidak menandai:
|
| Undefined variable '$fxRequestedQuotes'
|
*/

$viewVariables =
    get_defined_vars();

$requestedQuotesInput =
    $viewVariables['fxRequestedQuotes']
    ?? [];

$fxRequestedQuotes =
    is_array(
        $requestedQuotesInput
    )
        ? $requestedQuotesInput
        : [];


/*
|--------------------------------------------------------------------------
| NORMALISASI REQUESTED QUOTES
|--------------------------------------------------------------------------
*/

$fxRequestedQuotes =
    array_values(
        array_unique(
            array_filter(
                array_map(
                    static function (
                        mixed $code
                    ): string {
                        return strtoupper(
                            trim(
                                (string) $code
                            )
                        );
                    },
                    $fxRequestedQuotes
                ),
                static function (
                    string $code
                ): bool {
                    return preg_match(
                        '/^[A-Z]{3}$/',
                        $code
                    ) === 1
                    && $code !== 'IDR';
                }
            )
        )
    );

unset(
    $requestedQuotesInput,
    $viewVariables
);


/*
|--------------------------------------------------------------------------
| CONTEXT
|--------------------------------------------------------------------------
*/

$contextClass =
    preg_replace(
        '/[^a-z0-9_-]+/i',
        '-',
        $fxBoardContext
    );

if (
    !is_string($contextClass)
    || $contextClass === ''
) {
    $contextClass =
        'dashboard';
}


/*
|--------------------------------------------------------------------------
| SPREAD
|--------------------------------------------------------------------------
*/

$configuredSpread =
    (float) App::config(
        'fx.spread_percent',
        0.25
    );

$fxSpread =
    $configuredSpread > 0
        ? min(
            10,
            $configuredSpread
        )
        : 0.25;


/*
|--------------------------------------------------------------------------
| METADATA MATA UANG
|--------------------------------------------------------------------------
*/

$currencyMeta = [

    'USD' => [
        'flag' => 'us',
        'name' => 'Dolar Amerika',
    ],

    'EUR' => [
        'flag' => 'eu',
        'name' => 'Euro',
    ],

    'SGD' => [
        'flag' => 'sg',
        'name' => 'Dolar Singapura',
    ],

    'AUD' => [
        'flag' => 'au',
        'name' => 'Dolar Australia',
    ],

    'GBP' => [
        'flag' => 'gb',
        'name' => 'Pound Sterling',
    ],

    'JPY' => [
        'flag' => 'jp',
        'name' => 'Yen Jepang',
    ],

    'CNY' => [
        'flag' => 'cn',
        'name' => 'Yuan Tiongkok',
    ],

    'HKD' => [
        'flag' => 'hk',
        'name' => 'Dolar Hong Kong',
    ],

    'MYR' => [
        'flag' => 'my',
        'name' => 'Ringgit Malaysia',
    ],

    'THB' => [
        'flag' => 'th',
        'name' => 'Baht Thailand',
    ],

    'KRW' => [
        'flag' => 'kr',
        'name' => 'Won Korea Selatan',
    ],

    'CHF' => [
        'flag' => 'ch',
        'name' => 'Franc Swiss',
    ],

    'CAD' => [
        'flag' => 'ca',
        'name' => 'Dolar Kanada',
    ],

    'NZD' => [
        'flag' => 'nz',
        'name' => 'Dolar Selandia Baru',
    ],

    'SAR' => [
        'flag' => 'sa',
        'name' => 'Riyal Arab Saudi',
    ],

    'AED' => [
        'flag' => 'ae',
        'name' => 'Dirham UEA',
    ],

];


/*
|--------------------------------------------------------------------------
| URUTAN MATA UANG
|--------------------------------------------------------------------------
*/

$preferredCurrencies = [
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

$preferredOrder =
    array_flip(
        $fxRequestedQuotes !== []
            ? $fxRequestedQuotes
            : $preferredCurrencies
    );


/*
|--------------------------------------------------------------------------
| FORMAT NILAI
|--------------------------------------------------------------------------
*/

$formatMoney =
    static fn (
        float $value
    ): string =>
        'Rp'
        . number_format(
            $value,
            2,
            ',',
            '.'
        );

$formatWhole =
    static fn (
        float $value
    ): string =>
        'Rp'
        . number_format(
            $value,
            0,
            ',',
            '.'
        );


/*
|--------------------------------------------------------------------------
| SIAPKAN DATA KURS
|--------------------------------------------------------------------------
*/

$preparedRates = [];

foreach (
    $rawRates
    as $rawRate
) {

    if (
        !is_array(
            $rawRate
        )
    ) {
        continue;
    }


    /*
    |--------------------------------------------------------------------------
    | Currency Code
    |--------------------------------------------------------------------------
    */

    $currencyCode =
        strtoupper(
            trim(
                (string) (
                    $rawRate['quote_currency']
                    ?? ''
                )
            )
        );


    if (
        $currencyCode === ''
    ) {
        continue;
    }


    if (
        $fxRequestedQuotes !== []
        &&
        !in_array(
            $currencyCode,
            $fxRequestedQuotes,
            true
        )
    ) {
        continue;
    }


    /*
    |--------------------------------------------------------------------------
    | Display Rate
    |--------------------------------------------------------------------------
    */

    $displayRate =
        (float) (
            $rawRate['display_rate']
            ?? 0
        );


    if (
        $displayRate <= 0
    ) {

        $providerRate =
            (float) (
                $rawRate['rate']
                ?? 0
            );


        if (
            $providerRate > 0
        ) {

            $displayRate =
                $fxBase === 'IDR'
                    ? 1 / $providerRate
                    : $providerRate;

        }

    }


    if (
        $displayRate <= 0
    ) {
        continue;
    }


    /*
    |--------------------------------------------------------------------------
    | Kurs Beli / Jual
    |--------------------------------------------------------------------------
    */

    $officialBuy =
        (float) (
            $rawRate['buy_rate']
            ?? 0
        );

    $officialSell =
        (float) (
            $rawRate['sell_rate']
            ?? 0
        );


    $buy =
        $officialBuy > 0
            ? $officialBuy
            : $displayRate
                * (
                    1
                    - (
                        $fxSpread
                        / 100
                    )
                );


    $sell =
        $officialSell > 0
            ? $officialSell
            : $displayRate
                * (
                    1
                    + (
                        $fxSpread
                        / 100
                    )
                );


    /*
    |--------------------------------------------------------------------------
    | Perubahan
    |--------------------------------------------------------------------------
    */

    $change =
        (float) (
            $rawRate['change_percent']
            ?? 0
        );


    $direction =
        trim(
            (string) (
                $rawRate['direction']
                ?? 'flat'
            )
        );


    if (
        !in_array(
            $direction,
            [
                'up',
                'down',
                'flat',
            ],
            true
        )
    ) {

        $direction =
            $change > 0
                ? 'up'
                : (
                    $change < 0
                        ? 'down'
                        : 'flat'
                );

    }


    /*
    |--------------------------------------------------------------------------
    | Trend
    |--------------------------------------------------------------------------
    */

    $trend = [];


    foreach (
        (array) (
            $rawRate['trend']
            ?? []
        )
        as $point
    ) {

        if (
            is_array(
                $point
            )
        ) {

            $value =
                (float) (
                    $point['value']
                    ?? 0
                );


            $at =
                trim(
                    (string) (
                        $point['at']
                        ?? $point['date']
                        ?? ''
                    )
                );

        } else {

            $value =
                (float) $point;

            $at = '';

        }


        if (
            $value > 0
        ) {

            $trend[] = [

                'value' =>
                    $value,

                'at' =>
                    $at,

            ];

        }

    }


    /*
    |--------------------------------------------------------------------------
    | Fallback Trend
    |--------------------------------------------------------------------------
    */

    if (
        $trend === []
    ) {

        $trend = [

            [
                'value' =>
                    $displayRate,

                'at' =>
                    '',
            ],

            [
                'value' =>
                    $displayRate,

                'at' =>
                    (string) (
                        $rawRate['rate_date']
                        ?? ''
                    ),
            ],

        ];

    }


    /*
    |--------------------------------------------------------------------------
    | Minimum / Maximum
    |--------------------------------------------------------------------------
    */

    $trendValues =
        array_map(
            static fn (
                array $point
            ): float =>
                (float) $point['value'],
            $trend
        );


    $minimum =
        min(
            $trendValues
        );


    $maximum =
        max(
            $trendValues
        );


    /*
    |--------------------------------------------------------------------------
    | Posisi pada Range
    |--------------------------------------------------------------------------
    */

    $position =
        $maximum > $minimum
            ? (
                (
                    $displayRate
                    - $minimum
                )
                /
                (
                    $maximum
                    - $minimum
                )
            )
            * 100
            : 50;


    $position =
        max(
            0,
            min(
                100,
                $position
            )
        );


    /*
    |--------------------------------------------------------------------------
    | Metadata
    |--------------------------------------------------------------------------
    */

    $meta =
        $currencyMeta[$currencyCode]
        ?? [

            'flag' =>
                'world',

            'name' =>
                $currencyCode,

        ];


    /*
    |--------------------------------------------------------------------------
    | Data Final
    |--------------------------------------------------------------------------
    */

    $preparedRates[] = [

        'code' =>
            $currencyCode,

        'flag' =>
            $meta['flag'],

        'name' =>
            $meta['name'],

        'mid' =>
            $displayRate,

        'buy' =>
            $buy,

        'sell' =>
            $sell,

        'change' =>
            $change,

        'direction' =>
            $direction,

        'trend' =>
            $trend,

        'minimum' =>
            $minimum,

        'maximum' =>
            $maximum,

        'position' =>
            $position,

        'updated_at' =>
            trim(
                (string) (
                    $rawRate['rate_date']
                    ?? $rawRate['fetched_at']
                    ?? ''
                )
            ),

    ];

}


/*
|--------------------------------------------------------------------------
| URUTKAN MATA UANG
|--------------------------------------------------------------------------
*/

usort(
    $preparedRates,

    static fn (
        array $first,
        array $second
    ): int =>

        (
            $preferredOrder[
                $first['code']
            ]
            ?? 999
        )

        <=>

        (
            $preferredOrder[
                $second['code']
            ]
            ?? 999
        )
);


/*
|--------------------------------------------------------------------------
| BATAS JUMLAH CARD
|--------------------------------------------------------------------------
*/

if (
    $fxRateLimit > 0
) {

    $preparedRates =
        array_slice(
            $preparedRates,
            0,
            $fxRateLimit
        );

}


/*
|--------------------------------------------------------------------------
| LAST UPDATED
|--------------------------------------------------------------------------
*/

$lastUpdated =
    '-';


$updatedSource =
    (string) (
        $fxData['updated_at']
        ?? $fxData['rate_date']
        ?? ''
    );


if (
    $updatedSource === ''
    &&
    $preparedRates !== []
) {

    $updatedSource =
        (string) (
            $preparedRates[0][
                'updated_at'
            ]
            ?? ''
        );

}


$timestamp =
    strtotime(
        $updatedSource
    );


if (
    $timestamp !== false
) {

    $lastUpdated =
        date(
            'd/m/Y H:i',
            $timestamp
        );

}


/*
|--------------------------------------------------------------------------
| PERMISSION
|--------------------------------------------------------------------------
*/

$canRefreshFx =
    function_exists(
        'can'
    )
    &&
    can(
        'fx.refresh'
    );


/*
|--------------------------------------------------------------------------
| FLAG SPRITE
|--------------------------------------------------------------------------
*/

$flagSprite =
    asset(
        'img/flags.svg'
    );


/*
|--------------------------------------------------------------------------
| QUOTES CSV
|--------------------------------------------------------------------------
*/

$quotesCsv =
    $fxRequestedQuotes !== []
        ? implode(
            ',',
            $fxRequestedQuotes
        )
        : '';

?>


<section
    class="fx-modern-board fx-modern-board--<?= e($contextClass) ?>"
    id="<?= e($fxBoardId) ?>"
    data-fx-board
    data-endpoint="<?= e(url('/api/fx')) ?>"
    data-spread="<?= e(number_format($fxSpread, 2, '.', '')) ?>"
    data-context="<?= e($contextClass) ?>"
    data-quotes="<?= e($quotesCsv) ?>"
    data-flag-sprite="<?= e($flagSprite) ?>"
>


    <!-- =====================================================
         HEADER
         ===================================================== -->

    <header class="fx-modern-header">


        <div class="fx-modern-heading">


            <?php if ($fxPanelKicker !== ''): ?>

                <span class="fx-modern-kicker">

                    <?= e(
                        $fxPanelKicker
                    ) ?>

                </span>

            <?php endif; ?>


            <h2>

                <?= e(
                    $fxPanelTitle
                ) ?>

            </h2>


            <?php if (
                $fxPanelDescription !== ''
            ): ?>


                <p>

                    <?= e(
                        $fxPanelDescription
                    ) ?>

                </p>


            <?php endif; ?>


        </div>


        <!-- =================================================
             ACTIONS
             ================================================= -->

        <?php if (
            $fxShowActions
        ): ?>


            <div class="fx-modern-actions">


                <!-- STATUS -->

                <span
                    class="
                        fx-modern-pill
                        fx-modern-status-pill
                    "
                >


                    <i
                        class="fx-modern-live-dot"
                    ></i>


                    <span
                        data-fx-status
                    >

                        <?= !empty(
                            $fxData['cached']
                        )
                            ? 'Cache BI aktif'
                            : 'Data BI terbaru' ?>

                    </span>


                </span>


                <!-- PROVIDER -->

                <span
                    class="fx-modern-pill"
                    data-fx-provider
                >

                    <?= e(
                        $fxProvider
                    ) ?>

                </span>


                <!-- REFRESH -->

                <?php if (
                    $fxShowRefresh
                ): ?>


                    <button
                        class="fx-modern-refresh"
                        type="button"
                        data-fx-refresh
                        aria-label="Perbarui kurs BI"
                    >

                        <i
                            class="
                                bi
                                bi-arrow-clockwise
                            "
                        ></i>

                    </button>


                <?php endif; ?>


                <!-- SINKRON -->

                <?php if (
                    $fxShowSync
                    &&
                    $canRefreshFx
                ): ?>


                    <form
                        method="post"
                        action="<?= e(url('/fx/refresh')) ?>"
                        data-fx-sync-form
                    >


                        <?= csrf_field() ?>


                        <button
                            class="fx-modern-sync"
                            type="submit"
                            data-fx-sync
                        >


                            <i
                                class="
                                    bi
                                    bi-cloud-arrow-up
                                "
                            ></i>


                            Sinkron BI


                        </button>


                    </form>


                <?php endif; ?>


            </div>


        <?php endif; ?>


    </header>


    <!-- =====================================================
         TOOLBAR
         ===================================================== -->

    <?php if (
        $fxShowToolbar
    ): ?>


        <div class="fx-modern-toolbar">


            <!-- SEARCH -->

            <label class="fx-modern-search">


                <i
                    class="
                        bi
                        bi-search
                    "
                ></i>


                <input
                    type="search"
                    data-fx-search
                    placeholder="Cari mata uang..."
                    autocomplete="off"
                >


            </label>


            <!-- METADATA -->

            <div class="fx-modern-meta">


                <span>

                    Update

                    <strong
                        data-fx-updated
                    >

                        <?= e(
                            $lastUpdated
                        ) ?>

                    </strong>

                </span>


                <span>

                    Sumber

                    <strong>

                        Bank Indonesia

                    </strong>

                </span>


                <span>

                    <strong
                        data-fx-count
                    >

                        <?= count(
                            $preparedRates
                        ) ?>

                    </strong>

                    mata uang

                </span>


            </div>


        </div>


    <?php endif; ?>


    <!-- =====================================================
         WARNING
         ===================================================== -->

    <?php if (
        !empty(
            $fxData['error']
        )
    ): ?>


        <div
            class="fx-modern-warning"
            data-fx-warning
        >

            <?= e(
                (string) $fxData['error']
            ) ?>

        </div>


    <?php else: ?>


        <div
            class="fx-modern-warning"
            data-fx-warning
            hidden
        ></div>


    <?php endif; ?>


    <!-- =====================================================
         CURRENCY GRID
         ===================================================== -->

    <div
        class="fx-modern-grid"
        data-fx-grid
    >


        <?php foreach (
            $preparedRates
            as $rate
        ): ?>


            <?php


            /*
            |--------------------------------------------------------------------------
            | Arah
            |--------------------------------------------------------------------------
            */

            $symbol =
                $rate['direction']
                === 'up'

                    ? '↗'

                    : (
                        $rate['direction']
                        === 'down'

                            ? '↘'

                            : '→'
                    );


            /*
            |--------------------------------------------------------------------------
            | Trend JSON
            |--------------------------------------------------------------------------
            */

            $trendJson =
                json_encode(
                    $rate['trend'],
                    JSON_UNESCAPED_UNICODE
                    | JSON_UNESCAPED_SLASHES
                );


            if (
                !is_string(
                    $trendJson
                )
            ) {

                $trendJson =
                    '[]';

            }


            ?>


            <article
                class="fx-modern-card"
                tabindex="0"
                role="button"
                aria-label="Lihat detail kurs <?= e($rate['code']) ?>"
                data-fx-card
                data-code="<?= e($rate['code']) ?>"
                data-name="<?= e($rate['name']) ?>"
                data-flag="<?= e($rate['flag']) ?>"
                data-search="<?= e(strtolower($rate['code'] . ' ' . $rate['name'])) ?>"
                data-mid="<?= e((string) $rate['mid']) ?>"
                data-buy="<?= e((string) $rate['buy']) ?>"
                data-sell="<?= e((string) $rate['sell']) ?>"
                data-change="<?= e((string) $rate['change']) ?>"
                data-direction="<?= e($rate['direction']) ?>"
                data-trend="<?= e($trendJson) ?>"
            >


                <span
                    class="fx-modern-card-glow"
                    aria-hidden="true"
                ></span>


                <!-- =========================================
                     CARD HEADER
                     ========================================= -->

                <header class="fx-modern-card-head">


                    <div class="fx-modern-currency">


                        <!-- FLAG -->

                        <span
                            class="fx-modern-flag"
                            aria-label="Bendera <?= e($rate['name']) ?>"
                        >


                            <svg
                                viewBox="0 0 64 48"
                                role="img"
                                aria-hidden="true"
                            >


                                <use
                                    href="<?= e(
                                        $flagSprite
                                        . '#flag-'
                                        . $rate['flag']
                                    ) ?>"
                                ></use>


                            </svg>


                        </span>


                        <!-- CURRENCY -->

                        <div>


                            <strong>

                                <?= e(
                                    $rate['code']
                                ) ?>

                            </strong>


                            <small>

                                <?= e(
                                    $rate['name']
                                ) ?>

                            </small>


                        </div>


                    </div>


                    <!-- CHANGE -->

                    <span
                        class="
                            fx-modern-change
                            <?= e($rate['direction']) ?>
                        "
                        data-role="change"
                    >


                        <?= e(
                            $symbol
                        ) ?>


                        <?= number_format(
                            abs(
                                (float) $rate['change']
                            ),
                            3,
                            ',',
                            '.'
                        ) ?>%


                    </span>


                </header>


                <!-- =========================================
                     VALUE
                     ========================================= -->

                <div class="fx-modern-value">


                    <span>

                        1 <?= e($rate['code']) ?> =

                    </span>


                    <strong
                        data-role="mid"
                    >

                        <?= e(
                            $formatMoney(
                                (float) $rate['mid']
                            )
                        ) ?>

                    </strong>


                </div>


                <!-- =========================================
                     CHART
                     ========================================= -->

                <canvas
                    class="fx-modern-chart"
                    data-values="<?= e($trendJson) ?>"
                    data-direction="<?= e($rate['direction']) ?>"
                    aria-label="Grafik historis <?= e($rate['code']) ?>"
                ></canvas>


                <!-- =========================================
                     RANGE
                     ========================================= -->

                <div class="fx-modern-range">


                    <div class="fx-modern-range-head">


                        <span>

                            Rentang historis

                        </span>


                        <strong
                            data-role="position-label"
                        >

                            <?= number_format(
                                (float) $rate['position'],
                                0,
                                ',',
                                '.'
                            ) ?>%

                        </strong>


                    </div>


                    <div class="fx-modern-range-track">


                        <i
                            data-role="range-fill"
                            style="
                                width:
                                <?= e((string) $rate['position']) ?>%
                            "
                        ></i>


                        <b
                            data-role="range-marker"
                            style="
                                left:
                                <?= e((string) $rate['position']) ?>%
                            "
                        ></b>


                    </div>


                    <div class="fx-modern-range-values">


                        <span
                            data-role="minimum"
                        >

                            <?= e(
                                $formatWhole(
                                    (float) $rate['minimum']
                                )
                            ) ?>

                        </span>


                        <span
                            data-role="maximum"
                        >

                            <?= e(
                                $formatWhole(
                                    (float) $rate['maximum']
                                )
                            ) ?>

                        </span>


                    </div>


                </div>


                <!-- =========================================
                     BUY / SELL
                     ========================================= -->

                <?php if (
                    $fxShowBuySell
                ): ?>


                    <div class="fx-modern-buy-sell">


                        <!-- BUY -->

                        <div>


                            <span>

                                Beli BI

                            </span>


                            <strong
                                data-role="buy"
                            >

                                <?= e(
                                    $formatMoney(
                                        (float) $rate['buy']
                                    )
                                ) ?>

                            </strong>


                        </div>


                        <!-- SELL -->

                        <div>


                            <span>

                                Jual BI

                            </span>


                            <strong
                                data-role="sell"
                            >

                                <?= e(
                                    $formatMoney(
                                        (float) $rate['sell']
                                    )
                                ) ?>

                            </strong>


                        </div>


                    </div>


                <?php endif; ?>


                <!-- =========================================
                     HINT
                     ========================================= -->

                <span class="fx-modern-card-hint">


                    <i
                        class="
                            bi
                            bi-box-arrow-up-right
                        "
                    ></i>


                    Klik untuk detail


                </span>


            </article>


        <?php endforeach; ?>


    </div>


    <!-- =====================================================
         EMPTY STATE
         ===================================================== -->

    <div
        class="fx-modern-empty"
        data-fx-empty
        <?= $preparedRates === []
            ? ''
            : 'hidden' ?>
    >


        <i
            class="
                bi
                bi-exclamation-triangle-fill
            "
        ></i>


        <strong>

            Data kurs BI belum tersedia

        </strong>


        <span>

            Gunakan tombol refresh atau periksa koneksi cURL/SSL.

        </span>


    </div>


    <!-- =====================================================
         FOOTER
         ===================================================== -->

    <?php if (
        $fxShowFooter
    ): ?>


        <footer class="fx-modern-footer">

            Kurs bersumber dari Kurs Transaksi Bank Indonesia
            dan diperbarui harian pada hari kerja.
            Nilai utama adalah nilai tengah kurs beli dan jual BI.

        </footer>


    <?php endif; ?>


    <!-- =====================================================
         MODAL DETAIL
         ===================================================== -->

    <div
        class="fx-detail-modal"
        data-fx-modal
        hidden
        aria-hidden="true"
    >


        <!-- BACKDROP -->

        <button
            class="fx-detail-backdrop"
            type="button"
            data-fx-close
            aria-label="Tutup detail kurs"
        ></button>


        <!-- DIALOG -->

        <section
            class="fx-detail-dialog"
            role="dialog"
            aria-modal="true"
            aria-labelledby="fxDetailTitle"
        >


            <!-- DECORATION -->

            <span
                class="
                    fx-detail-silhouette
                    fx-detail-silhouette-one
                "
                aria-hidden="true"
            ></span>


            <span
                class="
                    fx-detail-silhouette
                    fx-detail-silhouette-two
                "
                aria-hidden="true"
            ></span>


            <!-- CLOSE -->

            <button
                class="fx-detail-close"
                type="button"
                data-fx-close
                aria-label="Tutup"
            >

                ×

            </button>


            <!-- =============================================
                 HEADER DETAIL
                 ============================================= -->

            <header class="fx-detail-header">


                <div class="fx-detail-currency">


                    <!-- FLAG -->

                    <span
                        class="fx-detail-flag"
                        data-detail-flag
                    ></span>


                    <!-- COPY -->

                    <div>


                        <span class="fx-detail-kicker">

                            KURS TRANSAKSI BANK INDONESIA

                        </span>


                        <h3 id="fxDetailTitle">


                            <span
                                data-detail-code
                            >

                                USD

                            </span>


                            / IDR


                        </h3>


                        <p
                            data-detail-name
                        >

                            Dolar Amerika

                        </p>


                    </div>


                </div>


                <!-- CHANGE -->

                <span
                    class="
                        fx-detail-change
                        flat
                    "
                    data-detail-change
                >

                    → 0,000%

                </span>


            </header>


            <!-- =============================================
                 RATE
                 ============================================= -->

            <div
                class="fx-detail-rate"
                data-detail-rate
            >

                Rp0,00

            </div>


            <!-- =============================================
                 DATE
                 ============================================= -->

            <p class="fx-detail-date">


                Data harian •


                <span
                    data-detail-date
                >

                    -

                </span>


            </p>


            <!-- =============================================
                 DETAIL CHART
                 ============================================= -->

            <div class="fx-detail-chart-wrap">


                <canvas
                    class="fx-detail-chart"
                    data-detail-chart
                    aria-label="Grafik kurs historis"
                ></canvas>


            </div>


            <!-- =============================================
                 DETAIL STATISTICS
                 ============================================= -->

            <div class="fx-detail-stats">


                <!-- BUY -->

                <div>


                    <span>

                        Kurs Beli BI

                    </span>


                    <strong
                        data-detail-buy
                    >

                        Rp0,00

                    </strong>


                </div>


                <!-- SELL -->

                <div>


                    <span>

                        Kurs Jual BI

                    </span>


                    <strong
                        data-detail-sell
                    >

                        Rp0,00

                    </strong>


                </div>


                <!-- MIN -->

                <div>


                    <span>

                        Minimum

                    </span>


                    <strong
                        data-detail-min
                    >

                        Rp0,00

                    </strong>


                </div>


                <!-- MAX -->

                <div>


                    <span>

                        Maksimum

                    </span>


                    <strong
                        data-detail-max
                    >

                        Rp0,00

                    </strong>


                </div>


            </div>


            <!-- =============================================
                 SOURCE
                 ============================================= -->

            <footer class="fx-detail-source">

                Sumber: Bank Indonesia

            </footer>


        </section>


    </div>


</section>
