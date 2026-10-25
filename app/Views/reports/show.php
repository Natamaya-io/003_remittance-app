<?php

declare(strict_types=1);

$rows = is_array($rows ?? null) ? $rows : [];
$filters = is_array($filters ?? null) ? $filters : [];
$summary = is_array($summary ?? null) ? $summary : [];
$returnQuery = http_build_query(array_filter($filters, static fn ($value): bool => $value !== ''));
$importSummary = json_decode((string) ($batch['import_summary_json'] ?? ''), true) ?: [];
$importIssues = is_array($importSummary['issues'] ?? null) ? $importSummary['issues'] : [];
$effectiveDate = (string) ($effectiveDate ?? ($batch['report_date'] ?? ''));
?>

<!-- =========================================================
     PAGE HEADING
     ========================================================= -->
<div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6">
    <div>
        <div class="flex items-center gap-2 mb-1">
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-bold bg-blue-50 text-blue-700 tracking-wider uppercase">
                <?= e($batch['month_key']) ?>
            </span>
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-700">
                <?= e(format_date($effectiveDate)) ?>
            </span>
        </div>
        <h2 class="text-2xl font-bold text-gray-900"><?= e($batch['name']) ?></h2>
        <p class="text-sm text-gray-500 mt-0.5">
            <span class="font-medium text-gray-700"><?= e($batch['client_name'] ?: 'Tanpa nama klien') ?></span>
            · Sumber berkas: <span class="font-mono text-xs"><?= e($batch['source_filename']) ?></span>
        </p>
    </div>
    <div class="flex flex-wrap items-center gap-2">
        <a class="py-2 px-3 text-xs font-medium rounded-lg border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 shadow-sm inline-flex items-center gap-1.5" href="<?= url('/reports/date/' . $effectiveDate) ?>">
            <i class="bi bi-arrow-left"></i>
            Report per Tim
        </a>
        <?php if (can('report.manage')): ?>
            <a class="py-2 px-3 text-xs font-medium rounded-lg border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 shadow-sm inline-flex items-center gap-1.5" href="<?= url('/reports/' . $batch['id'] . '/edit') ?>">
                <i class="bi bi-pencil"></i>
                Edit
            </a>
        <?php endif; ?>
        <a class="btn btn-primary py-2 px-3.5 text-xs font-medium rounded-lg inline-flex items-center gap-1.5" href="<?= url('/reports/' . $batch['id'] . '/export') ?>">
            <i class="bi bi-download"></i>
            Download Excel
        </a>
    </div>
</div>

<!-- =========================================================
     SUMMARY KPI GRID (6 METRIK OPERASIONAL)
     ========================================================= -->
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mb-6">
    <div class="bg-white border border-gray-200 rounded-xl p-3.5 shadow-sm">
        <span class="text-xs font-semibold text-gray-500 block mb-1">Total Baris</span>
        <strong class="text-xl font-bold text-gray-900 block font-mono"><?= number_format((int) ($summary['total'] ?? 0), 0, ',', '.') ?></strong>
    </div>
    <div class="bg-white border border-blue-200 bg-blue-50/30 rounded-xl p-3.5 shadow-sm">
        <span class="text-xs font-semibold text-blue-700 block mb-1">Terkirim</span>
        <strong class="text-xl font-bold text-blue-700 block font-mono"><?= number_format((int) ($summary['sent'] ?? 0), 0, ',', '.') ?></strong>
    </div>
    <div class="bg-white border border-amber-200 bg-amber-50/30 rounded-xl p-3.5 shadow-sm">
        <span class="text-xs font-semibold text-amber-700 block mb-1">Belum Terkirim</span>
        <strong class="text-xl font-bold text-amber-700 block font-mono"><?= number_format((int) ($summary['pending'] ?? 0), 0, ',', '.') ?></strong>
    </div>
    <div class="bg-white border border-emerald-200 bg-emerald-50/30 rounded-xl p-3.5 shadow-sm">
        <span class="text-xs font-semibold text-emerald-700 block mb-1">Success</span>
        <strong class="text-xl font-bold text-emerald-700 block font-mono"><?= number_format((int) ($summary['success'] ?? 0), 0, ',', '.') ?></strong>
    </div>
    <div class="bg-white border border-rose-200 bg-rose-50/30 rounded-xl p-3.5 shadow-sm">
        <span class="text-xs font-semibold text-rose-700 block mb-1">Failed</span>
        <strong class="text-xl font-bold text-rose-700 block font-mono"><?= number_format((int) ($summary['failed'] ?? 0), 0, ',', '.') ?></strong>
    </div>
    <div class="bg-white border border-gray-200 rounded-xl p-3.5 shadow-sm">
        <span class="text-xs font-semibold text-gray-500 block mb-1">Dilewati Impor</span>
        <strong class="text-xl font-bold text-gray-700 block font-mono"><?= number_format((int) ($batch['skipped_rows'] ?? 0), 0, ',', '.') ?></strong>
    </div>
