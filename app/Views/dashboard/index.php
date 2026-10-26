<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| DASHBOARD DATA
|--------------------------------------------------------------------------
*/

$currentUser = function_exists('auth_user')
    ? (auth_user() ?? [])
    : [];

$userName = trim(
    (string) (
        $currentUser['name']
        ?? 'Pengguna'
    )
);


/*
|--------------------------------------------------------------------------
| Data dari DashboardController
|--------------------------------------------------------------------------
*/

$todayData = (
    isset($today)
    && is_array($today)
)
    ? $today
    : [];

$recentTransactions = (
    isset($recent)
    && is_array($recent)
)
    ? $recent
    : [];

$trendRows = (
    isset($trend)
    && is_array($trend)
)
    ? $trend
    : [];

$statusRows = (
    isset($statuses)
    && is_array($statuses)
)
    ? $statuses
    : [];

$fxData = (
    isset($fx)
    && is_array($fx)
)
    ? $fx
    : [];


/*
|--------------------------------------------------------------------------
| Dashboard KPI
|--------------------------------------------------------------------------
*/

$todayTotal = (int) (
    $todayData['total']
    ?? 0
);

$todaySuccess = (int) (
    $todayData['success_count']
    ?? 0
);

$todayRefund = (int) (
    $todayData['refund_count']
    ?? 0
);

$complaintCount = isset($openComplaints)
    ? (int) $openComplaints
    : 0;

$invoiceCount = isset($pendingInvoices)
    ? (int) $pendingInvoices
    : 0;

$companyCount = isset($companies)
    ? (int) $companies
    : 0;

$successPercentage = $todayTotal > 0
    ? min(
        100,
        ($todaySuccess / $todayTotal) * 100
    )
    : 0;


/*
|--------------------------------------------------------------------------
| FX
|--------------------------------------------------------------------------
*/

$fxRates = is_array(
    $fxData['rates']
    ?? null
)
    ? $fxData['rates']
    : [];

$fxProvider = trim(
    (string) (
        $fxData['provider']
        ?? 'Offline'
    )
);


/*
|--------------------------------------------------------------------------
| Status
|--------------------------------------------------------------------------
*/

$statusTotal = 0;

foreach ($statusRows as $statusRow) {

    if (!is_array($statusRow)) {
        continue;
    }

    $statusTotal += (int) (
        $statusRow['total']
        ?? 0
    );
}


/*
|--------------------------------------------------------------------------
| Permissions
|--------------------------------------------------------------------------
*/

$canTransactionManage =
    function_exists('can')
    && can('transaction.manage');

$canReportManage =
    function_exists('can')
    && can('report.manage');

$canComplaintManage =
    function_exists('can')
    && can('complaint.manage');

$canAnalysis =
    function_exists('can')
    && (
        can('analysis.run')
        || can('analysis.view')
    );


/*
|--------------------------------------------------------------------------
| Quick Actions
|--------------------------------------------------------------------------
*/

$quickActions = [];


if ($canTransactionManage) {

    $quickActions[] = [
        'title' => 'Input Transaksi',
        'description' => 'Tambah transaksi manual.',
        'icon' => 'bi-plus-lg',
        'tone' => 'blue',
        'url' => url('/transactions/create'),
    ];

    $quickActions[] = [
        'title' => 'Import Excel',
        'description' => 'Validasi dan ekspor BRI.',
        'icon' => 'bi-file-earmark-excel',
        'tone' => 'green',
        'url' => url('/transactions/import'),
    ];

}


if ($canReportManage) {

    $quickActions[] = [
        'title' => 'Report Bulanan',
        'description' => 'Import monitoring bulanan.',
        'icon' => 'bi-calendar2-data',
        'tone' => 'purple',
        'url' => url('/reports/create'),
    ];

}


if ($canComplaintManage) {

    $quickActions[] = [
        'title' => 'Buat Complain',
        'description' => 'Tarik data dari report.',
        'icon' => 'bi-headset',
        'tone' => 'orange',
        'url' => url('/complaints/create'),
    ];

}


