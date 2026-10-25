<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| REPORT HARIAN - TINGKAT TANGGAL (PRELINE UI & ALPINE.JS NATIVE)
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

<div class="max-w-7xl mx-auto space-y-6">

    <!-- =========================================================
         PAGE ACTIONS
         ========================================================= -->
    <div class="flex items-center justify-end gap-2">
        <a class="py-2 px-3.5 inline-flex items-center gap-x-1.5 text-xs font-medium rounded-lg border border-gray-200 bg-white text-gray-700 shadow-xs hover:bg-gray-50 focus:outline-none focus:bg-gray-50 transition-colors" href="<?= e(url('/reports')) ?>">
            <i class="bi bi-arrow-left"></i>
            Daftar Bulan
        </a>
    </div>

    <!-- =========================================================
         PENCARIAN GLOBAL
         ========================================================= -->
    <section class="bg-white border border-gray-200 rounded-2xl p-4 sm:p-5 shadow-xs">
        <form method="get" action="<?= e(url('/reports/search')) ?>" class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div class="flex-1">
                <strong class="text-sm font-bold text-gray-900 block">Pencarian Transaksi Bulan Ini</strong>
                <span class="text-xs text-gray-500 mt-0.5 block">Cari data transaksi di seluruh tanggal dan tim dalam bulan <?= e($monthLabel) ?>.</span>
            </div>

            <div class="flex flex-wrap sm:flex-nowrap items-center gap-2 flex-1 max-w-xl">
                <div class="relative flex-1 min-w-[200px]">
                    <input class="py-2 px-3 ps-9 block w-full border border-gray-300 rounded-xl text-xs focus:border-blue-500 focus:ring-blue-500 shadow-xs" type="search" name="q" required placeholder="Cari data report..." value="<?= e((string) ($filters['q'] ?? '')) ?>">
                    <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none ps-3 text-gray-400">
                        <i class="bi bi-search text-xs"></i>
                    </div>
                </div>
                <button class="btn btn-primary py-2 px-3.5 inline-flex items-center justify-center gap-x-1.5 text-xs font-semibold rounded-lg shrink-0 focus:outline-none" type="submit">
                    <i class="bi bi-search"></i>
                    Cari
                </button>
                <?php if (can('report.manage')): ?>
                    <a class="btn btn-primary py-2 px-3.5 inline-flex items-center justify-center gap-x-2 text-xs font-semibold rounded-lg shrink-0 focus:outline-none" href="<?= e(url('/reports/create?month=' . urlencode($month))) ?>">
                        <i class="bi bi-cloud-arrow-up text-sm"></i>
                        Upload Excel
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </section>

    <!-- =========================================================
         FILTER TANGGAL & TIM
         ========================================================= -->
    <div class="bg-white border border-gray-200 rounded-2xl p-4 sm:p-5 shadow-xs">
        <form class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end" method="get" action="<?= e(url('/reports/month/' . $month)) ?>">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Tim</label>
                <select class="py-1.5 px-3 block w-full border border-gray-300 rounded-lg text-xs focus:border-blue-500 focus:ring-blue-500 shadow-xs" name="team">
                    <option value="">Semua tim</option>
                    <?php foreach ($teams as $team): ?>
                        <option value="<?= e($team) ?>" <?= selected((string) ($filters['team'] ?? ''), $team) ?>><?= e($team) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Tanggal mulai</label>
                <div class="datepicker-wrapper">
                    <span class="datepicker-icon-left">
                        <i class="bi bi-calendar3"></i>
                    </span>
                    <input class="form-datepicker has-icon-left text-xs" type="date" name="date_from" value="<?= e((string) ($filters['date_from'] ?? '')) ?>">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Tanggal akhir</label>
                <div class="datepicker-wrapper">
                    <span class="datepicker-icon-left">
                        <i class="bi bi-calendar3"></i>
                    </span>
                    <input class="form-datepicker has-icon-left text-xs" type="date" name="date_to" value="<?= e((string) ($filters['date_to'] ?? '')) ?>">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Status kirim</label>
                <select class="py-1.5 px-3 block w-full border border-gray-300 rounded-lg text-xs focus:border-blue-500 focus:ring-blue-500 shadow-xs" name="delivery_status">
                    <option value="">Semua status</option>
                    <option value="PENDING" <?= selected((string) ($filters['delivery_status'] ?? ''), 'PENDING') ?>>Pending</option>
                    <option value="SENT" <?= selected((string) ($filters['delivery_status'] ?? ''), 'SENT') ?>>Terkirim</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <a class="py-1.5 px-3 inline-flex justify-center items-center text-xs font-medium rounded-lg border border-gray-200 bg-white text-gray-700 shadow-xs hover:bg-gray-50 focus:outline-none focus:bg-gray-50 transition-colors flex-1" href="<?= e(url('/reports/month/' . $month)) ?>">
                    Reset
                </a>
                <button class="btn btn-primary py-1.5 px-3.5 inline-flex justify-center items-center gap-x-1.5 text-xs font-semibold rounded-lg focus:outline-none flex-1" type="submit">
                    <i class="bi bi-funnel"></i>
                    Filter
                </button>
            </div>
        </form>
    </div>

    <?php if (!empty($filters['team']) || !empty($filters['date_from']) || !empty($filters['date_to']) || !empty($filters['delivery_status'])): ?>
        <!-- ALERT STATUS: FILTER BULAN AKTIF -->
        <div
            class="alert alert-info"
            role="alert"
            x-data="{ show: true }"
            x-show="show"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
        >
            <div class="alert-content">
                <span class="alert-icon">
                    <i class="bi bi-info-circle-fill"></i>
                </span>
                <div class="alert-body">
                    <strong class="alert-title">Filter Aktif</strong>
                    <div class="alert-message">
                        Menampilkan tanggal report dengan kriteria filter aktif.
                        <a href="<?= e(url('/reports/month/' . $month)) ?>" class="underline font-semibold ml-1 hover:text-blue-900">Reset Semua</a>
                    </div>
                </div>
            </div>
            <button
                class="alert-close"
                type="button"
                @click="show = false"
                aria-label="Tutup notifikasi"
            >
                <i class="bi bi-x text-lg leading-none"></i>
            </button>
        </div>
    <?php endif; ?>

    <!-- =========================================================
         HIERARKI TINGKAT 2 (STEPPER PILLS)
         ========================================================= -->
    <div class="flex flex-wrap items-center gap-2 text-xs font-medium text-gray-500">
        <a class="inline-flex items-center gap-1.5 py-1 px-3 rounded-full bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors" href="<?= e(url('/reports')) ?>">
            <span class="size-4 rounded-full bg-gray-300 text-gray-700 text-[10px] inline-flex items-center justify-center font-bold">1</span>
            Bulan
        </a>
        <i class="bi bi-chevron-right text-gray-400 text-xs"></i>
        <span class="inline-flex items-center gap-1.5 py-1 px-3 rounded-full bg-blue-600 text-white font-bold shadow-xs">
            <span class="size-4 rounded-full bg-white text-blue-600 text-[10px] inline-flex items-center justify-center font-bold">2</span>
            Tanggal (<?= e($monthLabel) ?>)
        </span>
        <i class="bi bi-chevron-right text-gray-400 text-xs"></i>
        <span class="inline-flex items-center gap-1.5 py-1 px-3 rounded-full bg-gray-100 text-gray-500">
            <span class="size-4 rounded-full bg-gray-300 text-gray-700 text-[10px] inline-flex items-center justify-center font-bold">3</span>
            Tim dan Report
        </span>
    </div>

    <!-- =========================================================
         TOOLBAR MODE TAMPILAN: TABEL VS LIST (ALPINE + PRELINE UI)
         ========================================================= -->
    <div x-data="{ viewMode: localStorage.getItem('remitgo_report_view_mode') || localStorage.getItem('fable_report_view_mode') || 'table' }"
         x-init="$watch('viewMode', val => { localStorage.setItem('remitgo_report_view_mode', val); localStorage.setItem('fable_report_view_mode', val); })"
         class="space-y-4">

        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-gray-700">Tanggal Transaksi</span>
                <span class="inline-flex items-center py-0.5 px-2.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200 shadow-xs">
                    <?= count($dates) ?> Hari Aktif
                </span>
            </div>

            <div class="flex items-center gap-2">
                <!-- Preline UI Dropdown -->
                <div class="hs-dropdown relative inline-flex">
                    <button
                        id="hs-dropdown-month-options"
                        type="button"
                        class="hs-dropdown-toggle dropdown-toggle"
                        aria-haspopup="menu"
                        aria-expanded="false"
                        aria-label="Menu Opsi"
                    >
                        <i class="bi bi-gear"></i>
                        <span>Opsi</span>
                        <span class="text-gray-300 font-normal">·</span>
                        <span class="text-[11px] text-gray-500 font-medium capitalize" x-text="viewMode === 'table' ? 'Tabel' : 'List'"></span>
                        <i class="bi bi-chevron-down text-xs text-gray-400"></i>
                    </button>

                    <div
                        class="hs-dropdown-menu dropdown-menu transition-[opacity,margin] duration hs-dropdown-open:opacity-100 opacity-0 hidden"
                        role="menu"
                        aria-orientation="vertical"
                        aria-labelledby="hs-dropdown-month-options"
                    >
                        <span class="dropdown-header">Aksi Cepat</span>
                        <a class="dropdown-item" href="<?= e(url('/reports/create?month=' . urlencode($month))) ?>">
                            <i class="bi bi-cloud-arrow-up text-blue-600"></i>
                            Upload Excel Baru
                        </a>
                        <div class="dropdown-divider"></div>
                        <span class="dropdown-header">Mode Tampilan</span>
                        <button type="button" @click="viewMode = 'table'" class="dropdown-item justify-between" :class="viewMode === 'table' ? 'active font-semibold' : ''">
                            <span class="flex items-center gap-x-2.5">
                                <i class="bi bi-table text-indigo-600"></i>
                                Tampilan Tabel
                            </span>
                            <i x-show="viewMode === 'table'" class="bi bi-check2 text-blue-600 text-sm"></i>
                        </button>
                        <button type="button" @click="viewMode = 'list'" class="dropdown-item justify-between" :class="viewMode === 'list' ? 'active font-semibold' : ''">
                            <span class="flex items-center gap-x-2.5">
                                <i class="bi bi-list-ul text-amber-600"></i>
                                Tampilan List
                            </span>
                            <i x-show="viewMode === 'list'" class="bi bi-check2 text-blue-600 text-sm"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- =========================================================
             1. TAMPILAN TABEL ZEBRA (PRELINE UI + NAKED ZEBRA TABLE)
             ========================================================= -->
        <div x-show="viewMode === 'table'" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
            <div class="overflow-x-auto border border-gray-200 rounded-2xl bg-white shadow-xs">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50/75">
                        <tr>
                            <th scope="col" class="py-3.5 px-5 text-start text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal Transaksi</th>
                            <th scope="col" class="py-3.5 px-5 text-start text-xs font-semibold text-gray-600 uppercase tracking-wider">Berkas</th>
                            <th scope="col" class="py-3.5 px-5 text-start text-xs font-semibold text-gray-600 uppercase tracking-wider">Tim</th>
                            <th scope="col" class="py-3.5 px-5 text-end text-xs font-semibold text-gray-600 uppercase tracking-wider">Total Baris</th>
                            <th scope="col" class="py-3.5 px-5 text-start text-xs font-semibold text-gray-600 uppercase tracking-wider" style="min-width: 170px;">Progress Kirim</th>
                            <th scope="col" class="py-3.5 px-5 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Dilewati</th>
                            <th scope="col" class="py-3.5 px-5 text-start text-xs font-semibold text-gray-600 uppercase tracking-wider">Upload Terakhir</th>
                            <th scope="col" class="py-3.5 px-5 text-end text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
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
                            <tr class="even:bg-gray-50/50 [data-no-hover]">
                                <td class="py-4 px-5 whitespace-nowrap">
                                    <div class="flex items-center gap-x-3">
                                        <div class="size-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-sm shadow-xs">
                                            <i class="bi bi-calendar-event"></i>
                                        </div>
                                        <div>
                                            <strong class="text-sm font-bold text-gray-900 block"><?= e($dateLabel) ?></strong>
                                            <span class="text-xs text-gray-500 font-mono"><?= e($dateValue) ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-5 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                                        <i class="bi bi-files text-gray-400"></i>
                                        <?= (int) $dateCard['file_count'] ?> Berkas
                                    </span>
                                </td>
                                <td class="py-4 px-5 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                                        <i class="bi bi-people text-gray-400"></i>
                                        <?= (int) $dateCard['team_count'] ?> Tim
                                    </span>
                                </td>
                                <td class="py-4 px-5 whitespace-nowrap text-end font-mono font-bold text-gray-900">
                                    <?= number_format($total, 0, ',', '.') ?>
                                </td>
                                <td class="py-4 px-5 whitespace-nowrap">
                                    <div class="space-y-1.5">
                                        <div class="flex items-center justify-between text-xs">
                                            <span class="font-semibold text-emerald-700"><?= $progress ?>% Kirim</span>
                                            <span class="text-gray-500 font-mono"><?= number_format($pending, 0, ',', '.') ?> Pnd</span>
                                        </div>
                                        <div class="w-full h-1.5 bg-gray-200 rounded-full overflow-hidden flex">
                                            <div class="h-full bg-blue-600 rounded-full" style="width: <?= $progress ?>%;"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-5 whitespace-nowrap text-center">
                                    <?php if ($skipped > 0): ?>
                                        <span class="inline-flex items-center py-0.5 px-2 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                            <?= number_format($skipped, 0, ',', '.') ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-xs text-gray-400">0</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-4 px-5 whitespace-nowrap text-xs text-gray-500">
                                    <?= e(format_datetime($lastUpload)) ?>
                                </td>
                                <td class="py-4 px-5 whitespace-nowrap text-end">
                                    <a class="btn btn-primary py-1 px-2.5 inline-flex items-center gap-x-1 text-xs font-semibold rounded-lg focus:outline-none transition-colors" href="<?= e(url($dateUrl)) ?>">
                                        Buka Tim
                                        <i class="bi bi-arrow-right"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <?php if ($dates === []): ?>
                            <tr>
                                <td colspan="8" class="text-center py-12 px-6">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <i class="bi bi-calendar-x text-3xl text-gray-400"></i>
                                        <strong class="text-sm font-bold text-gray-800">Belum ada report pada bulan ini</strong>
                                        <span class="text-xs text-gray-500">Upload berkas Excel untuk membentuk daftar tanggal transaksi otomatis.</span>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- =========================================================
             2. TAMPILAN LIST CARDS (PRELINE RESPONSIVE CARDS)
             ========================================================= -->
        <div x-show="viewMode === 'list'" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
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
                <article class="bg-white border border-gray-200 rounded-2xl p-5 shadow-xs hover:shadow-md transition-shadow duration-200 flex flex-col justify-between space-y-4">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-x-3 min-w-0">
                                <div class="size-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg shadow-xs shrink-0">
                                    <i class="bi bi-calendar-date"></i>
                                </div>
                                <div class="min-w-0">
                                    <strong class="text-base font-bold text-gray-900 block truncate"><?= e($dateLabel) ?></strong>
                                    <span class="inline-flex items-center py-0.5 px-2 rounded-full text-[11px] font-semibold <?= $pending > 0 ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' ?>">
                                        <?= $pending > 0 ? $pending . ' pending' : 'Selesai' ?>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Meta Info -->
                        <div class="flex items-center gap-2 text-xs text-gray-500 font-medium">
                            <span class="inline-flex items-center gap-1">
                                <i class="bi bi-files text-gray-400"></i>
                                <?= (int) $dateCard['file_count'] ?> berkas
                            </span>
                            <span>·</span>
                            <span class="inline-flex items-center gap-1">
                                <i class="bi bi-people text-gray-400"></i>
                                <?= (int) $dateCard['team_count'] ?> tim
                            </span>
                        </div>

                        <!-- KPI Grid -->
                        <div class="grid grid-cols-2 gap-2 pt-2 border-t border-gray-100 text-xs">
                            <div class="bg-gray-50/75 rounded-xl p-2.5">
                                <span class="text-gray-500 block text-[11px]">Total Baris</span>
                                <strong class="text-sm font-bold text-gray-900 font-mono"><?= number_format($total, 0, ',', '.') ?></strong>
                            </div>
                            <div class="bg-emerald-50/50 rounded-xl p-2.5 border border-emerald-100">
                                <span class="text-emerald-700 block text-[11px]">Terkirim</span>
                                <strong class="text-sm font-bold text-emerald-800 font-mono"><?= number_format($sent, 0, ',', '.') ?></strong>
                            </div>
                            <div class="bg-amber-50/50 rounded-xl p-2.5 border border-amber-100">
                                <span class="text-amber-700 block text-[11px]">Pending</span>
                                <strong class="text-sm font-bold text-amber-800 font-mono"><?= number_format($pending, 0, ',', '.') ?></strong>
                            </div>
                            <div class="bg-gray-50/75 rounded-xl p-2.5">
                                <span class="text-gray-500 block text-[11px]">Dilewati</span>
                                <strong class="text-sm font-bold text-gray-700 font-mono"><?= number_format($skipped, 0, ',', '.') ?></strong>
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        <div class="space-y-1.5 pt-1">
                            <div class="flex items-center justify-between text-xs font-medium">
                                <span class="text-emerald-700 font-semibold"><?= $progress ?>% Terkirim</span>
                                <span class="text-gray-500 font-mono text-[11px]"><?= number_format($sent, 0, ',', '.') ?> dari <?= number_format($total, 0, ',', '.') ?></span>
                            </div>
                            <div class="w-full h-1.5 bg-gray-200 rounded-full overflow-hidden flex">
                                <div class="h-full bg-blue-600 rounded-full" style="width: <?= $progress ?>%;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Card Footer -->
                    <div class="pt-3 border-t border-gray-100 flex items-center justify-between gap-2">
                        <span class="text-[11px] text-gray-500 truncate">
                            Upload: <?= e(format_datetime($lastUpload)) ?>
                        </span>
                        <a class="btn btn-primary py-1.5 px-3 inline-flex items-center gap-x-1.5 text-xs font-semibold rounded-lg shrink-0 focus:outline-none transition-colors" href="<?= e(url($dateUrl)) ?>">
                            Buka Tim
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>

            <?php if ($dates === []): ?>
                <div class="col-span-full bg-white border border-gray-200 rounded-2xl p-12 text-center shadow-xs">
                    <i class="bi bi-calendar-x text-3xl text-gray-400 block mb-3"></i>
                    <h3 class="text-base font-bold text-gray-800 mb-1">Belum ada report pada bulan ini</h3>
                    <p class="text-xs text-gray-500 mb-4">Upload berkas Excel untuk membentuk daftar tanggal transaksi otomatis.</p>
                    <?php if (can('report.manage')): ?>
                        <a class="btn btn-primary py-2 px-3.5 inline-flex items-center gap-x-2 text-xs font-semibold rounded-lg focus:outline-none" href="<?= e(url('/reports/create?month=' . urlencode($month))) ?>">
                            <i class="bi bi-cloud-arrow-up text-sm"></i>
                            Upload Excel
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

    </div>

</div>
