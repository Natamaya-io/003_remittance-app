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
         HEADER & PAGE ACTION
    ====================================================== -->
    <header class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-2">
        <div>
            <?= render_heading_with_info(
                'Input Transaksi BRI',
                'Tahap 1: Input data transaksi melalui copy paste spreadsheet atau unggah file Excel/CSV. Data payer dapat dipilih langsung dari master Rekening Payer.',
                [
                    'titleTag'     => 'h1',
                    'titleClass'   => 'text-xl font-semibold font-heading text-gray-900 m-0',
                    'wrapperClass' => 'inline-flex items-center gap-2',
                ]
            ) ?>
        </div>

        <div class="flex items-center gap-2">
            <a class="btn-secondary" href="<?= url('/transactions') ?>">
                <i class="bi bi-arrow-left"></i>
                <span>Pilihan Bank</span>
            </a>
        </div>
    </header>

    <!-- =====================================================
         GAMIFIED STEPPER (4 TAHAP WORKFLOW)
    ====================================================== -->
    <div class="bg-white border border-gray-200 rounded-2xl p-4 shadow-sm mb-2">
        <div class="flex items-center justify-between gap-2 overflow-x-auto text-xs sm:text-sm select-none">
            <div class="flex items-center gap-2 text-[#315B8C] font-semibold flex-shrink-0">
                <span class="w-6 h-6 rounded-full bg-[#315B8C] text-white flex items-center justify-center text-xs font-semibold">1</span>
                <span>Input Data</span>
            </div>

            <i class="bi bi-chevron-right text-gray-300 text-xs flex-shrink-0"></i>

            <div class="flex items-center gap-2 text-gray-400 font-medium flex-shrink-0">
                <span class="w-6 h-6 rounded-full bg-[#F5F2F2] text-gray-500 border border-gray-200 flex items-center justify-center text-xs font-medium">2</span>
                <span>Bentuk Format</span>
            </div>

            <i class="bi bi-chevron-right text-gray-300 text-xs flex-shrink-0"></i>

            <div class="flex items-center gap-2 text-gray-400 font-medium flex-shrink-0">
                <span class="w-6 h-6 rounded-full bg-[#F5F2F2] text-gray-500 border border-gray-200 flex items-center justify-center text-xs font-medium">3</span>
                <span>Hapus Tanda Baca</span>
            </div>

            <i class="bi bi-chevron-right text-gray-300 text-xs flex-shrink-0"></i>

            <div class="flex items-center gap-2 text-gray-400 font-medium flex-shrink-0">
                <span class="w-6 h-6 rounded-full bg-[#F5F2F2] text-gray-500 border border-gray-200 flex items-center justify-center text-xs font-medium">4</span>
                <span>Download File</span>
            </div>
        </div>
    </div>

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
             CARD 1: PENGATURAN TRANSAKSI
        ================================================== -->
        <section class="trx-card">
            <header class="trx-card-head">
                <?= render_heading_with_info(
                    'Pengaturan Transaksi',
                    'Tentukan nama batch transaksi, email penerima file hasil olahan, dan alokasi tim operasional.',
                    [
                        'titleTag'     => 'h3',
                        'titleClass'   => 'text-base font-semibold font-heading text-gray-900 m-0',
                        'wrapperClass' => 'inline-flex items-center gap-2',
                    ]
                ) ?>
            </header>

            <div class="trx-config-grid">
                <label>
                    <span class="form-label">Nama batch</span>
                    <input
                        class="custom-form-input"
                        name="job_name"
                        placeholder="Contoh: TRANSAKSI 11 AGUSTUS 2026"
                    >
                </label>

                <label>
                    <span class="form-label">Email output</span>
                    <input
                        class="custom-form-input"
                        type="email"
                        name="output_email"
                        value="remit.service09@gmail.com"
                        required
                    >
                </label>

                <?php if (!App\Core\Auth::isTransactionAdmin()): ?>
                    <label>
                        <span class="form-label">Tim</span>
                        <select
                            class="custom-form-select"
                            name="team_no"
                        >
                            <?php for ($i = 1; $i <= 4; $i++): ?>
                                <option value="<?= $i ?>">Tim <?= $i ?></option>
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
                    <span>Buat perusahaan otomatis jika payer belum tersedia.</span>
                </label>
            </div>
        </section>

        <!-- =================================================
             CARD 2: SUMBER DATA
        ================================================== -->
        <section class="trx-card">
            <header class="trx-card-head">
                <?= render_heading_with_info(
                    'Sumber Data Transaksi',
                    'Pilih mode sumber data: copy paste langsung dari spreadsheet atau unggah file Excel/CSV.',
                    [
                        'titleTag'     => 'h3',
                        'titleClass'   => 'text-base font-semibold font-heading text-gray-900 m-0',
                        'wrapperClass' => 'inline-flex items-center gap-2',
                    ]
                ) ?>
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
                        <strong>Copy Paste Excel</strong>
                        <small>Input seperti spreadsheet</small>
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
                        <strong>Import Excel</strong>
                        <small>XLSX, XLS XML, CSV</small>
                    </span>
                </label>
            </div>

            <!-- =============================================
                 FILE PANEL
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
                    <strong>Pilih file transaksi</strong>
                    <span id="transactionFileName">
                        XLSX, XLS XML, atau CSV
                    </span>
                </label>
            </div>

            <!-- =============================================
                 MANUAL SPREADSHEET PANEL
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
                        <?= render_heading_with_info(
                            'Spreadsheet Transaksi',
                            'Copy dari Excel lalu tekan Ctrl + V pada sel pertama. Data payer dapat dipilih otomatis.',
                            [
                                'titleTag'     => 'strong',
                                'titleClass'   => 'text-sm font-semibold font-heading text-gray-900 m-0',
                                'wrapperClass' => 'inline-flex items-center gap-1.5',
                            ]
                        ) ?>
                    </div>

                    <div class="trx-toolbar-actions">
                        <span
                            class="trx-row-count"
                            id="manualRowCount"
                        >
                            0 baris
                        </span>

                        <button
                            class="btn-secondary !h-8 !px-3 text-xs"
                            type="button"
                            id="addManualRows"
                        >
                            <i class="bi bi-plus-lg"></i>
                            <span>10 baris</span>
                        </button>

                        <button
                            class="btn-secondary !h-8 !px-3 text-xs"
                            type="button"
                            id="clearManualGrid"
                        >
                            <i class="bi bi-eraser"></i>
                            <span>Bersihkan</span>
                        </button>
                    </div>
                </div>

                <div class="trx-master-note">
                    <i class="bi bi-bank"></i>
                    <div>
                        <strong>Master Rekening Payer</strong>
                        <span>
                            Memilih salah satu master akan mengisi Bill To, Nomor Rekening, Alamat, Kota, dan Negara pada baris yang sama.
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
                            <?php foreach ($manualColumns as $index => $column): ?>
                                <col
                                    data-grid-column="<?= $index ?>"
                                    data-column-key="<?= e($column['key']) ?>"
                                >
                            <?php endforeach; ?>
                        </colgroup>

                        <thead>
                            <tr class="trx-number-row">
                                <?php foreach ($manualColumns as $index => $column): ?>
                                    <th><?= $index + 1 ?></th>
                                <?php endforeach; ?>
                            </tr>

                            <tr class="trx-header-row">
                                <?php foreach ($manualColumns as $column): ?>
                                    <th><?= e($column['label']) ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>

                        <tbody></tbody>
                    </table>
                </div>

                <div class="trx-grid-footer">
                    <span>Kolom 15 sampai 18 memakai master Rekening Payer.</span>
                    <span>Sel kosong tetap putih.</span>
                </div>
            </div>
        </section>

        <!-- =================================================
             SUBMIT BAR
        ================================================== -->
        <section class="trx-submit-card">
            <div class="flex items-center gap-3">
                <i class="bi bi-check2-circle text-xl text-gray-700"></i>
                <div>
                    <strong class="text-sm font-semibold text-gray-900 block">Siap diproses</strong>
                    <span class="text-xs text-gray-500">Tahap 2 dan Tahap 3 dibuat otomatis.</span>
                </div>
            </div>

            <button
                class="btn-primary"
                type="submit"
                id="processTransactionButton"
            >
                <i class="bi bi-play-fill"></i>
                <span>Proses Tahap 1</span>
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
    gap: 20px;
    width: 100%;
    min-width: 0;
}

