<?php

declare(strict_types=1);

$filters = is_array($filters ?? null) ? $filters : [];
$teams = is_array($teams ?? null) ? $teams : [];
$batches = is_array($batches ?? null) ? $batches : [];
$rows = is_array($rows ?? null) ? $rows : [];
$queryText = (string) ($filters['q'] ?? '');
?>

<div class="page-heading">
    <div>
        <span class="eyebrow dark">PENCARIAN GLOBAL</span>
        <h2>Hasil Pencarian Report</h2>
        <p>Pencarian dijalankan pada seluruh bulan, tanggal, tim, metadata file, dan isi transaksi.</p>
    </div>
    <a class="btn btn-light" href="<?= url('/reports') ?>"><i class="bi bi-arrow-left"></i>Kembali</a>
</div>

<section class="panel-card mb-4">
    <form class="report-search-grid" method="get" action="<?= url('/reports/search') ?>">
        <label class="search-wide">
            <span>Kata kunci</span>
            <div class="global-search-input">
                <i class="bi bi-search"></i>
                <input class="form-control" type="search" name="q" required value="<?= e($queryText) ?>" placeholder="Cari tim, file, payer, beneficiary, bank, invoice...">
            </div>
        </label>
        <label>
            <span>Bulan</span>
            <input class="form-control" type="month" name="month" value="<?= e($filters['month'] ?? '') ?>">
        </label>
        <label>
            <span>Tanggal mulai</span>
            <input class="form-control" type="date" name="date_from" value="<?= e($filters['date_from'] ?? '') ?>">
        </label>
        <label>
            <span>Tanggal akhir</span>
            <input class="form-control" type="date" name="date_to" value="<?= e($filters['date_to'] ?? '') ?>">
        </label>
        <label>
            <span>Tim</span>
            <select class="form-select" name="team">
                <option value="">Semua tim</option>
                <?php foreach ($teams as $team): ?>
                    <option value="<?= e($team) ?>" <?= selected($filters['team'] ?? '', $team) ?>><?= e($team) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>
            <span>Status kirim</span>
            <select class="form-select" name="delivery_status">
                <option value="">Semua</option>
                <option value="PENDING" <?= selected($filters['delivery_status'] ?? '', 'PENDING') ?>>Pending</option>
                <option value="SENT" <?= selected($filters['delivery_status'] ?? '', 'SENT') ?>>Terkirim</option>
            </select>
        </label>
        <label>
            <span>Hasil</span>
            <select class="form-select" name="result_status">
                <option value="">Semua</option>
                <option value="PENDING" <?= selected($filters['result_status'] ?? '', 'PENDING') ?>>Pending</option>
                <option value="SUCCESS" <?= selected($filters['result_status'] ?? '', 'SUCCESS') ?>>Success</option>
                <option value="FAILED" <?= selected($filters['result_status'] ?? '', 'FAILED') ?>>Failed</option>
            </select>
        </label>
        <div class="filter-actions report-filter-actions">
            <button class="btn btn-primary" type="submit">Cari</button>
            <a class="btn btn-light" href="<?= url('/reports') ?>">Reset</a>
        </div>
    </form>
</section>

<div class="summary-grid report-search-summary mb-4">
    <div class="summary-card blue"><span>Kata kunci</span><strong class="small-value"><?= e($queryText) ?></strong></div>
    <div class="summary-card"><span>Report ditemukan</span><strong><?= number_format(count($batches), 0, ',', '.') ?></strong></div>
    <div class="summary-card green"><span>Baris transaksi ditemukan</span><strong><?= number_format(count($rows), 0, ',', '.') ?></strong></div>
</div>

<section class="panel-card mb-4">
    <div class="panel-head">
        <div>
            <h3>Report atau file yang cocok</h3>
            <p>Maksimal 100 report ditampilkan.</p>
        </div>
    </div>

    <div class="report-search-batch-grid">
        <?php foreach ($batches as $batch): ?>
            <article class="search-batch-card">
                <div class="report-team-top">
                    <span class="team-chip"><?= e(strtoupper((string) ($batch['effective_team'] ?? 'Tanpa Tim'))) ?></span>
                    <span class="batch-chip"><?= e($batch['effective_date'] ?? '') ?></span>
                </div>
                <h4><?= e($batch['source_filename']) ?></h4>
                <p><?= e($batch['name']) ?></p>
                <div class="mini-stat-row">
                    <span><b><?= (int) ($batch['total_rows_live'] ?? 0) ?></b> total</span>
                    <span><b><?= (int) ($batch['sent_rows_live'] ?? 0) ?></b> terkirim</span>
                    <span><b><?= (int) ($batch['pending_rows_live'] ?? 0) ?></b> pending</span>
                </div>
                <div class="card-actions">
                    <a class="btn btn-primary btn-sm" href="<?= url('/reports/' . $batch['id'] . '?q=' . urlencode($queryText)) ?>">Buka Report</a>
                    <a class="btn btn-light btn-sm" href="<?= url('/reports/date/' . $batch['effective_date']) ?>">Lihat Tanggal</a>
                </div>
            </article>
        <?php endforeach; ?>

        <?php if ($batches === []): ?>
            <div class="empty-card compact-empty">
                <i class="bi bi-file-earmark-x"></i>
                <h3>Tidak ada report yang cocok</h3>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="panel-card report-table-card">
    <div class="panel-head">
        <div>
            <h3>Baris transaksi yang cocok</h3>
            <p>Maksimal 200 baris ditampilkan. Klik nama report untuk membuka detail lengkap.</p>
        </div>
    </div>

    <div class="table-responsive report-search-table">
        <table class="table modern-table align-middle">
            <thead>
            <tr>
                <th>Tanggal</th>
                <th>Tim</th>
                <th>Report</th>
                <th>Payer</th>
                <th>Beneficiary</th>
                <th>Bank</th>
                <th>SWIFT</th>
                <th>Invoice</th>
                <th>Currency</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td class="text-nowrap"><?= e(format_date($row['report_date'] ?: $row['transaction_date'])) ?></td>
                    <td><strong><?= e($row['effective_team']) ?></strong></td>
                    <td><?= e($row['batch_name']) ?></td>
                    <td><?= e($row['bill_to']) ?></td>
                    <td><?= e($row['beneficiary_name']) ?></td>
                    <td><?= e($row['beneficiary_bank']) ?></td>
                    <td><span class="code-pill"><?= e($row['swift']) ?></span></td>
                    <td><?= e($row['invoice_no']) ?></td>
                    <td><strong><?= e($row['currency']) ?></strong></td>
                    <td class="text-nowrap"><?= e(number_format((float) $row['amount'], 2, ',', '.')) ?></td>
                    <td><?= status_badge((string) $row['delivery_status']) ?></td>
                    <td><a class="btn btn-light btn-sm" href="<?= url('/reports/' . $row['batch_id'] . '?q=' . urlencode($queryText)) ?>">Buka</a></td>
                </tr>
            <?php endforeach; ?>
            <?php if ($rows === []): ?>
                <tr><td colspan="12" class="empty-state">Tidak ada baris transaksi yang cocok.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