</div>

<?php if ($importIssues !== []): ?>
<!-- VALIDASI IMPOR QUALITY ALERT -->
<details class="bg-amber-50/60 border border-amber-200 rounded-xl p-4 mb-6 shadow-sm">
    <summary class="font-bold text-xs text-amber-900 cursor-pointer flex items-center justify-between">
        <span class="inline-flex items-center gap-2">
            <i class="bi bi-shield-check text-amber-600 text-base"></i>
            Ringkasan validasi impor berkas
        </span>
        <span class="py-0.5 px-2 rounded-full bg-amber-200 text-amber-800 text-[11px] font-bold"><?= count($importIssues) ?> isu ditemukan</span>
    </summary>
    <ul class="mt-3 space-y-1.5 text-xs text-amber-800 list-disc list-inside">
        <?php foreach (array_slice($importIssues, 0, 15) as $issue): ?>
            <li><?= e($issue) ?></li>
        <?php endforeach; ?>
    </ul>
</details>
<?php endif; ?>

<!-- =========================================================
     FILTER TRANSAKSI TOOLBAR
     ========================================================= -->
<div class="bg-white border border-gray-200 rounded-2xl p-4 mb-6 shadow-sm">
    <form class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end" method="get">
        <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">Tanggal transaksi</label>
            <select class="py-1.5 px-3 block w-full border border-gray-300 rounded-lg text-xs focus:border-blue-500 focus:ring-blue-500" name="date">
                <option value="">Semua tanggal</option>
                <?php foreach ($dates as $dateOption): ?>
                    <option value="<?= e($dateOption) ?>" <?= selected($filters['date'] ?? '', $dateOption) ?>><?= e(format_date($dateOption)) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">Status kirim</label>
            <select class="py-1.5 px-3 block w-full border border-gray-300 rounded-lg text-xs focus:border-blue-500 focus:ring-blue-500" name="delivery_status">
                <option value="">Semua status</option>
                <option value="PENDING" <?= selected($filters['delivery_status'] ?? '', 'PENDING') ?>>Belum Terkirim</option>
                <option value="SENT" <?= selected($filters['delivery_status'] ?? '', 'SENT') ?>>Terkirim</option>
            </select>
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">Hasil transaksi</label>
            <select class="py-1.5 px-3 block w-full border border-gray-300 rounded-lg text-xs focus:border-blue-500 focus:ring-blue-500" name="result_status">
                <option value="">Semua hasil</option>
                <option value="PENDING" <?= selected($filters['result_status'] ?? '', 'PENDING') ?>>Pending</option>
                <option value="SUCCESS" <?= selected($filters['result_status'] ?? '', 'SUCCESS') ?>>Success</option>
                <option value="FAILED" <?= selected($filters['result_status'] ?? '', 'FAILED') ?>>Failed</option>
            </select>
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">Cari data</label>
            <input class="py-1.5 px-3 block w-full border border-gray-300 rounded-lg text-xs focus:border-blue-500 focus:ring-blue-500" type="search" name="q" placeholder="Payer, bank, SWIFT..." value="<?= e($filters['q'] ?? '') ?>">
        </div>

        <div class="flex items-center gap-2">
            <button class="btn btn-primary py-1.5 px-3.5 text-xs font-medium rounded-lg inline-flex items-center gap-1.5 flex-1 justify-center" type="submit">
                <i class="bi bi-funnel"></i>
                Terapkan
            </button>
            <a class="py-1.5 px-3 text-xs font-medium rounded-lg border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 shadow-sm" href="<?= url('/reports/' . $batch['id']) ?>">
                Reset
            </a>
        </div>
    </form>
