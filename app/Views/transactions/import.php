<?php

declare(strict_types=1);


/*
|--------------------------------------------------------------------------
| VIEW DATA
|--------------------------------------------------------------------------
*/

$viewData =
    get_defined_vars();


$jobs =
    isset(
        $viewData['jobs']
    )
    &&
    is_array(
        $viewData['jobs']
    )
        ? $viewData['jobs']
        : [];


$payerAccounts =
    isset(
        $viewData[
            'payerAccounts'
        ]
    )
    &&
    is_array(
        $viewData[
            'payerAccounts'
        ]
    )
        ? $viewData[
            'payerAccounts'
        ]
        : [];


unset(
    $viewData
);


/**
 * @var array<int,array<string,mixed>> $jobs
 * @var array<int,array<string,mixed>> $payerAccounts
 */


$manualColumns = [

    [
        'key' =>
            'sequence_no',

        'label' =>
            'No.',
    ],

    [
        'key' =>
            'transaction_date',

        'label' =>
            'Date of Transaction',
    ],

    [
        'key' =>
            'payer',

        'label' =>
            'Bill To (Payer)',
    ],

    [
        'key' =>
            'beneficiary_name',

        'label' =>
            'Beneficiary Name',
    ],

    [
        'key' =>
            'beneficiary_account',

        'label' =>
            'Account number of Beneficiary',
    ],

    [
        'key' =>
            'beneficiary_bank',

        'label' =>
            'Beneficiary Bank',
    ],

    [
        'key' =>
            'beneficiary_bank_location',

        'label' =>
            'City, Country of Bank Beneficiary',
    ],

    [
        'key' =>
            'amount',

        'label' =>
            'Amounts',
    ],

    [
        'key' =>
            'beneficiary_country',

        'label' =>
            'Country of Beneficiary',
    ],

    [
        'key' =>
            'beneficiary_city',

        'label' =>
            'City of Beneficiary',
    ],

    [
        'key' =>
            'beneficiary_address',

        'label' =>
            'Complete Address of Beneficiary Account',
    ],

    [
        'key' =>
            'swift',

        'label' =>
            'Swift',
    ],

    [
        'key' =>
            'payment_details',

        'label' =>
            'Payment details 1',
    ],

    [
        'key' =>
            'currency',

        'label' =>
            'Currency',
    ],

    [
        'key' =>
            'payer_account',

        'label' =>
            'Nomor Rekening',
    ],

    [
        'key' =>
            'payer_address',

        'label' =>
            'Alamat payer',
    ],

    [
        'key' =>
            'payer_city',

        'label' =>
            'Kota Payer',
    ],

    [
        'key' =>
            'payer_country',

        'label' =>
            'Negara Payer',
    ],

    [
        'key' =>
            'invoice_no',

        'label' =>
            'No inv',
    ],

];

?>


