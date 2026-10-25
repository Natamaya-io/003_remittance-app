<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| REPORT HARIAN - TINGKAT TANGGAL (TABLE & LIST VIEW OVERHAUL)
|--------------------------------------------------------------------------
| Menampilkan daftar tanggal transaksi dalam bulan tertentu menggunakan format
| Tabel Zebra dan List terstruktur dengan kontrol peralihan instan.
|--------------------------------------------------------------------------
*/

$dates = is_array($dates ?? null) ? $dates : [];
$filters = is_array($filters ?? null) ? $filters : [];
$teams = is_array($teams ?? null) ? $teams : [];
$month = (string) ($month ?? date('Y-m'));

$monthNames = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
];

$monthNum = (int) substr($month, 5, 2);
$yearNum = substr($month, 0, 4);
$monthLabel = ($monthNames[$monthNum] ?? $month) . ' ' . $yearNum;
?>

<!-- =========================================================
     PAGE HEADING
     ========================================================= -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-bold bg-blue-50 text-blue-700 tracking-wider uppercase mb-1">
            TINGKAT TANGGAL
        </span>
        <h2 class="text-2xl font-bold text-gray-900">Report <?= e($monthLabel) ?></h2>
        <p class="text-sm text-gray-500 mt-0.5">Daftar hari transaksi aktif dalam periode bulan terpilih.</p>
    </div>

    <div class="flex items-center gap-2">
        <a class="py-2 px-3.5 text-xs font-medium rounded-lg border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 shadow-sm inline-flex items-center gap-1.5" href="<?= e(url('/reports')) ?>">
            <i class="bi bi-arrow-left"></i>
            Daftar Bulan
        </a>
        <?php if (can('report.manage')): ?>
            <a class="btn btn-primary py-2 px-4 text-xs font-medium rounded-lg inline-flex items-center gap-2" href="<?= e(url('/reports/create?month=' . urlencode($month))) ?>">
                <i class="bi bi-cloud-arrow-up"></i>
                Upload Excel
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- =========================================================
     PENCARIAN GLOBAL
     ========================================================= -->
<section class="bg-white border border-gray-200 rounded-2xl p-5 mb-6 shadow-sm">
    <form method="get" action="<?= e(url('/reports/search')) ?>" class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex-1">
            <strong class="text-sm font-bold text-gray-900 block">Pencarian Transaksi Bulan Ini</strong>
            <p class="text-xs text-gray-500 mt-0.5">Cari data transaksi di seluruh tanggal dan tim dalam bulan <?= e($monthLabel) ?>.</p>
        </div>

        <div class="flex items-center gap-2 flex-1 max-w-md">
            <div class="relative flex-1">
                <input class="py-2 px-3 ps-9 block w-full border border-gray-300 rounded-lg text-xs focus:border-blue-500 focus:ring-blue-500 shadow-sm" type="search" name="q" required placeholder="Cari data report..." value="<?= e((string) ($filters['q'] ?? '')) ?>">
                <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none ps-3 text-gray-400">
                    <i class="bi bi-search text-xs"></i>
                </div>
            </div>
            <button class="btn btn-primary py-2 px-4 text-xs font-medium rounded-lg inline-flex items-center gap-1.5 shrink-0" type="submit">
                <i class="bi bi-search"></i>
                Cari
            </button>
        </div>
    </form>
</section>

<!-- =========================================================
     FILTER TANGGAL & TIM
     ========================================================= -->
