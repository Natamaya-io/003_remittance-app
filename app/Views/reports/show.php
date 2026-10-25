<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| REPORT HARIAN - DETAIL BATCH / BARIS TRANSAKSI (PRELINE & ALPINE NATIVE)
|--------------------------------------------------------------------------
*/

$rows = is_array($rows ?? null) ? $rows : [];
$filters = is_array($filters ?? null) ? $filters : [];
$summary = is_array($summary ?? null) ? $summary : [];
$returnQuery = http_build_query(array_filter($filters, static fn ($value): bool => $value !== ''));
$importSummary = json_decode((string) ($batch['import_summary_json'] ?? ''), true) ?: [];
$importIssues = is_array($importSummary['issues'] ?? null) ? $importSummary['issues'] : [];
$effectiveDate = (string) ($effectiveDate ?? ($batch['report_date'] ?? ''));
?>

<div class="max-w-7xl mx-auto space-y-6">

    <!-- =========================================================
         PAGE HEADING
         ========================================================= -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1.5">
                <span class="inline-flex items-center py-0.5 px-2.5 rounded-full text-xs font-bold bg-blue-50 text-blue-800 border border-blue-200">
                    <?= e($batch['month_key']) ?>
                </span>
                <span class="inline-flex items-center py-0.5 px-2.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                    <?= e(format_date($effectiveDate)) ?>
                </span>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight"><?= e($batch['name']) ?></h2>
            <div class="text-xs text-gray-500 mt-1 flex items-center gap-2 flex-wrap">
                <span class="font-semibold text-gray-700"><?= e($batch['client_name'] ?: 'Tanpa nama klien') ?></span>
                <span>·</span>
                <span>Berkas: <span class="font-mono"><?= e($batch['source_filename']) ?></span></span>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a class="py-2 px-3.5 text-xs font-medium rounded-lg border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 shadow-xs inline-flex items-center gap-1.5 transition-colors" href="<?= url('/reports/date/' . $effectiveDate) ?>">
                <i class="bi bi-arrow-left"></i>
                Report per Tim
            </a>
            <?php if (can('report.manage')): ?>
                <a class="py-2 px-3 text-xs font-medium rounded-lg border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 shadow-xs inline-flex items-center gap-1.5 transition-colors" href="<?= url('/reports/' . $batch['id'] . '/edit') ?>">
                    <i class="bi bi-pencil"></i>
                    Edit
                </a>
            <?php endif; ?>
            <a class="btn btn-primary py-2 px-4 text-xs font-medium rounded-lg inline-flex items-center gap-1.5" href="<?= url('/reports/' . $batch['id'] . '/export') ?>">
                <i class="bi bi-download"></i>
                Download Excel
            </a>
        </div>
    </div>

    <!-- =========================================================
         SUMMARY KPI GRID (6 METRIK OPERASIONAL)
         ========================================================= -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <div class="bg-white border border-gray-200 rounded-2xl p-4 shadow-xs">
            <span class="text-xs font-semibold text-gray-500 block mb-1">Total Baris</span>
            <strong class="text-xl font-bold text-gray-900 block font-mono"><?= number_format((int) ($summary['total'] ?? 0), 0, ',', '.') ?></strong>
        </div>
        <div class="bg-white border border-blue-200 bg-blue-50/30 rounded-2xl p-4 shadow-xs">
            <span class="text-xs font-semibold text-blue-700 block mb-1">Terkirim</span>
            <strong class="text-xl font-bold text-blue-700 block font-mono"><?= number_format((int) ($summary['sent'] ?? 0), 0, ',', '.') ?></strong>
        </div>
        <div class="bg-white border border-amber-200 bg-amber-50/30 rounded-2xl p-4 shadow-xs">
            <span class="text-xs font-semibold text-amber-700 block mb-1">Belum Terkirim</span>
            <strong class="text-xl font-bold text-amber-700 block font-mono"><?= number_format((int) ($summary['pending'] ?? 0), 0, ',', '.') ?></strong>
        </div>
        <div class="bg-white border border-emerald-200 bg-emerald-50/30 rounded-2xl p-4 shadow-xs">
            <span class="text-xs font-semibold text-emerald-700 block mb-1">Success</span>
            <strong class="text-xl font-bold text-emerald-700 block font-mono"><?= number_format((int) ($summary['success'] ?? 0), 0, ',', '.') ?></strong>
        </div>
        <div class="bg-white border border-rose-200 bg-rose-50/30 rounded-2xl p-4 shadow-xs">
            <span class="text-xs font-semibold text-rose-700 block mb-1">Failed</span>
            <strong class="text-xl font-bold text-rose-700 block font-mono"><?= number_format((int) ($summary['failed'] ?? 0), 0, ',', '.') ?></strong>
        </div>
        <div class="bg-white border border-gray-200 rounded-2xl p-4 shadow-xs">
            <span class="text-xs font-semibold text-gray-500 block mb-1">Dilewati Impor</span>
            <strong class="text-xl font-bold text-gray-700 block font-mono"><?= number_format((int) ($batch['skipped_rows'] ?? 0), 0, ',', '.') ?></strong>
        </div>
    </div>

    <?php if ($importIssues !== []): ?>
        <!-- VALIDASI IMPOR QUALITY ALERT -->
        <details class="bg-amber-50/60 border border-amber-200 rounded-2xl p-4 shadow-xs">
            <summary class="font-bold text-xs text-amber-900 cursor-pointer flex items-center justify-between">
                <span class="inline-flex items-center gap-2">
                    <i class="bi bi-shield-check text-amber-600 text-base"></i>
                    Ringkasan validasi impor berkas
                </span>
                <span class="py-0.5 px-2.5 rounded-full bg-amber-200 text-amber-800 text-[11px] font-bold"><?= count($importIssues) ?> isu ditemukan</span>
            </summary>
            <ul class="mt-3 space-y-1 text-xs text-amber-800 list-disc list-inside">
                <?php foreach (array_slice($importIssues, 0, 15) as $issue): ?>
                    <li><?= e($issue) ?></li>
                <?php endforeach; ?>
            </ul>
        </details>
    <?php endif; ?>

    <!-- =========================================================
         FILTER TRANSAKSI TOOLBAR
         ========================================================= -->
    <div class="bg-white border border-gray-200 rounded-2xl p-4 sm:p-5 shadow-xs">
        <form class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end" method="get">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Tanggal transaksi</label>
                <select class="py-1.5 px-3 block w-full border border-gray-300 rounded-lg text-xs focus:border-blue-500 focus:ring-blue-500 shadow-xs" name="date">
                    <option value="">Semua tanggal</option>
                    <?php foreach ($dates as $dateOption): ?>
                        <option value="<?= e($dateOption) ?>" <?= selected($filters['date'] ?? '', $dateOption) ?>><?= e(format_date($dateOption)) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Status kirim</label>
                <select class="py-1.5 px-3 block w-full border border-gray-300 rounded-lg text-xs focus:border-blue-500 focus:ring-blue-500 shadow-xs" name="delivery_status">
                    <option value="">Semua status</option>
                    <option value="PENDING" <?= selected($filters['delivery_status'] ?? '', 'PENDING') ?>>Belum Terkirim</option>
                    <option value="SENT" <?= selected($filters['delivery_status'] ?? '', 'SENT') ?>>Terkirim</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Hasil transaksi</label>
                <select class="py-1.5 px-3 block w-full border border-gray-300 rounded-lg text-xs focus:border-blue-500 focus:ring-blue-500 shadow-xs" name="result_status">
                    <option value="">Semua hasil</option>
                    <option value="PENDING" <?= selected($filters['result_status'] ?? '', 'PENDING') ?>>Pending</option>
                    <option value="SUCCESS" <?= selected($filters['result_status'] ?? '', 'SUCCESS') ?>>Success</option>
                    <option value="FAILED" <?= selected($filters['result_status'] ?? '', 'FAILED') ?>>Failed</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Cari data</label>
                <input class="py-1.5 px-3 block w-full border border-gray-300 rounded-lg text-xs focus:border-blue-500 focus:ring-blue-500 shadow-xs" type="search" name="q" placeholder="Payer, bank, SWIFT..." value="<?= e($filters['q'] ?? '') ?>">
            </div>

            <div class="flex items-center gap-2">
                <button class="btn btn-primary py-1.5 px-3.5 text-xs font-medium rounded-lg inline-flex items-center gap-1.5 flex-1 justify-center" type="submit">
                    <i class="bi bi-funnel"></i>
                    Terapkan
                </button>
                <a class="py-1.5 px-3 text-xs font-medium rounded-lg border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 shadow-xs transition-colors" href="<?= url('/reports/' . $batch['id']) ?>">
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
    }" class="space-y-4">

        <?php if (can('report.manage')): ?>
            <form id="bulkStatusForm" method="post" action="<?= url('/reports/' . $batch['id'] . '/bulk-status') ?>" class="flex flex-wrap items-center justify-between gap-3 p-3 bg-white border border-gray-200 rounded-2xl shadow-xs text-xs">
                <?= csrf_field() ?>
                <input type="hidden" name="return_query" value="<?= e($returnQuery) ?>">

                <div class="flex items-center gap-2 ps-1">
                    <i class="bi bi-check2-square text-blue-600 text-sm"></i>
                    <strong class="text-gray-800"><span x-text="selectedCount">0</span> baris dipilih</strong>
                </div>

                <div class="flex items-center gap-2">
                    <select class="py-1.5 px-3 border border-gray-300 rounded-lg text-xs focus:border-blue-500 shadow-xs" name="delivery_status">
                        <option value="">Ubah Status Kirim...</option>
                        <option value="PENDING">Belum Terkirim</option>
                        <option value="SENT">Terkirim</option>
                    </select>

                    <select class="py-1.5 px-3 border border-gray-300 rounded-lg text-xs focus:border-blue-500 shadow-xs" name="result_status">
                        <option value="">Ubah Hasil...</option>
                        <option value="PENDING">Pending</option>
                        <option value="SUCCESS">Success</option>
                        <option value="FAILED">Failed</option>
                    </select>

                    <button class="btn btn-primary py-1.5 px-3 text-xs font-medium rounded-lg inline-flex items-center gap-1.5" type="submit" :disabled="selectedCount === 0">
                        Terapkan ke Pilihan
                    </button>
                </div>
            </form>
        <?php endif; ?>

        <!-- =========================================================
             WIDE TRANSACTION DATA TABLE (PRELINE UI + THE NAKED ZEBRA)
             ========================================================= -->
        <section class="bg-white border border-gray-200 rounded-2xl shadow-xs overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-gray-900"><?= number_format(count($rows), 0, ',', '.') ?> Baris Transaksi</h3>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-left text-xs">
                    <thead class="bg-gray-50/75">
                        <tr class="text-gray-600 font-semibold text-[11px] uppercase tracking-wider">
                            <?php if (can('report.manage')): ?>
                                <th class="py-3.5 px-3 w-10 text-center">
                                    <input type="checkbox" @change="toggleAll($el.checked)" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
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
                            <th class="py-3.5 px-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                    <?php foreach ($rows as $row): ?>
                        <tr class="even:bg-gray-50/50 [data-no-hover]">
                            <?php if (can('report.manage')): ?>
                                <td class="py-3 px-3 text-center">
                                    <input type="checkbox" name="row_ids[]" value="<?= (int) $row['id'] ?>" form="bulkStatusForm" @change="updateCount()" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                </td>
                            <?php endif; ?>
                            <td class="py-3 px-3 font-semibold text-gray-900 whitespace-nowrap"><?= e($row['team_label'] ?: $batch['effective_team'] ?: '-') ?></td>
                            <td class="py-3 px-3 font-mono text-gray-500"><?= (int) $row['row_order'] ?></td>
                            <td class="py-3 px-3 whitespace-nowrap"><?= e(format_date($row['transaction_date'])) ?></td>
                            <td class="py-3 px-3 font-medium text-gray-900 whitespace-nowrap max-w-xs truncate" title="<?= e($row['payer_name']) ?>"><?= e($row['payer_name']) ?></td>
                            <td class="py-3 px-3 font-medium text-gray-900 whitespace-nowrap max-w-xs truncate" title="<?= e($row['beneficiary_name']) ?>"><?= e($row['beneficiary_name']) ?></td>
                            <td class="py-3 px-3 font-mono text-xs whitespace-nowrap"><?= e($row['beneficiary_account']) ?></td>
                            <td class="py-3 px-3 whitespace-nowrap max-w-xs truncate" title="<?= e($row['beneficiary_bank']) ?>"><?= e($row['beneficiary_bank']) ?></td>
                            <td class="py-3 px-3 whitespace-nowrap max-w-xs truncate text-gray-500" title="<?= e($row['bank_address']) ?>"><?= e($row['bank_address']) ?></td>
                            <td class="py-3 px-3 text-right font-mono font-bold text-gray-900 whitespace-nowrap"><?= format_money((float) $row['amount'], (string) ($row['currency'] ?: 'IDR')) ?></td>
                            <td class="py-3 px-3 whitespace-nowrap"><?= e($row['country']) ?></td>
                            <td class="py-3 px-3 whitespace-nowrap"><?= e($row['city']) ?></td>
                            <td class="py-3 px-3 whitespace-nowrap max-w-xs truncate text-gray-500" title="<?= e($row['beneficiary_address']) ?>"><?= e($row['beneficiary_address']) ?></td>
                            <td class="py-3 px-3 font-mono whitespace-nowrap"><?= e($row['swift_code']) ?></td>
                            <td class="py-3 px-3 whitespace-nowrap max-w-xs truncate text-gray-500" title="<?= e($row['payment_details']) ?>"><?= e($row['payment_details']) ?></td>
                            <td class="py-3 px-3 font-mono whitespace-nowrap"><?= e($row['currency']) ?></td>
                            <td class="py-3 px-3 font-mono whitespace-nowrap"><?= e($row['invoice_number']) ?></td>
                            <td class="py-3 px-3 whitespace-nowrap"><?= e($row['contract_number']) ?></td>
                            <td class="py-3 px-3 whitespace-nowrap max-w-xs truncate text-gray-500" title="<?= e($row['correspondent_bank']) ?>"><?= e($row['correspondent_bank']) ?></td>
                            <td class="py-3 px-3 font-mono whitespace-nowrap"><?= e($row['correspondent_swift']) ?></td>
                            <td class="py-3 px-3 whitespace-nowrap"><?= e($row['correspondent_country']) ?></td>
                            <td class="py-3 px-3 whitespace-nowrap"><?= e($row['bank_of_payer']) ?></td>
                            <td class="py-3 px-3 whitespace-nowrap">
                                <?php if (!empty($row['invoice_link'])): ?>
                                    <a class="text-blue-600 hover:underline inline-flex items-center gap-1" href="<?= e($row['invoice_link']) ?>" target="_blank" rel="noopener">
                                        <i class="bi bi-box-arrow-up-right"></i> Link
                                    </a>
                                <?php else: ?>
                                    <span class="text-gray-300">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3 px-3 whitespace-nowrap"><?= e($row['bank_process'] ?: '-') ?></td>
                            <td class="py-3 px-3 whitespace-nowrap">
                                <?php if ($row['delivery_status'] === 'SENT'): ?>
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
                                <?php
                                $resBadge = match($row['result_status']) {
                                    'SUCCESS' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'FAILED' => 'bg-red-50 text-red-700 border-red-200',
                                    default => 'bg-gray-100 text-gray-700 border-gray-200',
                                };
                                ?>
                                <span class="inline-flex items-center py-0.5 px-2 rounded-full text-[11px] font-semibold border <?= $resBadge ?>">
                                    <?= e($row['result_status']) ?>
                                </span>
                            </td>
                            <td class="py-3 px-3 whitespace-nowrap max-w-xs truncate text-gray-500" title="<?= e($row['notes']) ?>"><?= e($row['notes'] ?: '-') ?></td>
                            <td class="py-3 px-3 text-gray-400 whitespace-nowrap"><?= e($row['source_sheet'] ?: '-') ?></td>
                            <td class="py-3 px-3 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1">
                                    <?php if (!empty($row['transaction_id'])): ?>
                                        <a class="p-1 rounded-md text-gray-500 hover:text-blue-600 hover:bg-gray-100" href="<?= url('/transactions/' . $row['transaction_id']) ?>" title="Buka transaksi sistem">
                                            <i class="bi bi-link-45deg text-base"></i>
                                        </a>
                                    <?php endif; ?>

                                    <?php if (can('report.manage')): ?>
                                        <div class="hs-dropdown relative inline-flex" x-data="{ openRow: false }">
                                            <button
                                                type="button"
                                                class="py-1 px-2.5 text-xs font-semibold rounded-lg border border-gray-200 bg-white text-gray-700 shadow-xs hover:bg-gray-50 focus:outline-none"
                                                @click="openRow = !openRow"
                                            >
                                                Ubah
                                            </button>
                                            <div
                                                class="transition-[opacity,margin] duration hidden min-w-64 bg-white shadow-xl rounded-xl p-3 border border-gray-200 z-50 absolute right-0 mt-2 text-left"
                                                :class="{ '!block opacity-100': openRow }"
                                                @click.outside="openRow = false"
                                            >
                                                <form method="post" action="<?= url('/reports/' . $batch['id'] . '/rows/' . $row['id'] . '/status') ?>" class="space-y-2.5">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="return_query" value="<?= e($returnQuery) ?>">
                                                    <div>
                                                        <label class="block text-[11px] font-semibold text-gray-700 mb-1">Status kirim</label>
                                                        <select class="py-1 px-2 block w-full border border-gray-300 rounded-lg text-xs" name="delivery_status">
                                                            <option value="PENDING" <?= selected($row['delivery_status'], 'PENDING') ?>>Belum Terkirim</option>
                                                            <option value="SENT" <?= selected($row['delivery_status'], 'SENT') ?>>Terkirim</option>
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <label class="block text-[11px] font-semibold text-gray-700 mb-1">Hasil</label>
                                                        <select class="py-1 px-2 block w-full border border-gray-300 rounded-lg text-xs" name="result_status">
                                                            <option value="PENDING" <?= selected($row['result_status'], 'PENDING') ?>>Pending</option>
                                                            <option value="SUCCESS" <?= selected($row['result_status'], 'SUCCESS') ?>>Success</option>
                                                            <option value="FAILED" <?= selected($row['result_status'], 'FAILED') ?>>Failed</option>
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <label class="block text-[11px] font-semibold text-gray-700 mb-1">Catatan</label>
                                                        <textarea class="py-1 px-2 block w-full border border-gray-300 rounded-lg text-xs" name="notes" rows="2"><?= e($row['notes']) ?></textarea>
                                                    </div>
                                                    <button class="btn btn-primary py-1 px-3 text-xs font-medium rounded-lg w-full justify-center" type="submit">
                                                        Simpan
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if ($rows === []): ?>
                        <tr>
                            <td colspan="30" class="py-12 text-center text-gray-400">
                                <i class="bi bi-file-earmark-x text-2xl block mb-2"></i>
                                Tidak ada baris yang cocok dengan filter.
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

    </div>

</div>