<div class="transaction-import-page">


    <!-- =====================================================
         WORKFLOW
    ====================================================== -->

    <section class="trx-workflow">

        <div class="trx-workflow-copy">

            <span class="trx-kicker">
                BANK BRI WORKFLOW
            </span>

            <h2>
                Excel / Copy Paste
                <span>→</span>
                Bentuk Tahap 2
                <span>→</span>
                Bersihkan Tahap 3
                <span>→</span>
                Download
            </h2>

            <p>
                Tahap pertama menerima tabel Excel atau copy paste.
                Data payer dapat dipilih langsung dari master Rekening Payer.
            </p>

        </div>


        <div class="trx-workflow-steps">

            <div class="trx-step active">
                <b>1</b>
                <span>Input</span>
            </div>

            <div class="trx-step">
                <b>2</b>
                <span>Bentuk tabel</span>
            </div>

            <div class="trx-step">
                <b>3</b>
                <span>Hapus tanda baca</span>
            </div>

            <div class="trx-step">
                <b>4</b>
                <span>Download</span>
            </div>

        </div>

    </section>


    <!-- =====================================================
         TITLE
    ====================================================== -->

    <section class="trx-heading">

        <div>

            <span class="eyebrow dark">
                BANK BRI
            </span>

            <h1>
                Import Template Bank BRI
            </h1>

            <p>
                Input data Excel atau copy paste, bentuk tabel BRI, bersihkan tanda baca pada isi data, lalu download Excel final.
            </p>

        </div>


        <a
            class="btn btn-light"
            href="<?= url('/transactions') ?>"
        >
            <i class="bi bi-arrow-left"></i>
            Pilihan Bank
        </a>

    </section>


    <!-- =====================================================
         FORM
    ====================================================== -->

    <form
        method="post"
        action="<?= url('/transactions/import') ?>"
        enctype="multipart/form-data"
        id="transactionSourceForm"
        class="trx-form"
    >

        <?= csrf_field() ?>


        <!-- =================================================
             CONFIGURATION
        ================================================== -->

        <section class="trx-card">

            <header class="trx-card-head">

                <span class="trx-card-number">
                    01
                </span>

                <div>

                    <h3>
                        Pengaturan transaksi
                    </h3>

                    <p>
                        Tentukan batch, email output, dan tim.
                    </p>

                </div>

            </header>


            <div class="trx-config-grid">

                <label>

                    <span class="form-label">
                        Nama batch
                    </span>

                    <input
                        class="form-control"
                        name="job_name"
                        placeholder="Contoh: TRANSAKSI 11 AGUSTUS 2026"
                    >

                </label>


                <label>

                    <span class="form-label">
                        Email output
                    </span>

                    <input
                        class="form-control"
                        type="email"
                        name="output_email"
                        value="remit.service09@gmail.com"
                        required
                    >

                </label>


                <?php if (
                    !App\Core\Auth::isTransactionAdmin()
                ): ?>

                    <label>

                        <span class="form-label">
                            Tim
                        </span>

                        <select
                            class="form-select"
                            name="team_no"
                        >

                            <?php for (
                                $i = 1;
                                $i <= 4;
                                $i++
                            ): ?>

                                <option value="<?= $i ?>">
                                    Tim <?= $i ?>
                                </option>

                            <?php endfor; ?>

                        </select>

                    </label>

                <?php endif; ?>


                <label class="trx-check">

                    <input
                        type="checkbox"
                        name="auto_create_company"
                        value="1"
                        checked
                    >

                    <span>
                        Buat perusahaan otomatis jika payer belum tersedia.
                    </span>

                </label>

            </div>

        </section>


        <!-- =================================================
             SOURCE
        ================================================== -->

        <section class="trx-card">

            <header class="trx-card-head">

                <span class="trx-card-number">
                    02
                </span>

                <div>

                    <h3>
                        Sumber data
                    </h3>

                    <p>
                        Gunakan copy paste spreadsheet atau import file.
                    </p>

                </div>

            </header>


            <div class="trx-source-tabs">

                <label
                    class="trx-source-tab active"
                    data-source-option="manual"
                >

                    <input
                        type="radio"
                        name="source_mode"
                        value="manual"
                        checked
                    >

                    <i class="bi bi-grid-3x3-gap"></i>

                    <span>
                        <strong>
                            Copy Paste Excel
                        </strong>

                        <small>
                            Input seperti spreadsheet
                        </small>
                    </span>

                </label>


                <label
                    class="trx-source-tab"
                    data-source-option="file"
                >

                    <input
                        type="radio"
                        name="source_mode"
                        value="file"
                    >

                    <i class="bi bi-file-earmark-excel"></i>

                    <span>
                        <strong>
                            Import Excel
                        </strong>

                        <small>
                            XLSX, XLS XML, CSV
                        </small>
                    </span>

                </label>

            </div>


            <!-- =============================================
                 FILE
            ============================================== -->

            <div
                class="trx-source-panel"
                data-source-panel="file"
                hidden
            >

                <label class="trx-drop-zone">

                    <input
                        type="file"
                        name="transaction_file"
                        accept=".xlsx,.xls,.csv"
                        id="transactionFile"
                    >

                    <i class="bi bi-cloud-arrow-up"></i>

                    <strong>
                        Pilih file transaksi
                    </strong>

                    <span id="transactionFileName">
                        XLSX, XLS XML, atau CSV
                    </span>

                </label>

            </div>


            <!-- =============================================
                 MANUAL
            ============================================== -->

            <div
                class="trx-source-panel active"
                data-source-panel="manual"
            >

                <input
                    type="hidden"
                    name="manual_rows_json"
                    id="manualRowsJson"
                    value="[]"
                >


                <div class="trx-grid-toolbar">

                    <div>

                        <strong>
                            Spreadsheet transaksi
                        </strong>

                        <span>
                            Copy dari Excel lalu tekan Ctrl + V pada sel pertama.
                        </span>

                    </div>


                    <div class="trx-toolbar-actions">

                        <span
                            class="trx-row-count"
                            id="manualRowCount"
                        >
                            0 baris
                        </span>

                        <button
                            class="btn btn-light btn-sm"
                            type="button"
                            id="addManualRows"
                        >
                            <i class="bi bi-plus-lg"></i>
                            10 baris
                        </button>

                        <button
                            class="btn btn-light btn-sm"
                            type="button"
                            id="clearManualGrid"
                        >
                            <i class="bi bi-eraser"></i>
                            Bersihkan
                        </button>

                    </div>

                </div>


                <div class="trx-master-note">

                    <i class="bi bi-bank"></i>

                    <div>

                        <strong>
                            Master Rekening Payer
                        </strong>

                        <span>
                            Memilih salah satu master akan mengisi Bill To,
                            Nomor Rekening, Alamat, Kota, dan Negara pada baris
                            yang sama.
                        </span>

                    </div>

                </div>


                <div
                    class="trx-grid-wrap"
                    id="manualGridScroll"
                >

                    <table
                        class="trx-excel-grid"
                        id="manualTransactionGrid"
                    >

                        <colgroup>

                            <?php foreach (
                                $manualColumns
                                as $index => $column
                            ): ?>

                                <col
                                    data-grid-column="<?= $index ?>"
                                    data-column-key="<?= e(
                                        $column['key']
                                    ) ?>"
                                >

                            <?php endforeach; ?>

                        </colgroup>


                        <thead>

                            <tr class="trx-number-row">

                                <?php foreach (
                                    $manualColumns
                                    as $index => $column
                                ): ?>

                                    <th>
                                        <?= $index + 1 ?>
                                    </th>

                                <?php endforeach; ?>

                            </tr>


                            <tr class="trx-header-row">

                                <?php foreach (
                                    $manualColumns
                                    as $column
                                ): ?>

                                    <th>
                                        <?= e(
                                            $column['label']
                                        ) ?>
                                    </th>

                                <?php endforeach; ?>

                            </tr>

                        </thead>


                        <tbody></tbody>

                    </table>

                </div>


                <div class="trx-grid-footer">

                    <span>
                        Kolom 15 sampai 18 memakai master Rekening Payer.
                    </span>

                    <span>
                        Sel kosong tetap putih.
                    </span>

                </div>

            </div>

        </section>


        <!-- =================================================
             SUBMIT
        ================================================== -->

        <section class="trx-submit-card">

            <div>

                <i class="bi bi-arrow-right-circle"></i>

                <span>

                    <strong>
                        Siap diproses
                    </strong>

                    <small>
                        Tahap 2 dan Tahap 3 dibuat otomatis.
                    </small>

                </span>

            </div>


            <button
                class="btn btn-primary"
                type="submit"
                id="processTransactionButton"
            >
                <i class="bi bi-play-fill"></i>
                Proses Tahap 1
            </button>

        </section>

    </form>