</div>

<!-- =========================================================
     BULK ACTION TOOLBAR (ALPINE.JS SELECTION COUNTER)
     ========================================================= -->
<div x-data="{
    selectedCount: 0,
    updateCount() {
        this.selectedCount = document.querySelectorAll('input[name=\'row_ids[]\']:checked').length;
    },
    toggleAll(checked) {
        document.querySelectorAll('input[name=\'row_ids[]\']').forEach(el => el.checked = checked);
        this.updateCount();
    }
}" class="space-y-3">

<?php if (can('report.manage')): ?>
<form id="bulkStatusForm" method="post" action="<?= url('/reports/' . $batch['id'] . '/bulk-status') ?>" class="flex flex-wrap items-center gap-2.5 p-3 bg-gray-50 border border-gray-200 rounded-xl text-xs">
    <?= csrf_field() ?>
    <input type="hidden" name="return_query" value="<?= e($returnQuery) ?>">

    <div class="flex items-center gap-2 ps-1">
        <i class="bi bi-check2-square text-blue-600 text-sm"></i>
        <strong class="text-gray-800"><span x-text="selectedCount">0</span> baris dipilih</strong>
    </div>

    <div class="flex items-center gap-2 ms-auto">
        <select class="py-1 px-2.5 border border-gray-300 rounded-lg text-xs focus:border-blue-500" name="delivery_status">
            <option value="">Ubah Status Kirim...</option>
            <option value="PENDING">Belum Terkirim</option>
            <option value="SENT">Terkirim</option>
        </select>

        <select class="py-1 px-2.5 border border-gray-300 rounded-lg text-xs focus:border-blue-500" name="result_status">
            <option value="">Ubah Hasil...</option>
            <option value="PENDING">Pending</option>
            <option value="SUCCESS">Success</option>
            <option value="FAILED">Failed</option>
        </select>

        <button class="btn btn-primary py-1 px-3 text-xs font-medium rounded-lg inline-flex items-center gap-1.5" type="submit" :disabled="selectedCount === 0">
            Terapkan ke Pilihan
        </button>
    </div>
</form>
<?php endif; ?>

<!-- =========================================================
     WIDE TRANSACTION DATA TABLE
     ========================================================= -->
