<?php

declare(strict_types=1);

$batches = is_array($batches ?? null) ? $batches : [];
$filters = is_array($filters ?? null) ? $filters : [];
$teams = is_array($teams ?? null) ? $teams : [];
$date = (string) ($date ?? date('Y-m-d'));
$dateObject = new \DateTime($date);
$monthNames = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
];
$dateLabel = $dateObject->format('j') . ' ' . ($monthNames[(int) $dateObject->format('n')] ?? '') . ' ' . $dateObject->format('Y');
$monthKey = $dateObject->format('Y-m');
$returnPath = '/reports/date/' . $date;
?>

<div class="page-heading">
    <div>
        <span class="eyebrow dark">TINGKAT TIM DAN REPORT</span>
        <h2><?= e($dateLabel) ?></h2>
        <p>Setiap kartu menampilkan satu file report dari satu tim. Buka report untuk melihat seluruh baris transaksi.</p>
    </div>
    <div class="page-heading-actions">
        <a class="btn btn-light" href="<?= url('/reports/month/' . $monthKey) ?>"><i class="bi bi-arrow-left"></i>Daftar Tanggal</a>
        <a class="btn btn-light" href="<?= url('/reports/date/' . $date . '/export') ?>"><i class="bi bi-file-earmark-excel"></i>Export Semua</a>
        <?php if (can('report.manage')): ?>
            <a class="btn btn-primary" href="<?= url('/reports/create?' . http_build_query(['month' => $monthKey, 'date' => $date])) ?>"><i class="bi bi-plus-lg"></i>Tambah Excel pada Tanggal Ini</a>
        <?php endif; ?>
    </div>
</div>

<section class="panel-card report-global-search mb-4">
    <form method="get" action="<?= url('/reports/search') ?>">
        <div class="global-search-copy">
            <strong>Pencarian global</strong>
            <span>Mencari seluruh bulan, tanggal, tim, file, payer, beneficiary, bank, dan transaksi.</span>
        </div>
        <div class="global-search-input">
            <i class="bi bi-search"></i>
            <input class="form-control" type="search" name="q" required placeholder="Cari seluruh data report..." value="<?= e($filters['q'] ?? '') ?>">
        </div>
        <button class="btn btn-primary" type="submit">Cari</button>
    </form>
</section>

<div class="panel-card mb-4">
    <form class="report-inline-filter" method="get" action="<?= url('/reports/date/' . $date) ?>">
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
        <div class="filter-actions">
            <button class="btn btn-primary" type="submit">Terapkan</button>
            <a class="btn btn-light" href="<?= url('/reports/date/' . $date) ?>">Reset</a>
        </div>
    </form>
</div>

<div class="report-hierarchy-note mb-4">
    <a href="<?= url('/reports') ?>"><b>1</b>Bulan</a>
    <i class="bi bi-chevron-right"></i>
    <a href="<?= url('/reports/month/' . $monthKey) ?>"><b>2</b>Tanggal</a>
    <i class="bi bi-chevron-right"></i>
    <span class="active"><b>3</b>Tim dan Report</span>
</div>

<div class="report-team-grid">
    <?php foreach ($batches as $batch): ?>
        <?php
        $total = (int) ($batch['total_rows_live'] ?? $batch['total_rows'] ?? 0);
        $sent = (int) ($batch['sent_rows_live'] ?? $batch['sent_rows'] ?? 0);
        $pending = (int) ($batch['pending_rows_live'] ?? $batch['pending_rows'] ?? 0);
        $success = (int) ($batch['success_rows_live'] ?? 0);
        $failed = (int) ($batch['failed_rows_live'] ?? 0);
        $progress = $total > 0 ? min(100, (int) round(($sent / $total) * 100)) : 0;
        ?>
        <article class="report-team-card">
            <div class="report-team-top">
                <span class="team-chip">TEAM: <?= e(strtoupper((string) ($batch['effective_team'] ?? 'Tanpa Tim'))) ?></span>
                <span class="batch-chip">Batch #<?= (int) $batch['id'] ?></span>
            </div>

            <h3 title="<?= e($batch['source_filename']) ?>"><?= e($batch['source_filename']) ?></h3>
            <p><?= e($batch['name']) ?></p>

            <div class="report-progress"><span style="width:<?= $progress ?>%"></span></div>

            <div class="report-card-stats three">
                <div><strong><?= number_format($total, 0, ',', '.') ?></strong><small>Total</small></div>
                <div><strong><?= number_format($sent, 0, ',', '.') ?></strong><small>Terkirim</small></div>
                <div><strong><?= number_format($pending, 0, ',', '.') ?></strong><small>Pending</small></div>
            </div>

            <div class="report-result-strip">
                <span class="success"><i class="bi bi-check-circle"></i><?= $success ?> success</span>
                <span class="danger"><i class="bi bi-x-circle"></i><?= $failed ?> failed</span>
                <span><i class="bi bi-skip-forward"></i><?= (int) ($batch['skipped_rows'] ?? 0) ?> dilewati</span>
            </div>

            <div class="report-team-actions">
                <a class="btn btn-primary btn-sm" href="<?= url('/reports/' . $batch['id']) ?>"><i class="bi bi-table"></i>Buka Report</a>
                <a class="btn btn-light btn-sm" href="<?= url('/reports/' . $batch['id'] . '/export') ?>"><i class="bi bi-file-earmark-excel"></i>Excel</a>
                <?php if (can('report.manage')): ?>
                    <a class="btn btn-light btn-sm" href="<?= url('/reports/' . $batch['id'] . '/edit') ?>"><i class="bi bi-pencil"></i>Edit</a>
                    <form method="post" action="<?= url('/reports/' . $batch['id']) ?>" data-confirm="Hapus report ini beserta seluruh barisnya?">
                        <?= csrf_field() ?>
                        <?= method_field('DELETE') ?>
                        <input type="hidden" name="return_to" value="<?= e($returnPath) ?>">
                        <button class="btn btn-outline-danger btn-sm" type="submit" title="Hapus report"><i class="bi bi-trash"></i></button>
                    </form>
                <?php endif; ?>
            </div>
        </article>
    <?php endforeach; ?>

    <?php if ($batches === []): ?>
        <div class="empty-card">
            <i class="bi bi-file-earmark-x"></i>
            <h3>Tidak ada report tim</h3>
            <p>Belum ada file pada tanggal ini atau data tidak cocok dengan filter yang dipilih.</p>
            <?php if (can('report.manage')): ?><a class="btn btn-primary" href="<?= url('/reports/create?' . http_build_query(['month' => $monthKey, 'date' => $date])) ?>">Tambah Excel</a><?php endif; ?>
        </div>
    <?php endif; ?>
</div>
