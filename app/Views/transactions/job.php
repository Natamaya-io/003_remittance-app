<?php

declare(strict_types=1);


/*
|--------------------------------------------------------------------------
| VIEW DATA
|--------------------------------------------------------------------------
*/

$viewData = get_defined_vars();


$job = isset($viewData['job']) && is_array($viewData['job'])
    ? $viewData['job']
    : [];


$transactions = isset($viewData['transactions'])
    && is_array($viewData['transactions'])
        ? $viewData['transactions']
        : [];


$stage2Rows = isset($viewData['stage2Rows'])
    && is_array($viewData['stage2Rows'])
        ? $viewData['stage2Rows']
        : [];


$stage3Rows = isset($viewData['stage3Rows'])
    && is_array($viewData['stage3Rows'])
        ? $viewData['stage3Rows']
        : [];


$outputHeaders = isset($viewData['outputHeaders'])
    && is_array($viewData['outputHeaders'])
        ? $viewData['outputHeaders']
        : [];


unset($viewData);


/*
|--------------------------------------------------------------------------
| JOB DEFAULTS
|--------------------------------------------------------------------------
*/

$job = array_replace(
    [
        'id' => 0,
        'team_no' => 0,
        'name' => 'Batch Transaksi',
        'source_filename' => '-',
        'source_sheet' => '-',
        'output_email' => '',
        'total_rows' => 0,
        'imported_rows' => 0,
        'skipped_rows' => 0,
        'status' => 'READY',
        'validation_json' => '',
    ],
    $job
);


/*
|--------------------------------------------------------------------------
| VALIDATION
|--------------------------------------------------------------------------
*/

$validation = json_decode(
    (string) $job['validation_json'],
    true
);


if (!is_array($validation)) {
    $validation = [];
}


$validationIssues =
    $validation['issues']
    ?? [];


if (!is_array($validationIssues)) {
    $validationIssues = [];
}


$jobId =
    (int) $job['id'];


$totalRows =
    (int) $job['total_rows'];


$importedRows =
    (int) $job['imported_rows'];


$skippedRows =
    (int) $job['skipped_rows'];


$outputCount =
    count(
        $outputHeaders
    );

?>


