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

<div class="page-heading">
    <div>
        <span class="eyebrow dark"><?= e($batch['month_key']) ?></span>
        <h2><?= e($batch['name']) ?></h2>
        <p><?= e($batch['client_name'] ?: 'Tanpa nama klien') ?> · Sumber: <?= e($batch['source_filename']) ?></p>
    </div>
    <div class="page-heading-actions">
        <a class="btn btn-light" href="<?= url('/reports/date/' . $effectiveDate) ?>"><i class="bi bi-arrow-left"></i>Report per Tim</a>
        <?php if (can('report.manage')): ?><a class="btn btn-light" href="<?= url('/reports/' . $batch['id'] . '/edit') ?>"><i class="bi bi-pencil"></i>Edit</a><?php endif; ?>
        <a class="btn btn-primary" href="<?= url('/reports/' . $batch['id'] . '/export') ?>"><i class="bi bi-download"></i>Download Excel</a>
    </div>
</div>

<div class="summary-grid mb-4">
    <div class="summary-card"><span>Total baris</span><strong><?= number_format((int) ($summary['total'] ?? 0), 0, ',', '.') ?></strong></div>
    <div class="summary-card blue"><span>Terkirim</span><strong><?= number_format((int) ($summary['sent'] ?? 0), 0, ',', '.') ?></strong></div>
    <div class="summary-card amber"><span>Belum terkirim</span><strong><?= number_format((int) ($summary['pending'] ?? 0), 0, ',', '.') ?></strong></div>
    <div class="summary-card green"><span>Success</span><strong><?= number_format((int) ($summary['success'] ?? 0), 0, ',', '.') ?></strong></div>
    <div class="summary-card red"><span>Failed</span><strong><?= number_format((int) ($summary['failed'] ?? 0), 0, ',', '.') ?></strong></div>
    <div class="summary-card amber"><span>Dilewati saat impor</span><strong><?= number_format((int) ($batch['skipped_rows'] ?? 0), 0, ',', '.') ?></strong></div>
</div>
<?php if ($importIssues !== []): ?>
<details class="panel-card import-quality-panel mb-4"><summary><strong><i class="bi bi-shield-check"></i> Ringkasan validasi impor</strong><span><?= count($importIssues) ?> contoh masalah</span></summary><ul><?php foreach (array_slice($importIssues, 0, 15) as $issue): ?><li><?= e($issue) ?></li><?php endforeach; ?></ul></details>
<?php endif; ?>

<div class="panel-card mb-4">
    <form class="filter-grid report-filter" method="get">
        <label><span>Tanggal transaksi</span><select class="form-select" name="date"><option value="">Semua tanggal</option><?php foreach ($dates as $dateOption): ?><option value="<?= e($dateOption) ?>" <?= selected($filters['date'] ?? '', $dateOption) ?>><?= e(format_date($dateOption)) ?></option><?php endforeach; ?></select></label>
        <label><span>Status kirim</span><select class="form-select" name="delivery_status"><option value="">Semua</option><option value="PENDING" <?= selected($filters['delivery_status'] ?? '', 'PENDING') ?>>Belum Terkirim</option><option value="SENT" <?= selected($filters['delivery_status'] ?? '', 'SENT') ?>>Terkirim</option></select></label>
        <label><span>Hasil</span><select class="form-select" name="result_status"><option value="">Semua</option><option value="PENDING" <?= selected($filters['result_status'] ?? '', 'PENDING') ?>>Pending</option><option value="SUCCESS" <?= selected($filters['result_status'] ?? '', 'SUCCESS') ?>>Success</option><option value="FAILED" <?= selected($filters['result_status'] ?? '', 'FAILED') ?>>Failed</option></select></label>
        <label class="filter-grow"><span>Cari data</span><input class="form-control" type="search" name="q" placeholder="Tim, payer, beneficiary, bank, SWIFT, invoice..." value="<?= e($filters['q'] ?? '') ?>"></label>
        <div class="filter-actions"><button class="btn btn-primary" type="submit">Terapkan</button><a class="btn btn-light" href="<?= url('/reports/' . $batch['id']) ?>">Reset</a></div>
    </form>
</div>

<?php if (can('report.manage')): ?>
<form id="bulkStatusForm" class="bulk-toolbar" method="post" action="<?= url('/reports/' . $batch['id'] . '/bulk-status') ?>">
    <?= csrf_field() ?>
    <input type="hidden" name="return_query" value="<?= e($returnQuery) ?>">
    <strong><span data-selected-count>0</span> dipilih</strong>
    <select class="form-select compact" name="delivery_status"><option value="">Status kirim...</option><option value="PENDING">Belum Terkirim</option><option value="SENT">Terkirim</option></select>
    <select class="form-select compact" name="result_status"><option value="">Hasil...</option><option value="PENDING">Pending</option><option value="SUCCESS">Success</option><option value="FAILED">Failed</option></select>
    <button class="btn btn-primary btn-sm" type="submit">Terapkan ke pilihan</button>
</form>
<?php endif; ?>

