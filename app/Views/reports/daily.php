<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| LAPORAN - REKAP TRANSAKSI HARIAN (PRELINE & ALPINE NATIVE)
|--------------------------------------------------------------------------
*/

$rows = is_array($rows ?? null) ? $rows : [];
$companies = is_array($companies ?? null) ? $companies : [];
$date = (string) ($date ?? date('Y-m-d'));
$companyId = (int) ($companyId ?? 0);
$print = (bool) ($print ?? false);
?>

<div class="max-w-7xl mx-auto space-y-6">

    <!-- =========================================================
         PAGE ACTIONS
         ========================================================= -->
    <div class="flex flex-wrap items-center justify-end gap-2">
        <?php if (can('transaction.manage')): ?>
            <a href="<?= url('/report_paste_import.php') ?>" class="btn-primary">
                <i class="bi bi-clipboard-data"></i>
                <span>Paste dari Excel</span>
            </a>
            <a href="<?= url('/transactions/import') ?>" class="btn-secondary">
                <i class="bi bi-file-earmark-excel"></i>
                <span>Import XLSX/CSV</span>
            </a>
            <a href="<?= url('/transactions/create') ?>" class="btn-secondary">
                <i class="bi bi-plus-lg"></i>
                <span>Input Manual</span>
            </a>
        <?php endif; ?>
    </div>

    <!-- =========================================================
         FILTER PANEL
         ========================================================= -->
    <div class="bg-white border border-gray-200 rounded-2xl p-4 sm:p-5 shadow-xs">
        <form method="get" action="<?= url('/reports/daily') ?>" class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
            <div class="md:col-span-3">
                <label class="block text-xs font-semibold text-gray-700 mb-1">Tanggal report</label>
                <?= render_datepicker('date', (string) $date, ['placeholder' => 'Pilih tanggal']) ?>
            </div>
            <div class="md:col-span-4"
                 <?php
                     $selectedCompanyName = 'Semua perusahaan';
                     foreach ($companies as $c) {
                         if ((int)$companyId === (int)$c['id']) {
                             $selectedCompanyName = $c['name'];
                             break;
                         }
                     }
                 ?>
                 x-data="{
                     open: false,
                     selected: <?= json_encode((string)$companyId) ?>,
                     label: <?= json_encode($selectedCompanyName) ?>
                 }">
                <label class="block text-xs font-semibold text-gray-700 mb-1">Perusahaan (PT)</label>
                <div class="custom-select-root relative" @click.outside="open = false">
                    <input type="hidden" name="company_id" :value="selected">
                    <button
                        type="button"
                        class="dropdown-toggle"
                        @click="open = !open"
                        :aria-expanded="open"
                        aria-label="Filter Perusahaan"
                    >
                        <i class="bi bi-building text-gray-400"></i>
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
                            :class="{ 'active': selected === '0' || selected === '' }"
                            @click="selected = '0'; label = 'Semua perusahaan'; open = false"
                        >
                            <span>Semua perusahaan</span>
                            <i x-show="selected === '0' || selected === ''" class="bi bi-check2 text-blue-600 text-sm ml-auto"></i>
                        </button>
                        <?php foreach ($companies as $company): ?>
                            <button
                                type="button"
                                class="dropdown-item"
                                :class="{ 'active': selected === '<?= (int) $company['id'] ?>' }"
                                @click="selected = '<?= (int) $company['id'] ?>'; label = '<?= addslashes(e($company['name'])) ?>'; open = false"
                            >
                                <span><?= e($company['name']) ?></span>
                                <i x-show="selected === '<?= (int) $company['id'] ?>'" class="bi bi-check2 text-blue-600 text-sm ml-auto"></i>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="md:col-span-5 flex flex-wrap items-center gap-2">
                <button class="btn-primary" type="submit">
                    <i class="bi bi-funnel"></i>
                    <span>Terapkan</span>
                </button>
                <a class="datepicker-preset-btn <?= $date === date('Y-m-d') ? 'active' : '' ?>" href="<?= url('/reports/daily') ?>">
                    Hari Ini
                </a>
                <?= render_tooltip('Export File CSV', '<a class="py-2 px-3 text-xs font-medium rounded-lg border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 shadow-xs inline-flex items-center gap-1.5 transition-colors" href="' . e(url('/reports/daily/export?' . http_build_query(['date' => $date, 'company_id' => $companyId]))) . '"><i class="bi bi-download"></i> CSV</a>') ?>
                <?= render_tooltip('Cetak Dokumen Laporan', '<button type="button" class="py-2 px-3 text-xs font-medium rounded-lg border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 shadow-xs inline-flex items-center gap-1.5 transition-colors" onclick="window.print()"><i class="bi bi-printer"></i> Cetak</button>') ?>
            </div>
        </form>
    </div>

    <?php if ($rows === []): ?>
        <!-- ALERT STATUS: KOSONG -->
        <div
            class="alert alert-info"
            role="alert"
            x-data="{ show: true }"
            x-show="show"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
        >
            <div class="alert-content">
                <span class="alert-icon">
                    <i class="bi bi-info-circle-fill"></i>
                </span>
                <div class="alert-body">
                    <strong class="alert-title">Tidak Ada Transaksi</strong>
                    <div class="alert-message">Belum ada catatan transaksi pada tanggal <?= e(format_date($date)) ?> untuk kriteria perusahaan yang dipilih.</div>
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
         WIDE DAILY TABLE (PRELINE UI + THE NAKED ZEBRA)
         ========================================================= -->
    <div class="bg-white border border-gray-200 rounded-2xl shadow-xs overflow-hidden print-report">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-gray-900">Report <?= e(format_date($date)) ?></h3>
                <span class="text-xs text-gray-500 font-mono"><?= number_format(count($rows), 0, ',', '.') ?> transaksi ditemukan pada filter aktif.</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-xs">
                <thead class="bg-gray-50/75">
                    <tr class="text-gray-600 font-semibold font-heading text-[11px] uppercase tracking-wider">
                        <th class="py-3.5 px-3">TEAM</th>
                        <th class="py-3.5 px-3">No. Invoice</th>
                        <th class="py-3.5 px-3">Date</th>
                        <th class="py-3.5 px-3">Bill To/Payer</th>
                        <th class="py-3.5 px-3">Beneficiary Name</th>
                        <th class="py-3.5 px-3">Account Number</th>
                        <th class="py-3.5 px-3">Beneficiary Bank</th>
                        <th class="py-3.5 px-3">Bank Location</th>
                        <th class="py-3.5 px-3 text-right">Amounts</th>
                        <th class="py-3.5 px-3">Country</th>
                        <th class="py-3.5 px-3">City</th>
                        <th class="py-3.5 px-3">Beneficiary Address</th>
                        <th class="py-3.5 px-3">SWIFT</th>
                        <th class="py-3.5 px-3">Payment Details</th>
                        <th class="py-3.5 px-3">Currency</th>
                        <th class="py-3.5 px-3">Correspondent Bank</th>
                        <th class="py-3.5 px-3">SWIFT Corr</th>
                        <th class="py-3.5 px-3">Corr Location</th>
                        <th class="py-3.5 px-3">Bank of Payer</th>
                        <th class="py-3.5 px-3">Reference No</th>
                        <th class="py-3.5 px-3">Invoice Link</th>
                        <th class="py-3.5 px-3">Status</th>
                        <th class="py-3.5 px-3">Refund Status</th>
                        <th class="py-3.5 px-3">Notes</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                <?php foreach ($rows as $row): ?>
                    <tr class="even:bg-gray-50/80 [data-no-hover]">
                        <td class="py-3 px-3"><strong><?= e($row['team_no']) ?></strong></td>
                        <td class="py-3 px-3 tabular-nums"><?= e($row['invoice_no'] ?? '') ?></td>
                        <td class="py-3 px-3 whitespace-nowrap"><?= e(format_date($row['transaction_date'])) ?></td>
                        <td class="py-3 px-3 font-medium text-gray-900 whitespace-nowrap"><?= e($row['company_name']) ?></td>
                        <td class="py-3 px-3 font-medium text-gray-900 whitespace-nowrap"><?= e($row['beneficiary_name'] ?? '') ?></td>
                        <td class="py-3 px-3 tabular-nums text-gray-600 whitespace-nowrap"><?= e($row['beneficiary_account'] ?? '') ?></td>
                        <td class="py-3 px-3 whitespace-nowrap"><?= e($row['beneficiary_bank'] ?? '') ?></td>
                        <td class="py-3 px-3 text-gray-500 max-w-[180px] truncate"><?= e($row['beneficiary_bank_location'] ?? '') ?></td>
                        <td class="py-3 px-3 text-right whitespace-nowrap font-semibold text-gray-900 tabular-nums"><?= e(number_format((float) $row['amount'], 2, ',', '.')) ?></td>
                        <td class="py-3 px-3 whitespace-nowrap"><?= e($row['beneficiary_country'] ?? '') ?></td>
                        <td class="py-3 px-3 whitespace-nowrap"><?= e($row['beneficiary_city'] ?? '') ?></td>
                        <td class="py-3 px-3 text-gray-500 max-w-[200px] truncate"><?= e($row['beneficiary_address'] ?? '') ?></td>
                        <td class="py-3 px-3"><span class="inline-flex items-center py-0.5 px-2 rounded-full bg-gray-100 text-gray-700 tabular-nums text-[11px]"><?= e($row['swift'] ?? '') ?></span></td>
                        <td class="py-3 px-3 text-gray-500 max-w-[180px] truncate"><?= e($row['payment_details'] ?? '') ?></td>
                        <td class="py-3 px-3 tabular-nums font-semibold text-gray-800"><?= e($row['currency'] ?? '') ?></td>
                        <td class="py-3 px-3 whitespace-nowrap"><?= e($row['correspondent_bank'] ?? '') ?></td>
                        <td class="py-3 px-3 tabular-nums text-[11px]"><?= e($row['correspondent_swift'] ?? '') ?></td>
                        <td class="py-3 px-3 whitespace-nowrap"><?= e($row['correspondent_location'] ?? '') ?></td>
                        <td class="py-3 px-3 whitespace-nowrap"><?= e($row['payer_bank'] ?? '') ?></td>
                        <td class="py-3 px-3 tabular-nums text-[11px]"><?= e($row['reference_no'] ?? '') ?></td>
                        <td class="py-3 px-3 whitespace-nowrap">
                            <?php if (!empty($row['invoice_link'])): ?>
                                <?= render_tooltip('Buka Invoice di Tab Baru', '<a class="text-blue-600 hover:underline inline-flex items-center gap-1" href="' . e($row['invoice_link']) . '" target="_blank" rel="noopener"><i class="bi bi-box-arrow-up-right"></i> Buka</a>') ?>
                            <?php else: ?>
                                <span class="text-gray-300">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3 px-3 whitespace-nowrap"><?= status_badge((string) $row['status']) ?></td>
                        <td class="py-3 px-3 whitespace-nowrap"><?= status_badge((string) $row['refund_status']) ?></td>
                        <td class="py-3 px-3 text-gray-500 max-w-[150px] truncate"><?= e($row['notes'] ?? '') ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($rows === []): ?>
                    <tr>
                        <td colspan="24" class="py-12 text-center text-gray-400">
                            <i class="bi bi-calendar-x text-2xl block mb-2"></i>
                            Belum ada transaksi pada filter yang dipilih.
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