<div class="transaction-job-page">

    <!-- =====================================================
         BATCH WORKFLOW
    ====================================================== -->

    <section class="job-workflow-banner">

        <div class="job-workflow-copy">

            <span class="job-workflow-kicker">
                BATCH TRANSAKSI T<?= (int) $job['team_no'] ?>
            </span>

            <h2>
                <?= e((string) $job['name']) ?>
            </h2>

            <p>

                <?= e((string) $job['source_filename']) ?>

                <span>·</span>

                <?= e((string) $job['source_sheet']) ?>

                <span>·</span>

                <?= $importedRows ?>

                transaksi tersimpan.

            </p>

        </div>


        <div class="job-workflow-steps">

            <div class="job-workflow-step completed">

                <b>1</b>

                <span>
                    Input selesai
                </span>

            </div>


            <div class="job-workflow-step completed">

                <b>2</b>

                <span>
                    Tabel terbentuk
                </span>

            </div>


            <div class="job-workflow-step completed final">

                <b>3</b>

                <span>
                    Tanda baca dibersihkan
                </span>

            </div>


            <div class="job-workflow-step">

                <b>4</b>

                <span>
                    Download
                </span>

            </div>

        </div>

    </section>


    <!-- =====================================================
         PAGE HEADER
    ====================================================== -->

    <section class="job-page-header">

        <div class="job-page-title">

            <span class="eyebrow dark">
                HASIL OPERASI TRANSAKSI
            </span>

            <h2>
                <?= e((string) $job['name']) ?>
            </h2>

            <p>
                Review hasil Tahap 1, Tahap 2, dan Tahap 3 di bawah.
                File final menggunakan isi Tahap 3, sedangkan nama kolom
                tetap mengikuti struktur Tahap 2.
            </p>

        </div>


        <div class="job-header-actions">

            <a
                class="btn btn-light"
                href="<?= url('/transactions/bri') ?>"
            >
                <i class="bi bi-arrow-left"></i>
                Input Baru
            </a>


            <a
                class="btn btn-light"
                href="<?= url(
                    '/transactions/jobs/' .
                    $jobId .
                    '/export-xlsx?stage=2'
                ) ?>"
            >
                <i class="bi bi-file-earmark-excel"></i>
                Tahap 2 XLSX
            </a>


            <a
                class="btn btn-primary"
                href="<?= url(
                    '/transactions/jobs/' .
                    $jobId .
                    '/export-xlsx'
                ) ?>"
            >
                <i class="bi bi-download"></i>
                Download Final XLSX
            </a>


            <a
                class="btn btn-secondary"
                href="<?= url(
                    '/transactions/jobs/' .
                    $jobId .
                    '/export-xls'
                ) ?>"
            >
                <i class="bi bi-download"></i>
                Final XLS
            </a>

        </div>

    </section>


    <!-- =====================================================
         STATS
    ====================================================== -->

    <section class="job-stat-grid">

        <article class="job-stat-card purple">

            <span>
                Total sumber
            </span>

            <strong>
                <?= $totalRows ?>
            </strong>

            <small>
                Baris dibaca
            </small>

        </article>


        <article class="job-stat-card green">

            <span>
                Berhasil masuk
            </span>

            <strong>
                <?= $importedRows ?>
            </strong>

            <small>
                Transaksi valid
            </small>

        </article>


        <article class="job-stat-card orange">

            <span>
                Dilewati
            </span>

            <strong>
                <?= $skippedRows ?>
            </strong>

            <small>
                Invalid / duplikat
            </small>

        </article>


        <article class="job-stat-card pink">

            <span>
                Kolom output
            </span>

            <strong>
                <?= $outputCount ?>
            </strong>

            <small>
                Struktur final
            </small>

        </article>

    </section>


    <!-- =====================================================
         BATCH INFORMATION
    ====================================================== -->

    <section class="job-info-card">

        <div class="job-section-header">

            <div>

                <span class="job-section-index">
                    INFO
                </span>

                <div>

                    <h3>
                        Informasi batch
                    </h3>

                    <p>
                        Informasi sumber dan konfigurasi proses transaksi.
                    </p>

                </div>

            </div>

        </div>


        <div class="job-info-grid">

            <div>

                <span>Nama batch</span>

                <strong>
                    <?= e((string) $job['name']) ?>
                </strong>

            </div>


            <div>

                <span>Tim</span>

                <strong>
                    Tim <?= (int) $job['team_no'] ?>
                </strong>

            </div>


            <div>

                <span>Sumber</span>

                <strong>
                    <?= e((string) $job['source_filename']) ?>
                </strong>

            </div>


            <div>

                <span>Sheet</span>

                <strong>
                    <?= e((string) $job['source_sheet']) ?>
                </strong>

            </div>


            <div>

                <span>Email output</span>

                <strong>
                    <?= e(
                        (string) (
                            $job['output_email']
                            ?: '-'
                        )
                    ) ?>
                </strong>

            </div>


            <div>

                <span>Status</span>

                <strong class="job-ready-status">
                    <?= e((string) $job['status']) ?>
                </strong>

            </div>

        </div>

    </section>


    <!-- =====================================================
         VALIDATION ISSUES
    ====================================================== -->

    <?php if ($validationIssues !== []): ?>

        <section class="job-warning-card">

            <div>

                <i class="bi bi-exclamation-triangle"></i>

                <div>

                    <strong>
                        Catatan validasi
                    </strong>

                    <p>
                        Beberapa baris sumber dilewati saat proses import.
                    </p>

                </div>

            </div>


            <ul>

                <?php foreach (
                    array_slice(
                        $validationIssues,
                        0,
                        10
                    )
                    as $issue
                ): ?>

                    <li>
                        <?= e(
                            is_string($issue)
                                ? $issue
                                : json_encode(
                                    $issue,
                                    JSON_UNESCAPED_UNICODE
                                )
                        ) ?>
                    </li>

                <?php endforeach; ?>

            </ul>

        </section>

    <?php endif; ?>


    <!-- =====================================================
         STAGE 1
    ====================================================== -->

    <section class="job-stage-card">

        <div class="job-stage-header">

            <div class="job-stage-title">

                <span class="job-stage-number stage-one">
                    1
                </span>

                <div>

                    <span class="job-stage-kicker">
                        TAHAP 1
                    </span>

                    <h3>
                        Data transaksi sumber
                    </h3>

                    <p>
                        Data valid yang berhasil masuk ke database transaksi.
                    </p>

                </div>

            </div>


            <span class="job-stage-count">
                <?= count($transactions) ?>
                baris
            </span>

        </div>


        <div class="job-table-scroll">

            <table class="job-data-table">

                <thead>

                    <tr>

                        <th>No.</th>

                        <th>
                            Date of Transaction
                        </th>

                        <th>
                            Bill To (Payer)
                        </th>

                        <th>
                            Beneficiary Name
                        </th>

                        <th>
                            Account number
                        </th>

                        <th>
                            Amounts
                        </th>

                        <th>
                            Currency
                        </th>

                        <th>
                            Swift
                        </th>

                        <th>
                            No inv
                        </th>

                    </tr>

                </thead>


                <tbody>

                <?php foreach (
                    $transactions
                    as $transaction
                ): ?>

                    <tr>

                        <td>
                            <?= e(
                                (string) (
                                    $transaction['sequence_no']
                                    ?? ''
                                )
                            ) ?>
                        </td>


                        <td>
                            <?= e(
                                (string) (
                                    $transaction['transaction_date']
                                    ?? ''
                                )
                            ) ?>
                        </td>


                        <td>
                            <?= e(
                                (string) (
                                    $transaction['company_name']
                                    ?? ''
                                )
                            ) ?>
                        </td>


                        <td>
                            <?= e(
                                (string) (
                                    $transaction['beneficiary_name']
                                    ?? ''
                                )
                            ) ?>
                        </td>


                        <td>
                            <?= e(
                                (string) (
                                    $transaction['beneficiary_account']
                                    ?? ''
                                )
                            ) ?>
                        </td>


                        <td>
                            <?= e(
                                (string) (
                                    $transaction['amount']
                                    ?? ''
                                )
                            ) ?>
                        </td>


                        <td>
                            <?= e(
                                (string) (
                                    $transaction['currency']
                                    ?? ''
                                )
                            ) ?>
                        </td>


                        <td>
                            <?= e(
                                (string) (
                                    $transaction['swift']
                                    ?? ''
                                )
                            ) ?>
                        </td>


                        <td>
                            <?= e(
                                (string) (
                                    $transaction['invoice_no']
                                    ?? ''
                                )
                            ) ?>
                        </td>

                    </tr>

                <?php endforeach; ?>


                <?php if ($transactions === []): ?>

                    <tr>

                        <td
                            colspan="9"
                            class="job-empty-state"
                        >
                            Tidak ada data Tahap 1.
                        </td>

                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </section>


    <!-- =====================================================
         STAGE 2
    ====================================================== -->

    <section class="job-stage-card">

        <div class="job-stage-header">

            <div class="job-stage-title">

                <span class="job-stage-number stage-two">
                    2
                </span>

                <div>

                    <span class="job-stage-kicker">
                        TAHAP 2
                    </span>

                    <h3>
                        Tabel gabungan
                    </h3>

                    <p>
                        Data Tahap 1 dibentuk menjadi struktur output operasional.
                    </p>

                </div>

            </div>


            <div class="job-stage-actions">

                <span class="job-stage-count">
                    <?= count($stage2Rows) ?>
                    baris
                </span>


                <a
                    class="btn btn-light btn-sm"
                    href="<?= url(
                        '/transactions/jobs/' .
                        $jobId .
                        '/export-xlsx?stage=2'
                    ) ?>"
                >
                    <i class="bi bi-download"></i>
                    XLSX
                </a>

            </div>

        </div>


        <div class="job-table-scroll">

            <table class="job-data-table output-table">

                <thead>

                    <tr>

                        <?php foreach ($outputHeaders as $header): ?>

                            <th>
                                <?= e((string) $header) ?>
                            </th>

                        <?php endforeach; ?>

                    </tr>

                </thead>


                <tbody>

                <?php foreach ($stage2Rows as $row): ?>

                    <tr>

                        <?php foreach (
                            $outputHeaders
                            as $columnIndex => $header
                        ): ?>

                            <td>
                                <?= e(
                                    (string) (
                                        $row[$columnIndex]
                                        ?? ''
                                    )
                                ) ?>
                            </td>

                        <?php endforeach; ?>

                    </tr>

                <?php endforeach; ?>


                <?php if ($stage2Rows === []): ?>

                    <tr>

                        <td
                            colspan="<?= max(
                                1,
                                count($outputHeaders)
                            ) ?>"
                            class="job-empty-state"
                        >
                            Belum ada data Tahap 2.
                        </td>

                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </section>


    <!-- =====================================================
         STAGE 3
    ====================================================== -->

    <section class="job-stage-card final-stage-card">

        <div class="job-stage-header">

            <div class="job-stage-title">

                <span class="job-stage-number stage-three">
                    3
                </span>

                <div>

                    <span class="job-stage-kicker">
                        TAHAP 3
                    </span>

                    <h3>
                        Final Format BRI (Excel Ready)
                    </h3>

                    <p>
                        Seluruh tanda baca pada isi data dihapus.
                        Header tabel tetap menggunakan struktur Tahap 2.
                    </p>

                </div>

            </div>


            <div class="job-stage-actions">

                <span class="job-stage-count final">
                    <?= count($stage3Rows) ?>
                    baris final
                </span>


                <a
                    class="btn btn-primary btn-sm"
                    href="<?= url(
                        '/transactions/jobs/' .
                        $jobId .
                        '/export-xlsx'
                    ) ?>"
                >
                    <i class="bi bi-download"></i>
                    XLSX
                </a>

            </div>

        </div>


        <div class="job-table-scroll">

            <table class="job-data-table output-table final-table">

                <thead>

                    <tr>

                        <?php foreach ($outputHeaders as $header): ?>

                            <th>
                                <?= e((string) $header) ?>
                            </th>

                        <?php endforeach; ?>

                    </tr>

                </thead>


                <tbody>

                <?php foreach ($stage3Rows as $row): ?>

                    <tr>

                        <?php foreach (
                            $outputHeaders
                            as $columnIndex => $header
                        ): ?>

                            <td>
                                <?= e(
                                    (string) (
                                        $row[$columnIndex]
                                        ?? ''
                                    )
                                ) ?>
                            </td>

                        <?php endforeach; ?>

                    </tr>

                <?php endforeach; ?>


                <?php if ($stage3Rows === []): ?>

                    <tr>

                        <td
                            colspan="<?= max(
                                1,
                                count($outputHeaders)
                            ) ?>"
                            class="job-empty-state"
                        >
                            Belum ada data final.
                        </td>

                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </section>


    <!-- =====================================================
         FINAL DOWNLOAD
    ====================================================== -->

    <section class="job-download-card">

        <div class="job-download-icon">

            <i class="bi bi-file-earmark-check"></i>

        </div>


        <div class="job-download-copy">

            <span>
                TAHAP 4
            </span>

            <h3>
                File final siap digunakan
            </h3>

            <p>
                Download XLSX untuk format modern atau XLS untuk kebutuhan
                kompatibilitas sistem lama.
            </p>

        </div>


        <div class="job-download-actions">

            <a
                class="btn btn-primary"
                href="<?= url(
                    '/transactions/jobs/' .
                    $jobId .
                    '/export-xlsx'
                ) ?>"
            >
                <i class="bi bi-file-earmark-excel"></i>
                Download Final XLSX
            </a>


            <a
                class="btn btn-secondary"
                href="<?= url(
                    '/transactions/jobs/' .
                    $jobId .
                    '/export-xls'
                ) ?>"
            >
                <i class="bi bi-file-earmark-spreadsheet"></i>
                Download Final XLS
            </a>

        </div>

    </section>