<div class="bg-white border border-gray-200 rounded-2xl p-4 mb-6 shadow-sm">
    <form class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end" method="get" action="<?= e(url('/reports/month/' . $month)) ?>">
        <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">Tim</label>
            <select class="py-1.5 px-3 block w-full border border-gray-300 rounded-lg text-xs focus:border-blue-500 focus:ring-blue-500" name="team">
                <option value="">Semua tim</option>
                <?php foreach ($teams as $team): ?>
                    <option value="<?= e($team) ?>" <?= selected((string) ($filters['team'] ?? ''), $team) ?>><?= e($team) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">Tanggal mulai</label>
            <input class="py-1.5 px-3 block w-full border border-gray-300 rounded-lg text-xs focus:border-blue-500 focus:ring-blue-500" type="date" name="date_from" value="<?= e((string) ($filters['date_from'] ?? '')) ?>">
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">Tanggal akhir</label>
            <input class="py-1.5 px-3 block w-full border border-gray-300 rounded-lg text-xs focus:border-blue-500 focus:ring-blue-500" type="date" name="date_to" value="<?= e((string) ($filters['date_to'] ?? '')) ?>">
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">Status kirim</label>
            <select class="py-1.5 px-3 block w-full border border-gray-300 rounded-lg text-xs focus:border-blue-500 focus:ring-blue-500" name="delivery_status">
                <option value="">Semua</option>
                <option value="PENDING" <?= selected((string) ($filters['delivery_status'] ?? ''), 'PENDING') ?>>Pending</option>
                <option value="SENT" <?= selected((string) ($filters['delivery_status'] ?? ''), 'SENT') ?>>Terkirim</option>
            </select>
        </div>

        <div class="flex items-center gap-2">
            <button class="btn btn-primary py-1.5 px-3.5 text-xs font-medium rounded-lg inline-flex items-center gap-1.5 flex-1 justify-center" type="submit">
                <i class="bi bi-funnel"></i>
                Terapkan
            </button>
            <a class="py-1.5 px-3 text-xs font-medium rounded-lg border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 shadow-sm" href="<?= e(url('/reports/month/' . $month)) ?>">
                Reset
            </a>
        </div>
    </form>
</div>

<!-- =========================================================
     HIERARKI TINGKAT 2 (STEPPER PILLS)
     ========================================================= -->
<div class="flex items-center gap-2 mb-6 text-xs font-medium text-gray-500">
    <a href="<?= e(url('/reports')) ?>" class="inline-flex items-center gap-1.5 py-1 px-3 rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 transition">
        <b class="w-4 h-4 rounded-full bg-gray-300 text-gray-800 text-[10px] inline-flex items-center justify-center">1</b>
        Bulan
    </a>
    <i class="bi bi-chevron-right text-gray-400 text-[10px]"></i>
    <span class="inline-flex items-center gap-1.5 py-1 px-3 rounded-lg bg-blue-600 text-white font-bold shadow-sm">
        <b class="w-4 h-4 rounded-full bg-white text-blue-600 text-[10px] inline-flex items-center justify-center">2</b>
        Tanggal
    </span>
    <i class="bi bi-chevron-right text-gray-400 text-[10px]"></i>
    <span class="inline-flex items-center gap-1.5 py-1 px-3 rounded-lg bg-gray-100 text-gray-500">
        <b class="w-4 h-4 rounded-full bg-gray-300 text-gray-700 text-[10px] inline-flex items-center justify-center">3</b>
        Tim dan Report
    </span>
</div>

<!-- =========================================================
     TOOLBAR MODE TAMPILAN: TABEL VS LIST (ALPINE + PRELINE UI)
     ========================================================= -->
<div x-data="{ viewMode: localStorage.getItem('fable_report_view_mode') || 'table' }"
     x-init="$watch('viewMode', val => localStorage.setItem('fable_report_view_mode', val))">

