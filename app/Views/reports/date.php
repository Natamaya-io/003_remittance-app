<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| REPORT HARIAN - TINGKAT TIM DAN REPORT (TABLE & LIST VIEW OVERHAUL)
|--------------------------------------------------------------------------
| Menampilkan daftar berkas batch report pada tanggal tertentu dalam format
| Tabel Zebra dan List terstruktur dengan kontrol peralihan instan.
|--------------------------------------------------------------------------
*/

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

<!-- =========================================================
     PAGE HEADING
     ========================================================= -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900"><?= e($dateLabel) ?></h2>
    </div>

    <div class="flex flex-wrap items-center gap-2">
        <a class="py-2 px-3 text-xs font-medium rounded-lg border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 shadow-sm inline-flex items-center gap-1.5" href="<?= e(url('/reports/month/' . $monthKey)) ?>">
            <i class="bi bi-arrow-left"></i>
            Daftar Tanggal
        </a>
        <a class="py-2 px-3 text-xs font-medium rounded-lg border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 shadow-sm inline-flex items-center gap-1.5" href="<?= e(url('/reports/date/' . $date . '/export')) ?>">
            <i class="bi bi-file-earmark-excel"></i>
            Export Semua
        </a>
        <?php if (can('report.manage')): ?>
            <a class="btn btn-primary py-2 px-3.5 text-xs font-medium rounded-lg inline-flex items-center gap-1.5" href="<?= e(url('/reports/create?' . http_build_query(['month' => $monthKey, 'date' => $date]))) ?>">
                <i class="bi bi-cloud-arrow-up"></i>
                Tambah Excel
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
            <strong class="text-sm font-bold text-gray-900 block">Pencarian Transaksi Tanggal Ini</strong>
            <p class="text-xs text-gray-500 mt-0.5">Mencari seluruh batch tim, payer, beneficiary, bank, dan nomor transaksi pada tanggal <?= e($dateLabel) ?>.</p>
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
     FILTER STATUS & TIM
     ========================================================= -->
<div class="bg-white border border-gray-200 rounded-2xl p-4 mb-6 shadow-sm">
    <form class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 items-end" method="get" action="<?= e(url('/reports/date/' . $date)) ?>">
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
            <label class="block text-xs font-semibold text-gray-700 mb-1">Status kirim</label>
            <select class="py-1.5 px-3 block w-full border border-gray-300 rounded-lg text-xs focus:border-blue-500 focus:ring-blue-500" name="delivery_status">
                <option value="">Semua status</option>
                <option value="PENDING" <?= selected((string) ($filters['delivery_status'] ?? ''), 'PENDING') ?>>Pending</option>
                <option value="SENT" <?= selected((string) ($filters['delivery_status'] ?? ''), 'SENT') ?>>Terkirim</option>
            </select>
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">Hasil</label>
            <select class="py-1.5 px-3 block w-full border border-gray-300 rounded-lg text-xs focus:border-blue-500 focus:ring-blue-500" name="result_status">
                <option value="">Semua hasil</option>
                <option value="PENDING" <?= selected((string) ($filters['result_status'] ?? ''), 'PENDING') ?>>Pending</option>
                <option value="SUCCESS" <?= selected((string) ($filters['result_status'] ?? ''), 'SUCCESS') ?>>Success</option>
                <option value="FAILED" <?= selected((string) ($filters['result_status'] ?? ''), 'FAILED') ?>>Failed</option>
            </select>
        </div>

        <div class="flex items-center gap-2">
            <button class="btn btn-primary py-1.5 px-3.5 text-xs font-medium rounded-lg inline-flex items-center gap-1.5 flex-1 justify-center" type="submit">
                <i class="bi bi-funnel"></i>
                Terapkan
            </button>
            <a class="py-1.5 px-3 text-xs font-medium rounded-lg border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 shadow-sm" href="<?= e(url('/reports/date/' . $date)) ?>">
                Reset
            </a>
        </div>
    </form>
</div>

<!-- =========================================================
     HIERARKI TINGKAT 3 (STEPPER PILLS)
     ========================================================= -->