<section class="panel-card report-table-card">
    <div class="panel-head">
        <div><h3><?= number_format(count($rows), 0, ',', '.') ?> baris ditampilkan</h3><p>Gunakan filter atau pencarian tabel untuk menemukan data.</p></div>
    </div>
    <div class="table-responsive report-wide-table">
        <table class="table modern-table" data-smart-table>
            <thead><tr>
                <?php if (can('report.manage')): ?><th class="select-column"><input type="checkbox" data-select-all></th><?php endif; ?>
                <th>TEAM</th><th>No.</th><th>Tanggal</th><th>Bill To/Payer</th><th>Beneficiary</th><th>Account</th><th>Beneficiary Bank</th><th>Bank Address</th><th>Amounts</th><th>Country</th><th>City</th><th>Beneficiary Address</th><th>SWIFT</th><th>Payment Details</th><th>Currency</th><th>Invoice</th><th>Contract</th><th>Correspondent</th><th>Swift Corr</th><th>Country Corr</th><th>Bank of Payer</th><th>Invoice Link</th><th>Bank Proses</th><th>Status Kirim</th><th>Hasil</th><th>Notes</th><th>Sheet</th><th>Aksi</th>
            </tr></thead>
            <tbody>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <?php if (can('report.manage')): ?><td><input type="checkbox" name="row_ids[]" value="<?= (int) $row['id'] ?>" form="bulkStatusForm" data-row-check></td><?php endif; ?>
                    <td><strong><?= e($row['team_label']) ?></strong></td>
                    <td><?= e($row['item_no']) ?></td>
                    <td class="text-nowrap"><?= e(format_date($row['transaction_date'])) ?></td>
                    <td><?= e($row['bill_to']) ?></td>
                    <td><?= e($row['beneficiary_name']) ?></td>
                    <td><?= e($row['beneficiary_account']) ?></td>
                    <td><?= e($row['beneficiary_bank']) ?></td>
                    <td><?= e($row['beneficiary_bank_address']) ?></td>
                    <td class="text-nowrap"><strong><?= e(number_format((float) $row['amount'], 2, ',', '.')) ?></strong></td>
                    <td><?= e($row['beneficiary_country']) ?></td>
                    <td><?= e($row['beneficiary_city']) ?></td>
                    <td><?= e($row['beneficiary_address']) ?></td>
                    <td><span class="code-pill"><?= e($row['swift']) ?></span></td>
                    <td><?= e($row['payment_details']) ?></td>
                    <td><strong><?= e($row['currency']) ?></strong></td>
                    <td><?= e($row['invoice_no']) ?><?php if ($row['invoice_date']): ?><small><?= e(format_date($row['invoice_date'])) ?></small><?php endif; ?></td>
                    <td><?= e($row['contract_no']) ?><?php if ($row['contract_date']): ?><small><?= e(format_date($row['contract_date'])) ?></small><?php endif; ?></td>
                    <td><?= e($row['correspondent_bank']) ?></td>
                    <td><?= e($row['correspondent_swift']) ?></td>
                    <td><?= e($row['correspondent_country']) ?></td>
                    <td><?= e($row['bank_of_payer']) ?></td>
                    <td><?php if ($row['invoice_link']): ?><a class="table-link" href="<?= e($row['invoice_link']) ?>" target="_blank" rel="noopener">Buka</a><?php endif; ?></td>
                    <td><?= e($row['processing_bank']) ?></td>
                    <td><?= status_badge($row['delivery_status']) ?></td>
                    <td><?= status_badge($row['result_status']) ?></td>
                    <td><?= e($row['notes']) ?></td>
                    <td><?= e($row['source_sheet']) ?></td>
                    <td class="sticky-action-cell">
                        <?php
                        $complaintEligible = in_array(strtolower((string) ($row['delivery_status'] ?? '')), ['sent','processed','delivered','success','terkirim','sukses'], true)
                            || in_array(strtolower((string) ($row['result_status'] ?? '')), ['success','processed','sukses'], true);
                        ?>
                        <?php if (can('complaint.manage') && $complaintEligible): ?><a class="btn btn-primary btn-sm mb-1" href="<?= url('/complaints/create?report_row_id=' . (int) $row['id']) ?>" title="Buat complain dari transaksi report yang sudah dijalankan"><i class="bi bi-headset"></i></a><?php endif; ?>
                        <?php if (can('report.manage')): ?>
                        <details class="row-editor">
                            <summary class="btn btn-light btn-sm">Ubah</summary>
                            <form method="post" action="<?= url('/reports/' . $batch['id'] . '/rows/' . $row['id'] . '/status') ?>">
                                <?= csrf_field() ?><input type="hidden" name="return_query" value="<?= e($returnQuery) ?>">
                                <label>Status kirim<select class="form-select" name="delivery_status"><option value="PENDING" <?= selected($row['delivery_status'], 'PENDING') ?>>Belum Terkirim</option><option value="SENT" <?= selected($row['delivery_status'], 'SENT') ?>>Terkirim</option></select></label>
                                <label>Hasil<select class="form-select" name="result_status"><option value="PENDING" <?= selected($row['result_status'], 'PENDING') ?>>Pending</option><option value="SUCCESS" <?= selected($row['result_status'], 'SUCCESS') ?>>Success</option><option value="FAILED" <?= selected($row['result_status'], 'FAILED') ?>>Failed</option></select></label>
                                <label>Catatan<textarea class="form-control" name="notes" rows="2"><?= e($row['notes']) ?></textarea></label>
                                <button class="btn btn-primary btn-sm" type="submit">Simpan</button>
                            </form>
                        </details>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if ($rows === []): ?><tr><td colspan="30" class="empty-state">Tidak ada baris yang cocok dengan filter.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