<div class="report-view-toolbar flex flex-wrap items-center justify-between gap-3">
    <div class="report-view-summary flex items-center gap-2">
        <span>Tanggal Transaksi Bulan <?= e($monthLabel) ?></span>
        <span class="count-badge"><?= count($dates) ?> Hari Transaksi</span>
    </div>

    <div class="flex items-center gap-2">
        <!-- Preline UI Dropdown -->
        <div class="hs-dropdown relative inline-flex">
            <button id="hs-dropdown-month-options" type="button" class="hs-dropdown-toggle inline-flex items-center gap-x-2 py-2 px-3 text-xs font-semibold rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 focus:outline-none focus:bg-gray-50 dark:bg-neutral-800 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-700 dark:focus:bg-neutral-700" aria-haspopup="menu" aria-expanded="false" aria-label="Menu Opsi">
                <i class="bi bi-gear"></i>
                Opsi Cepat
                <i class="bi bi-chevron-down text-[10px]"></i>
            </button>

            <div class="hs-dropdown-menu transition-[opacity,margin] duration hs-dropdown-open:opacity-100 opacity-0 hidden min-w-48 bg-white shadow-lg rounded-xl p-1.5 mt-2 border border-gray-100 z-50 dark:bg-neutral-900 dark:border-neutral-700" role="menu" aria-orientation="vertical" aria-labelledby="hs-dropdown-month-options">
                <a class="flex items-center gap-x-3 py-2 px-3 rounded-lg text-xs text-gray-700 hover:bg-gray-100 dark:text-neutral-300 dark:hover:bg-neutral-800 focus:outline-none" href="<?= e(url('/reports')) ?>">
                    <i class="bi bi-arrow-left text-blue-600"></i>
                    Kembali ke Daftar Bulan
                </a>
                <hr class="my-1 border-gray-100 dark:border-neutral-700">
                <button type="button" @click="viewMode = 'table'" class="w-full flex items-center gap-x-3 py-2 px-3 rounded-lg text-xs text-gray-700 hover:bg-gray-100 dark:text-neutral-300 dark:hover:bg-neutral-800 focus:outline-none">
                    <i class="bi bi-table text-indigo-600"></i>
                    Tampilan Tabel
                </button>
                <button type="button" @click="viewMode = 'list'" class="w-full flex items-center gap-x-3 py-2 px-3 rounded-lg text-xs text-gray-700 hover:bg-gray-100 dark:text-neutral-300 dark:hover:bg-neutral-800 focus:outline-none">
                    <i class="bi bi-list-ul text-amber-600"></i>
                    Tampilan List
                </button>
            </div>
        </div>

        <div class="view-mode-switcher" id="reportViewSwitcherMonth">
            <button type="button" class="view-mode-btn" :class="{ 'active': viewMode === 'table' }" @click="viewMode = 'table'" data-view="table">
                <i class="bi bi-table"></i>
                Tabel
            </button>
            <button type="button" class="view-mode-btn" :class="{ 'active': viewMode === 'list' }" @click="viewMode = 'list'" data-view="list">
                <i class="bi bi-list-ul"></i>
                List
            </button>
        </div>
    </div>
</div>

<!-- =========================================================
     WADAH TAMPILAN: TABEL & LIST MUTLAK
     ========================================================= -->