</div>


<style>

/* =========================================================
   JOB PAGE
========================================================= */

.transaction-job-page {

    display:
        flex;

    flex-direction:
        column;

    gap:
        24px;
}


/* =========================================================
   WORKFLOW
========================================================= */

.job-workflow-banner {

    display:
        grid;

    grid-template-columns:
        minmax(0, 1fr)
        minmax(500px, .95fr);

    align-items:
        center;

    gap:
        34px;

    padding:
        32px 36px;

    border:
        1px solid
        rgba(
            224,
            195,
            228,
            .72
        );

    border-radius:
        28px;

    background:

        linear-gradient(
            112deg,
            #ffdbea,
            #f1dcff 54%,
            #ccecff
        );

    box-shadow:
        0 16px 40px
        rgba(
            93,
            50,
            108,
            .07
        );
}


.job-workflow-kicker {

    display:
        block;

    margin-bottom:
        10px;

    color:
        #ffffff;

    font-size:
        12px;

    font-weight:
        800;

    letter-spacing:
        .15em;
}


.job-workflow-copy h2 {

    margin:
        0;

    color:
        #4c2f58;

    font-size:
        26px;

    font-weight:
        850;

    letter-spacing:
        -.03em;
}


.job-workflow-copy p {

    margin:
        8px 0 0;

    color:
        #745c79;

    font-size:
        14px;
}