<section class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden mb-6">
    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
        <div>
            <h3 class="text-sm font-bold text-gray-900"><?= number_format(count($rows), 0, ',', '.') ?> Baris Ditampilkan</h3>
            <p class="text-xs text-gray-500">Tabel data transaksi lengkap sesuai berkas Excel sumber.</p>
        </div>
    </div>

    <div class="table-responsive report-wide-table overflow-x-auto">
        <table class="table modern-table w-full text-left text-xs border-collapse" data-smart-table>
            <thead>
                <tr class="bg-gray-50/80 border-b border-gray-200 text-gray-600 font-semibold text-[11px] uppercase tracking-wider">
                    <?php if (can('report.manage')): ?>
                        <th class="py-3 px-3 w-10 text-center">
                            <input type="checkbox" @change="toggleAll($el.checked)" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </th>
                    <?php endif; ?>
                    <th class="py-3 px-3">TEAM</th>
                    <th class="py-3 px-3">No.</th>
                    <th class="py-3 px-3">Tanggal</th>
                    <th class="py-3 px-3">Bill To/Payer</th>
                    <th class="py-3 px-3">Beneficiary</th>
                    <th class="py-3 px-3">Account</th>
                    <th class="py-3 px-3">Beneficiary Bank</th>
                    <th class="py-3 px-3">Bank Address</th>
                    <th class="py-3 px-3 text-right">Amounts</th>
                    <th class="py-3 px-3">Country</th>
                    <th class="py-3 px-3">City</th>
                    <th class="py-3 px-3">Beneficiary Address</th>
                    <th class="py-3 px-3">SWIFT</th>
                    <th class="py-3 px-3">Payment Details</th>
                    <th class="py-3 px-3">Currency</th>
                    <th class="py-3 px-3">Invoice</th>
                    <th class="py-3 px-3">Contract</th>
                    <th class="py-3 px-3">Correspondent</th>
                    <th class="py-3 px-3">Swift Corr</th>
                    <th class="py-3 px-3">Country Corr</th>
                    <th class="py-3 px-3">Bank of Payer</th>
                    <th class="py-3 px-3">Invoice Link</th>
                    <th class="py-3 px-3">Bank Proses</th>
                    <th class="py-3 px-3">Status Kirim</th>
                    <th class="py-3 px-3">Hasil</th>
                    <th class="py-3 px-3">Notes</th>
                    <th class="py-3 px-3">Sheet</th>
                    <th class="py-3 px-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-gray-700">
            <?php foreach ($rows as $row): ?>
                <tr class="hover:bg-blue-50/30 transition-colors">
                    <?php if (can('report.manage')): ?>
                        <td class="py-2.5 px-3 text-center">
                            <input type="checkbox" name="row_ids[]" value="<?= (int) $row['id'] ?>" form="bulkStatusForm" @change="updateCount()" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </td>
                    <?php endif; ?>
                    <td class="py-2.5 px-3"><strong><?= e($row['team_label']) ?></strong></td>
                    <td class="py-2.5 px-3 text-gray-500"><?= e($row['item_no']) ?></td>
                    <td class="py-2.5 px-3 text-nowrap"><?= e(format_date($row['transaction_date'])) ?></td>
                    <td class="py-2.5 px-3"><?= e($row['bill_to']) ?></td>
                    <td class="py-2.5 px-3 font-medium text-gray-900"><?= e($row['beneficiary_name']) ?></td>
                    <td class="py-2.5 px-3 font-mono text-gray-600"><?= e($row['beneficiary_account']) ?></td>
                    <td class="py-2.5 px-3"><?= e($row['beneficiary_bank']) ?></td>
                    <td class="py-2.5 px-3 text-gray-500 max-w-[200px] truncate"><?= e($row['beneficiary_bank_address']) ?></td>
                    <td class="py-2.5 px-3 text-right text-nowrap font-bold text-gray-900 font-mono"><?= e(number_format((float) $row['amount'], 2, ',', '.')) ?></td>
                    <td class="py-2.5 px-3"><?= e($row['beneficiary_country']) ?></td>
                    <td class="py-2.5 px-3"><?= e($row['beneficiary_city']) ?></td>
                    <td class="py-2.5 px-3 text-gray-500 max-w-[200px] truncate"><?= e($row['beneficiary_address']) ?></td>
                    <td class="py-2.5 px-3"><span class="inline-flex items-center py-0.5 px-2 rounded bg-gray-100 text-gray-700 font-mono text-[11px]"><?= e($row['swift']) ?></span></td>
                    <td class="py-2.5 px-3 text-gray-500 max-w-[180px] truncate"><?= e($row['payment_details']) ?></td>
                    <td class="py-2.5 px-3 font-bold text-gray-800"><?= e($row['currency']) ?></td>
                    <td class="py-2.5 px-3"><?= e($row['invoice_no']) ?><?php if ($row['invoice_date']): ?><small class="block text-gray-400"><?= e(format_date($row['invoice_date'])) ?></small><?php endif; ?></td>
                    <td class="py-2.5 px-3"><?= e($row['contract_no']) ?><?php if ($row['contract_date']): ?><small class="block text-gray-400"><?= e(format_date($row['contract_date'])) ?></small><?php endif; ?></td>
                    <td class="py-2.5 px-3"><?= e($row['correspondent_bank']) ?></td>
                    <td class="py-2.5 px-3 font-mono text-[11px]"><?= e($row['correspondent_swift']) ?></td>
                    <td class="py-2.5 px-3"><?= e($row['correspondent_country']) ?></td>
                    <td class="py-2.5 px-3"><?= e($row['bank_of_payer']) ?></td>
                    <td class="py-2.5 px-3"><?php if ($row['invoice_link']): ?><a class="text-blue-600 hover:underline" href="<?= e($row['invoice_link']) ?>" target="_blank" rel="noopener">Buka</a><?php endif; ?></td>
                    <td class="py-2.5 px-3"><?= e($row['processing_bank']) ?></td>
                    <td class="py-2.5 px-3"><?= status_badge($row['delivery_status']) ?></td>
                    <td class="py-2.5 px-3"><?= status_badge($row['result_status']) ?></td>
                    <td class="py-2.5 px-3 text-gray-500 max-w-[150px] truncate"><?= e($row['notes']) ?></td>
                    <td class="py-2.5 px-3 text-gray-400 text-[11px]"><?= e($row['source_sheet']) ?></td>
                    <td class="py-2.5 px-3 text-right text-nowrap sticky-action-cell">
                        <div class="flex items-center justify-end gap-1.5">
                            <?php
                            $complaintEligible = in_array(strtolower((string) ($row['delivery_status'] ?? '')), ['sent','processed','delivered','success','terkirim','sukses'], true)
                                || in_array(strtolower((string) ($row['result_status'] ?? '')), ['success','processed','sukses'], true);
                            ?>
                            <?php if (can('complaint.manage') && $complaintEligible): ?>
                                <a class="p-1.5 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition" href="<?= url('/complaints/create?report_row_id=' . (int) $row['id']) ?>" title="Buat complain dari transaksi report">
                                    <i class="bi bi-headset"></i>
                                </a>
                            <?php endif; ?>

                            <?php if (can('report.manage')): ?>
                            <details class="relative inline-block text-left">
                                <summary class="py-1 px-2.5 text-xs font-medium rounded-lg border border-gray-200 bg-white hover:bg-gray-50 cursor-pointer list-none inline-flex items-center gap-1 shadow-sm">
                                    Ubah
                                </summary>
                                <div class="absolute right-0 mt-2 w-64 bg-white border border-gray-200 rounded-xl shadow-xl p-3 z-30 text-left">
                                    <form method="post" action="<?= url('/reports/' . $batch['id'] . '/rows/' . $row['id'] . '/status') ?>" class="space-y-2.5">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="return_query" value="<?= e($returnQuery) ?>">
                                        <div>
                                            <label class="block text-[11px] font-semibold text-gray-700 mb-1">Status kirim</label>
                                            <select class="py-1 px-2 block w-full border border-gray-300 rounded text-xs" name="delivery_status">
                                                <option value="PENDING" <?= selected($row['delivery_status'], 'PENDING') ?>>Belum Terkirim</option>
                                                <option value="SENT" <?= selected($row['delivery_status'], 'SENT') ?>>Terkirim</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-semibold text-gray-700 mb-1">Hasil</label>
                                            <select class="py-1 px-2 block w-full border border-gray-300 rounded text-xs" name="result_status">
                                                <option value="PENDING" <?= selected($row['result_status'], 'PENDING') ?>>Pending</option>
                                                <option value="SUCCESS" <?= selected($row['result_status'], 'SUCCESS') ?>>Success</option>
                                                <option value="FAILED" <?= selected($row['result_status'], 'FAILED') ?>>Failed</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-semibold text-gray-700 mb-1">Catatan</label>
                                            <textarea class="py-1 px-2 block w-full border border-gray-300 rounded text-xs" name="notes" rows="2"><?= e($row['notes']) ?></textarea>
                                        </div>
                                        <button class="btn btn-primary py-1 px-3 text-xs font-medium rounded w-full justify-center" type="submit">
                                            Simpan
                                        </button>
                                    </form>
                                </div>
                            </details>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if ($rows === []): ?>
                <tr>
                    <td colspan="30" class="py-10 text-center text-gray-400">
                        <i class="bi bi-file-earmark-x text-2xl block mb-2"></i>
                        Tidak ada baris yang cocok dengan filter.
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
</div> <!-- end Alpine selection wrap -->