<div class="report-view-container mode-table" :class="viewMode === 'table' ? 'mode-table' : 'mode-list'" id="reportViewContainerMonth">

    <!-- 1. TAMPILAN TABEL ZEBRA (HIGH DENSITY) -->
    <div class="report-table-view">
        <div class="report-table-responsive">
            <table class="report-zebra-table">
                <thead>
                    <tr>
                        <th>Tanggal Transaksi</th>
                        <th>Berkas</th>
                        <th>Tim</th>
                        <th class="text-end">Total Baris</th>
                        <th style="min-width: 170px;">Progress Kirim</th>
                        <th class="text-center">Dilewati</th>
                        <th>Upload Terakhir</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dates as $dateCard): ?>
                        <?php
                        $dateValue = (string) $dateCard['report_day'];
                        $dateObject = new \DateTime($dateValue);
                        $dateLabel = $dateObject->format('j') . ' ' . ($monthNames[(int) $dateObject->format('n')] ?? '') . ' ' . $dateObject->format('Y');

                        $total = (int) ($dateCard['total_rows'] ?? 0);
                        $sent = (int) ($dateCard['sent_rows'] ?? 0);
                        $pending = (int) ($dateCard['pending_rows'] ?? 0);
                        $skipped = (int) ($dateCard['skipped_rows'] ?? 0);
                        $progress = $total > 0 ? min(100, (int) round(($sent / $total) * 100)) : 0;

                        $query = array_filter([
                            'team' => $filters['team'] ?? '',
                            'delivery_status' => $filters['delivery_status'] ?? '',
                            'result_status' => $filters['result_status'] ?? '',
                        ], static fn($v) => $v !== '');

                        $dateUrl = '/reports/date/' . $dateValue . ($query ? '?' . http_build_query($query) : '');
                        $lastUpload = $dateCard['last_upload'] ?? null;
                        ?>
                        <tr>
                            <td>
                                <div class="report-month-title">
                                    <div class="month-icon">
                                        <i class="bi bi-calendar-event"></i>
                                    </div>
                                    <div>
                                        <strong><?= e($dateLabel) ?></strong>
                                        <small><?= e($dateValue) ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="chip-meta">
                                    <i class="bi bi-files"></i>
                                    <?= (int) $dateCard['file_count'] ?> Berkas
                                </span>
                            </td>
                            <td>
                                <span class="chip-meta">
                                    <i class="bi bi-people"></i>
                                    <?= (int) $dateCard['team_count'] ?> Tim
                                </span>
                            </td>
                            <td class="text-end">
                                <strong style="font-size: 14px; color: #0f172a;">
                                    <?= number_format($total, 0, ',', '.') ?>
                                </strong>
                            </td>
                            <td>
                                <div class="table-progress-wrap">
                                    <div class="table-progress-bar">
                                        <div class="table-progress-fill" style="width: <?= $progress ?>%;"></div>
                                    </div>
                                    <div class="table-progress-labels">
                                        <span class="sent-txt"><?= $progress ?>% Kirim</span>
                                        <span class="pending-txt"><?= number_format($pending, 0, ',', '.') ?> Pnd</span>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <?php if ($skipped > 0): ?>
                                    <span class="status-pill warning"><?= number_format($skipped, 0, ',', '.') ?></span>
                                <?php else: ?>
                                    <span style="color: #94a3b8;">0</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span style="font-size: 12px; color: #64748b;">
                                    <?= e(format_datetime($lastUpload)) ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <a class="btn btn-primary btn-sm" href="<?= e(url($dateUrl)) ?>">
                                    Buka Tanggal
                                    <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if ($dates === []): ?>
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 48px 24px;">
                                <div style="display: flex; flex-direction: column; align-items: center; gap: 8px;">
                                    <i class="bi bi-calendar-x" style="font-size: 32px; color: #94a3b8;"></i>
                                    <strong style="color: #475569; font-size: 15px;">Belum ada report pada bulan ini</strong>
                                    <span style="color: #94a3b8; font-size: 13px;">Upload berkas Excel untuk membentuk daftar tanggal transaksi otomatis.</span>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- 2. TAMPILAN LIST VIEW -->
    <div class="report-list-view">
        <?php foreach ($dates as $dateCard): ?>
            <?php
            $dateValue = (string) $dateCard['report_day'];
            $dateObject = new \DateTime($dateValue);
            $dateLabel = $dateObject->format('j') . ' ' . ($monthNames[(int) $dateObject->format('n')] ?? '') . ' ' . $dateObject->format('Y');

            $total = (int) ($dateCard['total_rows'] ?? 0);
            $sent = (int) ($dateCard['sent_rows'] ?? 0);
            $pending = (int) ($dateCard['pending_rows'] ?? 0);
            $skipped = (int) ($dateCard['skipped_rows'] ?? 0);
            $progress = $total > 0 ? min(100, (int) round(($sent / $total) * 100)) : 0;

            $query = array_filter([
                'team' => $filters['team'] ?? '',
                'delivery_status' => $filters['delivery_status'] ?? '',
                'result_status' => $filters['result_status'] ?? '',
            ], static fn($v) => $v !== '');

            $dateUrl = '/reports/date/' . $dateValue . ($query ? '?' . http_build_query($query) : '');
            $lastUpload = $dateCard['last_upload'] ?? null;
            ?>
            <article class="report-list-item">
                <div class="report-list-header">
                    <div class="report-list-title-box">
                        <div class="month-icon" style="display:inline-flex;align-items:center;justify-content:center;width:40px;height:40px;border-radius:10px;background:#edf5fd;color:#2563eb;font-size:18px;">
                            <i class="bi bi-calendar-date"></i>
                        </div>
                        <div>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <strong style="font-size: 16px; color: #0f172a;"><?= e($dateLabel) ?></strong>
                                <span class="status-pill <?= $pending > 0 ? 'warning' : 'success' ?>">
                                    <?= $pending > 0 ? $pending . ' pending' : 'Selesai' ?>
                                </span>
                            </div>
                            <span style="font-size: 12px; color: #64748b;">
                                <?= (int) $dateCard['file_count'] ?> berkas · <?= (int) $dateCard['team_count'] ?> tim
                            </span>
                        </div>
                    </div>

                    <a class="btn btn-primary btn-sm" href="<?= e(url($dateUrl)) ?>">
                        Buka Tanggal
                        <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>

                <div class="report-list-body">
                    <div class="report-list-stat-item">
                        <span class="stat-label">Total Transaksi</span>
                        <span class="stat-val"><?= number_format($total, 0, ',', '.') ?> baris</span>
                    </div>

                    <div class="report-list-stat-item">
                        <span class="stat-label">Terkirim</span>
                        <span class="stat-val" style="color: #059669;"><?= number_format($sent, 0, ',', '.') ?> baris</span>
                    </div>

                    <div class="report-list-stat-item">
                        <span class="stat-label">Pending</span>
                        <span class="stat-val" style="color: #d97706;"><?= number_format($pending, 0, ',', '.') ?> baris</span>
                    </div>

                    <div class="report-list-stat-item">
                        <span class="stat-label">Dilewati</span>
                        <span class="stat-val"><?= number_format($skipped, 0, ',', '.') ?> baris</span>
                    </div>
                </div>

                <div class="report-list-footer">
                    <div style="flex: 1; max-width: 320px;">
                        <div class="table-progress-bar">
                            <div class="table-progress-fill" style="width: <?= $progress ?>%;"></div>
                        </div>
                        <div class="table-progress-labels">
                            <span class="sent-txt"><?= $progress ?>% Terkirim</span>
                            <span><?= number_format($sent, 0, ',', '.') ?> dari <?= number_format($total, 0, ',', '.') ?></span>
                        </div>
                    </div>

                    <div>
                        Upload terakhir: <strong><?= e(format_datetime($lastUpload)) ?></strong>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>

        <?php if ($dates === []): ?>
            <div class="empty-card" style="padding: 48px 24px; text-align: center; background: #ffffff; border: 1px solid #dce6f0; border-radius: 16px;">
                <i class="bi bi-calendar-x" style="font-size: 32px; color: #94a3b8; display: block; margin-bottom: 12px;"></i>
                <h3 style="font-size: 16px; color: #1e293b; margin-bottom: 6px;">Belum ada report pada bulan ini</h3>
                <p style="color: #64748b; font-size: 13px; margin-bottom: 16px;">Upload berkas Excel untuk membentuk daftar tanggal transaksi otomatis.</p>
                <?php if (can('report.manage')): ?>
                    <a class="btn btn-primary" href="<?= e(url('/reports/create?month=' . urlencode($month))) ?>">
                        <i class="bi bi-cloud-arrow-up me-1"></i>
                        Upload Excel
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

