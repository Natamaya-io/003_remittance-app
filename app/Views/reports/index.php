<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| REPORT HARIAN - INDEX
|--------------------------------------------------------------------------
|
| Halaman utama Report Harian.
|
| Hierarki:
| 1. Bulan
| 2. Tanggal
| 3. Tim dan Report
|
*/


/*
|--------------------------------------------------------------------------
| Data dari Controller
|--------------------------------------------------------------------------
|
| isset() digunakan supaya Intelephense tidak memberikan peringatan
| Undefined variable ketika file View dianalisis secara terpisah.
|
*/

$months = (
    isset($months)
    && is_array($months)
)
    ? $months
    : [];

$filters = (
    isset($filters)
    && is_array($filters)
)
    ? $filters
    : [];

$teams = (
    isset($teams)
    && is_array($teams)
)
    ? $teams
    : [];


/*
|--------------------------------------------------------------------------
| Nama Bulan Indonesia
|--------------------------------------------------------------------------
*/

$monthNames = [
    1 => 'Januari',
    2 => 'Februari',
    3 => 'Maret',
    4 => 'April',
    5 => 'Mei',
    6 => 'Juni',
    7 => 'Juli',
    8 => 'Agustus',
    9 => 'September',
    10 => 'Oktober',
    11 => 'November',
    12 => 'Desember',
];


/*
|--------------------------------------------------------------------------
| Filter Aktif
|--------------------------------------------------------------------------
*/

$activeAdvancedFilters = array_filter(
    $filters,
    static function (
        mixed $value,
        string|int $key
    ): bool {
        return (string) $key !== 'q'
            && $value !== ''
            && $value !== null;
    },
    ARRAY_FILTER_USE_BOTH
);

?>


<!-- =========================================================
     PAGE HEADING
     ========================================================= -->

<div class="page-heading">

    <div>

        <span class="eyebrow dark">
            REPORT OPERASIONAL
        </span>

        <h2>
            Report Harian
        </h2>

        <p>
            Alur report menggunakan tiga tingkat.
            Pilih bulan, pilih tanggal, lalu buka
            report dari setiap tim.
        </p>

    </div>


    <?php if (can('report.manage')): ?>

        <a
            class="btn btn-primary"
            href="<?= e(url('/reports/create')) ?>"
        >

            <i class="bi bi-cloud-arrow-up"></i>

            Upload Excel

        </a>

    <?php endif; ?>

</div>


<!-- =========================================================
     GLOBAL SEARCH
     ========================================================= -->

<section class="panel-card report-global-search mb-4">

    <form
        method="get"
        action="<?= e(url('/reports/search')) ?>"
    >

        <div class="global-search-copy">

            <strong>
                Pencarian global
            </strong>

            <span>
                Mencari seluruh bulan, tanggal,
                tim, file, payer, beneficiary,
                bank, SWIFT, invoice, kontrak,
                dan currency.
            </span>

        </div>


        <div class="global-search-input">

            <i class="bi bi-search"></i>

            <input
                class="form-control"
                type="search"
                name="q"
                required
                autocomplete="off"
                placeholder="Cari seluruh data report..."
                value="<?= e(
                    (string) (
                        $filters['q']
                        ?? ''
                    )
                ) ?>"
            >

        </div>


        <button
            class="btn btn-primary"
            type="submit"
        >

            <i class="bi bi-search"></i>

            Cari Keseluruhan

        </button>

    </form>

</section>


<!-- =========================================================
     ADVANCED FILTER
     ========================================================= -->

<details
    class="panel-card report-filter-panel mb-4"
    <?= $activeAdvancedFilters !== [] ? 'open' : '' ?>