.job-workflow-copy p span {

    margin:
        0 5px;

    color:
        #ad76ae;
}


.job-workflow-steps {

    display:
        grid;

    grid-template-columns:
        repeat(
            2,
            minmax(0, 1fr)
        );

    gap:
        10px;
}


.job-workflow-step {

    display:
        flex;

    align-items:
        center;

    gap:
        11px;

    min-height:
        52px;

    padding:
        9px 14px;

    color:
        #66506c;

    background:
        rgba(
            255,
            255,
            255,
            .72
        );

    border:
        1px solid
        rgba(
            255,
            255,
            255,
            .85
        );

    border-radius:
        999px;

    font-size:
        13px;

    font-weight:
        700;
}


.job-workflow-step b {

    display:
        inline-flex;

    align-items:
        center;

    justify-content:
        center;

    width:
        32px;

    height:
        32px;

    flex:
        0 0 32px;

    border-radius:
        50%;

    color:
        #7750c8;

    background:
        rgba(
            120,
            74,
            211,
            .09
        );
}


.job-workflow-step.completed {

    color:
        #15795d;

    background:
        #e7faf2;
}


.job-workflow-step.completed b {

    color:
        #15795d;

    background:
        #d4f5e8;
}


.job-workflow-step.final {

    color:
        #ffffff;

    background:

        linear-gradient(
            105deg,
            #7442dc,
            #a744d6,
            #eb408c
        );

    border-color:
        transparent;
}