</div>


<style>

/* =========================================================
   PAGE
========================================================= */

.transaction-import-page {
    display: flex;
    flex-direction: column;
    gap: 22px;
    width: 100%;
    min-width: 0;
}


/* =========================================================
   WORKFLOW
========================================================= */

.trx-workflow {
    display: grid;
    grid-template-columns: minmax(0, 1.15fr) minmax(480px, .85fr);
    align-items: center;
    gap: 30px;

    padding: 26px 30px;

    border: 1px solid #ead5e7;
    border-radius: 26px;

    background:
        linear-gradient(
            115deg,
            #ffdce9 0%,
            #f0dcff 52%,
            #cfeeff 100%
        );
}


.trx-workflow-copy {
    min-width: 0;
}


.trx-kicker {
    display: block;
    margin-bottom: 10px;

    color: #ffffff;

    font-size: 11px;
    font-weight: 800;
    letter-spacing: .14em;
}


.trx-workflow h2 {
    max-width: 760px;

    margin: 0;

    color: #4c2f57;

    font-size: clamp(22px, 2.1vw, 32px);
    line-height: 1.35;
    letter-spacing: -.03em;
}


.trx-workflow h2 span {
    color: #9348d6;
}


.trx-workflow p {
    max-width: 710px;

    margin: 12px 0 0;

    color: #775f79;

    font-size: 14px;
    line-height: 1.6;
}


.trx-workflow-steps {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 10px;
}


.trx-step {
    display: flex;
    align-items: center;
    gap: 11px;

    min-width: 0;
    min-height: 54px;

    padding: 10px 15px;

    border: 1px solid rgba(255,255,255,.8);
    border-radius: 999px;

    color: #65506b;

    background: rgba(255,255,255,.75);

    font-weight: 700;
}


.trx-step b {
    display: inline-flex;
    align-items: center;
    justify-content: center;

    width: 32px;
    height: 32px;

    flex: 0 0 32px;

    border-radius: 50%;

    color: #7450b7;
    background: #eee6f4;
}


.trx-step.active {
    color: #ffffff;

    border-color: transparent;

    background:
        linear-gradient(
            105deg,
            #7841df,
            #ab43d2,
            #eb408b
        );
}


.trx-step.active b {
    color: #ffffff;
    background: rgba(255,255,255,.2);
}


/* =========================================================
   HEADING
========================================================= */

.trx-heading {
    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 30px;

    padding: 8px 2px;
}


.trx-heading > div {
    min-width: 0;
    max-width: 1000px;
}


.trx-heading h1 {
    margin: 8px 0 8px;

    color: #4b3056;

    font-size: clamp(30px, 3vw, 42px);
    line-height: 1.1;
    letter-spacing: -.035em;
}


.trx-heading p {
    margin: 0;

    color: #847286;

    font-size: 14px;
    line-height: 1.55;
}


/* =========================================================
   FORM
========================================================= */

.trx-form {
    display: flex;
    flex-direction: column;
    gap: 18px;
}


.trx-card {
    padding: 24px;

    border: 1px solid #eaddea;
    border-radius: 22px;

    background: #ffffff;

    box-shadow:
        0 10px 28px
        rgba(92, 53, 105, .045);
}


.trx-card-head {
    display: flex;
    align-items: center;
    gap: 13px;

    margin-bottom: 18px;
}


.trx-card-number {
    display: inline-flex;
    align-items: center;
    justify-content: center;

    width: 44px;
    height: 44px;

    flex: 0 0 44px;

    border-radius: 14px;

    color: #7741d8;
    background: #eee1ff;

    font-size: 12px;
    font-weight: 800;
}


.trx-card-head h3 {
    margin: 0;

    color: #4e3457;

    font-size: 18px;
}


.trx-card-head p {
    margin: 3px 0 0;

    color: #8c7c8f;

    font-size: 12px;
}


/* =========================================================
   CONFIGURATION
========================================================= */

.trx-config-grid {
    display: grid;
    grid-template-columns:
        minmax(260px, 1.3fr)
        minmax(260px, 1.1fr)
        minmax(220px, .8fr);

    gap: 14px;
    align-items: end;
}


.trx-config-grid label:not(.trx-check) {
    display: flex;
    flex-direction: column;
    gap: 7px;
}


.trx-check {
    display: flex;
    align-items: center;
    gap: 10px;

    min-height: 49px;

    padding: 10px 13px;

    border: 1px solid #e8dce9;
    border-radius: 13px;

    background: #fffafd;

    color: #654f6a;

    font-size: 12px;
}


.trx-check input {
    accent-color: #7741d8;
}


/* =========================================================
   SOURCE TABS
========================================================= */

.trx-source-tabs {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));

    gap: 12px;

    margin-bottom: 16px;
}


.trx-source-tab {
    position: relative;

    display: grid;
    grid-template-columns: auto minmax(0,1fr);
    align-items: center;

    gap: 12px;

    min-height: 74px;

    padding: 14px 16px;

    border: 1px solid #e6dae8;
    border-radius: 16px;

    background: #ffffff;

    cursor: pointer;
}


.trx-source-tab input {
    position: absolute;
    opacity: 0;
}


