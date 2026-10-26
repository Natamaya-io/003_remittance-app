<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| LAPORAN - DETAIL BATCH (PRELINE UI & ALPINE.JS NATIVE)
|--------------------------------------------------------------------------
*/

$batch = is_array($batch ?? null) ? $batch : [];
$rows = is_array($rows ?? null) ? $rows : [];
$filters = is_array($filters ?? null) ? $filters : [];
$summary = is_array($summary ?? null) ? $summary : [];
$distinctTeams = is_array($distinctTeams ?? null) ? $distinctTeams : [];
$distinctCurrencies = is_array($distinctCurrencies ?? null) ? $distinctCurrencies : [];

$queryText = (string) ($filters['q'] ?? '');
$returnQuery = http_build_query($filters);

$batchDate = (string) ($batch['effective_date'] ?? $effectiveDate ?? $batch['report_date'] ?? '');
$dateBackUrl = $batchDate !== '' ? url('/reports/date/' . $batchDate) : url('/reports');
$teamNameDisplay = (string) ($batch['effective_team'] ?? $batch['team_name'] ?? $batch['client_name'] ?? 'Tanpa Tim');

$totalRows = (int) ($summary['total_rows'] ?? count($rows));
$sentRows = (int) ($summary['sent_rows'] ?? 0);
$pendingRows = (int) ($summary['pending_rows'] ?? 0);
$successRows = (int) ($summary['success_rows'] ?? 0);
$failedRows = (int) ($summary['failed_rows'] ?? 0);
$totalAmount = (float) ($summary['total_amount'] ?? 0.0);
?>