>

    <summary>

        <span>

            <i class="bi bi-sliders"></i>

            Filter lanjutan

        </span>

        <small>
            Bulan, rentang tanggal, tim,
            status kirim, dan hasil
        </small>

    </summary>


    <form
        class="report-advanced-grid"
        method="get"
        action="<?= e(url('/reports')) ?>"
    >


        <!-- BULAN -->

        <label>

            <span>
                Bulan report
            </span>

            <input
                class="form-control"
                type="month"
                name="month"
                value="<?= e(
                    (string) (
                        $filters['month']
                        ?? ''
                    )
                ) ?>"
            >

        </label>


        <!-- TANGGAL MULAI -->

        <label>

            <span>
                Tanggal mulai
            </span>

            <input
                class="form-control"
                type="date"
                name="date_from"
                value="<?= e(
                    (string) (
                        $filters['date_from']
                        ?? ''
                    )
                ) ?>"
            >

        </label>


        <!-- TANGGAL AKHIR -->

        <label>

            <span>
                Tanggal akhir
            </span>

            <input
                class="form-control"
                type="date"
                name="date_to"
                value="<?= e(
                    (string) (
                        $filters['date_to']
                        ?? ''
                    )
                ) ?>"
            >

        </label>


        <!-- TIM -->

        <label>

            <span>
                Tim
            </span>

            <select
                class="form-select"
                name="team"
            >

                <option value="">
                    Semua tim
                </option>


                <?php foreach ($teams as $team): ?>

                    <?php

                    $teamValue = trim(
                        (string) $team
                    );

                    if ($teamValue === '') {
                        continue;
                    }

                    ?>

                    <option
                        value="<?= e($teamValue) ?>"
                        <?= selected(
                            (string) (
                                $filters['team']
                                ?? ''
                            ),
                            $teamValue
                        ) ?>
                    >

                        <?= e($teamValue) ?>

                    </option>

                <?php endforeach; ?>

            </select>

        </label>


        <!-- STATUS KIRIM -->

        <label>

            <span>
                Status kirim
            </span>

            <select
                class="form-select"
                name="delivery_status"
            >

                <option value="">
                    Semua status
                </option>

                <option
                    value="PENDING"
                    <?= selected(
                        (string) (
                            $filters['delivery_status']
                            ?? ''
                        ),
                        'PENDING'
                    ) ?>
                >
                    Pending
                </option>

                <option
                    value="SENT"
                    <?= selected(
                        (string) (
                            $filters['delivery_status']
                            ?? ''
                        ),
                        'SENT'
                    ) ?>
                >
                    Terkirim
                </option>

            </select>

        </label>


        <!-- HASIL -->

        <label>

            <span>
                Hasil
            </span>

            <select
                class="form-select"
                name="result_status"
            >

                <option value="">
                    Semua hasil
                </option>

                <option
                    value="PENDING"
                    <?= selected(
                        (string) (
                            $filters['result_status']
                            ?? ''
                        ),
                        'PENDING'
                    ) ?>
                >
                    Pending
                </option>

                <option
                    value="SUCCESS"
                    <?= selected(
                        (string) (
                            $filters['result_status']
                            ?? ''
                        ),
                        'SUCCESS'
                    ) ?>
                >
                    Success
                </option>

                <option
                    value="FAILED"
                    <?= selected(
                        (string) (
                            $filters['result_status']
                            ?? ''
                        ),
                        'FAILED'
                    ) ?>
                >
                    Failed
                </option>

            </select>

        </label>


        <!-- BUTTON -->

        <div class="filter-actions report-filter-actions">

            <button
                class="btn btn-primary"
                type="submit"
            >

                <i class="bi bi-funnel"></i>

                Terapkan

            </button>


            <a
                class="btn btn-light"
                href="<?= e(url('/reports')) ?>"
            >

                <i class="bi bi-arrow-counterclockwise"></i>

                Reset

            </a>

        </div>

    </form>

</details>


<!-- =========================================================
     HIERARCHY
     ========================================================= -->

<div class="report-hierarchy-note mb-4">

    <span class="active">

        <b>1</b>

        Bulan

    </span>


    <i class="bi bi-chevron-right"></i>


    <span>

        <b>2</b>

        Tanggal

    </span>


    <i class="bi bi-chevron-right"></i>


    <span>

        <b>3</b>

        Tim dan Report

    </span>

</div>


<!-- =========================================================
     MONTH GRID
     ========================================================= -->