/* =========================================================
   FORM & CARDS
========================================================= */
.trx-form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.trx-card {
    padding: 24px;
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    background: #ffffff;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.trx-card-head {
    display: flex;
    align-items: center;
    margin-bottom: 20px;
}

/* =========================================================
   CONFIGURATION
========================================================= */
.trx-config-grid {
    display: grid;
    grid-template-columns: minmax(260px, 1.3fr) minmax(260px, 1.1fr) minmax(220px, .8fr);
    gap: 16px;
    align-items: end;
}

.trx-config-grid label:not(.trx-check) {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.form-label {
    font-size: 13px;
    font-weight: 500;
    color: #413333;
}

.trx-check {
    display: flex;
    align-items: center;
    gap: 10px;
    min-height: 40px;
    padding: 8px 14px;
    border: 1px solid #d1d5db;
    border-radius: 9px;
    background: #F5F2F2;
    color: #413333;
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    user-select: none;
}

.trx-check input {
    accent-color: #315B8C;
    width: 16px;
    height: 16px;
    border-radius: 4px;
}

/* =========================================================
   SOURCE TABS
========================================================= */
.trx-source-tabs {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 14px;
    margin-bottom: 20px;
}

.trx-source-tab {
    position: relative;
    display: grid;
    grid-template-columns: auto minmax(0, 1fr);
    align-items: center;
    gap: 14px;
    min-height: 64px;
    padding: 12px 18px;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    background: #F5F2F2;
    cursor: pointer;
    transition: background-color 0.15s ease, border-color 0.15s ease;
}

.trx-source-tab input {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}

.trx-source-tab > i {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 38px;
    height: 38px;
    border-radius: 8px;
    color: #413333;
    background: #ffffff;
    border: 1px solid #d1d5db;
    font-size: 18px;
    transition: background-color 0.15s ease, color 0.15s ease;
}

.trx-source-tab > span {
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.trx-source-tab strong {
    font-family: 'DM Sans', sans-serif;
    font-size: 14px;
    font-weight: 600;
    color: #413333;
}

.trx-source-tab small {
    font-size: 12px;
    color: #6b7280;
}

.trx-source-tab.active {
    border-color: #315B8C;
    background: #315B8C;
}

.trx-source-tab.active > i {
    color: #315B8C;
    background: #ffffff;
    border-color: #ffffff;
}

.trx-source-tab.active strong {
    color: #ffffff;
}

.trx-source-tab.active small {
    color: rgba(255, 255, 255, 0.85);
}

/* =========================================================
   PANELS & FILE DROP ZONE
========================================================= */
.trx-source-panel {
    display: none;
}

.trx-source-panel.active {
    display: block;
}

.trx-drop-zone {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    gap: 6px;
    min-height: 140px;
    padding: 24px;
    border: 1.5px dashed #315B8C;
    border-radius: 12px;
    background: #F5F2F2;
    cursor: pointer;
    transition: background-color 0.15s ease;
}

.trx-drop-zone:hover {
    background: #eef2f6;
}

.trx-drop-zone input {
    display: none;
}

.trx-drop-zone > i {
    margin-bottom: 4px;
    color: #315B8C;
    font-size: 28px;
}

.trx-drop-zone strong {
    font-family: 'DM Sans', sans-serif;
    font-size: 14px;
    font-weight: 600;
    color: #413333;
}

.trx-drop-zone span {
    color: #6b7280;
    font-size: 12px;
}

/* =========================================================
   GRID TOOLBAR & MASTER NOTE
========================================================= */
.trx-grid-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 12px;
    padding: 10px 14px;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    background: #F5F2F2;
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
    min-height: 32px;
    padding: 0 12px;
    border-radius: 999px;
    color: #413333;
    background: #ffffff;
    border: 1px solid #d1d5db;
    font-size: 11px;
    font-weight: 600;
}

.trx-master-note {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 14px;
    padding: 10px 14px;
    border: 1px solid #d1d5db;
    border-radius: 10px;
    color: #413333;
    background: #F5F2F2;
}

.trx-master-note > i {
    color: #315B8C;
    font-size: 18px;
    flex-shrink: 0;
}

.trx-master-note > div {
    display: flex;
    flex-direction: column;
    gap: 1px;
}

.trx-master-note strong {
    font-size: 12px;
    font-weight: 600;
    color: #413333;
}

.trx-master-note span {
    font-size: 11.5px;
    color: #6b7280;
    line-height: 1.4;
}

/* =========================================================
   TABLE WRAPPER & SPREADSHEET GRID
========================================================= */
.trx-grid-wrap {
    position: relative;
    width: 100%;
    max-width: 100%;
    max-height: 520px;
    overflow: auto;
    border: 1px solid #d1d5db;
    border-radius: 12px;
    background: #ffffff;
    scrollbar-width: thin;
    scrollbar-color: #cbd5e1 #f8fafc;
}

.trx-grid-wrap::-webkit-scrollbar {
    width: 7px;
    height: 7px;
}

.trx-grid-wrap::-webkit-scrollbar-track {
    background: #f8fafc;
}

.trx-grid-wrap::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 9999px;
}