if ($canAnalysis) {

    $quickActions[] = [
        'title' => 'Analisis SPK',
        'description' => 'Entropy dan TOPSIS.',
        'icon' => 'bi-diagram-3',
        'tone' => 'cyan',
        'url' => url('/analysis'),
    ];

}


/*
|--------------------------------------------------------------------------
| KPI Cards
|--------------------------------------------------------------------------
*/

$kpiCards = [

    [
        'label' => 'Transaksi Hari Ini',
        'value' => $todayTotal,
        'description' => 'Seluruh transaksi',
        'icon' => 'bi-arrow-left-right',
        'tone' => 'blue',
    ],

    [
        'label' => 'Sukses Hari Ini',
        'value' => $todaySuccess,
        'description' => 'Berstatus sukses',
        'icon' => 'bi-check2-circle',
        'tone' => 'green',
    ],

    [
        'label' => 'Refund Hari Ini',
        'value' => $todayRefund,
        'description' => 'Pengajuan refund',
        'icon' => 'bi-arrow-counterclockwise',
        'tone' => 'red',
    ],

    [
        'label' => 'Complain Aktif',
        'value' => $complaintCount,
        'description' => 'Belum diselesaikan',
        'icon' => 'bi-headset',
        'tone' => 'orange',
    ],

    [
        'label' => 'Invoice Pending',
        'value' => $invoiceCount,
        'description' => 'Menunggu proses',
        'icon' => 'bi-receipt',
        'tone' => 'purple',
    ],

    [
        'label' => 'PT Aktif',
        'value' => $companyCount,
        'description' => 'Perusahaan terdaftar',
        'icon' => 'bi-buildings',
        'tone' => 'teal',
    ],

];


$statusColors = [
    '#2563eb',
    '#10b981',
    '#f59e0b',
    '#dc2626',
    '#7c3aed',
    '#0891b2',
];


$fxBoardFile =
    dirname(__DIR__)
    . '/partials/fx-idr-board.php';

?>


<!-- =========================================================
     DASHBOARD
     ========================================================= -->

<div
    class="dashboard-modern"
    id="dashboardModernV14"