.job-workflow-step.final b {

    color:
        #ffffff;

    background:
        rgba(
            255,
            255,
            255,
            .20
        );
}


/* =========================================================
   HEADER
========================================================= */

.job-page-header {

    display:
        grid;

    grid-template-columns:
        minmax(0, 1fr)
        auto;

    align-items:
        center;

    gap:
        32px;
}


.job-page-title {

    max-width:
        900px;
}


.job-page-title h2 {

    margin:
        8px 0 9px;

    color:
        #4b3056;

    font-size:
        clamp(
            30px,
            3vw,
            42px
        );

    font-weight:
        850;

    line-height:
        1.12;

    letter-spacing:
        -.04em;
}


.job-page-title p {

    margin:
        0;

    color:
        #817084;

    font-size:
        15px;

    line-height:
        1.6;
}


.job-header-actions {

    display:
        grid;

    grid-template-columns:
        repeat(
            2,
            max-content
        );

    gap:
        10px;

    justify-content:
        end;
}


/* =========================================================
   STATISTICS
========================================================= */

.job-stat-grid {

    display:
        grid;

    grid-template-columns:
        repeat(
            4,
            minmax(0, 1fr)
        );

    gap:
        14px;
}


.job-stat-card {

    display:
        flex;

    flex-direction:
        column;

    justify-content:
        center;

    min-height:
        132px;

    padding:
        22px 24px;

    color:
        #ffffff;

    border-radius:
        20px;

    box-shadow:
        0 10px 25px
        rgba(
            94,
            49,
            108,
            .11
        );
}


