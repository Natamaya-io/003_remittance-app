<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| REPORT HARIAN - REKAP TRANSAKSI HARIAN (PRELINE & ALPINE NATIVE)
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
         PAGE HEADING
         ========================================================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Rekap Transaksi Harian</h2>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <?php if (can('transaction.manage')): ?>
                <a href="<?= url('/report_paste_import.php') ?>" class="btn btn-primary py-2 px-3.5 text-xs font-medium rounded-lg inline-flex items-center gap-1.5">
                    <i class="bi bi-clipboard-data"></i>
                    Paste dari Excel
                </a>
                <a href="<?= url('/transactions/import') ?>" class="py-2 px-3 text-xs font-medium rounded-lg border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 shadow-xs inline-flex items-center gap-1.5 transition-colors">
                    <i class="bi bi-file-earmark-excel"></i>
                    Import XLSX/CSV
                </a>
                <a href="<?= url('/transactions/create') ?>" class="py-2 px-3 text-xs font-medium rounded-lg border border-blue-200 bg-blue-50 text-blue-700 hover:bg-blue-100 shadow-xs inline-flex items-center gap-1.5 transition-colors">
                    <i class="bi bi-plus-lg"></i>
                    Input Manual
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- =========================================================
         FILTER PANEL
         ========================================================= -->
    <div class="bg-white border border-gray-200 rounded-2xl p-4 sm:p-5 shadow-xs">
        <form method="get" action="<?= url('/reports/daily') ?>" class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
            <div class="md:col-span-3">
                <label class="block text-xs font-semibold text-gray-700 mb-1">Tanggal report</label>
                <input type="date" class="py-2 px-3 block w-full border border-gray-300 rounded-xl text-xs focus:border-blue-500 focus:ring-blue-500 shadow-xs" name="date" value="<?= e($date) ?>">
            </div>
            <div class="md:col-span-4">
                <label class="block text-xs font-semibold text-gray-700 mb-1">Perusahaan (PT)</label>
                <select class="py-2 px-3 block w-full border border-gray-300 rounded-xl text-xs focus:border-blue-500 focus:ring-blue-500 shadow-xs" name="company_id">
                    <option value="0">Semua perusahaan</option>
                    <?php foreach ($companies as $company): ?>
                        <option value="<?= (int) $company['id'] ?>" <?= selected($companyId, $company['id']) ?>><?= e($company['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="md:col-span-5 flex flex-wrap items-center gap-2">
                <button class="btn btn-primary py-2 px-3.5 text-xs font-medium rounded-lg inline-flex items-center gap-1.5" type="submit">
                    <i class="bi bi-funnel"></i>
                    Terapkan
                </button>
                <a class="py-2 px-3 text-xs font-medium rounded-lg border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 shadow-xs transition-colors" href="<?= url('/reports/daily') ?>">
                    Hari Ini
                </a>
                <a class="py-2 px-3 text-xs font-medium rounded-lg border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 shadow-xs inline-flex items-center gap-1.5 transition-colors" href="<?= url('/reports/daily/export?' . http_build_query(['date' => $date, 'company_id' => $companyId])) ?>">
                    <i class="bi bi-download"></i>
                    CSV
                </a>
                <button type="button" class="py-2 px-3 text-xs font-medium rounded-lg border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 shadow-xs inline-flex items-center gap-1.5 transition-colors" onclick="window.print()">
                    <i class="bi bi-printer"></i>
                    Cetak
                </button>
            </div>
        </form>
    </div>

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
            <table class="min-w-full divide-y divide-gray-200 text-left text-xs">
                <thead class="bg-gray-50/75">
                    <tr class="text-gray-600 font-semibold text-[11px] uppercase tracking-wider">
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
                <tbody class="divide-y divide-gray-100 text-gray-700">
                <?php foreach ($rows as $row): ?>
                    <tr class="even:bg-gray-50/50 [data-no-hover]">
                        <td class="py-3 px-3"><strong><?= e($row['team_no']) ?></strong></td>
                        <td class="py-3 px-3 font-mono"><?= e($row['invoice_no'] ?? '') ?></td>
                        <td class="py-3 px-3 whitespace-nowrap"><?= e(format_date($row['transaction_date'])) ?></td>
                        <td class="py-3 px-3 font-medium text-gray-900 whitespace-nowrap"><?= e($row['company_name']) ?></td>
                        <td class="py-3 px-3 font-medium text-gray-900 whitespace-nowrap"><?= e($row['beneficiary_name'] ?? '') ?></td>
                        <td class="py-3 px-3 font-mono text-gray-600 whitespace-nowrap"><?= e($row['beneficiary_account'] ?? '') ?></td>
                        <td class="py-3 px-3 whitespace-nowrap"><?= e($row['beneficiary_bank'] ?? '') ?></td>
                        <td class="py-3 px-3 text-gray-500 max-w-[180px] truncate"><?= e($row['beneficiary_bank_location'] ?? '') ?></td>
                        <td class="py-3 px-3 text-right whitespace-nowrap font-bold text-gray-900 font-mono"><?= e(number_format((float) $row['amount'], 2, ',', '.')) ?></td>
                        <td class="py-3 px-3 whitespace-nowrap"><?= e($row['beneficiary_country'] ?? '') ?></td>
                        <td class="py-3 px-3 whitespace-nowrap"><?= e($row['beneficiary_city'] ?? '') ?></td>
                        <td class="py-3 px-3 text-gray-500 max-w-[200px] truncate"><?= e($row['beneficiary_address'] ?? '') ?></td>
                        <td class="py-3 px-3"><span class="inline-flex items-center py-0.5 px-2 rounded-full bg-gray-100 text-gray-700 font-mono text-[11px]"><?= e($row['swift'] ?? '') ?></span></td>
                        <td class="py-3 px-3 text-gray-500 max-w-[180px] truncate"><?= e($row['payment_details'] ?? '') ?></td>
                        <td class="py-3 px-3 font-mono font-bold text-gray-800"><?= e($row['currency'] ?? '') ?></td>
                        <td class="py-3 px-3 whitespace-nowrap"><?= e($row['correspondent_bank'] ?? '') ?></td>
                        <td class="py-3 px-3 font-mono text-[11px]"><?= e($row['correspondent_swift'] ?? '') ?></td>
                        <td class="py-3 px-3 whitespace-nowrap"><?= e($row['correspondent_location'] ?? '') ?></td>
                        <td class="py-3 px-3 whitespace-nowrap"><?= e($row['payer_bank'] ?? '') ?></td>
                        <td class="py-3 px-3 font-mono text-[11px]"><?= e($row['reference_no'] ?? '') ?></td>
                        <td class="py-3 px-3 whitespace-nowrap">
                            <?php if (!empty($row['invoice_link'])): ?>
                                <a class="text-blue-600 hover:underline inline-flex items-center gap-1" href="<?= e($row['invoice_link']) ?>" target="_blank" rel="noopener">
                                    <i class="bi bi-box-arrow-up-right"></i> Buka
                                </a>
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
