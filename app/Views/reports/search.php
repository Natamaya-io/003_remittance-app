<?php

declare(strict_types=1);

$filters = is_array($filters ?? null) ? $filters : [];
$teams = is_array($teams ?? null) ? $teams : [];
$batches = is_array($batches ?? null) ? $batches : [];
$rows = is_array($rows ?? null) ? $rows : [];
$queryText = (string) ($filters['q'] ?? '');
?>

<!-- =========================================================
     PAGE HEADING
     ========================================================= -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Hasil Pencarian Report</h2>
    </div>
    <a class="py-2 px-3.5 text-xs font-medium rounded-lg border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 shadow-sm inline-flex items-center gap-2" href="<?= url('/reports') ?>">
        <i class="bi bi-arrow-left"></i>
        Kembali
    </a>
</div>

<!-- =========================================================
     FILTER PARAMETER GRID
     ========================================================= -->
<section class="bg-white border border-gray-200 rounded-2xl p-5 mb-6 shadow-sm">
    <form class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4" method="get" action="<?= url('/reports/search') ?>">
        <div class="sm:col-span-2 lg:col-span-4">
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Kata kunci pencarian *</label>
            <div class="relative">
                <input class="py-2.5 px-4 ps-10 block w-full border border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm" type="search" name="q" required value="<?= e($queryText) ?>" placeholder="Cari tim, nama file, payer, beneficiary, bank, invoice, SWIFT...">
                <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none ps-3.5 text-gray-400">
                    <i class="bi bi-search"></i>
                </div>
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">Bulan</label>
            <input class="py-1.5 px-3 block w-full border border-gray-300 rounded-lg text-xs focus:border-blue-500 focus:ring-blue-500" type="month" name="month" value="<?= e($filters['month'] ?? '') ?>">
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">Tanggal mulai</label>
            <input class="py-1.5 px-3 block w-full border border-gray-300 rounded-lg text-xs focus:border-blue-500 focus:ring-blue-500" type="date" name="date_from" value="<?= e($filters['date_from'] ?? '') ?>">
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">Tanggal akhir</label>
            <input class="py-1.5 px-3 block w-full border border-gray-300 rounded-lg text-xs focus:border-blue-500 focus:ring-blue-500" type="date" name="date_to" value="<?= e($filters['date_to'] ?? '') ?>">
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">Tim</label>
            <select class="py-1.5 px-3 block w-full border border-gray-300 rounded-lg text-xs focus:border-blue-500 focus:ring-blue-500" name="team">
                <option value="">Semua tim</option>
                <?php foreach ($teams as $team): ?>
                    <option value="<?= e($team) ?>" <?= selected($filters['team'] ?? '', $team) ?>><?= e($team) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">Status kirim</label>
            <select class="py-1.5 px-3 block w-full border border-gray-300 rounded-lg text-xs focus:border-blue-500 focus:ring-blue-500" name="delivery_status">
                <option value="">Semua status</option>
                <option value="PENDING" <?= selected($filters['delivery_status'] ?? '', 'PENDING') ?>>Pending</option>
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

        <div class="sm:col-span-2 flex items-end gap-2">
            <button class="btn btn-primary py-2 px-5 text-xs font-medium rounded-lg inline-flex items-center gap-1.5" type="submit">
                <i class="bi bi-search"></i>
                Cari Data
            </button>
            <a class="py-2 px-3.5 text-xs font-medium rounded-lg border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 shadow-sm" href="<?= url('/reports') ?>">
                Reset
            </a>
        </div>
    </form>
</section>

<!-- =========================================================
     SUMMARY METRIC CARDS
     ========================================================= -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="bg-blue-50/50 border border-blue-200 rounded-xl p-4 shadow-sm">
        <span class="text-xs font-semibold text-blue-700 block mb-1">Kata Kunci</span>
        <strong class="text-lg font-bold text-blue-900 block truncate"><?= e($queryText) ?></strong>
    </div>
    <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
        <span class="text-xs font-semibold text-gray-500 block mb-1">Report Ditemukan</span>
        <strong class="text-2xl font-bold text-gray-900 block font-mono"><?= number_format(count($batches), 0, ',', '.') ?></strong>
    </div>
    <div class="bg-emerald-50/50 border border-emerald-200 rounded-xl p-4 shadow-sm">
        <span class="text-xs font-semibold text-emerald-700 block mb-1">Baris Transaksi Ditemukan</span>
        <strong class="text-2xl font-bold text-emerald-800 block font-mono"><?= number_format(count($rows), 0, ',', '.') ?></strong>
    </div>
</div>

<!-- =========================================================
     REPORT BATCH CARDS GRID
     ========================================================= -->
