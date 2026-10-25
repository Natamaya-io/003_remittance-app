<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| HASIL PENCARIAN REPORT (PRELINE UI & TAILWIND CSS NATIVE)
|--------------------------------------------------------------------------
*/

$filters = is_array($filters ?? null) ? $filters : [];
$teams = is_array($teams ?? null) ? $teams : [];
$batches = is_array($batches ?? null) ? $batches : [];
$rows = is_array($rows ?? null) ? $rows : [];
$queryText = (string) ($filters['q'] ?? '');
?>

<div class="max-w-7xl mx-auto space-y-6">

    <!-- =========================================================
         PAGE ACTIONS
         ========================================================= -->
    <div class="flex items-center justify-end gap-2">
        <a class="py-2 px-3.5 inline-flex items-center gap-x-2 text-xs font-medium rounded-lg border border-gray-200 bg-white text-gray-700 shadow-xs hover:bg-gray-50 focus:outline-hidden focus:bg-gray-50 transition-colors" href="<?= e(url('/reports')) ?>">
            <i class="bi bi-arrow-left"></i>
            Kembali ke Report
        </a>
    </div>

    <!-- =========================================================
         FILTER PARAMETER GRID
         ========================================================= -->
    <section class="bg-white border border-gray-200 rounded-2xl p-4 sm:p-5 shadow-xs">
        <form class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4" method="get" action="<?= e(url('/reports/search')) ?>">
            <div class="sm:col-span-2 lg:col-span-4">
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Kata kunci pencarian *</label>
                <div class="relative">
                    <input
                        class="py-2.5 px-4 ps-10 block w-full border border-gray-300 rounded-xl text-xs sm:text-sm focus:border-blue-500 focus:ring-blue-500 shadow-xs"
                        type="search"
                        name="q"
                        required
                        value="<?= e($queryText) ?>"
                        placeholder="Cari tim, nama berkas, payer, beneficiary, bank, invoice, SWIFT..."
                    >
                    <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none ps-3.5 text-gray-400">
                        <i class="bi bi-search text-xs sm:text-sm"></i>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Bulan</label>
                <input class="py-2 px-3 block w-full border border-gray-300 rounded-xl text-xs focus:border-blue-500 focus:ring-blue-500 shadow-xs" type="month" name="month" value="<?= e($filters['month'] ?? '') ?>">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Tanggal mulai</label>
                <input class="py-2 px-3 block w-full border border-gray-300 rounded-xl text-xs focus:border-blue-500 focus:ring-blue-500 shadow-xs" type="date" name="date_from" value="<?= e($filters['date_from'] ?? '') ?>">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Tanggal akhir</label>
                <input class="py-2 px-3 block w-full border border-gray-300 rounded-xl text-xs focus:border-blue-500 focus:ring-blue-500 shadow-xs" type="date" name="date_to" value="<?= e($filters['date_to'] ?? '') ?>">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Tim</label>
                <select class="py-2 px-3 block w-full border border-gray-300 rounded-xl text-xs focus:border-blue-500 focus:ring-blue-500 shadow-xs bg-white" name="team">
                    <option value="">Semua tim</option>
                    <?php foreach ($teams as $team): ?>
                        <option value="<?= e($team) ?>" <?= selected($filters['team'] ?? '', $team) ?>><?= e($team) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Status kirim</label>
                <select class="py-2 px-3 block w-full border border-gray-300 rounded-xl text-xs focus:border-blue-500 focus:ring-blue-500 shadow-xs bg-white" name="delivery_status">
                    <option value="">Semua status</option>
                    <option value="PENDING" <?= selected($filters['delivery_status'] ?? '', 'PENDING') ?>>Pending</option>
                    <option value="SENT" <?= selected($filters['delivery_status'] ?? '', 'SENT') ?>>Terkirim</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Hasil transaksi</label>
                <select class="py-2 px-3 block w-full border border-gray-300 rounded-xl text-xs focus:border-blue-500 focus:ring-blue-500 shadow-xs bg-white" name="result_status">
                    <option value="">Semua hasil</option>
                    <option value="PENDING" <?= selected($filters['result_status'] ?? '', 'PENDING') ?>>Pending</option>
                    <option value="SUCCESS" <?= selected($filters['result_status'] ?? '', 'SUCCESS') ?>>Success</option>
                    <option value="FAILED" <?= selected($filters['result_status'] ?? '', 'FAILED') ?>>Failed</option>
                </select>
            </div>

            <div class="sm:col-span-2 flex items-end gap-2 pt-1">
                <button class="btn-primary py-2 px-5 inline-flex items-center gap-x-1.5 text-xs font-semibold rounded-lg shadow-xs focus:outline-hidden" type="submit">
                    <i class="bi bi-search"></i>
                    Cari Data
                </button>
                <a class="py-2 px-3.5 inline-flex items-center text-xs font-medium rounded-lg border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 shadow-xs transition-colors focus:outline-hidden focus:bg-gray-50" href="<?= e(url('/reports/search')) ?>">
                    Reset
                </a>
            </div>
        </form>
    </section>

    <!-- =========================================================
         SUMMARY METRIC CARDS
         ========================================================= -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-blue-50/60 border border-blue-200 rounded-2xl p-4 shadow-xs">
            <span class="text-xs font-semibold text-blue-700 block mb-1">Kata Kunci</span>
            <strong class="text-base sm:text-lg font-bold text-blue-900 block truncate"><?= e($queryText !== '' ? $queryText : '(Semua)') ?></strong>
        </div>
        <div class="bg-white border border-gray-200 rounded-2xl p-4 shadow-xs">
            <span class="text-xs font-semibold text-gray-500 block mb-1">Report Ditemukan</span>
            <strong class="text-2xl font-bold text-gray-900 block font-mono"><?= number_format(count($batches), 0, ',', '.') ?></strong>
        </div>
        <div class="bg-emerald-50/60 border border-emerald-200 rounded-2xl p-4 shadow-xs">
            <span class="text-xs font-semibold text-emerald-700 block mb-1">Baris Transaksi Ditemukan</span>
            <strong class="text-2xl font-bold text-emerald-800 block font-mono"><?= number_format(count($rows), 0, ',', '.') ?></strong>
        </div>
    </div>

    <!-- =========================================================
         REPORT BATCH CARDS GRID
         ========================================================= -->
    <section class="bg-white border border-gray-200 rounded-2xl p-4 sm:p-5 shadow-xs">
        <div class="mb-4 pb-3 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900">Berkas Report yang Cocok</h3>
            <span class="text-xs font-mono text-gray-500"><?= number_format(count($batches), 0, ',', '.') ?> berkas</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php foreach ($batches as $batch): ?>
                <article class="bg-gray-50/60 border border-gray-200 rounded-xl p-4 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between gap-2 mb-2">
                            <span class="inline-flex items-center py-0.5 px-2 rounded-full bg-blue-50 text-blue-700 border border-blue-200 text-[11px] font-bold uppercase tracking-wider">
                                <?= e(strtoupper((string) ($batch['effective_team'] ?? 'Tanpa Tim'))) ?>
                            </span>
                            <span class="inline-flex items-center py-0.5 px-2 rounded-full bg-gray-100 text-gray-700 text-[11px] font-medium font-mono">
                                <?= e((string) ($batch['effective_date'] ?? '')) ?>
                            </span>
                        </div>
                        <h4 class="text-sm font-bold text-gray-900 truncate mb-1" title="<?= e((string) ($batch['source_filename'] ?? '')) ?>">
                            <?= e((string) ($batch['source_filename'] ?? '')) ?>
                        </h4>
                        <p class="text-xs text-gray-500 mb-3 truncate"><?= e((string) ($batch['name'] ?? '')) ?></p>

                        <div class="flex items-center gap-3 py-2 px-3 bg-white rounded-lg border border-gray-200 text-xs mb-3">
                            <span><b class="text-gray-800"><?= (int) ($batch['total_rows_live'] ?? 0) ?></b> total</span>
                            <span><b class="text-emerald-600"><?= (int) ($batch['sent_rows_live'] ?? 0) ?></b> terkirim</span>
                            <span><b class="text-amber-600"><?= (int) ($batch['pending_rows_live'] ?? 0) ?></b> pending</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 pt-2 border-t border-gray-200">
                        <a class="btn-primary py-1.5 px-3 flex-1 justify-center inline-flex items-center gap-x-1.5 text-xs font-semibold rounded-lg shadow-xs focus:outline-hidden" href="<?= e(url('/reports/' . $batch['id'] . '?q=' . urlencode($queryText))) ?>">
                            Buka Report
                        </a>
                        <?php $batchDateLink = !empty($batch['effective_date']) ? url('/reports/date/' . $batch['effective_date']) : url('/reports'); ?>
                        <a class="py-1.5 px-3 inline-flex items-center justify-center text-xs font-medium rounded-lg border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 shadow-xs transition-colors focus:outline-hidden focus:bg-gray-50" href="<?= e($batchDateLink) ?>">
                            Lihat Tanggal
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>

            <?php if ($batches === []): ?>
                <div class="col-span-full py-10 text-center text-gray-400">
                    <i class="bi bi-file-earmark-x text-3xl block mb-2 text-gray-300"></i>
                    <h3 class="text-sm font-semibold text-gray-700">Tidak ada report yang cocok</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Coba gunakan kata kunci atau rentang tanggal lain.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- =========================================================
         TRANSACTIONS RESULT TABLE
         ========================================================= -->
    <section class="bg-white border border-gray-200 rounded-2xl shadow-xs overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900">Baris Transaksi yang Cocok</h3>
            <span class="text-xs font-mono text-gray-500"><?= number_format(count($rows), 0, ',', '.') ?> baris</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
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
                    <tr class="even:bg-gray-50/50">
                        <td class="py-2.5 px-3 text-nowrap"><?= e(format_date((string) ($row['report_date'] ?: $row['transaction_date']))) ?></td>
                        <td class="py-2.5 px-3"><strong><?= e((string) ($row['effective_team'] ?? '')) ?></strong></td>
                        <td class="py-2.5 px-3 font-medium text-gray-800 max-w-[160px] truncate"><?= e((string) ($row['batch_name'] ?? '')) ?></td>
                        <td class="py-2.5 px-3"><?= e((string) ($row['bill_to'] ?? '')) ?></td>
                        <td class="py-2.5 px-3 font-semibold text-gray-900"><?= e((string) ($row['beneficiary_name'] ?? '')) ?></td>
                        <td class="py-2.5 px-3"><?= e((string) ($row['beneficiary_bank'] ?? '')) ?></td>
                        <td class="py-2.5 px-3"><span class="inline-flex items-center py-0.5 px-1.5 rounded bg-gray-100 text-gray-700 font-mono text-[11px]"><?= e((string) ($row['swift'] ?? '')) ?></span></td>
                        <td class="py-2.5 px-3"><?= e((string) ($row['invoice_no'] ?? '')) ?></td>
                        <td class="py-2.5 px-3 font-bold text-gray-800"><?= e((string) ($row['currency'] ?? '')) ?></td>
                        <td class="py-2.5 px-3 text-right text-nowrap font-bold text-gray-900 font-mono"><?= e(number_format((float) ($row['amount'] ?? 0), 2, ',', '.')) ?></td>
                        <td class="py-2.5 px-3"><?= status_badge((string) ($row['delivery_status'] ?? '')) ?></td>
                        <td class="py-2.5 px-3 text-right">
                            <a class="py-1 px-2.5 inline-flex items-center gap-x-1 text-xs font-semibold rounded-lg border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 shadow-xs transition-colors focus:outline-hidden focus:bg-gray-50" href="<?= e(url('/reports/' . $row['batch_id'] . '?q=' . urlencode($queryText))) ?>">
                                Buka
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($rows === []): ?>
                    <tr>
                        <td colspan="12" class="py-10 text-center text-gray-400">
                            <i class="bi bi-file-earmark-x text-2xl block mb-2 text-gray-300"></i>
                            Tidak ada baris transaksi yang cocok.
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

</div>
