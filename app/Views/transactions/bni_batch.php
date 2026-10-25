<?php

declare(strict_types=1);
$batch = is_array($batch ?? null) ? $batch : [];
$rows = is_array($rows ?? null) ? $rows : [];
?>
<div class="bni-batch-page">
    <div class="page-actions d-flex justify-content-end gap-2 flex-wrap mb-4">
        <a class="btn btn-light" href="<?= url('/transactions/bni') ?>"><i class="bi bi-arrow-left me-1"></i> Input BNI</a>
        <a class="btn btn-light" href="<?= url('/transactions') ?>">Pilihan Bank</a>
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
                        <td class="text-end"><a class="btn btn-sm btn-primary" href="<?= url('/transactions/bni/rows/' . (int) $row['id'] . '/print') ?>"><i class="bi bi-printer me-1"></i> Preview / Cetak</a></td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($rows===[]): ?><tr><td colspan="9" class="empty-state">Batch tidak memiliki data.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>