<div class="max-w-screen-2xl mx-auto space-y-6"
     x-data="{
        selectedCount: 0,
        allSelected: false,
        editModalOpen: false,
        activeRow: {
            id: 0,
            order: '1',
            payer: '',
            beneficiary: '',
            amount: '',
            delivery: 'PENDING',
            result: 'PENDING',
            notes: ''
        },
        updateCount() {
            const checked = document.querySelectorAll('input[name=\'row_ids[]\']:checked').length;
            const total = document.querySelectorAll('input[name=\'row_ids[]\']').length;
            this.selectedCount = checked;
            this.allSelected = total > 0 && checked === total;
        },
        toggleAll(checked) {
            document.querySelectorAll('input[name=\'row_ids[]\']').forEach(el => el.checked = checked);
            this.updateCount();
        },
        openEditFromButton(btn) {
            this.activeRow = {
                id: parseInt(btn.dataset.rowId, 10) || 0,
                order: btn.dataset.rowOrder || '1',
                payer: btn.dataset.rowPayer || '',
                beneficiary: btn.dataset.rowBeneficiary || '',
                amount: btn.dataset.rowAmount || '',
                delivery: btn.dataset.rowDelivery || 'PENDING',
                result: btn.dataset.rowResult || 'PENDING',
                notes: btn.dataset.rowNotes || ''
            };
            this.editModalOpen = true;
        }
     }">

    <!-- =========================================================
         PAGE HEADING
         ========================================================= -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1.5 flex-wrap">
                <span class="inline-flex items-center py-0.5 px-2.5 rounded-full text-xs font-bold bg-blue-50 text-blue-800 border border-blue-200">
                    <?= e((string) ($batch['month_key'] ?? '')) ?>
                </span>
                <span class="inline-flex items-center py-0.5 px-2.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                    <?= e(format_date($batchDate)) ?>
                </span>
                <span class="text-xs text-gray-400">·</span>
                <span class="text-xs font-semibold text-gray-700"><?= e($teamNameDisplay) ?></span>
                <span class="text-xs text-gray-400">·</span>
                <span class="text-xs text-gray-500">Berkas: <span class="font-mono text-gray-700"><?= e((string) ($batch['source_filename'] ?? '')) ?></span></span>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a class="py-2 px-3.5 inline-flex items-center gap-x-2 text-xs font-medium rounded-lg border border-gray-200 bg-white text-gray-700 shadow-xs hover:bg-gray-50 focus:outline-hidden focus:bg-gray-50 transition-colors" href="<?= e($dateBackUrl) ?>">
                <i class="bi bi-arrow-left"></i>
                Report per Tim
            </a>

            <!-- Alpine Dropdown Opsi Batch -->
            <div class="relative inline-flex" x-data="{ openOptions: false }" @click.outside="openOptions = false">
                <button
                    type="button"
                    class="dropdown-toggle"
                    @click="openOptions = !openOptions"
                    :aria-expanded="openOptions"
                    aria-label="Menu Opsi Batch"
                >
                    <i class="bi bi-sliders"></i>
                    <span>Opsi Batch</span>
                    <i class="bi bi-chevron-down text-xs text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': openOptions }"></i>
                </button>

                <div
                    class="dropdown-menu absolute right-0 top-full mt-2 w-52 z-50 bg-white"
                    x-show="openOptions"
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    style="display: none;"
                    role="menu"
                >
                    <span class="dropdown-header">Export &amp; Berkas</span>
                    <a class="dropdown-item" href="<?= e(url('/reports/' . $batch['id'] . '/export')) ?>">
                        <i class="bi bi-file-earmark-excel text-emerald-600"></i>
                        Download File Excel
                    </a>
                    <?php if (can('report.manage')): ?>
                        <div class="dropdown-divider"></div>
                        <span class="dropdown-header">Manajemen</span>
                        <a class="dropdown-item" href="<?= e(url('/reports/' . $batch['id'] . '/edit')) ?>">
                            <i class="bi bi-pencil text-blue-600"></i>
                            Edit Metadata Report
                        </a>
                        <a class="dropdown-item" href="<?= e(url('/reports/create?' . http_build_query(['month' => $batch['month_key'] ?? '', 'date' => $batchDate]))) ?>">
                            <i class="bi bi-cloud-arrow-up text-indigo-600"></i>
                            Tambah Berkas Excel
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <a class="btn-primary py-2 px-3.5 inline-flex items-center gap-x-1.5 text-xs font-semibold rounded-lg shadow-xs focus:outline-hidden" href="<?= e(url('/reports/' . $batch['id'] . '/export')) ?>">
                <i class="bi bi-download"></i>
                Download Excel
            </a>
        </div>
    </div>

    <?php if ($pendingRows > 0): ?>
        <!-- ALERT STATUS: PENDING TRANSAKSI -->
        <div
            class="alert alert-warning"
            role="alert"
            x-data="{ show: true }"
            x-show="show"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
        >
            <div class="alert-content">
                <span class="alert-icon">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </span>
                <div class="alert-body">
                    <strong class="alert-title">Transaksi Pending</strong>
                    <div class="alert-message">Terdapat <?= number_format($pendingRows, 0, ',', '.') ?> baris transaksi yang belum terkirim (pending) pada berkas report ini.</div>
                </div>
            </div>
            <button
                class="alert-close"
                type="button"
                @click="show = false"
                aria-label="Tutup notifikasi"
            >
                <i class="bi bi-x text-lg leading-none"></i>
            </button>
        </div>
    <?php endif; ?>

    <!-- =========================================================
         SUMMARY KPI GRID (6 METRIK OPERASIONAL)
         ========================================================= -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <div class="bg-white border border-gray-200 rounded-2xl p-4 shadow-xs">
            <span class="text-xs font-medium text-gray-500 block mb-1">Total Baris</span>
            <strong class="text-xl font-bold text-gray-900 block font-mono"><?= number_format($totalRows, 0, ',', '.') ?></strong>
        </div>
        <div class="bg-white border border-gray-200 rounded-2xl p-4 shadow-xs">
            <span class="text-xs font-medium text-gray-500 block mb-1">Total Nominal</span>
            <strong class="text-base font-bold text-gray-900 block font-mono truncate" title="<?= number_format($totalAmount, 2, ',', '.') ?>">
                <?= number_format($totalAmount, 0, ',', '.') ?>
            </strong>
        </div>
        <div class="bg-emerald-50/60 border border-emerald-200 rounded-2xl p-4 shadow-xs">
            <span class="text-xs font-medium text-emerald-700 block mb-1">Sukses</span>
            <strong class="text-xl font-bold text-emerald-800 block font-mono"><?= number_format($successRows, 0, ',', '.') ?></strong>
        </div>
        <div class="bg-blue-50/60 border border-blue-200 rounded-2xl p-4 shadow-xs">
            <span class="text-xs font-medium text-blue-700 block mb-1">Terkirim</span>
            <strong class="text-xl font-bold text-blue-800 block font-mono"><?= number_format($sentRows, 0, ',', '.') ?></strong>
        </div>
        <div class="bg-amber-50/60 border border-amber-200 rounded-2xl p-4 shadow-xs">
            <span class="text-xs font-medium text-amber-700 block mb-1">Pending</span>
            <strong class="text-xl font-bold text-amber-800 block font-mono"><?= number_format($pendingRows, 0, ',', '.') ?></strong>
        </div>
        <div class="bg-red-50/60 border border-red-200 rounded-2xl p-4 shadow-xs">
            <span class="text-xs font-medium text-red-700 block mb-1">Gagal / Retur</span>
            <strong class="text-xl font-bold text-red-800 block font-mono"><?= number_format($failedRows, 0, ',', '.') ?></strong>
        </div>
    </div>

    <!-- =========================================================
         FILTER PENCARIAN & PARAMETER TABEL
         ========================================================= -->
    <div class="bg-white border border-gray-200 rounded-2xl p-4 sm:p-5 shadow-xs">
        <form method="get" action="<?= e(url('/reports/' . $batch['id'])) ?>" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Cari transaksi dalam report ini</label>
                    <?= render_search_input('q', $queryText, [
                        'placeholder' => 'Cari payer, beneficiary, bank, invoice, SWIFT, akun...',
                    ]) ?>
                </div>

                <div x-data="{
                    open: false,
                    selected: '<?= e($filters['delivery_status'] ?? '') ?>',
                    label: '<?= ($filters['delivery_status'] ?? '') === 'PENDING' ? 'Belum Terkirim' : (($filters['delivery_status'] ?? '') === 'SENT' ? 'Terkirim' : 'Semua status kirim') ?>'
                }">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Status kirim</label>
                    <div class="custom-select-root relative" @click.outside="open = false">
                        <input type="hidden" name="delivery_status" :value="selected">
                        <button
                            type="button"
                            class="dropdown-toggle"
                            @click="open = !open"
                            :aria-expanded="open"
                            aria-label="Filter Status Kirim"
                        >
                            <i class="bi bi-send text-gray-400"></i>
                            <span class="truncate text-gray-900" x-text="label"></span>
                            <i class="bi bi-chevron-down text-xs text-gray-400 ml-auto transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                        </button>

                        <div
                            class="dropdown-menu absolute left-0 right-0 top-full mt-1.5 max-h-60 overflow-y-auto z-50 bg-white"
                            x-show="open"
                            x-cloak
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0"
                            x-transition:enter-end="opacity-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0"
                            :class="{ 'block': open, 'hidden': !open }"
                            style="display: none;"
                            role="listbox"
                        >
                            <button
                                type="button"
                                class="dropdown-item"
                                :class="{ 'active': selected === '' }"
                                @click="selected = ''; label = 'Semua status kirim'; open = false"
                            >
                                <span>Semua status kirim</span>
                                <i x-show="selected === ''" class="bi bi-check2 text-blue-600 text-sm ml-auto"></i>
                            </button>
                            <button
                                type="button"
                                class="dropdown-item"
                                :class="{ 'active': selected === 'PENDING' }"
                                @click="selected = 'PENDING'; label = 'Belum Terkirim'; open = false"
                            >
                                <span>Belum Terkirim</span>
                                <i x-show="selected === 'PENDING'" class="bi bi-check2 text-blue-600 text-sm ml-auto"></i>
                            </button>
                            <button
                                type="button"
                                class="dropdown-item"
                                :class="{ 'active': selected === 'SENT' }"
                                @click="selected = 'SENT'; label = 'Terkirim'; open = false"
                            >
                                <span>Terkirim</span>
                                <i x-show="selected === 'SENT'" class="bi bi-check2 text-blue-600 text-sm ml-auto"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div x-data="{
                    open: false,
                    selected: '<?= e($filters['result_status'] ?? '') ?>',
                    label: '<?= ($filters['result_status'] ?? '') === 'PENDING' ? 'Pending' : (($filters['result_status'] ?? '') === 'SUCCESS' ? 'Success' : (($filters['result_status'] ?? '') === 'FAILED' ? 'Failed' : 'Semua hasil')) ?>'
                }">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Hasil transaksi</label>
                    <div class="custom-select-root relative" @click.outside="open = false">
                        <input type="hidden" name="result_status" :value="selected">
                        <button
                            type="button"
                            class="dropdown-toggle"
                            @click="open = !open"
                            :aria-expanded="open"
                            aria-label="Filter Hasil Transaksi"
                        >
                            <i class="bi bi-check-circle text-gray-400"></i>
                            <span class="truncate text-gray-900" x-text="label"></span>
                            <i class="bi bi-chevron-down text-xs text-gray-400 ml-auto transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                        </button>

                        <div
                            class="dropdown-menu absolute left-0 right-0 top-full mt-1.5 max-h-60 overflow-y-auto z-50 bg-white"
                            x-show="open"
                            x-cloak
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0"
                            x-transition:enter-end="opacity-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0"
                            :class="{ 'block': open, 'hidden': !open }"
                            style="display: none;"
                            role="listbox"
                        >
                            <button
                                type="button"
                                class="dropdown-item"
                                :class="{ 'active': selected === '' }"
                                @click="selected = ''; label = 'Semua hasil'; open = false"
                            >
                                <span>Semua hasil</span>
                                <i x-show="selected === ''" class="bi bi-check2 text-blue-600 text-sm ml-auto"></i>
                            </button>
                            <button
                                type="button"
                                class="dropdown-item"
                                :class="{ 'active': selected === 'PENDING' }"
                                @click="selected = 'PENDING'; label = 'Pending'; open = false"
                            >
                                <span>Pending</span>
                                <i x-show="selected === 'PENDING'" class="bi bi-check2 text-blue-600 text-sm ml-auto"></i>
                            </button>
                            <button
                                type="button"
                                class="dropdown-item"
                                :class="{ 'active': selected === 'SUCCESS' }"
                                @click="selected = 'SUCCESS'; label = 'Success'; open = false"
                            >
                                <span>Success</span>
                                <i x-show="selected === 'SUCCESS'" class="bi bi-check2 text-blue-600 text-sm ml-auto"></i>
                            </button>
                            <button
                                type="button"
                                class="dropdown-item"
                                :class="{ 'active': selected === 'FAILED' }"
                                @click="selected = 'FAILED'; label = 'Failed'; open = false"
                            >
                                <span>Failed</span>
                                <i x-show="selected === 'FAILED'" class="bi bi-check2 text-blue-600 text-sm ml-auto"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-gray-100">
                <a class="py-1.5 px-3 inline-flex items-center text-xs font-medium rounded-lg border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 shadow-xs transition-colors focus:outline-hidden focus:bg-gray-50" href="<?= e(url('/reports/' . $batch['id'])) ?>">
                    Reset
                </a>
                <button class="btn-primary py-1.5 px-3.5 inline-flex items-center gap-x-1.5 text-xs font-semibold rounded-lg shadow-xs focus:outline-hidden" type="submit">
                    <i class="bi bi-funnel"></i>
                    Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    <!-- =========================================================
         WIDE TRANSACTION DATA TABLE (PRELINE UI + THE NAKED ZEBRA)
         ========================================================= -->
    <section class="bg-white border border-gray-200 rounded-2xl shadow-xs overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-gray-900"><?= number_format(count($rows), 0, ',', '.') ?> Baris Transaksi</h3>
            </div>
            <?php if (can('report.manage')): ?>
                <div class="text-xs text-gray-500">
                    <span x-text="selectedCount">0</span> baris dipilih
                </div>
            <?php endif; ?>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-xs border-collapse">
                <thead class="bg-gray-50/75 border-b border-gray-200">
                    <tr class="text-gray-600 font-semibold text-[11px] uppercase tracking-wider">
                        <?php if (can('report.manage')): ?>
                            <th class="py-3.5 px-3 w-10 text-center">
                                <input type="checkbox" :checked="allSelected" @change="toggleAll($el.checked)" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </th>
                        <?php endif; ?>
                        <th class="py-3.5 px-3">TEAM</th>
                        <th class="py-3.5 px-3">No.</th>
                        <th class="py-3.5 px-3">Tanggal</th>
                        <th class="py-3.5 px-3">Bill To/Payer</th>
                        <th class="py-3.5 px-3">Beneficiary</th>
                        <th class="py-3.5 px-3">Account</th>
                        <th class="py-3.5 px-3">Beneficiary Bank</th>
                        <th class="py-3.5 px-3">Bank Address</th>
                        <th class="py-3.5 px-3 text-right">Amounts</th>
                        <th class="py-3.5 px-3">Country</th>
                        <th class="py-3.5 px-3">City</th>
                        <th class="py-3.5 px-3">Beneficiary Address</th>
                        <th class="py-3.5 px-3">SWIFT</th>
                        <th class="py-3.5 px-3">Payment Details</th>
                        <th class="py-3.5 px-3">Currency</th>
                        <th class="py-3.5 px-3">Invoice</th>
                        <th class="py-3.5 px-3">Contract</th>
                        <th class="py-3.5 px-3">Correspondent</th>
                        <th class="py-3.5 px-3">Swift Corr</th>
                        <th class="py-3.5 px-3">Country Corr</th>
                        <th class="py-3.5 px-3">Bank of Payer</th>
                        <th class="py-3.5 px-3">Invoice Link</th>
                        <th class="py-3.5 px-3">Bank Proses</th>
                        <th class="py-3.5 px-3">Status Kirim</th>
                        <th class="py-3.5 px-3">Hasil</th>
                        <th class="py-3.5 px-3">Notes</th>
                        <th class="py-3.5 px-3">Sheet</th>
                        <th class="py-3.5 px-4 text-right sticky right-0 bg-gray-50/95 z-20 shadow-[-4px_0_10px_rgba(0,0,0,0.04)]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                <?php foreach ($rows as $row): ?>
                    <?php
                    $rowId = (int) ($row['id'] ?? 0);
                    $itemNumber = (int) ($row['item_no'] ?: ($row['source_row'] ?? 1));
                    $teamLabel = (string) ($row['team_label'] ?: $teamNameDisplay ?: '-');
                    $trxDate = (string) ($row['transaction_date'] ?? '');
                    $payerName = (string) ($row['bill_to'] ?? '');
                    $beneficiaryName = (string) ($row['beneficiary_name'] ?? '');
                    $beneficiaryAccount = (string) ($row['beneficiary_account'] ?? '');
                    $beneficiaryBank = (string) ($row['beneficiary_bank'] ?? '');
                    $bankAddress = (string) ($row['beneficiary_bank_address'] ?? '');
                    $amountNum = (float) ($row['amount'] ?? 0);
                    $currencyStr = (string) ($row['currency'] ?: 'IDR');
                    $amountFormatted = format_money($amountNum, $currencyStr);
                    $countryStr = (string) ($row['beneficiary_country'] ?? '');
                    $cityStr = (string) ($row['beneficiary_city'] ?? '');
                    $beneficiaryAddress = (string) ($row['beneficiary_address'] ?? '');
                    $swiftCode = (string) ($row['swift'] ?? '');
                    $paymentDetails = (string) ($row['payment_details'] ?? '');
                    $invoiceNo = (string) ($row['invoice_no'] ?? '');
                    $contractNo = (string) ($row['contract_no'] ?? '');
                    $corrBank = (string) ($row['correspondent_bank'] ?? '');
                    $corrSwift = (string) ($row['correspondent_swift'] ?? '');
                    $corrCountry = (string) ($row['correspondent_country'] ?? '');
                    $bankOfPayer = (string) ($row['bank_of_payer'] ?? '');
                    $invoiceLink = (string) ($row['invoice_link'] ?? '');
                    $processingBank = (string) ($row['processing_bank'] ?? '');
                    $deliveryStatus = (string) ($row['delivery_status'] ?? 'PENDING');
                    $resultStatus = (string) ($row['result_status'] ?? 'PENDING');
                    $notesStr = (string) ($row['notes'] ?? '');
                    $sourceSheet = (string) ($row['source_sheet'] ?? '-');
                    ?>
                    <tr class="even:bg-gray-50/80 [data-no-hover]">
                        <?php if (can('report.manage')): ?>
                            <td class="py-3 px-3 text-center">
                                <input type="checkbox" name="row_ids[]" value="<?= $rowId ?>" form="bulkStatusForm" @change="updateCount()" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </td>
                        <?php endif; ?>
                        <td class="py-3 px-3 font-semibold text-gray-900 whitespace-nowrap"><?= e($teamLabel) ?></td>
                        <td class="py-3 px-3 font-mono text-gray-500"><?= $itemNumber ?></td>
                        <td class="py-3 px-3 whitespace-nowrap"><?= e(format_date($trxDate)) ?></td>
                        <td class="py-3 px-3 font-medium text-gray-900 whitespace-nowrap max-w-xs truncate" title="<?= e($payerName) ?>"><?= e($payerName) ?></td>
                        <td class="py-3 px-3 font-medium text-gray-900 whitespace-nowrap max-w-xs truncate" title="<?= e($beneficiaryName) ?>"><?= e($beneficiaryName) ?></td>
                        <td class="py-3 px-3 font-mono text-xs whitespace-nowrap"><?= e($beneficiaryAccount) ?></td>
                        <td class="py-3 px-3 whitespace-nowrap max-w-xs truncate" title="<?= e($beneficiaryBank) ?>"><?= e($beneficiaryBank) ?></td>
                        <td class="py-3 px-3 whitespace-nowrap max-w-xs truncate text-gray-500" title="<?= e($bankAddress) ?>"><?= e($bankAddress) ?></td>
                        <td class="py-3 px-3 text-right font-mono font-bold text-gray-900 whitespace-nowrap"><?= $amountFormatted ?></td>
                        <td class="py-3 px-3 whitespace-nowrap"><?= e($countryStr) ?></td>
                        <td class="py-3 px-3 whitespace-nowrap"><?= e($cityStr) ?></td>
                        <td class="py-3 px-3 whitespace-nowrap max-w-xs truncate text-gray-500" title="<?= e($beneficiaryAddress) ?>"><?= e($beneficiaryAddress) ?></td>
                        <td class="py-3 px-3 font-mono whitespace-nowrap"><?= e($swiftCode) ?></td>
                        <td class="py-3 px-3 whitespace-nowrap max-w-xs truncate text-gray-500" title="<?= e($paymentDetails) ?>"><?= e($paymentDetails) ?></td>
                        <td class="py-3 px-3 font-mono whitespace-nowrap"><?= e($currencyStr) ?></td>
                        <td class="py-3 px-3 font-mono whitespace-nowrap"><?= e($invoiceNo) ?></td>
                        <td class="py-3 px-3 whitespace-nowrap"><?= e($contractNo) ?></td>
                        <td class="py-3 px-3 whitespace-nowrap max-w-xs truncate text-gray-500" title="<?= e($corrBank) ?>"><?= e($corrBank) ?></td>
                        <td class="py-3 px-3 font-mono whitespace-nowrap"><?= e($corrSwift) ?></td>
                        <td class="py-3 px-3 whitespace-nowrap"><?= e($corrCountry) ?></td>
                        <td class="py-3 px-3 whitespace-nowrap"><?= e($bankOfPayer) ?></td>
                        <td class="py-3 px-3 whitespace-nowrap">
                            <?php if ($invoiceLink !== ''): ?>
                                <a class="text-blue-600 hover:underline inline-flex items-center gap-1" href="<?= e($invoiceLink) ?>" target="_blank" rel="noopener">
                                    <i class="bi bi-box-arrow-up-right"></i> Link
                                </a>
                            <?php else: ?>
                                <span class="text-gray-300">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3 px-3 whitespace-nowrap"><?= e($processingBank ?: '-') ?></td>
                        <td class="py-3 px-3 whitespace-nowrap">
                            <?php if ($deliveryStatus === 'SENT'): ?>
                                <span class="inline-flex items-center gap-1 py-0.5 px-2 rounded-full text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                    <i class="bi bi-send-check"></i> Terkirim
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center gap-1 py-0.5 px-2 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                    <i class="bi bi-clock"></i> Belum
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3 px-3 whitespace-nowrap">
                            <?php if ($resultStatus === 'SUCCESS'): ?>
                                <span class="inline-flex items-center gap-1 py-0.5 px-2 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <i class="bi bi-check-lg"></i> Sukses
                                </span>
                            <?php elseif ($resultStatus === 'FAILED'): ?>
                                <span class="inline-flex items-center gap-1 py-0.5 px-2 rounded-full text-[11px] font-semibold bg-red-50 text-red-700 border border-red-200">
                                    <i class="bi bi-x-lg"></i> Gagal
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center gap-1 py-0.5 px-2 rounded-full text-[11px] font-semibold bg-gray-100 text-gray-700">
                                    Pending
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3 px-3 whitespace-nowrap max-w-xs truncate text-gray-500" title="<?= e($notesStr) ?>"><?= e($notesStr ?: '-') ?></td>
                        <td class="py-3 px-3 text-gray-400 whitespace-nowrap"><?= e($sourceSheet) ?></td>
                        
                        <!-- STICKY ACTION COLUMN (ALWAYS PINNED TO THE RIGHT) -->
                        <td class="py-3 px-4 text-right sticky right-0 bg-white z-10 shadow-[-4px_0_10px_rgba(0,0,0,0.04)] whitespace-nowrap">
                            <div class="inline-flex items-center gap-1.5 justify-end">
                                <?php if (!empty($row['transaction_id'])): ?>
                                    <?= render_tooltip('Buka Transaksi Sistem', '<a class="p-1 rounded-md text-gray-500 hover:text-blue-600 hover:bg-gray-100" href="' . e(url('/transactions/' . $row['transaction_id'])) . '"><i class="bi bi-link-45deg text-base"></i></a>', ['placement' => 'left']) ?>
                                <?php endif; ?>

                                <?php if (can('report.manage')): ?>
                                    <?= render_tooltip('Ubah Status & Catatan', '<button
                                        type="button"
                                        data-row-id="' . $rowId . '"
                                        data-row-order="' . $itemNumber . '"
                                        data-row-payer="' . e($payerName) . '"
                                        data-row-beneficiary="' . e($beneficiaryName) . '"
                                        data-row-amount="' . e($amountFormatted) . '"
                                        data-row-delivery="' . e($deliveryStatus) . '"
                                        data-row-result="' . e($resultStatus) . '"
                                        data-row-notes="' . e($notesStr) . '"
                                        @click="openEditFromButton($el)"
                                        class="py-1 px-2.5 inline-flex items-center gap-x-1 text-xs font-semibold rounded-lg border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 shadow-xs transition-colors focus:outline-hidden focus:bg-gray-50"
                                    >
                                        <i class="bi bi-pencil-square text-blue-600"></i>
                                        Ubah
                                    </button>', ['placement' => 'left']) ?>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($rows === []): ?>
                    <tr>
                        <td colspan="30" class="py-12 text-center text-gray-400">
                            <i class="bi bi-file-earmark-x text-2xl block mb-2 text-gray-300"></i>
                            Tidak ada baris yang cocok dengan filter.
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <!-- =========================================================
         FLOATING STICKY BULK BAR (SLIDES UP WHEN ROWS ARE CHECKED)
         ========================================================= -->
    <?php if (can('report.manage')): ?>
        <div
            x-show="selectedCount > 0"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed bottom-6 inset-x-0 z-40 max-w-2xl mx-auto px-4 pointer-events-none"
            style="display: none;"
        >
            <div class="pointer-events-auto bg-[#2B2A2A] text-white p-3 sm:p-4 rounded-2xl shadow-2xl border border-gray-700 flex flex-wrap items-center justify-between gap-3">
                <form id="bulkStatusForm" method="post" action="<?= e(url('/reports/' . $batch['id'] . '/bulk-status')) ?>" class="flex flex-wrap items-center justify-between gap-3 w-full text-xs">
                    <?= csrf_field() ?>
                    <input type="hidden" name="return_query" value="<?= e($returnQuery) ?>">

                    <div class="flex items-center gap-2">
                        <span class="size-6 rounded-full bg-blue-500 text-white flex items-center justify-center font-bold text-xs" x-text="selectedCount">0</span>
                        <span class="font-semibold text-gray-100">baris dipilih</span>
                    </div>

                    <div class="flex items-center gap-2 flex-wrap">
                        <select class="py-1.5 px-2.5 rounded-lg text-xs bg-gray-800 text-white border border-gray-600 focus:border-blue-400 focus:outline-none" name="delivery_status">
                            <option value="">Status Kirim...</option>
                            <option value="PENDING">Belum Terkirim</option>
                            <option value="SENT">Terkirim</option>
                        </select>

                        <select class="py-1.5 px-2.5 rounded-lg text-xs bg-gray-800 text-white border border-gray-600 focus:border-blue-400 focus:outline-none" name="result_status">
                            <option value="">Hasil...</option>
                            <option value="PENDING">Pending</option>
                            <option value="SUCCESS">Success</option>
                            <option value="FAILED">Failed</option>
                        </select>

                        <button class="btn-primary py-1.5 px-3.5 inline-flex items-center gap-x-1.5 text-xs font-semibold rounded-lg shadow-xs focus:outline-hidden" type="submit">
                            Terapkan
                        </button>

                        <button type="button" @click="toggleAll(false)" class="py-1.5 px-2.5 inline-flex items-center text-xs font-medium rounded-lg text-gray-300 hover:text-white hover:bg-gray-700/80 transition-colors focus:outline-hidden">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <!-- =========================================================
         UNIVERSAL ROW STATUS EDIT MODAL (CLEAN & NON-CLIPPING)
         ========================================================= -->
    <?php if (can('report.manage')): ?>
        <div
            x-show="editModalOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs"
            style="display: none;"
            @keydown.escape.window="editModalOpen = false"
        >
            <div
                class="bg-white rounded-2xl border border-gray-200 shadow-2xl w-full max-w-md p-6 relative space-y-4"
                @click.outside="editModalOpen = false"
            >
                <!-- Modal Header -->
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <div>
                        <h3 class="text-sm font-bold text-gray-900">Ubah Status Transaksi #<span x-text="activeRow.order"></span></h3>
                        <p class="text-xs text-gray-500 mt-0.5 truncate max-w-xs" x-text="activeRow.beneficiary"></p>
                    </div>
                    <button type="button" @click="editModalOpen = false" class="size-8 inline-flex justify-center items-center text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition-colors focus:outline-hidden focus:bg-gray-100">
                        <i class="bi bi-x-lg text-sm"></i>
                    </button>
                </div>

                <!-- Transaction Preview Strip -->
                <div class="bg-gray-50 rounded-xl p-3 border border-gray-200 text-xs space-y-1">
                    <div class="flex justify-between text-gray-500">
                        <span>Payer:</span>
                        <strong class="text-gray-800 font-medium truncate max-w-[200px]" x-text="activeRow.payer"></strong>
                    </div>
                    <div class="flex justify-between text-gray-500">
                        <span>Nominal:</span>
                        <strong class="text-blue-700 font-bold font-mono" x-text="activeRow.amount"></strong>
                    </div>
                </div>

                <!-- Edit Form -->
                <form method="post" :action="'<?= e(url('/reports/' . $batch['id'] . '/rows/')) ?>' + activeRow.id + '/status'" class="space-y-4">
                    <?= csrf_field() ?>
                    <input type="hidden" name="return_query" value="<?= e($returnQuery) ?>">

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Status Kirim</label>
                        <select class="py-2 px-3 block w-full border border-gray-300 rounded-xl text-xs focus:border-blue-500 focus:ring-blue-500 shadow-xs bg-white" name="delivery_status" x-model="activeRow.delivery">
                            <option value="PENDING">Belum Terkirim</option>
                            <option value="SENT">Terkirim</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Hasil Transaksi</label>
                        <select class="py-2 px-3 block w-full border border-gray-300 rounded-xl text-xs focus:border-blue-500 focus:ring-blue-500 shadow-xs bg-white" name="result_status" x-model="activeRow.result">
                            <option value="PENDING">Pending</option>
                            <option value="SUCCESS">Success</option>
                            <option value="FAILED">Failed</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Catatan Operasional</label>
                        <textarea class="py-2 px-3 block w-full border border-gray-300 rounded-xl text-xs focus:border-blue-500 focus:ring-blue-500 shadow-xs" name="notes" rows="2" x-model="activeRow.notes" placeholder="Catatan opsional..."></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-gray-100">
                        <button type="button" @click="editModalOpen = false" class="py-2 px-3.5 inline-flex items-center text-xs font-medium rounded-lg border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 shadow-xs transition-colors focus:outline-hidden focus:bg-gray-50">
                            Batal
                        </button>
                        <button type="submit" class="btn-primary py-2 px-4 inline-flex items-center justify-center text-xs font-semibold rounded-lg shadow-xs focus:outline-hidden">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>

</div>
