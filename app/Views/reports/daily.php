<?php

declare(strict_types=1);

$rows = is_array($rows ?? null) ? $rows : [];
$companies = is_array($companies ?? null) ? $companies : [];
$date = (string) ($date ?? date('Y-m-d'));
$companyId = (int) ($companyId ?? 0);
$print = (bool) ($print ?? false);
?>

<div class="report-title">
    <div>
        <h2>Report Harian</h2>
        <p>Struktur tabel mengikuti format report transaksi operasional.</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <?php if (can('transaction.manage')): ?>
            <a href="<?= url('/report_paste_import.php') ?>" class="btn btn-primary"><i class="bi bi-clipboard-data me-1"></i>Paste dari Excel</a>
            <a href="<?= url('/transactions/import') ?>" class="btn btn-light"><i class="bi bi-file-earmark-excel me-1"></i>Import XLSX/CSV</a>
            <a href="<?= url('/transactions/create') ?>" class="btn btn-outline-primary"><i class="bi bi-plus-lg me-1"></i>Input Manual</a>
        <?php endif; ?>
    </div>
</div>

<div class="filter-panel mb-3">
    <form method="get" action="<?= url('/reports/daily') ?>" class="row g-2 align-items-end">
        <div class="col-md-3">
            <label class="form-label">Tanggal report</label>
            <input type="date" class="form-control" name="date" value="<?= e($date) ?>">
        </div>
        <div class="col-md-5">
            <label class="form-label">Perusahaan</label>
            <select class="form-select" name="company_id">
                <option value="0">Semua perusahaan</option>
                <?php foreach ($companies as $company): ?>
                    <option value="<?= (int) $company['id'] ?>" <?= selected($companyId, $company['id']) ?>><?= e($company['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4 d-flex gap-2">
            <button class="btn btn-primary"><i class="bi bi-funnel me-1"></i>Terapkan</button>
            <a class="btn btn-light" href="<?= url('/reports/daily') ?>">Hari Ini</a>
            <a class="btn btn-light" href="<?= url('/reports/daily/export?' . http_build_query(['date' => $date, 'company_id' => $companyId])) ?>"><i class="bi bi-download me-1"></i>CSV</a>
            <button type="button" class="btn btn-light" onclick="window.print()"><i class="bi bi-printer me-1"></i>Cetak</button>
        </div>
    </form>
</div>

<div class="panel-card print-report">
    <div class="panel-head">
        <div>
            <h3>Report <?= e(format_date($date)) ?></h3>
            <p><?= number_format(count($rows), 0, ',', '.') ?> transaksi ditemukan.</p>
        </div>
    </div>

    <div class="table-responsive report-wide">
        <table class="table align-middle modern-table" data-smart-table>
            <thead>
            <tr>
                <th>TEAM</th>
                <th>No. Invoice</th>
                <th>Date of Transaction</th>
                <th>Bill To/Payer</th>
                <th>Beneficiary Name</th>
                <th>Account Number of Beneficiary</th>
                <th>Beneficiary Bank</th>
                <th>Beneficiary Bank Location</th>
                <th>Amounts</th>
                <th>Beneficiary Country</th>
                <th>Beneficiary City</th>
                <th>Beneficiary Address</th>
                <th>SWIFT</th>
                <th>Payment Details</th>
                <th>Currency</th>
                <th>Correspondent Bank</th>
                <th>SWIFT Bank Correspondent</th>
                <th>Correspondent Bank Location</th>
                <th>Bank of Payer</th>
                <th>Reference Number</th>
                <th>Invoice Link</th>
                <th>Status</th>
                <th>Refund Status</th>
                <th>Notes</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td><?= e($row['team_no']) ?></td>
                    <td><?= e($row['invoice_no'] ?? '') ?></td>
                    <td><?= e(format_date($row['transaction_date'])) ?></td>
                    <td><?= e($row['company_name']) ?></td>
                    <td><?= e($row['beneficiary_name'] ?? '') ?></td>
                    <td><?= e($row['beneficiary_account'] ?? '') ?></td>
                    <td><?= e($row['beneficiary_bank'] ?? '') ?></td>
                    <td><?= e($row['beneficiary_bank_location'] ?? '') ?></td>
                    <td class="text-nowrap"><?= e(number_format((float) $row['amount'], 2, ',', '.')) ?></td>
                    <td><?= e($row['beneficiary_country'] ?? '') ?></td>
                    <td><?= e($row['beneficiary_city'] ?? '') ?></td>
                    <td><?= e($row['beneficiary_address'] ?? '') ?></td>
                    <td><?= e($row['swift'] ?? '') ?></td>
                    <td><?= e($row['payment_details'] ?? '') ?></td>
                    <td><?= e($row['currency'] ?? '') ?></td>
                    <td><?= e($row['correspondent_bank'] ?? '') ?></td>
                    <td><?= e($row['correspondent_swift'] ?? '') ?></td>
                    <td><?= e($row['correspondent_location'] ?? '') ?></td>
                    <td><?= e($row['payer_bank'] ?? '') ?></td>
                    <td><?= e($row['reference_no'] ?? '') ?></td>
                    <td><?php if (!empty($row['invoice_link'])): ?><a href="<?= e($row['invoice_link']) ?>" target="_blank" rel="noopener">Buka</a><?php endif; ?></td>
                    <td><?= status_badge((string) $row['status']) ?></td>
                    <td><?= status_badge((string) $row['refund_status']) ?></td>
                    <td><?= e($row['notes'] ?? '') ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if ($rows === []): ?>
                <tr><td colspan="24" class="empty-state">Belum ada transaksi pada filter yang dipilih.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