<div class="flex items-center gap-2 mb-6 text-xs font-medium text-gray-500">
    <a href="<?= e(url('/reports')) ?>" class="inline-flex items-center gap-1.5 py-1 px-3 rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 transition">
        <b class="w-4 h-4 rounded-full bg-gray-300 text-gray-800 text-[10px] inline-flex items-center justify-center">1</b>
        Bulan
    </a>
    <i class="bi bi-chevron-right text-gray-400 text-[10px]"></i>
    <a href="<?= e(url('/reports/month/' . $monthKey)) ?>" class="inline-flex items-center gap-1.5 py-1 px-3 rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 transition">
        <b class="w-4 h-4 rounded-full bg-gray-300 text-gray-800 text-[10px] inline-flex items-center justify-center">2</b>
        Tanggal
    </a>
    <i class="bi bi-chevron-right text-gray-400 text-[10px]"></i>
    <span class="inline-flex items-center gap-1.5 py-1 px-3 rounded-lg bg-blue-600 text-white font-bold shadow-sm">
        <b class="w-4 h-4 rounded-full bg-white text-blue-600 text-[10px] inline-flex items-center justify-center">3</b>
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
        <span>Berkas Report Tanggal <?= e($dateLabel) ?></span>
        <span class="count-badge"><?= count($batches) ?> Berkas Batch</span>
    </div>

    <div class="flex items-center gap-2">
        <!-- Preline UI Dropdown -->
        <div class="hs-dropdown relative inline-flex">
            <button id="hs-dropdown-date-options" type="button" class="hs-dropdown-toggle inline-flex items-center gap-x-2 py-2 px-3 text-xs font-semibold rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 focus:outline-none focus:bg-gray-50 dark:bg-neutral-800 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-700 dark:focus:bg-neutral-700" aria-haspopup="menu" aria-expanded="false" aria-label="Menu Opsi">
                <i class="bi bi-gear"></i>
                Opsi Cepat
                <i class="bi bi-chevron-down text-[10px]"></i>
            </button>

            <div class="hs-dropdown-menu transition-[opacity,margin] duration hs-dropdown-open:opacity-100 opacity-0 hidden min-w-48 bg-white shadow-lg rounded-xl p-1.5 mt-2 border border-gray-100 z-50 dark:bg-neutral-900 dark:border-neutral-700" role="menu" aria-orientation="vertical" aria-labelledby="hs-dropdown-date-options">
                <a class="flex items-center gap-x-3 py-2 px-3 rounded-lg text-xs text-gray-700 hover:bg-gray-100 dark:text-neutral-300 dark:hover:bg-neutral-800 focus:outline-none" href="<?= e(url('/reports/date/' . rawurlencode($date) . '/export')) ?>">
                    <i class="bi bi-file-earmark-excel text-emerald-600"></i>
                    Export Seluruh Data Hari Ini (.xlsx)
                </a>
                <a class="flex items-center gap-x-3 py-2 px-3 rounded-lg text-xs text-gray-700 hover:bg-gray-100 dark:text-neutral-300 dark:hover:bg-neutral-800 focus:outline-none" href="<?= e(url('/reports/month/' . $monthKey)) ?>">
                    <i class="bi bi-arrow-left text-blue-600"></i>
                    Kembali ke Daftar Tanggal
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

        <div class="view-mode-switcher" id="reportViewSwitcherDate">
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
<div class="report-view-container mode-table" :class="viewMode === 'table' ? 'mode-table' : 'mode-list'" id="reportViewContainerDate">

    <!-- 1. TAMPILAN TABEL ZEBRA (HIGH DENSITY) -->
    <div class="report-table-view">
        <div class="report-table-responsive">
            <table class="report-zebra-table">
                <thead>
                    <tr>
                        <th>Batch & Tim</th>
                        <th>Berkas & Nama Report</th>
                        <th class="text-end">Total Baris</th>
                        <th style="min-width: 170px;">Status Kirim</th>
                        <th>Hasil Eksekusi</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($batches as $batch): ?>
                        <?php
                        $total = (int) ($batch['total_rows_live'] ?? $batch['total_rows'] ?? 0);
                        $sent = (int) ($batch['sent_rows_live'] ?? $batch['sent_rows'] ?? 0);
                        $pending = (int) ($batch['pending_rows_live'] ?? $batch['pending_rows'] ?? 0);
                        $success = (int) ($batch['success_rows_live'] ?? 0);
                        $failed = (int) ($batch['failed_rows_live'] ?? 0);
                        $skipped = (int) ($batch['skipped_rows'] ?? 0);
                        $progress = $total > 0 ? min(100, (int) round(($sent / $total) * 100)) : 0;
                        ?>
                        <tr>
                            <td>
                                <div>
                                    <span class="status-pill secondary">Batch #<?= (int) $batch['id'] ?></span>
                                    <strong style="display: block; margin-top: 4px; color: #1e293b; font-size: 13px;">
                                        TEAM: <?= e(strtoupper((string) ($batch['effective_team'] ?? 'Tanpa Tim'))) ?>
                                    </strong>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <strong style="color: #0f172a; font-size: 14px;" title="<?= e($batch['source_filename']) ?>">
                                        <i class="bi bi-file-earmark-excel text-success me-1"></i>
                                        <?= e($batch['source_filename']) ?>
                                    </strong>
                                    <small style="display: block; color: #64748b; margin-top: 2px;">
                                        <?= e($batch['name']) ?>
                                    </small>
                                </div>
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
                                        <span class="sent-txt"><?= number_format($sent, 0, ',', '.') ?> Kirim</span>
                                        <span class="pending-txt"><?= number_format($pending, 0, ',', '.') ?> Pnd</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                                    <span class="status-pill success" title="Berhasil">
                                        <i class="bi bi-check me-1"></i><?= $success ?>
                                    </span>
                                    <?php if ($failed > 0): ?>
                                        <span class="status-pill danger" title="Gagal / Retur">
                                            <i class="bi bi-x me-1"></i><?= $failed ?>
                                        </span>
                                    <?php endif; ?>
                                    <?php if ($skipped > 0): ?>
                                        <span class="status-pill secondary" title="Dilewati">
                                            <?= $skipped ?> skip
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="text-end">
                                <div style="display: inline-flex; align-items: center; gap: 4px;">
                                    <a class="btn btn-primary btn-sm" href="<?= e(url('/reports/' . $batch['id'])) ?>">
                                        <i class="bi bi-table me-1"></i> Buka
                                    </a>
                                    <a class="btn btn-light btn-sm" href="<?= e(url('/reports/' . $batch['id'] . '/export')) ?>" title="Export Excel">
                                        <i class="bi bi-file-earmark-excel"></i>
                                    </a>
                                    <?php if (can('report.manage')): ?>
                                        <a class="btn btn-light btn-sm" href="<?= e(url('/reports/' . $batch['id'] . '/edit')) ?>" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form class="d-inline" method="post" action="<?= e(url('/reports/' . $batch['id'])) ?>" data-confirm="Hapus report ini beserta seluruh barisnya?">
                                            <?= csrf_field() ?>
                                            <?= method_field('DELETE') ?>
                                            <input type="hidden" name="return_to" value="<?= e($returnPath) ?>">
                                            <button class="btn btn-outline-danger btn-sm" type="submit" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if ($batches === []): ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 48px 24px;">
                                <div style="display: flex; flex-direction: column; align-items: center; gap: 8px;">
                                    <i class="bi bi-file-earmark-x" style="font-size: 32px; color: #94a3b8;"></i>
                                    <strong style="color: #475569; font-size: 15px;">Tidak ada report tim</strong>
                                    <span style="color: #94a3b8; font-size: 13px;">Belum ada berkas pada tanggal ini atau data tidak cocok dengan filter yang dipilih.</span>
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
        <?php foreach ($batches as $batch): ?>
            <?php
            $total = (int) ($batch['total_rows_live'] ?? $batch['total_rows'] ?? 0);
            $sent = (int) ($batch['sent_rows_live'] ?? $batch['sent_rows'] ?? 0);
            $pending = (int) ($batch['pending_rows_live'] ?? $batch['pending_rows'] ?? 0);
            $success = (int) ($batch['success_rows_live'] ?? 0);
            $failed = (int) ($batch['failed_rows_live'] ?? 0);
            $skipped = (int) ($batch['skipped_rows'] ?? 0);
            $progress = $total > 0 ? min(100, (int) round(($sent / $total) * 100)) : 0;
            ?>
            <article class="report-list-item">
                <div class="report-list-header">
                    <div>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <strong style="font-size: 15px; color: #0f172a;"><?= e($batch['source_filename']) ?></strong>
                            <span class="status-pill secondary">Batch #<?= (int) $batch['id'] ?></span>
                            <span class="chip-meta" style="font-weight: 700; color: #1e3a8a;">
                                TEAM: <?= e(strtoupper((string) ($batch['effective_team'] ?? 'Tanpa Tim'))) ?>
                            </span>
                        </div>
                        <span style="font-size: 12px; color: #64748b; margin-top: 2px; display: block;">
                            <?= e($batch['name']) ?>
                        </span>
                    </div>

                    <div style="display: inline-flex; align-items: center; gap: 6px;">
                        <a class="btn btn-primary btn-sm" href="<?= e(url('/reports/' . $batch['id'])) ?>">
                            <i class="bi bi-table me-1"></i> Buka Report
                        </a>
                        <a class="btn btn-light btn-sm" href="<?= e(url('/reports/' . $batch['id'] . '/export')) ?>">
                            <i class="bi bi-file-earmark-excel"></i>
                        </a>
                        <?php if (can('report.manage')): ?>
                            <a class="btn btn-light btn-sm" href="<?= e(url('/reports/' . $batch['id'] . '/edit')) ?>" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form class="d-inline" method="post" action="<?= e(url('/reports/' . $batch['id'])) ?>" data-confirm="Hapus report ini beserta seluruh barisnya?">
                                <?= csrf_field() ?>
                                <?= method_field('DELETE') ?>
                                <input type="hidden" name="return_to" value="<?= e($returnPath) ?>">
                                <button class="btn btn-outline-danger btn-sm" type="submit" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="report-list-body">
                    <div class="report-list-stat-item">
                        <span class="stat-label">Total Baris</span>
                        <span class="stat-val"><?= number_format($total, 0, ',', '.') ?></span>
                    </div>
                    <div class="report-list-stat-item">
                        <span class="stat-label">Terkirim</span>
                        <span class="stat-val" style="color: #059669;"><?= number_format($sent, 0, ',', '.') ?></span>
                    </div>
                    <div class="report-list-stat-item">
                        <span class="stat-label">Pending</span>
                        <span class="stat-val" style="color: #d97706;"><?= number_format($pending, 0, ',', '.') ?></span>
                    </div>
                    <div class="report-list-stat-item">
                        <span class="stat-label">Hasil Sukses / Gagal</span>
                        <span class="stat-val">
                            <span class="text-success"><?= $success ?> OK</span> / 
                            <span class="text-danger"><?= $failed ?> Fail</span>
                        </span>
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
                        <span>Dilewati: <strong><?= $skipped ?></strong> baris</span>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>

        <?php if ($batches === []): ?>
            <div class="empty-card" style="padding: 48px 24px; text-align: center; background: #ffffff; border: 1px solid #dce6f0; border-radius: 16px;">
                <i class="bi bi-file-earmark-x" style="font-size: 32px; color: #94a3b8; display: block; margin-bottom: 12px;"></i>
                <h3 style="font-size: 16px; color: #1e293b; margin-bottom: 6px;">Tidak ada report tim</h3>
                <p style="color: #64748b; font-size: 13px; margin-bottom: 16px;">Belum ada file pada tanggal ini atau data tidak cocok dengan filter yang dipilih.</p>
                <?php if (can('report.manage')): ?>
                    <a class="btn btn-primary" href="<?= e(url('/reports/create?' . http_build_query(['month' => $monthKey, 'date' => $date]))) ?>">
                        <i class="bi bi-cloud-arrow-up me-1"></i>
                        Tambah Excel
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

</div> <!-- end reportViewContainerDate -->
</div> <!-- end Alpine x-data -->

<!-- =========================================================
     JAVASCRIPT: PERALIHAN MODE TABEL / LIST
     ========================================================= -->
<script>
(() => {
    const container = document.getElementById('reportViewContainerDate');
    const switcher = document.getElementById('reportViewSwitcherDate');
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