.trx-source-tab > i {
    display: inline-flex;
    align-items: center;
    justify-content: center;

    width: 44px;
    height: 44px;

    border-radius: 13px;

    color: #7741d8;

    background: #eee3ff;

    font-size: 20px;
}


.trx-source-tab > span {
    min-width: 0;

    display: flex;
    flex-direction: column;

    gap: 2px;
}


.trx-source-tab strong {
    color: #4e3856;
}


.trx-source-tab small {
    color: #8a7a8d;
}


.trx-source-tab.active {
    border-color: #b47ad6;

    background:
        linear-gradient(
            135deg,
            #fff7fb,
            #f5ebff
        );
}


/* =========================================================
   PANEL
========================================================= */

.trx-source-panel {
    display: none;
}


.trx-source-panel.active {
    display: block;
}


/* =========================================================
   FILE
========================================================= */

.trx-drop-zone {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;

    gap: 5px;

    min-height: 145px;

    padding: 24px;

    border: 1px dashed #c09bd5;
    border-radius: 16px;

    background: #fffafd;

    cursor: pointer;
}


.trx-drop-zone input {
    display: none;
}


.trx-drop-zone > i {
    margin-bottom: 5px;

    color: #8448dd;

    font-size: 30px;
}


.trx-drop-zone strong {
    color: #4f3758;
}


.trx-drop-zone span {
    color: #8a788d;

    font-size: 12px;
}


/* =========================================================
   TOOLBAR
========================================================= */

.trx-grid-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 18px;

    margin-bottom: 12px;
    padding: 13px 15px;

    border: 1px solid #eaddea;
    border-radius: 14px;

    background: #fffafd;
}


.trx-grid-toolbar > div:first-child {
    min-width: 0;

    display: flex;
    flex-direction: column;

    gap: 2px;
}


.trx-grid-toolbar strong {
    color: #4d3656;
}


.trx-grid-toolbar span {
    color: #88788b;

    font-size: 12px;
}


.trx-toolbar-actions {
    display: flex;
    align-items: center;
    flex-wrap: wrap;

    gap: 8px;
}


.trx-row-count {
    display: inline-flex;
    align-items: center;

    min-height: 34px;

    padding: 0 11px;

    border-radius: 999px;

    color: #7440cd;

    background: #efe4ff;

    font-size: 11px !important;
    font-weight: 750;
}


/* =========================================================
   MASTER NOTE
========================================================= */

.trx-master-note {
    display: flex;
    align-items: flex-start;

    gap: 11px;

    margin-bottom: 12px;
    padding: 12px 14px;

    border-radius: 13px;

    color: #6e5874;

    background:
        linear-gradient(
            135deg,
            #f5eaff,
            #edf7ff
        );
}


.trx-master-note > i {
    margin-top: 1px;

    color: #7842d6;

    font-size: 17px;
}


.trx-master-note > div {
    display: flex;
    flex-direction: column;

    gap: 2px;
}


.trx-master-note strong {
    color: #53385d;
}


.trx-master-note span {
    font-size: 12px;
    line-height: 1.45;
}


/* =========================================================
   TABLE WRAPPER
========================================================= */

.trx-grid-wrap {
    position: relative;

    width: 100%;
    max-width: 100%;

    max-height: 520px;

    overflow: auto;

    border: 1px solid #dfd3e3;
    border-radius: 14px;

    background: #ffffff;

    scrollbar-width: thin;
    scrollbar-color: #bf91d4 #f7f1f8;
}


/* =========================================================
   TABLE
========================================================= */

.trx-excel-grid {
    width: max-content;
    min-width: 100%;

    border-collapse: separate;
    border-spacing: 0;

    table-layout: fixed;

    background: #ffffff;
}


.trx-excel-grid th,
.trx-excel-grid td {
    height: 40px;

    padding: 7px 9px;

    border-right: 1px solid #e5dce7;
    border-bottom: 1px solid #e5dce7;

    box-sizing: border-box;

    font-size: 12px;
    line-height: 1.35;

    vertical-align: middle;
}


/* NUMBER */

.trx-number-row th {
    position: sticky;
    top: 0;

    z-index: 12;

    height: 34px;

    color: #ffffff;

    background:
        linear-gradient(
            100deg,
            #783fe0,
            #a340d2,
            #df3c96
        );

    font-weight: 800;
    text-align: center;
}


/* HEADER */

.trx-header-row th {
    position: sticky;
    top: 34px;

    z-index: 11;

    min-height: 56px;

    color: #4b3156;

    background:
        linear-gradient(
            135deg,
            #fff0f8,
            #f2e8ff,
            #e8efff
        );

    font-weight: 750;
    text-align: center;

    white-space: normal;
    overflow-wrap: anywhere;
}


/* CELL */

.trx-excel-grid td {
    color: #503f57;

    background: #ffffff;

    outline: none;

    white-space: pre-wrap;
    overflow-wrap: anywhere;
}


.trx-excel-grid td.has-value {
    background: #fffafd;
}


.trx-excel-grid td:not(.has-value) {
    background: #ffffff !important;
}


.trx-excel-grid td[contenteditable="true"]:focus {
    position: relative;
    z-index: 4;

    background: #f8efff !important;

    box-shadow:
        inset
        0 0 0 2px
        #8545dd;
}


/* =========================================================
   MASTER SELECT
========================================================= */

.trx-excel-grid td.payer-master-cell {
    padding: 4px 5px;

    background: #ffffff !important;
}


.payer-master-select {
    width: 100%;
    height: 32px;

    padding: 4px 28px 4px 8px;

    border: 1px solid #ded2e1;
    border-radius: 8px;

    outline: 0;

    color: #503a57;

    background: #ffffff;

    font-family: inherit;
    font-size: 11px;
    font-weight: 600;

    cursor: pointer;
}