.trx-grid-wrap::-webkit-scrollbar-thumb:hover {
    background: #315B8C;
}

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
    height: 38px;
    padding: 6px 8px;
    border-right: 1px solid #e5e7eb;
    border-bottom: 1px solid #e5e7eb;
    box-sizing: border-box;
    font-size: 12px;
    line-height: 1.35;
    vertical-align: middle;
}

/* ROW 1: NUMBER ROW (SOLID DEEP BRAND BLUE #315B8C) */
.trx-number-row th {
    position: sticky;
    top: 0;
    z-index: 12;
    height: 32px;
    color: #ffffff;
    background: #315B8C;
    font-size: 11px;
    font-weight: 600;
    text-align: center;
    border-right: 1px solid rgba(255, 255, 255, 0.2);
    border-bottom: 1px solid #1e3c5d;
}

/* ROW 2: HEADER ROW (SOLID ASPHALT OFF-WHITE #F5F2F2) */
.trx-header-row th {
    position: sticky;
    top: 32px;
    z-index: 11;
    min-height: 50px;
    color: #413333;
    background: #F5F2F2;
    font-family: 'DM Sans', sans-serif;
    font-size: 11px;
    font-weight: 600;
    text-align: center;
    border-bottom: 2px solid #d1d5db;
    white-space: normal;
    overflow-wrap: anywhere;
}