</div> <!-- end reportViewContainerMonth -->
</div> <!-- end Alpine x-data -->

<!-- =========================================================
     JAVASCRIPT: PERALIHAN MODE TABEL / LIST
     ========================================================= -->
<script>
(() => {
    const container = document.getElementById('reportViewContainerMonth');
    const switcher = document.getElementById('reportViewSwitcherMonth');
    if (!container || !switcher) return;

    const savedMode = localStorage.getItem('fable_report_view_mode') || 'table';
    setMode(savedMode);

    switcher.addEventListener('click', (e) => {
        const btn = e.target.closest('.view-mode-btn');
        if (!btn) return;
        const mode = btn.getAttribute('data-view');
        if (mode) {
            setMode(mode);
            localStorage.setItem('fable_report_view_mode', mode);
        }
    });

    function setMode(mode) {
        if (mode === 'list') {
            container.classList.remove('mode-table');
            container.classList.add('mode-list');
        } else {
            container.classList.remove('mode-list');
            container.classList.add('mode-table');
        }

        const buttons = switcher.querySelectorAll('.view-mode-btn');
        buttons.forEach(b => {
            if (b.getAttribute('data-view') === mode) {
                b.classList.add('active');
            } else {
                b.classList.remove('active');
            }
        });
    }
})();
</script>
