<?php

declare(strict_types=1);

$dates = is_array($dates ?? null) ? $dates : [];
$filters = is_array($filters ?? null) ? $filters : [];
$teams = is_array($teams ?? null) ? $teams : [];
$month = (string) ($month ?? date('Y-m'));
$monthNames = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
];
$monthLabel = ($monthNames[(int) substr($month, 5, 2)] ?? $month) . ' ' . substr($month, 0, 4);
?>

<div class="page-heading">
    <div>
        <span class="eyebrow dark">TINGKAT TANGGAL</span>
        <h2>Report <?= e($monthLabel) ?></h2>
        <p>Satu kartu mewakili satu tanggal. Klik tanggal untuk menampilkan report dari semua tim pada tanggal tersebut.</p>
    </div>
    <div class="page-heading-actions">
        <a class="btn btn-light" href="<?= url('/reports') ?>"><i class="bi bi-arrow-left"></i>Daftar Bulan</a>
        <?php if (can('report.manage')): ?>
            <a class="btn btn-primary" href="<?= url('/reports/create?month=' . urlencode($month)) ?>"><i class="bi bi-plus-lg"></i>Upload Excel</a>
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
    <form class="report-inline-filter" method="get" action="<?= url('/reports/month/' . $month) ?>">
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
            <span>Tanggal mulai</span>
            <input class="form-control" type="date" name="date_from" value="<?= e($filters['date_from'] ?? '') ?>">
        </label>
        <label>
            <span>Tanggal akhir</span>
            <input class="form-control" type="date" name="date_to" value="<?= e($filters['date_to'] ?? '') ?>">
        </label>
        <label>
            <span>Status kirim</span>
            <select class="form-select" name="delivery_status">
                <option value="">Semua</option>
                <option value="PENDING" <?= selected($filters['delivery_status'] ?? '', 'PENDING') ?>>Pending</option>
                <option value="SENT" <?= selected($filters['delivery_status'] ?? '', 'SENT') ?>>Terkirim</option>
            </select>
        </label>
        <div class="filter-actions">
            <button class="btn btn-primary" type="submit">Terapkan</button>
            <a class="btn btn-light" href="<?= url('/reports/month/' . $month) ?>">Reset</a>
        </div>
    </form>
</div>

<div class="report-hierarchy-note mb-4">
    <a href="<?= url('/reports') ?>"><b>1</b>Bulan</a>
    <i class="bi bi-chevron-right"></i>
    <span class="active"><b>2</b>Tanggal</span>
    <i class="bi bi-chevron-right"></i>
    <span><b>3</b>Tim dan Report</span>
</div>

<div class="report-date-grid">
    <?php foreach ($dates as $dateCard): ?>
        <?php
        $dateValue = (string) $dateCard['report_day'];
        $dateObject = new \DateTime($dateValue);
        $dateLabel = $dateObject->format('j') . ' ' . ($monthNames[(int) $dateObject->format('n')] ?? '') . ' ' . $dateObject->format('Y');
        $total = (int) ($dateCard['total_rows'] ?? 0);
        $sent = (int) ($dateCard['sent_rows'] ?? 0);
        $pending = (int) ($dateCard['pending_rows'] ?? 0);
        $progress = $total > 0 ? min(100, (int) round(($sent / $total) * 100)) : 0;
        $query = array_filter([
            'team' => $filters['team'] ?? '',
            'delivery_status' => $filters['delivery_status'] ?? '',
            'result_status' => $filters['result_status'] ?? '',
        ], static fn($value) => $value !== '');
        $dateUrl = '/reports/date/' . $dateValue . ($query ? '?' . http_build_query($query) : '');
        ?>
        <article class="report-date-card">
            <div class="report-date-head">
                <div class="report-card-icon"><i class="bi bi-calendar-date"></i></div>
                <span class="status-pill <?= $pending > 0 ? 'warning' : 'success' ?>"><?= $pending > 0 ? $pending . ' pending' : 'Selesai' ?></span>
            </div>
            <h3><?= e($dateLabel) ?></h3>
            <p><?= (int) $dateCard['file_count'] ?> file · <?= (int) $dateCard['team_count'] ?> tim</p>

            <div class="report-progress"><span style="width:<?= $progress ?>%"></span></div>

            <div class="report-card-stats four">
                <div><strong><?= number_format($total, 0, ',', '.') ?></strong><small>Total baris</small></div>
                <div><strong><?= number_format($sent, 0, ',', '.') ?></strong><small>Terkirim</small></div>
                <div><strong><?= number_format($pending, 0, ',', '.') ?></strong><small>Pending</small></div>
                <div><strong><?= number_format((int) ($dateCard['skipped_rows'] ?? 0), 0, ',', '.') ?></strong><small>Dilewati</small></div>
            </div>

            <div class="report-card-footer">
                <span>Upload terakhir <?= e(format_datetime($dateCard['last_upload'] ?? null)) ?></span>
                <a class="btn btn-primary btn-sm" href="<?= url($dateUrl) ?>">Lihat Report Tim <i class="bi bi-arrow-right"></i></a>
            </div>
        </article>
    <?php endforeach; ?>

    <?php if ($dates === []): ?>
        <div class="empty-card">
            <i class="bi bi-calendar-x"></i>
            <h3>Belum ada report pada bulan ini</h3>
            <p>Upload file Excel dan tentukan tanggal report agar kartu tanggal terbentuk otomatis.</p>
            <?php if (can('report.manage')): ?><a class="btn btn-primary" href="<?= url('/reports/create?month=' . urlencode($month)) ?>">Upload Excel</a><?php endif; ?>
        </div>
    <?php endif; ?>
</div>