<section class="bg-white border border-gray-200 rounded-2xl p-5 mb-6 shadow-sm">
    <div class="mb-4 pb-3 border-b border-gray-100 flex items-center justify-between">
        <div>
            <h3 class="text-sm font-bold text-gray-900">Berkas Report yang Cocok</h3>
            <p class="text-xs text-gray-500">Maksimal 100 batch report ditampilkan.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php foreach ($batches as $batch): ?>
            <article class="bg-gray-50/60 border border-gray-200 rounded-xl p-4 hover:bg-white hover:shadow-md transition-all flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between gap-2 mb-2">
                        <span class="inline-flex items-center py-0.5 px-2 rounded-md bg-blue-100 text-blue-800 text-[11px] font-bold uppercase tracking-wider">
                            <?= e(strtoupper((string) ($batch['effective_team'] ?? 'Tanpa Tim'))) ?>
                        </span>
                        <span class="inline-flex items-center py-0.5 px-2 rounded-md bg-gray-200 text-gray-700 text-[11px] font-medium font-mono">
                            <?= e($batch['effective_date'] ?? '') ?>
                        </span>
                    </div>
                    <h4 class="text-sm font-bold text-gray-900 truncate mb-1" title="<?= e($batch['source_filename']) ?>">
                        <?= e($batch['source_filename']) ?>
                    </h4>
                    <p class="text-xs text-gray-500 mb-3 truncate"><?= e($batch['name']) ?></p>

                    <div class="flex items-center gap-3 py-2 px-3 bg-white rounded-lg border border-gray-100 text-xs mb-3">
                        <span><b class="text-gray-800"><?= (int) ($batch['total_rows_live'] ?? 0) ?></b> total</span>
                        <span><b class="text-emerald-600"><?= (int) ($batch['sent_rows_live'] ?? 0) ?></b> terkirim</span>
                        <span><b class="text-amber-600"><?= (int) ($batch['pending_rows_live'] ?? 0) ?></b> pending</span>
                    </div>
                </div>

                <div class="flex items-center gap-2 pt-2 border-t border-gray-100">
                    <a class="btn btn-primary py-1 px-3 text-xs font-medium rounded-lg flex-1 justify-center" href="<?= url('/reports/' . $batch['id'] . '?q=' . urlencode($queryText)) ?>">
                        Buka Report
                    </a>
                    <a class="py-1 px-3 text-xs font-medium rounded-lg border border-gray-200 bg-white text-gray-700 hover:bg-gray-50" href="<?= url('/reports/date/' . $batch['effective_date']) ?>">
                        Lihat Tanggal
                    </a>
                </div>
            </article>
        <?php endforeach; ?>

        <?php if ($batches === []): ?>
            <div class="col-span-full py-8 text-center text-gray-400">
                <i class="bi bi-file-earmark-x text-3xl block mb-2"></i>
                <h3 class="text-sm font-semibold text-gray-700">Tidak ada report yang cocok</h3>
                <p class="text-xs text-gray-500">Coba gunakan kata kunci atau rentang tanggal lain.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- =========================================================
     TRANSACTIONS RESULT TABLE
     ========================================================= -->
<section class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden mb-6">
    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
        <div>
            <h3 class="text-sm font-bold text-gray-900">Baris Transaksi yang Cocok</h3>
            <p class="text-xs text-gray-500">Maksimal 200 baris ditampilkan. Klik aksi untuk membuka transaksi lengkap.</p>
        </div>
    </div>

    <div class="table-responsive report-search-table overflow-x-auto">
        <table class="table modern-table w-full text-left text-xs border-collapse" data-smart-table>
            <thead>
                <tr class="bg-gray-50/80 border-b border-gray-200 text-gray-600 font-semibold text-[11px] uppercase tracking-wider">
                    <th class="py-3 px-3">Tanggal</th>
                    <th class="py-3 px-3">Tim</th>
                    <th class="py-3 px-3">Report</th>
                    <th class="py-3 px-3">Payer</th>
                    <th class="py-3 px-3">Beneficiary</th>
                    <th class="py-3 px-3">Bank</th>
                    <th class="py-3 px-3">SWIFT</th>
                    <th class="py-3 px-3">Invoice</th>
                    <th class="py-3 px-3">Currency</th>
                    <th class="py-3 px-3 text-right">Amount</th>
                    <th class="py-3 px-3">Status</th>
                    <th class="py-3 px-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-gray-700">
            <?php foreach ($rows as $row): ?>
                <tr class="hover:bg-blue-50/30 transition-colors">
                    <td class="py-2.5 px-3 text-nowrap"><?= e(format_date($row['report_date'] ?: $row['transaction_date'])) ?></td>
                    <td class="py-2.5 px-3"><strong><?= e($row['effective_team']) ?></strong></td>
                    <td class="py-2.5 px-3 font-medium text-gray-800 max-w-[160px] truncate"><?= e($row['batch_name']) ?></td>
                    <td class="py-2.5 px-3"><?= e($row['bill_to']) ?></td>
                    <td class="py-2.5 px-3 font-semibold text-gray-900"><?= e($row['beneficiary_name']) ?></td>
                    <td class="py-2.5 px-3"><?= e($row['beneficiary_bank']) ?></td>
                    <td class="py-2.5 px-3"><span class="inline-flex items-center py-0.5 px-1.5 rounded bg-gray-100 text-gray-700 font-mono text-[11px]"><?= e($row['swift']) ?></span></td>
                    <td class="py-2.5 px-3"><?= e($row['invoice_no']) ?></td>
                    <td class="py-2.5 px-3 font-bold text-gray-800"><?= e($row['currency']) ?></td>
                    <td class="py-2.5 px-3 text-right text-nowrap font-bold text-gray-900 font-mono"><?= e(number_format((float) $row['amount'], 2, ',', '.')) ?></td>
                    <td class="py-2.5 px-3"><?= status_badge((string) $row['delivery_status']) ?></td>
                    <td class="py-2.5 px-3 text-right">
                        <a class="py-1 px-2.5 text-xs font-medium rounded-lg border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 shadow-sm" href="<?= url('/reports/' . $row['batch_id'] . '?q=' . urlencode($queryText)) ?>">
                            Buka
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if ($rows === []): ?>
                <tr>
                    <td colspan="12" class="py-10 text-center text-gray-400">
                        <i class="bi bi-file-earmark-x text-2xl block mb-2"></i>
                        Tidak ada baris transaksi yang cocok.
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
