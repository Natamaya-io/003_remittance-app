<?php

declare(strict_types=1);

$briJobs = (int) ($briJobs ?? 0);
$bniBatches = (int) ($bniBatches ?? 0);
?>

<div class="transaction-bank-hub space-y-6">
    <section class="bank-choice-grid grid grid-cols-1 md:grid-cols-2 gap-6">
        <article class="bank-choice-card bri bg-white border border-gray-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
            <div>
                <div class="bank-choice-icon w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-2xl mb-4">
                    <i class="bi bi-bank2"></i>
                </div>
                <div class="bank-choice-copy" x-data="{ showInfo: false }">
                    <span class="bank-choice-kicker text-xs font-semibold text-blue-600 tracking-wide uppercase">BANK BRI</span>
                    <div class="flex items-center gap-2 mt-1 mb-2">
                        <h3 class="text-lg font-semibold font-heading text-gray-900 m-0">Transaksi BRI</h3>
                        <button
                            type="button"
                            @click="showInfo = !showInfo"
                            class="info-toggle-btn"
                            :class="{ 'is-active': showInfo }"
                            :aria-expanded="showInfo.toString()"
                            aria-label="Tampilkan info BRI"
                            title="Informasi bantuan"
                        >
                            <i class="bi bi-info-circle text-xs leading-none"></i>
                        </button>
                    </div>
                    <div
                        x-show="showInfo"
                        x-cloak
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        class="text-xs text-gray-600 leading-relaxed mb-3 p-2.5 rounded-xl bg-blue-50/50 border border-blue-100 flex items-start gap-1.5"
                        style="display: none;"
                    >
                        <i class="bi bi-info-circle text-[11px] text-blue-500 mt-0.5 shrink-0" aria-hidden="true"></i>
                        <span>Input data transaksi, bentuk tabel batch, bersihkan tanda baca otomatis, lalu ekspor Excel final.</span>
                    </div>
                </div>
                <div class="bank-flow-mini flex flex-wrap gap-2 text-xs text-gray-500 mb-6">
                    <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-lg bg-gray-100 font-medium"><b>1</b> Input</span>
                    <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-lg bg-gray-100 font-medium"><b>2</b> Bentuk tabel</span>
                    <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-lg bg-gray-100 font-medium"><b>3</b> Hapus tanda baca</span>
                    <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-lg bg-gray-100 font-medium"><b>4</b> Download Excel</span>
                </div>
            </div>
            <div class="bank-choice-footer pt-4 border-t border-gray-100 flex items-center justify-between gap-3 mt-auto">
                <span class="bank-choice-count text-xs text-gray-500"><strong class="font-semibold text-gray-800 tabular-nums"><?= $briJobs ?></strong> batch tersimpan</span>
                <div class="bank-choice-actions flex items-center gap-2">
                    <a class="btn btn-light py-2 px-3 text-xs font-medium rounded-lg border border-gray-200 hover:bg-gray-50 text-gray-700" href="<?= url('/transactions/bri/history') ?>">Riwayat</a>
                    <?php if (can('transaction.manage')): ?>
                        <a class="btn btn-primary py-2 px-3.5 text-xs font-medium rounded-lg" href="<?= url('/transactions/bri') ?>">
                            <i class="bi bi-file-earmark-arrow-up me-1"></i> Import Template
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </article>

        <article class="bank-choice-card bni bg-white border border-gray-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
            <div>
                <div class="bank-choice-icon w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl mb-4">
                    <i class="bi bi-file-earmark-spreadsheet"></i>
                </div>
                <div class="bank-choice-copy" x-data="{ showInfo: false }">
                    <span class="bank-choice-kicker text-xs font-semibold text-emerald-600 tracking-wide uppercase">BANK BNI</span>
                    <div class="flex items-center gap-2 mt-1 mb-2">
                        <h3 class="text-lg font-semibold font-heading text-gray-900 m-0">Transaksi BNI</h3>
                        <button
                            type="button"
                            @click="showInfo = !showInfo"
                            class="info-toggle-btn"
                            :class="{ 'is-active': showInfo }"
                            :aria-expanded="showInfo.toString()"
                            aria-label="Tampilkan info BNI"
                            title="Informasi bantuan"
                        >
                            <i class="bi bi-info-circle text-xs leading-none"></i>
                        </button>
                    </div>
                    <div
                        x-show="showInfo"
                        x-cloak
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        class="text-xs text-gray-600 leading-relaxed mb-3 p-2.5 rounded-xl bg-emerald-50/50 border border-emerald-100 flex items-start gap-1.5"
                        style="display: none;"
                    >
                        <i class="bi bi-info-circle text-[11px] text-emerald-500 mt-0.5 shrink-0" aria-hidden="true"></i>
                        <span>Copy paste tabel Excel, bentuk data formulir Kiriman Uang BNI, lalu cetak PDF/preview.</span>
                    </div>
                </div>
                <div class="bank-flow-mini two flex flex-wrap gap-2 text-xs text-gray-500 mb-6">
                    <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-lg bg-gray-100 font-medium"><b>1</b> Copy Paste Excel</span>
                    <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-lg bg-gray-100 font-medium"><b>2</b> Form BNI & Cetak</span>
                </div>
            </div>
            <div class="bank-choice-footer pt-4 border-t border-gray-100 flex items-center justify-between gap-3 mt-auto">
                <span class="bank-choice-count text-xs text-gray-500"><strong class="font-semibold text-gray-800 tabular-nums"><?= $bniBatches ?></strong> batch tersimpan</span>
                <?php if (can('transaction.manage')): ?>
                    <a class="btn btn-primary py-2 px-3.5 text-xs font-medium rounded-lg" href="<?= url('/transactions/bni') ?>">
                        <i class="bi bi-file-earmark-arrow-up me-1"></i> Import Template
                    </a>
                <?php else: ?>
                    <a class="btn btn-light py-2 px-3 text-xs font-medium rounded-lg border border-gray-200 hover:bg-gray-50 text-gray-700" href="<?= url('/transactions/bni') ?>">Lihat BNI</a>
                <?php endif; ?>
            </div>
        </article>
    </section>
</div>