.job-stat-card.purple {

    background:

        linear-gradient(
            135deg,
            #8250dc,
            #9650d3
        );
}


.job-stat-card.green {

    background:

        linear-gradient(
            135deg,
            #2bc89a,
            #31b9a1
        );
}


.job-stat-card.orange {

    background:

        linear-gradient(
            135deg,
            #ffb03d,
            #ff8741
        );
}


.job-stat-card.pink {

    background:

        linear-gradient(
            135deg,
            #eb408b,
            #d6469f
        );
}


.job-stat-card span {

    color:
        rgba(
            255,
            255,
            255,
            .96
        );

    font-size:
        12px;

    font-weight:
        700;
}


.job-stat-card strong {

    margin:
        9px 0 5px;

    color:
        #ffffff;

    font-size:
        30px;

    font-weight:
        850;

    line-height:
        1;

    text-shadow:
        0 1px 3px
        rgba(
            44,
            18,
            52,
            .18
        );
}


.job-stat-card small {

    color:
        rgba(
            255,
            255,
            255,
            .91
        );

    font-weight:
        550;
}


/* =========================================================
   INFO / STAGE CARD
========================================================= */

.job-info-card,
.job-stage-card {

    padding:
        26px;

    border:
        1px solid
        #eaddeb;

    border-radius:
        22px;

    background:
        #ffffff;

    box-shadow:
        0 10px 30px
        rgba(
            94,
            54,
            106,
            .055
        );
}


.job-section-header {

    margin-bottom:
        20px;
}


.job-section-header > div {

    display:
        flex;

    align-items:
        center;

    gap:
        13px;
}


.job-section-index {

    display:
        inline-flex;

    align-items:
        center;

    justify-content:
        center;

    min-width:
        48px;

    height:
        42px;

    padding:
        0 10px;

    border-radius:
        12px;

    color:
        #7741d2;

    background:
        #eee3ff;

    font-size:
        10px;

    font-weight:
        800;
}


.job-section-header h3 {

    margin:
        0;

    color:
        #4c3456;

    font-size:
        18px;
}


.job-section-header p {

    margin:
        3px 0 0;

    color:
        #89798d;

    font-size:
        12px;
}


/* =========================================================
   INFO GRID
========================================================= */

.job-info-grid {

    display:
        grid;

    grid-template-columns:
        repeat(
            3,
            minmax(0, 1fr)
        );

    gap:
        12px;
}


.job-info-grid > div {

    display:
        flex;

    flex-direction:
        column;

    gap:
        5px;

    min-width:
        0;

    padding:
        14px 16px;

    border:
        1px solid
        #eee3ef;

    border-radius:
        15px;

    background:
        #fffafd;
}


.job-info-grid span {

    color:
        #8d7e90;

    font-size:
        11px;
}


.job-info-grid strong {

    overflow:
        hidden;

    color:
        #4d3855;

    font-size:
        13px;

    text-overflow:
        ellipsis;

    white-space:
        nowrap;
}


.job-ready-status {

    color:
        #18795e !important;
}


/* =========================================================
   WARNING
========================================================= */

.job-warning-card {

    padding:
        20px 22px;

    border:
        1px solid
        #f2d9b4;

    border-radius:
        20px;

    color:
        #83591f;

    background:
        #fff7e9;
}


.job-warning-card > div {

    display:
        flex;

    align-items:
        center;

    gap:
        13px;
}


.job-warning-card > div > i {

    font-size:
        22px;

    color:
        #e78a1b;
}


.job-warning-card strong {

    color:
        #714719;
}


.job-warning-card p {

    margin:
        3px 0 0;

    font-size:
        12px;
}


