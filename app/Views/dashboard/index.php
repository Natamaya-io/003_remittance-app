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

$mustChangePassword = (int) (
    $currentUser['must_change_password']
    ?? \App\Core\Auth::user()['must_change_password']
    ?? 0
) === 1;


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
        'title' => 'Laporan Bulanan',
        'description' => 'Import monitoring bulanan.',
        'icon' => 'bi-calendar2-data',
        'tone' => 'purple',
        'url' => url('/reports/create'),
    ];

}


if ($canComplaintManage) {

    $quickActions[] = [
        'title' => 'Buat Complain',
        'description' => 'Tarik data dari laporan.',
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
    '#315B8C',
    '#F2765E',
    '#413333',
    '#10b981',
    '#f59e0b',
    '#dc2626',
];


$fxBoardFile =
    dirname(__DIR__)
    . '/partials/fx-idr-board.php';

?>


<!-- =========================================================
     DASHBOARD
     ========================================================= -->

<div
    class="dashboard-modern w-full max-w-none"
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


            <?= render_heading_with_info(
                'Selamat datang, ' . e($userName),
                'Pantau transaksi, laporan, kurs referensi, complain, invoice, dan analisis keputusan melalui satu dashboard operasional.',
                [
                    'titleTag'     => 'h2',
                    'titleClass'   => 'm-0',
                    'wrapperClass' => 'inline-flex items-center gap-1.5',
                ]
            ) ?>


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

                        Import Laporan

                    </a>

                <?php endif; ?>


            </div>


        </div>


        <div class="dashboard-hero-visual" aria-hidden="true">
            <img
                class="dashboard-hero-illustration"
                src="<?= e(url('/main_card.svg')) ?>"
                alt="Ilustrasi Selamat Datang"
                width="240"
                height="215"
                loading="eager"
                decoding="async"
            >
        </div>


    </section>


    <!-- =====================================================
         QUICK ACTIONS
         ===================================================== -->

    <section
        class="dashboard-panel dashboard-quick-panel"
    >


        <header class="dashboard-section-head">


            <div>

                <?= render_heading_with_info(
                    'Pekerjaan operasional',
                    'Pintasan ringkas sesuai hak akses akun.',
                    [
                        'titleTag'     => 'h3',
                        'titleClass'   => 'm-0',
                        'wrapperClass' => 'inline-flex items-center gap-1.5',
                    ]
                ) ?>

            </div>

        </header>


        <div class="dashboard-quick-row">


            <?php foreach ($quickActions as $action): ?>

                <a
                    class="dashboard-quick-item"
                    href="<?= e(
                        (string) $action['url']
                    ) ?>"
                >

                    <i
                        class="
                            bi
                            <?= e(
                                (string) $action['icon']
                            ) ?>
                            text-gray-900
                            text-lg
                            leading-none
                            shrink-0
                        "
                        aria-hidden="true"
                    ></i>

                    <span class="dashboard-quick-copy">

                        <strong class="inline-flex items-center gap-1">

                            <?= e(
                                (string) $action['title']
                            ) ?>

                            <?php if (!empty($action['description'])): ?>

                                <?= render_tooltip(
                                    (string) $action['description'],
                                    '<span class="hs-tooltip-toggle info-toggle-btn cursor-help" onclick="event.stopPropagation(); event.preventDefault();" aria-label="Informasi ' . e((string) $action['title']) . '"><i class="bi bi-info-circle text-[11px] leading-none"></i></span>',
                                    ['placement' => 'top']
                                ) ?>

                            <?php endif; ?>

                        </strong>

                    </span>

                    <i
                        class="
                            bi
                            bi-arrow-up-right
                            dashboard-quick-arrow
                        "
                        aria-hidden="true"
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

                <i
                    class="
                        bi
                        <?= e(
                            (string) $card['icon']
                        ) ?>
                        text-gray-900
                        text-xl
                        leading-none
                        shrink-0
                    "
                    aria-hidden="true"
                ></i>

                <div>

                    <span class="inline-flex items-center gap-1">

                        <?= e(
                            (string) $card['label']
                        ) ?>

                        <?php if (!empty($card['description'])): ?>

                            <?= render_tooltip(
                                (string) $card['description'],
                                '<span class="hs-tooltip-toggle info-toggle-btn cursor-help" aria-label="Informasi ' . e((string) $card['label']) . '"><i class="bi bi-info-circle text-[11px] leading-none"></i></span>',
                                ['placement' => 'top']
                            ) ?>

                        <?php endif; ?>

                    </span>

                    <strong>

                        <?= number_format(
                            (int) $card['value'],
                            0,
                            ',',
                            '.'
                        ) ?>

                    </strong>

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

                    <?= render_heading_with_info(
                        'Tren Transaksi 14 Hari',
                        'Pergerakan jumlah transaksi berdasarkan tanggal.',
                        [
                            'titleTag'     => 'h3',
                            'titleClass'   => 'm-0',
                            'wrapperClass' => 'inline-flex items-center gap-1.5',
                        ]
                    ) ?>

                </div>


                <a
                    class="dashboard-small-link"
                    href="<?= e(
                        url('/reports')
                    ) ?>"
                >

                    Buka laporan

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

                    <?= render_heading_with_info(
                        'Distribusi Status',
                        'Komposisi transaksi selama 30 hari.',
                        [
                            'titleTag'     => 'h3',
                            'titleClass'   => 'm-0',
                            'wrapperClass' => 'inline-flex items-center gap-1.5',
                        ]
                    ) ?>

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
            '';

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

                <?= render_heading_with_info(
                    'Transaksi Terbaru',
                    'Aktivitas transaksi terbaru dalam sistem.',
                    [
                        'titleTag'     => 'h3',
                        'titleClass'   => 'm-0',
                        'wrapperClass' => 'inline-flex items-center gap-1.5',
                    ]
                ) ?>

            </div>


            <a
                class="dashboard-small-link"
                href="<?= e(
                    url('/reports')
                ) ?>"
            >

                Lihat semua

                <i class="bi bi-arrow-right"></i>

            </a>


        </header>


        <div
            class="
                table-responsive
                dashboard-table-wrap
            "
        >


            <table
                class="table modern-table"
                data-no-smart-table
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


                        <tr>


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


    </section>