<div class="report-month-grid">


    <?php foreach ($months as $month): ?>


        <?php

        /*
        |--------------------------------------------------------------------------
        | Validasi Data Bulan
        |--------------------------------------------------------------------------
        */

        if (!is_array($month)) {
            continue;
        }


        $monthKey = trim(
            (string) (
                $month['month_key']
                ?? ''
            )
        );


        /*
         * Format wajib YYYY-MM
         */

        if (
            preg_match(
                '/^\d{4}-\d{2}$/',
                $monthKey
            ) !== 1
        ) {
            continue;
        }


        $monthNumber = (int) substr(
            $monthKey,
            5,
            2
        );


        $year = substr(
            $monthKey,
            0,
            4
        );


        $monthLabel = (
            $monthNames[$monthNumber]
            ?? $monthKey
        )
            . ' '
            . $year;


        /*
        |--------------------------------------------------------------------------
        | Statistik
        |--------------------------------------------------------------------------
        */

        $total = (int) (
            $month['total_rows']
            ?? 0
        );


        $sent = (int) (
            $month['sent_rows']
            ?? 0
        );


        $pending = (int) (
            $month['pending_rows']
            ?? 0
        );


        $skipped = (int) (
            $month['skipped_rows']
            ?? 0
        );


        $fileCount = (int) (
            $month['file_count']
            ?? 0
        );


        $dateCount = (int) (
            $month['date_count']
            ?? 0
        );


        $teamCount = (int) (
            $month['team_count']
            ?? 0
        );


        /*
        |--------------------------------------------------------------------------
        | Progress
        |--------------------------------------------------------------------------
        */

        $progress = $total > 0
            ? min(
                100,
                max(
                    0,
                    (int) round(
                        ($sent / $total)
                        * 100
                    )
                )
            )
            : 0;


        /*
        |--------------------------------------------------------------------------
        | Filter diteruskan ke halaman bulan
        |--------------------------------------------------------------------------
        */

        $query = array_filter(
            [
                'team' =>
                    (string) (
                        $filters['team']
                        ?? ''
                    ),

                'date_from' =>
                    (string) (
                        $filters['date_from']
                        ?? ''
                    ),

                'date_to' =>
                    (string) (
                        $filters['date_to']
                        ?? ''
                    ),

                'delivery_status' =>
                    (string) (
                        $filters['delivery_status']
                        ?? ''
                    ),

                'result_status' =>
                    (string) (
                        $filters['result_status']
                        ?? ''
                    ),
            ],
            static fn(
                mixed $value
            ): bool => trim(
                (string) $value
            ) !== ''
        );


        /*
        |--------------------------------------------------------------------------
        | URL Bulan
        |--------------------------------------------------------------------------
        */

        $monthUrl =
            '/reports/month/'
            . rawurlencode($monthKey);


        if ($query !== []) {

            $monthUrl .= '?'
                . http_build_query($query);

        }


        /*
        |--------------------------------------------------------------------------
        | Last Upload
        |--------------------------------------------------------------------------
        */

        $lastUpload = isset(
            $month['last_upload']
        )
            ? (string) $month['last_upload']
            : null;

        ?>


        <!-- =================================================
             MONTH CARD
             ================================================= -->

        <article class="report-month-card">


            <!-- ICON -->

            <div class="report-card-icon">

                <i class="bi bi-calendar3"></i>

            </div>


            <!-- MONTH CODE -->

            <span class="report-card-code">

                <?= e($monthKey) ?>

            </span>


            <!-- TITLE -->

            <h3>

                <?= e($monthLabel) ?>

            </h3>


            <!-- SUBTITLE -->

            <p>

                <?= number_format(
                    $fileCount,
                    0,
                    ',',
                    '.'
                ) ?>

                file

                ·

                <?= number_format(
                    $dateCount,
                    0,
                    ',',
                    '.'
                ) ?>

                tanggal

                ·

                <?= number_format(
                    $teamCount,
                    0,
                    ',',
                    '.'
                ) ?>

                tim

            </p>


            <!-- PROGRESS -->

            <div
                class="report-progress"
                title="<?= e(
                    (string) $progress
                ) ?>% terkirim"
            >

                <span
                    style="width: <?= $progress ?>%"
                ></span>

            </div>


            <!-- STATISTICS -->

            <div class="report-card-stats four">


                <!-- TOTAL -->

                <div>

                    <strong>

                        <?= number_format(
                            $total,
                            0,
                            ',',
                            '.'
                        ) ?>

                    </strong>

                    <small>
                        Total baris
                    </small>

                </div>


                <!-- SENT -->

                <div>

                    <strong>

                        <?= number_format(
                            $sent,
                            0,
                            ',',
                            '.'
                        ) ?>

                    </strong>

                    <small>
                        Terkirim
                    </small>

                </div>


                <!-- PENDING -->

                <div>

                    <strong>

                        <?= number_format(
                            $pending,
                            0,
                            ',',
                            '.'
                        ) ?>

                    </strong>

                    <small>
                        Pending
                    </small>

                </div>


                <!-- SKIPPED -->

                <div>

                    <strong>

                        <?= number_format(
                            $skipped,
                            0,
                            ',',
                            '.'
                        ) ?>

                    </strong>

                    <small>
                        Dilewati
                    </small>

                </div>


            </div>


            <!-- CARD FOOTER -->

            <div class="report-card-footer">


                <span>

                    Upload terakhir

                    <strong>

                        <?= e(
                            format_datetime(
                                $lastUpload
                            )
                        ) ?>

                    </strong>

                </span>


                <a
                    class="btn btn-primary btn-sm"
                    href="<?= e(
                        url($monthUrl)
                    ) ?>"
                >

                    Lihat Tanggal

                    <i class="bi bi-arrow-right"></i>

                </a>


            </div>


        </article>


    <?php endforeach; ?>


    <!-- =====================================================
         EMPTY STATE
         ===================================================== -->

    <?php if ($months === []): ?>


        <div class="empty-card">


            <i class="bi bi-calendar-x"></i>


            <h3>

                Tidak ada bulan yang cocok

            </h3>


            <p>

                Ubah filter atau upload report
                baru untuk mulai mengisi data.

            </p>


            <?php if (can('report.manage')): ?>


                <a
                    class="btn btn-primary"
                    href="<?= e(
                        url('/reports/create')
                    ) ?>"
                >

                    <i class="bi bi-cloud-arrow-up"></i>

                    Upload Report

                </a>


            <?php endif; ?>


        </div>


    <?php endif; ?>


</div>