.job-warning-card ul {

    margin:
        15px 0 0;

    padding-left:
        22px;

    font-size:
        12px;
}


/* =========================================================
   STAGE HEADER
========================================================= */

.job-stage-header {

    display:
        flex;

    align-items:
        center;

    justify-content:
        space-between;

    gap:
        20px;

    margin-bottom:
        20px;
}


.job-stage-title {

    display:
        flex;

    align-items:
        center;

    gap:
        14px;
}


.job-stage-number {

    display:
        inline-flex;

    align-items:
        center;

    justify-content:
        center;

    width:
        46px;

    height:
        46px;

    flex:
        0 0 46px;

    border-radius:
        15px;

    color:
        #ffffff;

    font-size:
        16px;

    font-weight:
        800;
}


.job-stage-number.stage-one {

    background:

        linear-gradient(
            135deg,
            #8050dc,
            #9d52d8
        );
}


.job-stage-number.stage-two {

    background:

        linear-gradient(
            135deg,
            #3ab7e2,
            #6576df
        );
}


.job-stage-number.stage-three {

    background:

        linear-gradient(
            135deg,
            #eb438b,
            #af47d8
        );
}


.job-stage-kicker {

    display:
        block;

    margin-bottom:
        2px;

    color:
        #7841d3;

    font-size:
        10px;

    font-weight:
        800;

    letter-spacing:
        .12em;
}


.job-stage-title h3 {

    margin:
        0;

    color:
        #4a3254;

    font-size:
        18px;
}


.job-stage-title p {

    margin:
        4px 0 0;

    color:
        #88788c;

    font-size:
        12px;
}


.job-stage-actions {

    display:
        flex;

    align-items:
        center;

    gap:
        9px;
}


.job-stage-count {

    padding:
        7px 11px;

    border-radius:
        999px;

    color:
        #7440cb;

    background:
        #eee4ff;

    font-size:
        10px;

    font-weight:
        750;
}


.job-stage-count.final {

    color:
        #b52e70;

    background:
        #ffe4f0;
}


/* =========================================================
   TABLE
========================================================= */

.job-table-scroll {

    width:
        100%;

    overflow:
        auto;

    border:
        1px solid
        #e7dce9;

    border-radius:
        16px;

    background:
        #ffffff;

    scrollbar-width:
        thin;

    scrollbar-color:
        #c99cda
        #f7f1f8;
}


.job-data-table {

    width:
        max-content;

    min-width:
        100%;

    border-collapse:
        separate;

    border-spacing:
        0;

    background:
        #ffffff;
}


.job-data-table th,
.job-data-table td {

    min-width:
        125px;

    max-width:
        320px;

    padding:
        11px 12px;

    border-right:
        1px solid
        #eadfeb;

    border-bottom:
        1px solid
        #eadfeb;

    vertical-align:
        top;

    font-size:
        11px;

    line-height:
        1.45;

    white-space:
        normal;

    overflow-wrap:
        anywhere;
}


.job-data-table th {

    position:
        sticky;

    top:
        0;

    z-index:
        2;

    color:
        #4e3458;

    background:

        linear-gradient(
            135deg,
            #fff0f8,
            #f1e5ff,
            #e9efff
        );

    font-weight:
        750;

    text-align:
        left;
}


.job-data-table td {

    color:
        #594a5f;

    background:
        #ffffff;
}


.job-data-table tbody tr:hover td {

    background:
        #fffafd;
}


.final-table th {

    color:
        #ffffff;

    background:

        linear-gradient(
            105deg,
            #7240da,
            #a541d3,
            #e9408b
        );
}


.final-table tbody tr:hover td {

    background:
        #fff6fb;
}


.job-empty-state {

    padding:
        34px !important;

    color:
        #8b7c8e !important;

    text-align:
        center;
}


/* =========================================================
   DOWNLOAD
========================================================= */