/* CELLS */
.trx-excel-grid td {
    color: #111827;
    background: #ffffff;
    outline: none;
    white-space: pre-wrap;
    overflow-wrap: anywhere;
}

.trx-excel-grid tbody tr:nth-child(even) td:not(.has-value) {
    background: #F5F2F2;
}

.trx-excel-grid td.has-value {
    background: #ffffff;
    font-weight: 500;
}

.trx-excel-grid td[contenteditable="true"]:focus {
    position: relative;
    z-index: 4;
    background: #eff6ff !important;
    box-shadow: inset 0 0 0 2px #315B8C;
}

/* =========================================================
   MASTER SELECT
========================================================= */
.trx-excel-grid td.payer-master-cell {
    padding: 3px 4px;
    background: #ffffff !important;
}

.payer-master-select {
    width: 100%;
    height: 30px;
    padding: 2px 24px 2px 6px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    outline: 0;
    color: #413333;
    background: #ffffff;
    font-family: inherit;
    font-size: 11px;
    font-weight: 500;
    cursor: pointer;
}

.payer-master-select:hover {
    border-color: #9ca3af;
}

.payer-master-select:focus {
    border-color: #315B8C;
    box-shadow: 0 0 0 2px rgba(49, 91, 140, 0.15);
}

td[data-key="payer_account"] .payer-master-select {
    color: #315B8C;
    font-weight: 600;
}

td[data-key="payer_city"] .payer-master-select {
    color: #413333;
}

td[data-key="payer_country"] .payer-master-select {
    color: #413333;
}

/* =========================================================
   GRID FOOTER
========================================================= */
.trx-grid-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding-top: 10px;
    color: #6b7280;
    font-size: 11.5px;
}

/* =========================================================
   SUBMIT BAR
========================================================= */
.trx-submit-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    padding: 18px 22px;
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    background: #ffffff;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

/* =========================================================
   RESPONSIVE
========================================================= */
@media (max-width: 1024px) {
    .trx-config-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 768px) {
    .trx-config-grid,
    .trx-source-tabs {
        grid-template-columns: 1fr;
    }

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

                    if (window.RemitToast) {
                        window.RemitToast.error('Spreadsheet masih kosong.');
                    }
                    return;
                }

            } else {

                if (
                    !fileInput
                        ?.files
                        ?.length
                ) {
                    event.preventDefault();

                    if (window.RemitToast) {
                        window.RemitToast.error('Pilih file transaksi terlebih dahulu.');
                    }
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