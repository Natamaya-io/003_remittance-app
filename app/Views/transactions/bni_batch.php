<?php

declare(strict_types=1);
$batch = is_array($batch ?? null) ? $batch : [];
$rows = is_array($rows ?? null) ? $rows : [];
?>
<div class="bni-batch-page">
    <div class="page-actions d-flex justify-content-end gap-2 flex-wrap mb-4">
        <a class="btn-secondary" href="<?= url('/transactions/bni') ?>">
            <i class="bi bi-arrow-left"></i>
            <span>Input BNI</span>
        </a>
        <a class="btn-secondary" href="<?= url('/transactions') ?>">
            <span>Pilihan Bank</span>
        </a>
    </div>
    <section class="panel-card">
        <div class="table-responsive">
            <table class="table modern-table bni-batch-table">
                <thead><tr><th>No</th><th>Tanggal</th><th>Penerima</th><th>Rekening</th><th>Bank</th><th>Pengirim</th><th>Currency</th><th>Amount</th><th class="text-end">Form BNI</th></tr></thead>
                <tbody>
                <?php foreach ($rows as $row): $data=is_array($row['payload'] ?? null)?$row['payload']:[]; ?>
                    <tr>
                        <td><?= (int) ($row['row_no'] ?? 0) ?></td>
                        <td><?= e((string) ($data['transaction_date'] ?? '-')) ?></td>
                        <td><strong><?= e((string) ($data['beneficiary_name'] ?? '-')) ?></strong></td>
                        <td><?= e((string) ($data['beneficiary_account'] ?? '-')) ?></td>
                        <td><?= e((string) ($data['beneficiary_bank'] ?? '-')) ?></td>
                        <td><?= e((string) ($data['remitter_name'] ?? '-')) ?></td>
                        <td><?= e((string) ($data['currency'] ?? '-')) ?></td>
                        <td><?= number_format((float) ($data['amount'] ?? 0),2,',','.') ?></td>
                        <td class="text-end"><a class="btn-primary btn-primary-sm" href="<?= url('/transactions/bni/rows/' . (int) $row['id'] . '/print') ?>"><i class="bi bi-printer"></i><span>Preview / Cetak</span></a></td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($rows===[]): ?><tr><td colspan="9" class="empty-state">Batch tidak memiliki data.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>