.job-download-card {

    display:
        grid;

    grid-template-columns:
        auto minmax(0, 1fr) auto;

    align-items:
        center;

    gap:
        18px;

    padding:
        25px 28px;

    border:
        1px solid
        #e4d4e7;

    border-radius:
        22px;

    background:

        linear-gradient(
            110deg,
            #fff5fb,
            #f1e7ff,
            #e4f5ff
        );

    box-shadow:
        0 12px 32px
        rgba(
            92,
            52,
            105,
            .065
        );
}


.job-download-icon {

    display:
        inline-flex;

    align-items:
        center;

    justify-content:
        center;

    width:
        58px;

    height:
        58px;

    border-radius:
        18px;

    color:
        #ffffff;

    font-size:
        26px;

    background:

        linear-gradient(
            135deg,
            #7341db,
            #eb408d
        );
}


.job-download-copy span {

    color:
        #7841ce;

    font-size:
        10px;

    font-weight:
        800;

    letter-spacing:
        .13em;
}


.job-download-copy h3 {

    margin:
        4px 0;

    color:
        #4b3155;

    font-size:
        18px;
}


.job-download-copy p {

    margin:
        0;

    color:
        #817084;

    font-size:
        12px;
}


.job-download-actions {

    display:
        flex;

    gap:
        10px;
}


/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 1280px) {

    .job-workflow-banner {

        grid-template-columns:
            1fr;
    }


    .job-workflow-steps {

        grid-template-columns:
            repeat(
                4,
                minmax(0, 1fr)
            );
    }


    .job-page-header {

        grid-template-columns:
            1fr;
    }


    .job-header-actions {

        justify-content:
            start;
    }
}


@media (max-width: 1000px) {

    .job-stat-grid {

        grid-template-columns:
            repeat(
                2,
                minmax(0, 1fr)
            );
    }


    .job-info-grid {

        grid-template-columns:
            repeat(
                2,
                minmax(0, 1fr)
            );
    }


    .job-download-card {

        grid-template-columns:
            auto minmax(0, 1fr);
    }


    .job-download-actions {

        grid-column:
            1 / -1;
    }
}


@media (max-width: 720px) {

    .job-workflow-banner {

        padding:
            25px;
    }


    .job-workflow-steps,
    .job-stat-grid,
    .job-info-grid {

        grid-template-columns:
            1fr;
    }


    .job-header-actions {

        grid-template-columns:
            1fr;
    }


    .job-header-actions .btn {

        width:
            100%;
    }


    .job-stage-header {

        align-items:
            flex-start;

        flex-direction:
            column;
    }


    .job-stage-actions {

        width:
            100%;
    }


    .job-download-card {

        grid-template-columns:
            1fr;
    }


    .job-download-actions {

        flex-direction:
            column;
    }


    .job-download-actions .btn {

        width:
            100%;
    }

}


<style>
.job-data-table-wrapper{
    max-width:100%;
    overflow:auto;
    max-height:520px;
}
.job-data-table{
    min-width:1400px;
    border-collapse:separate;
    border-spacing:0;
}
.job-data-table thead th{
    position:sticky;
    top:0;
    z-index:5;
}
.job-data-table th:first-child,
.job-data-table td:first-child{
    position:sticky;
    left:0;
    z-index:4;
    background:#fff;
}
.job-data-table th{
    cursor:grab;
    user-select:none;
}
.job-data-table td{
    white-space:pre-wrap;
}
</style>
<script>
document.addEventListener('DOMContentLoaded',function(){
 const table=document.querySelector('.job-data-table');
 if(!table) return;
 let dragCol=null;
 table.querySelectorAll('th').forEach((th,i)=>{
   th.draggable=true;
   th.addEventListener('dragstart',()=>dragCol=i);
   th.addEventListener('dragover',e=>e.preventDefault());
   th.addEventListener('drop',()=>{
     let target=[...table.rows].map(r=>r.cells[i]);
     let source=[...table.rows].map(r=>r.cells[dragCol]);
     [...table.rows].forEach((r,n)=>{
       if(dragCol<i) r.insertBefore(source[n], target[n].nextSibling);
       else r.insertBefore(source[n], target[n]);
     });
   });
 });
});
</script>
</style>