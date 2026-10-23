<?php

declare(strict_types=1);

$briJobs = (int) ($briJobs ?? 0);
$bniBatches = (int) ($bniBatches ?? 0);
?>

<div class="transaction-bank-hub">
    <section class="bank-hub-heading">
        <div>
            <span class="eyebrow dark">TRANSAKSI</span>
            <h2>Pilih Bank Transaksi</h2>
            <p>Pilih alur transaksi Bank BRI atau Bank BNI.</p>
        </div>
    </section>

    <section class="bank-choice-grid">
        <article class="bank-choice-card bri">
            <div class="bank-choice-icon"><i class="bi bi-bank2"></i></div>
            <div class="bank-choice-copy">
                <span class="bank-choice-kicker">BANK BRI</span>
                <h3>Transaksi BRI</h3>
                <p>Input data, bentuk tabel, bersihkan tanda baca, lalu download Excel final.</p>
            </div>
            <div class="bank-flow-mini">
                <span><b>1</b> Input</span>
                <span><b>2</b> Bentuk tabel</span>
                <span><b>3</b> Hapus tanda baca</span>
                <span><b>4</b> Download Excel</span>
            </div>
            <div class="bank-choice-footer">
                <span class="bank-choice-count"><strong><?= $briJobs ?></strong> batch tersimpan</span>
                <div class="bank-choice-actions">
                    <a class="btn btn-light" href="<?= url('/transactions/bri/history') ?>">Riwayat</a>
                    <?php if (can('transaction.manage')): ?>
                        <a class="btn btn-primary" href="<?= url('/transactions/bri') ?>">
                            <i class="bi bi-file-earmark-arrow-up me-1"></i> Import Template
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </article>

        <article class="bank-choice-card bni">
            <div class="bank-choice-icon"><i class="bi bi-file-earmark-spreadsheet"></i></div>
            <div class="bank-choice-copy">
                <span class="bank-choice-kicker">BANK BNI</span>
                <h3>Transaksi BNI</h3>
                <p>Copy paste Excel, bentuk data formulir Kiriman Uang BNI, lalu preview dan cetak.</p>
            </div>
            <div class="bank-flow-mini two">
                <span><b>1</b> Copy Paste Excel</span>
                <span><b>2</b> Form BNI & Cetak</span>
            </div>
            <div class="bank-choice-footer">
                <span class="bank-choice-count"><strong><?= $bniBatches ?></strong> batch tersimpan</span>
                <?php if (can('transaction.manage')): ?>
                    <a class="btn btn-primary" href="<?= url('/transactions/bni') ?>">
                        <i class="bi bi-file-earmark-arrow-up me-1"></i> Import Template
                    </a>
                <?php else: ?>
                    <a class="btn btn-light" href="<?= url('/transactions/bni') ?>">Lihat BNI</a>
                <?php endif; ?>
            </div>
        </article>
    </section>
</div>