>

    <span
        id="DASHBOARD_MODERN_V14_ACTIVE"
        hidden
    ></span>


    <!-- =====================================================
         HERO
         ===================================================== -->

    <section class="dashboard-hero-modern">


        <div class="dashboard-hero-copy">


            <h2>
                Selamat datang,
                <?= e($userName) ?>
            </h2>


            <p>
                Pantau transaksi, report, kurs referensi,
                complain, invoice, dan analisis keputusan
                melalui satu dashboard operasional.
            </p>


            <div class="dashboard-hero-actions">


                <?php if ($canTransactionManage): ?>

                    <a
                        class="dashboard-action-primary"
                        href="<?= e(
                            url('/transactions/create')
                        ) ?>"
                    >

                        <i class="bi bi-plus-lg"></i>

                        Input Transaksi

                    </a>

                <?php endif; ?>


                <?php if ($canReportManage): ?>

                    <a
                        class="dashboard-action-secondary"
                        href="<?= e(
                            url('/reports/create')
                        ) ?>"
                    >

                        <i class="bi bi-file-earmark-spreadsheet"></i>

                        Import Report

                    </a>

                <?php endif; ?>


            </div>


        </div>


        <!-- AKTIVITAS -->

        <aside class="dashboard-activity-card">


            <div class="dashboard-activity-head">


                <div>

                    <span>
                        Aktivitas hari ini
                    </span>

                    <strong>

                        <?= number_format(
                            $todayTotal,
                            0,
                            ',',
                            '.'
                        ) ?>

                    </strong>

                    <small>
                        transaksi masuk
                    </small>

                </div>


                <b>

                    <?= number_format(
                        $successPercentage,
                        1,
                        ',',
                        '.'
                    ) ?>% sukses

                </b>


            </div>


            <canvas
                id="dashboardMiniChart"
                aria-label="Tren aktivitas 14 hari"
            ></canvas>


            <div class="dashboard-activity-meta">

                <span>
                    14 hari terakhir
                </span>

                <span>

                    <?= number_format(
                        $todaySuccess,
                        0,
                        ',',
                        '.'
                    ) ?>

                    sukses

                </span>

            </div>


        </aside>


    </section>


    <!-- =====================================================
         QUICK ACTIONS
         ===================================================== -->

    <section
        class="dashboard-panel dashboard-quick-panel"
    >


        <header class="dashboard-section-head">


            <div>

                <span class="dashboard-section-label">
                    AKSES CEPAT
                </span>

                <h3>
                    Pekerjaan operasional
                </h3>

                <p>
                    Pintasan ringkas sesuai hak akses akun.
                </p>

            </div>


            <div class="dashboard-fx-summary">


                <i class="bi bi-activity"></i>


                <div>

                    <strong>

                        <?= count($fxRates) ?>
                        mata uang

                    </strong>

                    <small>
                        <?= e($fxProvider) ?>
                    </small>

                </div>


            </div>


        </header>


        <div class="dashboard-quick-row">


            <?php foreach ($quickActions as $action): ?>

                <a
                    class="
                        dashboard-quick-item
                        <?= e(
                            (string) $action['tone']
                        ) ?>
                    "
                    href="<?= e(
                        (string) $action['url']
                    ) ?>"
                >


                    <span class="dashboard-quick-icon">

                        <i
                            class="
                                bi
                                <?= e(
                                    (string) $action['icon']
                                ) ?>
                            "
                        ></i>

                    </span>


                    <span class="dashboard-quick-copy">

                        <strong>

                            <?= e(
                                (string) $action['title']
                            ) ?>

                        </strong>

                        <small>

                            <?= e(
                                (string) $action['description']
                            ) ?>

                        </small>

                    </span>


                    <i
                        class="
                            bi
                            bi-arrow-up-right
                            dashboard-quick-arrow
                        "
                    ></i>


                </a>

            <?php endforeach; ?>


        </div>


    </section>


    <!-- =====================================================
         KPI CARDS
         ===================================================== -->

    <section class="dashboard-kpi-row">


        <?php foreach ($kpiCards as $card): ?>

            <article class="dashboard-kpi-card">


                <span
                    class="
                        dashboard-kpi-icon
                        <?= e(
                            (string) $card['tone']
                        ) ?>
                    "
                >

                    <i
                        class="
                            bi
                            <?= e(
                                (string) $card['icon']
                            ) ?>
                        "
                    ></i>

                </span>


                <div>


                    <span>

                        <?= e(
                            (string) $card['label']
                        ) ?>

                    </span>


                    <strong>

                        <?= number_format(
                            (int) $card['value'],
                            0,
                            ',',
                            '.'
                        ) ?>

                    </strong>


                    <small>

                        <?= e(
                            (string) $card['description']
                        ) ?>

                    </small>


                </div>


            </article>

        <?php endforeach; ?>


    </section>


    <!-- =====================================================
         ANALYTICS
         ===================================================== -->

    <section class="dashboard-analytics-grid">


        <!-- TREND -->

        <article class="dashboard-panel">


            <header class="dashboard-section-head">


                <div>

                    <span class="dashboard-section-label">
                        TRANSACTION TREND
                    </span>

                    <h3>
                        Tren Transaksi 14 Hari
                    </h3>

                    <p>
                        Pergerakan jumlah transaksi
                        berdasarkan tanggal.
                    </p>

                </div>


                <a
                    class="dashboard-small-link"
                    href="<?= e(
                        url('/reports')
                    ) ?>"
                >

                    Buka report

                    <i class="bi bi-arrow-right"></i>

                </a>


            </header>


            <div class="dashboard-trend-chart">

                <canvas
                    id="dashboardTrendChart"
                ></canvas>

            </div>


        </article>


        <!-- STATUS -->

        <article class="dashboard-panel">


            <header class="dashboard-section-head">


                <div>

                    <span class="dashboard-section-label">
                        STATUS
                    </span>

                    <h3>
                        Distribusi Status
                    </h3>

                    <p>
                        Komposisi transaksi selama 30 hari.
                    </p>

                </div>


            </header>


            <div class="dashboard-status-layout">


                <div class="dashboard-status-chart">

                    <canvas
                        id="dashboardStatusChart"
                    ></canvas>

                </div>


                <div class="dashboard-status-list">


                    <?php foreach (
                        array_slice(
                            $statusRows,
                            0,
                            6
                        )
                        as $index => $statusRow
                    ): ?>


                        <?php

                        if (!is_array($statusRow)) {
                            continue;
                        }

                        $statusName = strtoupper(
                            trim(
                                (string) (
                                    $statusRow['label']
                                    ?? $statusRow['status']
                                    ?? 'UNKNOWN'
                                )
                            )
                        );

                        $statusCount = (int) (
                            $statusRow['total']
                            ?? 0
                        );

                        $statusPercentage =
                            $statusTotal > 0
                                ? (
                                    $statusCount
                                    / $statusTotal
                                ) * 100
                                : 0;

                        $statusColor =
                            $statusColors[
                                $index
                                % count($statusColors)
                            ];

                        ?>


                        <div class="dashboard-status-item">


                            <span>

                                <i
                                    style="
                                        background:
                                        <?= e($statusColor) ?>
                                    "
                                ></i>

                                <?= e($statusName) ?>

                            </span>


                            <strong>

                                <?= number_format(
                                    $statusPercentage,
                                    1,
                                    ',',
                                    '.'
                                ) ?>%

                            </strong>


                        </div>


                    <?php endforeach; ?>


                    <?php if ($statusRows === []): ?>


                        <div class="dashboard-status-item">

                            <span>
                                Belum ada data
                            </span>

                            <strong>
                                0%
                            </strong>

                        </div>


                    <?php endif; ?>


                </div>


            </div>


        </article>


    </section>


    <!-- =====================================================
         FX BOARD
         ===================================================== -->

    <?php if (is_file($fxBoardFile)): ?>


        <?php

        /*
        |--------------------------------------------------------------------------
        | Variable untuk partial fx-idr-board.php
        |--------------------------------------------------------------------------
        */

        $fx = $fxData;

        $fxBoardId =
            'fxDashboardBoard';

        $fxBoardContext =
            'dashboard';

        $fxRateLimit =
            0;

        $fxShowToolbar =
            true;

        $fxShowActions =
            true;

        $fxShowRefresh =
            true;

        $fxShowSync =
            true;

        $fxShowFooter =
            true;

        $fxShowBuySell =
            true;

        $fxPanelKicker =
            'LIVE FX MONITOR';

        $fxPanelTitle =
            'Kurs Referensi IDR';

        $fxPanelDescription =
            'Kurs Transaksi Bank Indonesia harian dengan grafik historis yang dapat diklik.';


        require $fxBoardFile;


        unset(
            $fxBoardId,
            $fxBoardContext,
            $fxRateLimit,
            $fxShowToolbar,
            $fxShowActions,
            $fxShowRefresh,
            $fxShowSync,
            $fxShowFooter,
            $fxShowBuySell,
            $fxPanelKicker,
            $fxPanelTitle,
            $fxPanelDescription
        );

        ?>


    <?php else: ?>


        <div class="alert alert-warning">

            Panel kurs tidak ditemukan.

        </div>


    <?php endif; ?>


    <!-- =====================================================
         TRANSAKSI TERBARU
         ===================================================== -->

    <section class="dashboard-panel">


        <header class="dashboard-section-head">


            <div>

                <span class="dashboard-section-label">
                    RECENT ACTIVITY
                </span>

                <h3>
                    Transaksi Terbaru
                </h3>

                <p>
                    Aktivitas transaksi terbaru dalam sistem.
                </p>

            </div>


            <a
                class="dashboard-small-link"
                href="<?= e(
                    url('/transactions')
                ) ?>"
            >

                Lihat semua

                <i class="bi bi-arrow-right"></i>

            </a>


        </header>


        <div x-data="{ search: '', status: 'all' }" class="dashboard-recent-filter-wrap">

        <div class="flex flex-wrap items-center justify-between gap-2 p-3 bg-gray-50 border border-b-0 border-gray-200 rounded-t-xl">
            <div class="relative flex-1 min-w-[200px] max-w-xs">
                <input
                    type="text"
                    x-model="search"
                    placeholder="Cari transaksi (referensi, PT, penerima)..."
                    class="py-1.5 px-3 ps-8 block w-full border border-gray-200 rounded-lg text-xs focus:border-blue-500 focus:ring-blue-500"
                >
                <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none ps-2.5 text-gray-400">
                    <i class="bi bi-search text-xs"></i>
                </div>
                <button
                    type="button"
                    x-show="search.length > 0"
                    @click="search = ''"
                    class="absolute inset-y-0 end-0 flex items-center pe-2.5 text-gray-400 hover:text-gray-600"
                    style="display: none;"
                >
                    <i class="bi bi-x-circle text-xs"></i>
                </button>
            </div>

            <div class="inline-flex rounded-lg shadow-sm">
                <button
                    type="button"
                    @click="status = 'all'"
                    :class="status === 'all' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'"
                    class="py-1.5 px-3 inline-flex items-center text-xs font-medium border border-gray-200 first:rounded-s-lg last:rounded-e-lg focus:z-10 focus:outline-none"
                >
                    Semua
                </button>
                <button
                    type="button"
                    @click="status = 'success'"
                    :class="status === 'success' ? 'bg-emerald-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'"
                    class="py-1.5 px-3 inline-flex items-center text-xs font-medium border-t border-b border-e border-gray-200 first:rounded-s-lg last:rounded-e-lg focus:z-10 focus:outline-none"
                >
                    Sukses
                </button>
                <button
                    type="button"
                    @click="status = 'pending'"
                    :class="status === 'pending' ? 'bg-amber-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'"
                    class="py-1.5 px-3 inline-flex items-center text-xs font-medium border-t border-b border-e border-gray-200 first:rounded-s-lg last:rounded-e-lg focus:z-10 focus:outline-none"
                >
                    Pending
                </button>
                <button
                    type="button"
                    @click="status = 'refund'"
                    :class="status === 'refund' ? 'bg-rose-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'"
                    class="py-1.5 px-3 inline-flex items-center text-xs font-medium border-t border-b border-e border-gray-200 first:rounded-s-lg last:rounded-e-lg focus:z-10 focus:outline-none"
                >
                    Refund
                </button>
            </div>
        </div>

        <div
            class="
                table-responsive
                dashboard-table-wrap
            "
        >


            <table
                class="table modern-table"
                data-smart-table
            >


                <thead>

                    <tr>

                        <th>
                            Tanggal
                        </th>

                        <th>
                            Perusahaan
                        </th>

                        <th>
                            Referensi
                        </th>

                        <th>
                            Beneficiary
                        </th>

                        <th>
                            Nominal
                        </th>

                        <th>
                            Status
                        </th>

                    </tr>

                </thead>


                <tbody>


                    <?php foreach (
                        $recentTransactions
                        as $transaction
                    ): ?>


                        <?php

                        if (!is_array($transaction)) {
                            continue;
                        }


                        $transactionId = (int) (
                            $transaction['id']
                            ?? 0
                        );


                        $transactionDate = trim(
                            (string) (
                                $transaction['transaction_date']
                                ?? ''
                            )
                        );


                        $companyName = trim(
                            (string) (
                                $transaction['company_name']
                                ?? '-'
                            )
                        );


                        $reference = trim(
                            (string) (
                                $transaction['reference_no']
                                ?? ''
                            )
                        );


                        if ($reference === '') {

                            $reference =
                                'TRX-'
                                . $transactionId;

                        }


                        $beneficiary = trim(
                            (string) (
                                $transaction['beneficiary_name']
                                ?? '-'
                            )
                        );


                        $amount = (float) (
                            $transaction['amount']
                            ?? 0
                        );


                        $currency = strtoupper(
                            trim(
                                (string) (
                                    $transaction['currency']
                                    ?? 'IDR'
                                )
                            )
                        );


                        $transactionStatus = trim(
                            (string) (
                                $transaction['status']
                                ?? 'PENDING'
                            )
                        );

                        ?>


                        <tr x-show="(!search || '<?= strtolower(addslashes($reference . ' ' . $companyName . ' ' . $beneficiary)) ?>'.includes(search.toLowerCase())) && (status === 'all' || '<?= strtolower(addslashes($transactionStatus)) ?>'.includes(status))">


                            <td class="text-nowrap">

                                <?php

                                if (
                                    function_exists(
                                        'format_date'
                                    )
                                    && $transactionDate !== ''
                                ) {

                                    echo format_date(
                                        $transactionDate
                                    );

                                } else {

                                    echo e(
                                        $transactionDate
                                    );

                                }

                                ?>

                            </td>


                            <td>

                                <strong>

                                    <?= e(
                                        $companyName
                                    ) ?>

                                </strong>

                            </td>


                            <td>

                                <a
                                    class="dashboard-table-link"
                                    href="<?= e(
                                        url(
                                            '/transactions/'
                                            . $transactionId
                                        )
                                    ) ?>"
                                >

                                    <?= e(
                                        $reference
                                    ) ?>

                                </a>

                            </td>


                            <td>

                                <?= e(
                                    $beneficiary
                                ) ?>

                            </td>


                            <td class="text-nowrap">

                                <strong>

                                    <?php

                                    if (
                                        function_exists(
                                            'format_money'
                                        )
                                    ) {

                                        echo format_money(
                                            $amount,
                                            $currency
                                        );

                                    } else {

                                        echo e(
                                            $currency
                                            . ' '
                                            . number_format(
                                                $amount,
                                                2,
                                                ',',
                                                '.'
                                            )
                                        );

                                    }

                                    ?>

                                </strong>

                            </td>


                            <td>

                                <?php

                                if (
                                    function_exists(
                                        'status_badge'
                                    )
                                ) {

                                    echo status_badge(
                                        $transactionStatus
                                    );

                                } else {

                                    echo e(
                                        $transactionStatus
                                    );

                                }

                                ?>

                            </td>


                        </tr>


                    <?php endforeach; ?>


                    <?php if (
                        $recentTransactions === []
                    ): ?>


                        <tr>

                            <td
                                colspan="6"
                                class="dashboard-empty-cell"
                            >

                                Belum ada transaksi tersimpan.

                            </td>

                        </tr>


                    <?php endif; ?>


                </tbody>


            </table>


        </div>

        </div> <!-- end dashboard-recent-filter-wrap -->


    </section>


</div>


<!-- =========================================================
     DASHBOARD DATA UNTUK JAVASCRIPT
     ========================================================= -->

<script>

window.dashboardModernData = <?= json_encode(
    [
        'trend' =>
            $trendRows,

        'statuses' =>
            $statusRows,
    ],
    JSON_UNESCAPED_UNICODE
    | JSON_UNESCAPED_SLASHES
    | JSON_HEX_TAG
    | JSON_HEX_APOS
    | JSON_HEX_AMP
    | JSON_HEX_QUOT
) ?>;

</script>