<?php

declare(strict_types=1);

$briJobs = (int) ($briJobs ?? 0);
$bniBatches = (int) ($bniBatches ?? 0);
?>

<div class="transaction-bank-hub space-y-6">
    <section class="bank-choice-grid grid grid-cols-1 md:grid-cols-2 gap-6">
        <article class="bank-choice-card bri bg-white border border-gray-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
            <div>
                <img src="<?= e(url('/bri-logo.svg')) ?>" alt="Bank BRI" class="h-12 w-auto max-w-[140px] mb-4 object-contain select-none">
                <div class="bank-choice-copy">
                    <span class="bank-choice-kicker text-xs font-semibold tracking-wide uppercase">BANK BRI</span>
                    <div class="mt-1 mb-3">
                        <?= render_heading_with_info(
                            'Transaksi BRI',
                            'Input data transaksi, bentuk tabel batch, bersihkan tanda baca otomatis, lalu ekspor Excel final.',
                            [
                                'titleTag'     => 'h3',
                                'titleClass'   => 'text-lg font-semibold font-heading text-gray-900 m-0',
                                'wrapperClass' => 'inline-flex items-center gap-1.5',
                            ]
                        ) ?>
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
                    <a class="btn-secondary" href="<?= url('/transactions/bri/history') ?>">
                        <span>Riwayat</span>
                    </a>
                    <?php if (can('transaction.manage')): ?>
                        <a class="btn-primary" href="<?= url('/transactions/bri') ?>">
                            <i class="bi bi-file-earmark-arrow-up"></i>
                            <span>Import Template</span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </article>

        <article class="bank-choice-card bni bg-white border border-gray-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
            <div>
                <img src="<?= e(url('/bni-logo.svg')) ?>" alt="Bank BNI" class="h-12 w-auto max-w-[140px] mb-4 object-contain select-none">
                <div class="bank-choice-copy">
                    <span class="bank-choice-kicker text-xs font-semibold tracking-wide uppercase">BANK BNI</span>
                    <div class="mt-1 mb-3">
                        <?= render_heading_with_info(
                            'Transaksi BNI',
                            'Copy paste tabel Excel, bentuk data formulir Kiriman Uang BNI, lalu cetak PDF/preview.',
                            [
                                'titleTag'     => 'h3',
                                'titleClass'   => 'text-lg font-semibold font-heading text-gray-900 m-0',
                                'wrapperClass' => 'inline-flex items-center gap-1.5',
                            ]
                        ) ?>
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
                    <a class="btn-primary" href="<?= url('/transactions/bni') ?>">
                        <i class="bi bi-file-earmark-arrow-up"></i>
                        <span>Import Template</span>
                    </a>
                <?php else: ?>
                    <a class="btn-secondary" href="<?= url('/transactions/bni') ?>">
                        <span>Lihat BNI</span>
                    </a>
                <?php endif; ?>
            </div>
        </article>
    </section>
</div>