</div>

<?php if ($mustChangePassword): ?>
<!-- =========================================================
     MODAL WAJIB PERBARUI KATA SANDI (DASHBOARD)
     ========================================================= -->
<div x-data="{
    showPasswordModal: (function() {
        try {
            return sessionStorage.getItem('remitgo_postpone_password_modal') !== '1';
        } catch (e) {
            return true;
        }
    })(),
    dismissModal() {
        this.showPasswordModal = false;
        try {
            sessionStorage.setItem('remitgo_postpone_password_modal', '1');
        } catch (e) {}
    }
}">
    <template x-teleport="body">
        <div
            x-show="showPasswordModal"
            x-cloak
            class="fixed inset-0 z-[9999] overflow-y-auto"
            role="dialog"
            aria-modal="true"
            aria-labelledby="modal-password-title"
            style="display: none;"
            @keydown.escape.window="dismissModal()"
        >
    <!-- 1. Full-screen Backdrop -->
    <div
        x-show="showPasswordModal"
        x-transition:enter="transition-opacity ease-out duration-150"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-in duration-100"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 w-full h-full bg-gray-900/60 backdrop-blur-sm"
        @click="dismissModal()"
        aria-hidden="true"
    ></div>

    <!-- 2. Modal Positioning Container (Terpusat) -->
    <div class="min-h-full flex items-center justify-center p-4 relative z-10 pointer-events-none">
        <div
            class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-gray-200 text-left relative my-8 pointer-events-auto overflow-hidden p-6 sm:p-7"
            x-show="showPasswordModal"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
        >
            <!-- Modal Header -->
            <div class="flex items-center justify-between gap-4 mb-5 pb-4 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <i class="bi bi-shield-lock text-2xl text-gray-800 shrink-0"></i>
                    <div class="flex items-center gap-2">
                        <h3 id="modal-password-title" class="text-base font-semibold font-heading text-gray-900 m-0">Perbarui Kata Sandi</h3>
                        <?= render_tooltip(
                            'Demi keamanan operasional, akun Anda wajib memperbarui kata sandi awal bawaan.',
                            '<span class="hs-tooltip-toggle info-toggle-btn cursor-help" aria-label="Info perbarui kata sandi"><i class="bi bi-info-circle text-xs leading-none"></i></span>',
                            ['placement' => 'top']
                        ) ?>
                    </div>
                </div>
                <button
                    type="button"
                    @click="dismissModal()"
                    class="modal-close"
                    aria-label="Tutup modal"
                    title="Tutup modal"
                >
                    <i class="bi bi-x-lg text-xs leading-none"></i>
                </button>
            </div>

            <!-- Form Ubah Password -->
            <form
                method="post"
                action="<?= url('/profile/password') ?>"
                class="space-y-4"
                x-data="{ showCurrent: false, showNew: false, showConfirm: false }"
                @submit="try { sessionStorage.removeItem('remitgo_postpone_password_modal'); } catch (e) {}"
            >
                <?= csrf_field() . method_field('PUT') ?>
                <input type="hidden" name="return_to" value="<?= e(url('/')) ?>">

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Kata Sandi Saat Ini</label>
                    <div class="relative flex items-center">
                        <input
                            :type="showCurrent ? 'text' : 'password'"
                            class="custom-form-input has-trailing-action"
                            name="current_password"
                            placeholder="Masukkan kata sandi saat ini"
                            required
                            autocomplete="current-password"
                        >
                        <i
                            x-show="!showCurrent"
                            @click="showCurrent = true"
                            role="button"
                            tabindex="0"
                            class="bi bi-eye absolute right-3 text-gray-400 hover:text-gray-700 cursor-pointer text-base select-none leading-none p-1 transition-colors"
                            aria-label="Tampilkan kata sandi"
                            title="Tampilkan kata sandi"
                        ></i>
                        <i
                            x-show="showCurrent"
                            x-cloak
                            @click="showCurrent = false"
                            role="button"
                            tabindex="0"
                            class="bi bi-eye-slash absolute right-3 text-gray-400 hover:text-gray-700 cursor-pointer text-base select-none leading-none p-1 transition-colors"
                            aria-label="Sembunyikan kata sandi"
                            title="Sembunyikan kata sandi"
                        ></i>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Kata Sandi Baru</label>
                    <div class="relative flex items-center">
                        <input
                            :type="showNew ? 'text' : 'password'"
                            class="custom-form-input has-trailing-action"
                            name="new_password"
                            placeholder="Minimal 8 karakter"
                            minlength="8"
                            required
                            autocomplete="new-password"
                        >
                        <i
                            x-show="!showNew"
                            @click="showNew = true"
                            role="button"
                            tabindex="0"
                            class="bi bi-eye absolute right-3 text-gray-400 hover:text-gray-700 cursor-pointer text-base select-none leading-none p-1 transition-colors"
                            aria-label="Tampilkan kata sandi"
                            title="Tampilkan kata sandi"
                        ></i>
                        <i
                            x-show="showNew"
                            x-cloak
                            @click="showNew = false"
                            role="button"
                            tabindex="0"
                            class="bi bi-eye-slash absolute right-3 text-gray-400 hover:text-gray-700 cursor-pointer text-base select-none leading-none p-1 transition-colors"
                            aria-label="Sembunyikan kata sandi"
                            title="Sembunyikan kata sandi"
                        ></i>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Konfirmasi Kata Sandi Baru</label>
                    <div class="relative flex items-center">
                        <input
                            :type="showConfirm ? 'text' : 'password'"
                            class="custom-form-input has-trailing-action"
                            name="new_password_confirmation"
                            placeholder="Ulangi kata sandi baru"
                            minlength="8"
                            required
                            autocomplete="new-password"
                        >
                        <i
                            x-show="!showConfirm"
                            @click="showConfirm = true"
                            role="button"
                            tabindex="0"
                            class="bi bi-eye absolute right-3 text-gray-400 hover:text-gray-700 cursor-pointer text-base select-none leading-none p-1 transition-colors"
                            aria-label="Tampilkan kata sandi"
                            title="Tampilkan kata sandi"
                        ></i>
                        <i
                            x-show="showConfirm"
                            x-cloak
                            @click="showConfirm = false"
                            role="button"
                            tabindex="0"
                            class="bi bi-eye-slash absolute right-3 text-gray-400 hover:text-gray-700 cursor-pointer text-base select-none leading-none p-1 transition-colors"
                            aria-label="Sembunyikan kata sandi"
                            title="Sembunyikan kata sandi"
                        ></i>
                    </div>
                </div>

                <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2">
                    <button
                        type="button"
                        @click="dismissModal()"
                        class="btn-secondary"
                    >
                        <span>Nanti Saja</span>
                    </button>
                    <button
                        type="submit"
                        class="btn-primary"
                    >
                        <i class="bi bi-shield-check"></i>
                        <span>Simpan Kata Sandi</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
        </div>
    </template>
</div>
<?php endif; ?>

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