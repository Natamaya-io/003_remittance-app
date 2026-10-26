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
                <?= render_search_input('q', $queryText, [
                    'placeholder' => 'Cari tim, nama berkas, payer, beneficiary, bank, invoice, SWIFT...',
                    'required' => true,
                ]) ?>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Bulan</label>
                <?= render_datepicker('month', (string) ($filters['month'] ?? ''), ['mode' => 'month', 'placeholder' => 'Semua bulan']) ?>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Tanggal mulai</label>
                <?= render_datepicker('date_from', (string) ($filters['date_from'] ?? ''), ['placeholder' => 'Pilih tanggal mulai']) ?>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Tanggal akhir</label>
                <?= render_datepicker('date_to', (string) ($filters['date_to'] ?? ''), ['placeholder' => 'Pilih tanggal akhir']) ?>
            </div>

            <div x-data="{
                open: false,
                selected: '<?= addslashes(e($filters['team'] ?? '')) ?>',
                label: '<?= ($filters['team'] ?? '') !== '' ? addslashes(e($filters['team'])) : 'Semua tim' ?>'
            }">
                <label class="block text-xs font-semibold text-gray-700 mb-1">Tim</label>
                <div class="custom-select-root relative" @click.outside="open = false">
                    <input type="hidden" name="team" :value="selected">
                    <button
                        type="button"
                        class="dropdown-toggle"
                        @click="open = !open"
                        :aria-expanded="open"
                        aria-label="Filter Tim"
                    >
                        <i class="bi bi-people text-gray-400"></i>
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
                            @click="selected = ''; label = 'Semua tim'; open = false"
                        >
                            <span>Semua tim</span>
                            <i x-show="selected === ''" class="bi bi-check2 text-blue-600 text-sm ml-auto"></i>
                        </button>
                        <?php foreach ($teams as $team): ?>
                            <button
                                type="button"
                                class="dropdown-item"
                                :class="{ 'active': selected === '<?= addslashes(e($team)) ?>' }"
                                @click="selected = '<?= addslashes(e($team)) ?>'; label = '<?= addslashes(e($team)) ?>'; open = false"
                            >
                                <span><?= e($team) ?></span>
                                <i x-show="selected === '<?= addslashes(e($team)) ?>'" class="bi bi-check2 text-blue-600 text-sm ml-auto"></i>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div x-data="{
                open: false,
                selected: '<?= e($filters['delivery_status'] ?? '') ?>',
                label: '<?= ($filters['delivery_status'] ?? '') === 'PENDING' ? 'Pending' : (($filters['delivery_status'] ?? '') === 'SENT' ? 'Terkirim' : 'Semua status') ?>'
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
                            @click="selected = ''; label = 'Semua status'; open = false"
                        >
                            <span>Semua status</span>
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

    <?php if ($queryText !== '' && $batches === [] && $rows === []): ?>
        <!-- ALERT STATUS: HASIL PENCARIAN KOSONG -->
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
                    <strong class="alert-title">Tidak Ada Hasil</strong>
                    <div class="alert-message">Tidak ditemukan berkas report atau transaksi yang cocok dengan kata kunci &quot;<?= e($queryText) ?>&quot;. Silakan periksa kembali kata kunci atau gunakan parameter filter yang lebih longgar.</div>
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
                        <th class="py-3 px-3 font-heading">Tanggal</th>
                        <th class="py-3 px-3 font-heading">Tim</th>
                        <th class="py-3 px-3 font-heading">Report</th>
                        <th class="py-3 px-3 font-heading">Payer</th>
                        <th class="py-3 px-3 font-heading">Beneficiary</th>
                        <th class="py-3 px-3 font-heading">Bank</th>
                        <th class="py-3 px-3 font-heading">SWIFT</th>
                        <th class="py-3 px-3 font-heading">Invoice</th>
                        <th class="py-3 px-3 font-heading">Currency</th>
                        <th class="py-3 px-3 text-right font-heading">Amount</th>
                        <th class="py-3 px-3 font-heading">Status</th>
                        <th class="py-3 px-3 text-right font-heading">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                <?php foreach ($rows as $row): ?>
                    <tr class="even:bg-gray-50/80 [data-no-hover]">
                        <td class="py-2.5 px-3 text-nowrap"><?= e(format_date((string) ($row['report_date'] ?: $row['transaction_date']))) ?></td>
                        <td class="py-2.5 px-3"><strong><?= e((string) ($row['effective_team'] ?? '')) ?></strong></td>
                        <td class="py-2.5 px-3 font-medium text-gray-800 max-w-[160px] truncate"><?= e((string) ($row['batch_name'] ?? '')) ?></td>
                        <td class="py-2.5 px-3"><?= e((string) ($row['bill_to'] ?? '')) ?></td>
                        <td class="py-2.5 px-3 font-semibold text-gray-900"><?= e((string) ($row['beneficiary_name'] ?? '')) ?></td>
                        <td class="py-2.5 px-3"><?= e((string) ($row['beneficiary_bank'] ?? '')) ?></td>
                        <td class="py-2.5 px-3"><span class="inline-flex items-center py-0.5 px-1.5 rounded bg-gray-100 text-gray-700 tabular-nums text-[11px]"><?= e((string) ($row['swift'] ?? '')) ?></span></td>
                        <td class="py-2.5 px-3"><?= e((string) ($row['invoice_no'] ?? '')) ?></td>
                        <td class="py-2.5 px-3 font-semibold text-gray-800"><?= e((string) ($row['currency'] ?? '')) ?></td>
                        <td class="py-2.5 px-3 text-right text-nowrap font-semibold text-gray-900 tabular-nums"><?= e(number_format((float) ($row['amount'] ?? 0), 2, ',', '.')) ?></td>
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