.payer-master-select:hover {
    border-color: #bd91d5;
}


.payer-master-select:focus {
    border-color: #7e43db;

    box-shadow:
        0 0 0 2px
        rgba(
            126,
            67,
            219,
            .10
        );
}


td[data-key="payer_account"] .payer-master-select {
    color: #7440d5;
}


td[data-key="payer_city"] .payer-master-select {
    color: #087c98;
}


td[data-key="payer_country"] .payer-master-select {
    color: #15785c;
}


/* =========================================================
   FOOTER
========================================================= */

.trx-grid-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 12px;

    padding-top: 9px;

    color: #8a798e;

    font-size: 11px;
}


/* =========================================================
   SUBMIT
========================================================= */

.trx-submit-card {
    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 20px;

    padding: 17px 20px;

    border: 1px solid #e7d9e9;
    border-radius: 18px;

    background:
        linear-gradient(
            110deg,
            #fff7fb,
            #f4ecff
        );
}


.trx-submit-card > div {
    display: flex;
    align-items: center;

    gap: 12px;
}


.trx-submit-card > div > i {
    display: inline-flex;
    align-items: center;
    justify-content: center;

    width: 42px;
    height: 42px;

    border-radius: 13px;

    color: #7741d7;

    background: #ede2ff;

    font-size: 19px;
}


.trx-submit-card span {
    display: flex;
    flex-direction: column;
}


.trx-submit-card strong {
    color: #4c3455;
}


.trx-submit-card small {
    margin-top: 2px;

    color: #8b7a8d;
}


/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 1250px) {

    .trx-workflow {
        grid-template-columns: 1fr;
    }

    .trx-workflow-steps {
        grid-template-columns:
            repeat(
                4,
                minmax(0,1fr)
            );
    }

    .trx-config-grid {
        grid-template-columns:
            repeat(
                2,
                minmax(0,1fr)
            );
    }

}


@media (max-width: 800px) {

    .trx-workflow-steps,
    .trx-config-grid,
    .trx-source-tabs {
        grid-template-columns: 1fr;
    }


    .trx-heading,
    .trx-grid-toolbar,
    .trx-submit-card {
        align-items: stretch;
        flex-direction: column;
    }


    .trx-toolbar-actions {
        justify-content: flex-start;
    }

}

</style>


<script>

(() => {

    'use strict';


    /* =====================================================
       COLUMNS
    ====================================================== */

    const columns =
        <?= json_encode(
            array_column(
                $manualColumns,
                'key'
            ),
            JSON_UNESCAPED_UNICODE
            |
            JSON_UNESCAPED_SLASHES
        ) ?>;


    /*
     * MASTER DATA
     */

    const payerAccounts =
        <?= json_encode(
            array_values(
                array_map(
                    static function (
                        array $account
                    ): array {

                        return [

                            'id' =>
                                (int) (
                                    $account['id']
                                    ?? 0
                                ),

                            'company_id' =>
                                (int) (
                                    $account['company_id']
                                    ?? 0
                                ),

                            'company_name' =>
                                trim(
                                    (string) (
                                        $account['company_name']
                                        ?? ''
                                    )
                                ),

                            'account_no' =>
                                trim(
                                    (string) (
                                        $account['account_no']
                                        ?? ''
                                    )
                                ),

                            'address' =>
                                trim(
                                    (string) (
                                        $account['address']
                                        ?? ''
                                    )
                                ),

                            'city' =>
                                trim(
                                    (string) (
                                        $account['city']
                                        ?? ''
                                    )
                                ),

                            'country' =>
                                trim(
                                    (string) (
                                        $account['country']
                                        ?? ''
                                    )
                                ),

                        ];

                    },
                    $payerAccounts
                )
            ),
            JSON_UNESCAPED_UNICODE
            |
            JSON_UNESCAPED_SLASHES
        ) ?>;


    const payerMap =
        new Map(
            payerAccounts.map(
                account => [
                    String(account.id),
                    account
                ]
            )
        );


    const payerColumns = [
        'payer_account',
        'payer_address',
        'payer_city',
        'payer_country'
    ];


    /*
     * DOM
     */

    const form =
        document.getElementById(
            'transactionSourceForm'
        );


    const grid =
        document.getElementById(
            'manualTransactionGrid'
        );


    const tbody =
        grid?.querySelector(
            'tbody'
        );


    const gridWrap =
        document.getElementById(
            'manualGridScroll'
        );


    const hidden =
        document.getElementById(
            'manualRowsJson'
        );


    const counter =
        document.getElementById(
            'manualRowCount'
        );


    const addButton =
        document.getElementById(
            'addManualRows'
        );


    const clearButton =
        document.getElementById(
            'clearManualGrid'
        );


    const fileInput =
        document.getElementById(
            'transactionFile'
        );


    const fileName =
        document.getElementById(
            'transactionFileName'
        );


    const sourceOptions =
        document.querySelectorAll(
            '[data-source-option]'
        );


    const sourcePanels =
        document.querySelectorAll(
            '[data-source-panel]'
        );


    const colElements =
        grid
            ? [
                ...grid.querySelectorAll(
                    'colgroup col'
                )
            ]
            : [];


    if (
        !form
        ||
        !grid
        ||
        !tbody
        ||
        !hidden
    ) {
        return;
    }


    /* =====================================================
       COLUMN WIDTH
    ====================================================== */

    const widths = {

        sequence_no:
            75,

        transaction_date:
            145,

        payer:
            190,

        beneficiary_name:
            185,

        beneficiary_account:
            220,

        beneficiary_bank:
            180,

        beneficiary_bank_location:
            240,

        amount:
            125,

        beneficiary_country:
            180,

        beneficiary_city:
            160,

        beneficiary_address:
            290,

        swift:
            120,

        payment_details:
            210,

        currency:
            105,

        payer_account:
            170,

        payer_address:
            280,

        payer_city:
            160,

        payer_country:
            160,

        invoice_no:
            110

    };


    const canvas =
        document.createElement(
            'canvas'
        );


    const context =
        canvas.getContext(
            '2d'
        );


    const measure =
        value => {

            const text =
                String(
                    value
                    ?? ''
                );


            if (!context) {
                return text.length * 7;
            }


            context.font =
                '500 12px "Segoe UI", Arial, sans-serif';


            return context
                .measureText(
                    text
                )
                .width;

        };


    /* =====================================================
       OPTION TEXT
    ====================================================== */

    /*
     * INI BAGIAN UTAMA PERBAIKAN.
     *
     * Tidak ada lagi:
     *
     * 050201 - PT FABLE
     * JAKARTA - 050201
     * INDONESIA - 050201
     *
     * Masing masing hanya menampilkan isi kolomnya.
     */

    const optionText =
        (
            account,
            key
        ) => {

            switch (key) {

                case 'payer_account':

                    return (
                        account.account_no
                        || '-'
                    );


                case 'payer_address':

                    return (
                        account.address
                        || '-'
                    );


                case 'payer_city':

                    return (
                        account.city
                        || '-'
                    );


                case 'payer_country':

                    return (
                        account.country
                        || '-'
                    );


                default:

                    return '-';

            }

        };


    /* =====================================================
       CELL VALUE
    ====================================================== */

    const cellValue =
        cell => {

            if (!cell) {
                return '';
            }


            const select =
                cell.querySelector(
                    '.payer-master-select'
                );


            if (select) {

                const account =
                    payerMap.get(
                        select.value
                    );


                if (!account) {
                    return '';
                }


                return optionText(
                    account,
                    cell.dataset.key
                    ?? ''
                );

            }


            return String(
                cell.innerText
                ?? ''
            )
                .replace(
                    /\r/g,
                    ''
                )
                .trim();

        };


    const updateState =
        cell => {

            cell?.classList.toggle(
                'has-value',
                cellValue(cell)
                !== ''
            );

        };


    /* =====================================================
       CREATE SELECT
    ====================================================== */

    const createSelect =
        (
            cell,
            key
        ) => {

            const select =
                document.createElement(
                    'select'
                );


            select.className =
                'payer-master-select';


            const empty =
                document.createElement(
                    'option'
                );


            empty.value =
                '';


            empty.textContent =
                '-- Pilih --';


            select.appendChild(
                empty
            );


            payerAccounts.forEach(
                account => {

                    if (
                        Number(
                            account.id
                        )
                        <= 0
                    ) {
                        return;
                    }


                    const option =
                        document.createElement(
                            'option'
                        );


                    option.value =
                        String(
                            account.id
                        );


                    /*
                     * YANG DITAMPILKAN HANYA SESUAI HEADER.
                     */

                    option.textContent =
                        optionText(
                            account,
                            key
                        );


                    select.appendChild(
                        option
                    );

                }
            );


            select.addEventListener(
                'change',
                () => {

                    applyPayer(
                        cell.parentElement,
                        select.value
                    );

                }
            );


            cell.classList.add(
                'payer-master-cell'
            );


            cell.appendChild(
                select
            );

        };


    /* =====================================================
       NORMAL TEXT CELL
    ====================================================== */

    const setText =
        (
            row,
            key,
            value
        ) => {

            const cell =
                row.querySelector(
                    `td[data-key="${key}"]`
                );


            if (!cell) {
                return;
            }


            cell.textContent =
                String(
                    value
                    ?? ''
                );


            updateState(
                cell
            );

        };


    /* =====================================================
       APPLY PAYER
    ====================================================== */

    const applyPayer =
        (
            row,
            accountId
        ) => {

            if (!row) {
                return;
            }


            const account =
                payerMap.get(
                    String(
                        accountId
                    )
                );


            if (!account) {

                payerColumns.forEach(
                    key => {

                        const select =
                            row.querySelector(
                                `td[data-key="${key}"] select`
                            );


                        if (select) {
                            select.value =
                                '';
                        }

                    }
                );


                row.dataset.payerAccountId =
                    '';


                row.dataset.payerCompanyId =
                    '';


                updateCounter();

                return;
            }


            /*
             * Satu master rekening untuk satu baris.
             */

            row.dataset.payerAccountId =
                String(
                    account.id
                );


            row.dataset.payerCompanyId =
                String(
                    account.company_id
                );


            /*
             * Sinkronkan semua dropdown.
             *
             * Display masing masing tetap berbeda.
             */

            payerColumns.forEach(
                key => {

                    const cell =
                        row.querySelector(
                            `td[data-key="${key}"]`
                        );


                    const select =
                        cell?.querySelector(
                            'select'
                        );


                    if (select) {
                        select.value =
                            String(
                                account.id
                            );
                    }


                    updateState(
                        cell
                    );

                }
            );


            /*
             * Bill To otomatis dari company master.
             */

            setText(
                row,
                'payer',
                account.company_name
            );


            updateCounter();

            resizeColumns();

        };


    /* =====================================================
       CREATE ROW
    ====================================================== */

    const createRow =
        () => {

            const row =
                document.createElement(
                    'tr'
                );


            columns.forEach(
                (
                    key,
                    index
                ) => {

                    const cell =
                        document.createElement(
                            'td'
                        );


                    cell.dataset.key =
                        key;


                    cell.dataset.column =
                        String(
                            index
                        );


                    if (
                        payerColumns.includes(
                            key
                        )
                    ) {
                        cell.contentEditable =
                            'false';


                        createSelect(
                            cell,
                            key
                        );

                    } else {
                        cell.contentEditable =
                            'true';


                        cell.spellcheck =
                            false;
                    }


                    row.appendChild(
                        cell
                    );

                }
            );


            tbody.appendChild(
                row
            );


            return row;

        };


    const ensureRows =
        minimum => {

            while (
                tbody.rows.length
                <
                minimum
            ) {
                createRow();
            }

        };


    ensureRows(
        15
    );


    /* =====================================================
       COUNTER
    ====================================================== */

    function updateCounter() {

        let count = 0;


        [
            ...tbody.rows
        ].forEach(
            row => {

                const filled =
                    [
                        ...row.cells
                    ]
                    .some(
                        cell =>
                            cellValue(cell)
                            !== ''
                    );


                if (filled) {
                    count++;
                }

            }
        );


        if (counter) {
            counter.textContent =
                `${count} baris`;
        }

    }


    /* =====================================================
       AUTO WIDTH
    ====================================================== */

    function resizeColumns() {

        let total = 0;


        columns.forEach(
            (
                key,
                index
            ) => {

                let width =
                    widths[key]
                    ?? 100;


                const header =
                    grid.tHead
                        ?.rows[1]
                        ?.cells[index];


                if (header) {
                    width =
                        Math.max(
                            width,
                            measure(
                                header.innerText
                            )
                            +
                            26
                        );
                }


                [
                    ...tbody.rows
                ].forEach(
                    row => {

                        const value =
                            cellValue(
                                row.cells[
                                    index
                                ]
                            );


                        if (
                            value !== ''
                        ) {
                            width =
                                Math.max(
                                    width,
                                    Math.min(
                                        400,
                                        measure(
                                            value
                                        )
                                        +
                                        30
                                    )
                                );
                        }

                    }
                );


                width =
                    Math.min(
                        400,
                        width
                    );


                if (
                    colElements[index]
                ) {
                    colElements[index]
                        .style
                        .width =
                        `${width}px`;
                }


                total += width;

            }
        );


        grid.style.width =
            `${Math.max(
                total,
                gridWrap
                    ?.clientWidth
                ||
                0
            )}px`;

    }


    /* =====================================================
       FIND MASTER FROM PASTE
    ====================================================== */

    const findAccount =
        (
            key,
            value
        ) => {

            const needle =
                String(
                    value
                    ?? ''
                )
                .trim()
                .toLowerCase();


            if (needle === '') {
                return null;
            }


            return (
                payerAccounts.find(
                    account => {

                        return (
                            optionText(
                                account,
                                key
                            )
                            .trim()
                            .toLowerCase()
                            ===
                            needle
                        );

                    }
                )
                ??
                null
            );

        };


    /* =====================================================
       SET CELL
    ====================================================== */

    const setCell =
        (
            cell,
            value
        ) => {

            if (!cell) {
                return;
            }


            const key =
                cell.dataset.key
                ?? '';


            if (
                payerColumns.includes(
                    key
                )
            ) {

                const account =
                    findAccount(
                        key,
                        value
                    );


                if (account) {
                    applyPayer(
                        cell.parentElement,
                        account.id
                    );
                }


                return;
            }


            cell.textContent =
                String(
                    value
                    ?? ''
                )
                .trim();


            updateState(
                cell
            );

        };


    /* =====================================================
       HEADER FILTER
    ====================================================== */

    const headerKeywords = [
        'date of transaction',
        'bill to',
        'beneficiary name',
        'account number of beneficiary',
        'amounts',
        'currency'
    ];


    const isHeader =
        row => {

            const text =
                row
                .join(' ')
                .toLowerCase();


            let score = 0;


            headerKeywords.forEach(
                keyword => {

                    if (
                        text.includes(
                            keyword
                        )
                    ) {
                        score++;
                    }

                }
            );


            return score >= 3;

        };


    const isNumberHeader =
        row => {

            const values =
                row
                .map(
                    value =>
                        String(
                            value
                            ?? ''
                        )
                        .trim()
                )
                .filter(Boolean);


            if (
                values.length < 4
            ) {
                return false;
            }


            const numeric =
                values.filter(
                    value =>
                        /^\d+$/.test(
                            value
                        )
                )
                .length;


            return (
                numeric
                /
                values.length
            )
            >=
            .8;

        };


    const cleanMatrix =
        matrix => {

            const result =
                matrix.filter(
                    row =>
                        row.some(
                            value =>
                                String(
                                    value
                                    ?? ''
                                )
                                .trim()
                                !== ''
                        )
                );


            while (
                result.length
                &&
                isNumberHeader(
                    result[0]
                )
            ) {
                result.shift();
            }


            if (
                result.length
                &&
                isHeader(
                    result[0]
                )
            ) {
                result.shift();
            }


            return result;

        };


    /* =====================================================
       PASTE
    ====================================================== */

    grid.addEventListener(
        'paste',
        event => {

            const cell =
                event.target.closest(
                    'td'
                );


            if (!cell) {
                return;
            }


            const text =
                event.clipboardData
                    ?.getData(
                        'text/plain'
                    )
                ??
                '';


            if (
                !text.includes('\t')
                &&
                !text.includes('\n')
            ) {
                return;
            }


            event.preventDefault();


            let matrix =
                text
                .replace(
                    /\r\n/g,
                    '\n'
                )
                .replace(
                    /\r/g,
                    '\n'
                )
                .split('\n')
                .map(
                    line =>
                        line.split('\t')
                );


            matrix =
                cleanMatrix(
                    matrix
                );


            if (
                matrix.length ===
                0
            ) {
                return;
            }


            const startRow =
                cell.parentElement
                    .rowIndex
                -
                grid.tHead.rows.length;


            const startColumn =
                Number(
                    cell.dataset.column
                    ?? 0
                );


            ensureRows(
                startRow
                +
                matrix.length
                +
                5
            );


            matrix.forEach(
                (
                    sourceRow,
                    rowOffset
                ) => {

                    sourceRow.forEach(
                        (
                            value,
                            columnOffset
                        ) => {

                            const column =
                                startColumn
                                +
                                columnOffset;


                            if (
                                column
                                >=
                                columns.length
                            ) {
                                return;
                            }


                            const target =
                                tbody.rows[
                                    startRow
                                    +
                                    rowOffset
                                ]
                                ?.cells[
                                    column
                                ];


                            setCell(
                                target,
                                value
                            );

                        }
                    );

                }
            );


            updateCounter();

            resizeColumns();

        }
    );


    /* =====================================================
       INPUT
    ====================================================== */

    grid.addEventListener(
        'input',
        event => {

            const cell =
                event.target.closest(
                    'td'
                );


            if (!cell) {
                return;
            }


            updateState(
                cell
            );


            updateCounter();

            resizeColumns();

        }
    );


    /* =====================================================
       ADD ROWS
    ====================================================== */

    addButton
        ?.addEventListener(
            'click',
            () => {

                ensureRows(
                    tbody.rows.length
                    +
                    10
                );


                resizeColumns();

            }
        );


    /* =====================================================
       CLEAR
    ====================================================== */

    clearButton
        ?.addEventListener(
            'click',
            () => {

                [
                    ...tbody.rows
                ].forEach(
                    row => {

                        row.dataset
                            .payerAccountId =
                            '';


                        row.dataset
                            .payerCompanyId =
                            '';


                        [
                            ...row.cells
                        ].forEach(
                            cell => {

                                const select =
                                    cell.querySelector(
                                        'select'
                                    );


                                if (select) {
                                    select.value =
                                        '';

                                } else {
                                    cell.textContent =
                                        '';
                                }


                                cell.classList.remove(
                                    'has-value'
                                );

                            }
                        );

                    }
                );


                updateCounter();

                resizeColumns();

            }
        );


    /* =====================================================
       SOURCE TABS
    ====================================================== */

    const setMode =
        mode => {

            sourceOptions.forEach(
                option => {

                    option.classList.toggle(
                        'active',
                        option.dataset
                            .sourceOption
                        ===
                        mode
                    );

                }
            );


            sourcePanels.forEach(
                panel => {

                    const active =
                        panel.dataset
                            .sourcePanel
                        ===
                        mode;


                    panel.classList.toggle(
                        'active',
                        active
                    );


                    panel.hidden =
                        !active;

                }
            );


            if (
                mode ===
                'manual'
            ) {
                requestAnimationFrame(
                    resizeColumns
                );
            }

        };


    sourceOptions.forEach(
        option => {

            const radio =
                option.querySelector(
                    'input[type="radio"]'
                );


            radio
                ?.addEventListener(
                    'change',
                    () => {

                        if (
                            radio.checked
                        ) {
                            setMode(
                                radio.value
                            );
                        }

                    }
                );

        }
    );


    /* =====================================================
       FILE
    ====================================================== */

    fileInput
        ?.addEventListener(
            'change',
            () => {

                if (!fileName) {
                    return;
                }


                fileName.textContent =
                    fileInput.files
                        ?.length
                        ? fileInput
                            .files[0]
                            .name
                        : 'XLSX, XLS XML, atau CSV';

            }
        );


    /* =====================================================
       SERIALIZE
    ====================================================== */

    const serialize =
        () => {

            const rows =
                [];


            [
                ...tbody.rows
            ].forEach(
                row => {

                    const data =
                        {};


                    let filled =
                        false;


                    [
                        ...row.cells
                    ].forEach(
                        (
                            cell,
                            index
                        ) => {

                            const value =
                                cellValue(
                                    cell
                                );


                            data[
                                columns[index]
                            ] =
                                value;


                            if (
                                value !== ''
                            ) {
                                filled =
                                    true;
                            }

                        }
                    );


                    if (filled) {

                        rows.push(
                            data
                        );

                    }

                }
            );


            hidden.value =
                JSON.stringify(
                    rows
                );


            return rows;

        };


    /* =====================================================
       SUBMIT
    ====================================================== */

    form.addEventListener(
        'submit',
        event => {

            const mode =
                form.querySelector(
                    '[name="source_mode"]:checked'
                )
                ?.value
                ??
                'manual';


            if (
                mode ===
                'manual'
            ) {

                const rows =
                    serialize();


                if (
                    rows.length === 0
                ) {
                    event.preventDefault();


                    alert(
                        'Spreadsheet masih kosong.'
                    );


                    return;
                }

            } else {

                if (
                    !fileInput
                        ?.files
                        ?.length
                ) {
                    event.preventDefault();


                    alert(
                        'Pilih file transaksi terlebih dahulu.'
                    );


                    return;
                }

            }


            const button =
                document.getElementById(
                    'processTransactionButton'
                );


            if (button) {

                button.disabled =
                    true;


                button.innerHTML =
                    '<i class="bi bi-arrow-repeat"></i> Memproses...';

            }

        }
    );


    updateCounter();

    resizeColumns();


    window.addEventListener(
        'resize',
        resizeColumns
    );

})();

</script>