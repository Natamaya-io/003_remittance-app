<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| REPORT HARIAN - INDEX (PRELINE UI & ALPINE.JS NATIVE)
|--------------------------------------------------------------------------
*/

$months = is_array($months ?? null) ? $months : [];
$filters = is_array($filters ?? null) ? $filters : [];
$teams = is_array($teams ?? null) ? $teams : [];

$monthNames = [
    1 => 'Januari',
    2 => 'Februari',
    3 => 'Maret',
    4 => 'April',
    5 => 'Mei',
    6 => 'Juni',
    7 => 'Juli',
    8 => 'Agustus',
    9 => 'September',
    10 => 'Oktober',
    11 => 'November',
    12 => 'Desember',
];

$activeAdvancedFilters = array_filter(
    $filters,
    static function (mixed $value, string|int $key): bool {
        return (string) $key !== 'q' && $value !== '' && $value !== null;
    },
    ARRAY_FILTER_USE_BOTH
);
?>

<div class="max-w-7xl mx-auto space-y-6">

    <!-- =========================================================
         PAGE HEADING
         ========================================================= -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Report Harian</h2>
        </div>

        <?php if (can('report.manage')): ?>
            <div class="flex items-center gap-2">
                <a class="btn btn-primary py-2 px-4 text-xs font-medium rounded-lg inline-flex items-center gap-2" href="<?= e(url('/reports/create')) ?>">
                    <i class="bi bi-cloud-arrow-up"></i>
                    Upload Excel
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- =========================================================
         PENCARIAN GLOBAL
         ========================================================= -->
    <section class="bg-white border border-gray-200 rounded-2xl p-4 sm:p-5 shadow-xs">
        <form method="get" action="<?= e(url('/reports/search')) ?>" class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex-1">
                <strong class="text-sm font-bold text-gray-900 block">Pencarian Transaksi</strong>
                <span class="text-xs text-gray-500 mt-0.5 block">Cari data di seluruh bulan, tanggal, tim, berkas, payer, dan beneficiary.</span>
            </div>

            <div class="flex items-center gap-2 flex-1 max-w-md">
                <div class="relative flex-1">
                    <input
                        class="py-2 px-3 ps-9 block w-full border border-gray-300 rounded-xl text-xs focus:border-blue-500 focus:ring-blue-500 shadow-xs"
                        type="search"
                        name="q"
                        required
                        autocomplete="off"
                        placeholder="Ketik kata kunci pencarian..."
                        value="<?= e((string) ($filters['q'] ?? '')) ?>"
                    >
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
         FILTER LANJUTAN (ALPINE.JS ACCORDION)
         ========================================================= -->
    <div x-data="{ openFilter: <?= $activeAdvancedFilters !== [] ? 'true' : 'false' ?> }" class="bg-white border border-gray-200 rounded-2xl p-4 sm:p-5 shadow-xs">
        <button type="button" @click="openFilter = !openFilter" class="w-full flex items-center justify-between text-left focus:outline-none">
            <div class="flex items-center gap-2.5">
                <span class="size-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-sm">
                    <i class="bi bi-sliders"></i>
                </span>
                <div>
                    <strong class="text-xs font-bold text-gray-900 block">Filter Lanjutan</strong>
                    <span class="text-[11px] text-gray-500 block">Saring berdasarkan bulan, rentang tanggal, tim, status kirim, dan hasil</span>
                </div>
            </div>
            <i class="bi bi-chevron-down text-gray-400 text-xs transition-transform duration-200" :class="{ 'rotate-180': openFilter }"></i>
        </button>

        <form x-show="openFilter" x-transition class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 pt-4 mt-4 border-t border-gray-100" method="get" action="<?= e(url('/reports')) ?>">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Bulan report</label>
                <input class="py-1.5 px-3 block w-full border border-gray-300 rounded-lg text-xs focus:border-blue-500 focus:ring-blue-500 shadow-xs" type="month" name="month" value="<?= e((string) ($filters['month'] ?? '')) ?>">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Tanggal mulai</label>
                <input class="py-1.5 px-3 block w-full border border-gray-300 rounded-lg text-xs focus:border-blue-500 focus:ring-blue-500 shadow-xs" type="date" name="date_from" value="<?= e((string) ($filters['date_from'] ?? '')) ?>">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Tanggal akhir</label>
                <input class="py-1.5 px-3 block w-full border border-gray-300 rounded-lg text-xs focus:border-blue-500 focus:ring-blue-500 shadow-xs" type="date" name="date_to" value="<?= e((string) ($filters['date_to'] ?? '')) ?>">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Tim</label>
                <select class="py-1.5 px-3 block w-full border border-gray-300 rounded-lg text-xs focus:border-blue-500 focus:ring-blue-500 shadow-xs" name="team">
                    <option value="">Semua tim</option>
                    <?php foreach ($teams as $team): ?>
                        <?php $teamValue = trim((string) $team); if ($teamValue === '') continue; ?>
                        <option value="<?= e($teamValue) ?>" <?= selected((string) ($filters['team'] ?? ''), $teamValue) ?>>
                            <?= e($teamValue) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Status kirim</label>
                <select class="py-1.5 px-3 block w-full border border-gray-300 rounded-lg text-xs focus:border-blue-500 focus:ring-blue-500 shadow-xs" name="delivery_status">
                    <option value="">Semua status</option>
                    <option value="PENDING" <?= selected((string) ($filters['delivery_status'] ?? ''), 'PENDING') ?>>Pending</option>
                    <option value="SENT" <?= selected((string) ($filters['delivery_status'] ?? ''), 'SENT') ?>>Terkirim</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Hasil</label>
                <select class="py-1.5 px-3 block w-full border border-gray-300 rounded-lg text-xs focus:border-blue-500 focus:ring-blue-500 shadow-xs" name="result_status">
                    <option value="">Semua hasil</option>
                    <option value="PENDING" <?= selected((string) ($filters['result_status'] ?? ''), 'PENDING') ?>>Pending</option>
                    <option value="SUCCESS" <?= selected((string) ($filters['result_status'] ?? ''), 'SUCCESS') ?>>Success</option>
                    <option value="FAILED" <?= selected((string) ($filters['result_status'] ?? ''), 'FAILED') ?>>Failed</option>
                </select>
            </div>

            <div class="sm:col-span-2 lg:col-span-3 flex items-center justify-end gap-2 pt-2 border-t border-gray-100">
                <a class="py-1.5 px-3.5 text-xs font-medium rounded-lg border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 shadow-xs transition-colors" href="<?= e(url('/reports')) ?>">
                    Reset
                </a>
                <button class="btn btn-primary py-1.5 px-4 text-xs font-medium rounded-lg inline-flex items-center gap-1.5" type="submit">
                    <i class="bi bi-funnel"></i>
                    Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    <!-- =========================================================
         HIERARKI TINGKAT 1 (STEPPER PILLS)
         ========================================================= -->
    <div class="flex flex-wrap items-center gap-2 text-xs font-medium text-gray-500">
        <span class="inline-flex items-center gap-1.5 py-1 px-3 rounded-full bg-blue-600 text-white font-bold shadow-xs">
            <span class="size-4 rounded-full bg-white text-blue-600 text-[10px] inline-flex items-center justify-center font-bold">1</span>
            Bulan
        </span>
        <i class="bi bi-chevron-right text-gray-400 text-xs"></i>
        <span class="inline-flex items-center gap-1.5 py-1 px-3 rounded-full bg-gray-100 text-gray-500">
            <span class="size-4 rounded-full bg-gray-300 text-gray-700 text-[10px] inline-flex items-center justify-center font-bold">2</span>
            Tanggal
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
    <div x-data="{ viewMode: localStorage.getItem('fable_report_view_mode') || 'table' }"
         x-init="$watch('viewMode', val => localStorage.setItem('fable_report_view_mode', val))"
         class="space-y-4">

        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-gray-700">Periode Bulan</span>
                <span class="inline-flex items-center py-0.5 px-2.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200 shadow-xs">
                    <?= count($months) ?> Bulan Terdaftar
                </span>
            </div>

            <div class="flex items-center gap-2">
                <!-- Preline UI Dropdown -->
                <div class="hs-dropdown relative inline-flex">
                    <button
                        id="hs-dropdown-report-options"
                        type="button"
                        class="hs-dropdown-toggle inline-flex items-center gap-x-2 py-1.5 px-3 text-xs font-semibold rounded-xl border border-gray-200 bg-white text-gray-700 shadow-xs hover:bg-gray-50 focus:outline-none transition-colors"
                        aria-haspopup="menu"
                        aria-expanded="false"
                        aria-label="Menu Opsi"
                    >
                        <i class="bi bi-gear"></i>
                        Opsi
                        <i class="bi bi-chevron-down text-xs text-gray-400"></i>
                    </button>

                    <div
                        class="hs-dropdown-menu transition-[opacity,margin] duration hs-dropdown-open:opacity-100 opacity-0 hidden min-w-48 bg-white shadow-lg rounded-xl p-1.5 border border-gray-200 z-50"
                        role="menu"
                        aria-orientation="vertical"
                        aria-labelledby="hs-dropdown-report-options"
                    >
                        <a class="flex items-center gap-x-3 py-2 px-3 rounded-lg text-xs text-gray-700 hover:bg-gray-100 focus:outline-none" href="<?= e(url('/reports/create')) ?>">
                            <i class="bi bi-cloud-arrow-up text-blue-600"></i>
                            Upload Report Baru
                        </a>
                        <a class="flex items-center gap-x-3 py-2 px-3 rounded-lg text-xs text-gray-700 hover:bg-gray-100 focus:outline-none" href="<?= e(url('/reports/search')) ?>">
                            <i class="bi bi-search text-emerald-600"></i>
                            Pencarian Global
                        </a>
                        <hr class="my-1 border-gray-100">
                        <button type="button" @click="viewMode = 'table'" class="w-full flex items-center gap-x-3 py-2 px-3 rounded-lg text-xs text-gray-700 hover:bg-gray-100 focus:outline-none">
                            <i class="bi bi-table text-indigo-600"></i>
                            Tampilan Tabel
                        </button>
                        <button type="button" @click="viewMode = 'list'" class="w-full flex items-center gap-x-3 py-2 px-3 rounded-lg text-xs text-gray-700 hover:bg-gray-100 focus:outline-none">
                            <i class="bi bi-list-ul text-amber-600"></i>
                            Tampilan List
                        </button>
                    </div>
                </div>

                <!-- Mode Switcher Buttons -->
                <div class="inline-flex rounded-xl bg-gray-100 p-1 border border-gray-200">
                    <button
                        type="button"
                        class="inline-flex items-center gap-1.5 py-1 px-3 text-xs font-semibold rounded-lg transition-colors"
                        :class="viewMode === 'table' ? 'bg-white text-gray-900 shadow-xs' : 'text-gray-500 hover:text-gray-800'"
                        @click="viewMode = 'table'"
                    >
                        <i class="bi bi-table"></i>
                        Tabel
                    </button>
                    <button
                        type="button"
                        class="inline-flex items-center gap-1.5 py-1 px-3 text-xs font-semibold rounded-lg transition-colors"
                        :class="viewMode === 'list' ? 'bg-white text-gray-900 shadow-xs' : 'text-gray-500 hover:text-gray-800'"
                        @click="viewMode = 'list'"
                    >
                        <i class="bi bi-list-ul"></i>
                        List
                    </button>
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
                            <th scope="col" class="py-3.5 px-5 text-start text-xs font-semibold text-gray-600 uppercase tracking-wider">Periode Bulan</th>
                            <th scope="col" class="py-3.5 px-5 text-start text-xs font-semibold text-gray-600 uppercase tracking-wider">Berkas</th>
                            <th scope="col" class="py-3.5 px-5 text-start text-xs font-semibold text-gray-600 uppercase tracking-wider">Hari Aktif</th>
                            <th scope="col" class="py-3.5 px-5 text-start text-xs font-semibold text-gray-600 uppercase tracking-wider">Tim</th>
                            <th scope="col" class="py-3.5 px-5 text-end text-xs font-semibold text-gray-600 uppercase tracking-wider">Total Baris</th>
                            <th scope="col" class="py-3.5 px-5 text-start text-xs font-semibold text-gray-600 uppercase tracking-wider" style="min-width: 170px;">Progress Kirim</th>
                            <th scope="col" class="py-3.5 px-5 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Dilewati</th>
                            <th scope="col" class="py-3.5 px-5 text-start text-xs font-semibold text-gray-600 uppercase tracking-wider">Upload Terakhir</th>
                            <th scope="col" class="py-3.5 px-5 text-end text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach ($months as $month): ?>
                            <?php
                            if (!is_array($month)) continue;
                            $monthKey = trim((string) ($month['month_key'] ?? ''));
                            if (preg_match('/^\d{4}-\d{2}$/', $monthKey) !== 1) continue;

                            $monthNum = (int) substr($monthKey, 5, 2);
                            $year = substr($monthKey, 0, 4);
                            $monthLabel = ($monthNames[$monthNum] ?? $monthKey) . ' ' . $year;

                            $total = (int) ($month['total_rows'] ?? 0);
                            $sent = (int) ($month['sent_rows'] ?? 0);
                            $pending = (int) ($month['pending_rows'] ?? 0);
                            $skipped = (int) ($month['skipped_rows'] ?? 0);
                            $fileCount = (int) ($month['file_count'] ?? 0);
                            $dateCount = (int) ($month['date_count'] ?? 0);
                            $teamCount = (int) ($month['team_count'] ?? 0);

                            $progress = $total > 0 ? min(100, (int) round(($sent / $total) * 100)) : 0;

                            $query = array_filter([
                                'team' => (string) ($filters['team'] ?? ''),
                                'date_from' => (string) ($filters['date_from'] ?? ''),
                                'date_to' => (string) ($filters['date_to'] ?? ''),
                                'delivery_status' => (string) ($filters['delivery_status'] ?? ''),
                                'result_status' => (string) ($filters['result_status'] ?? ''),
                            ], static fn($val): bool => trim((string) $val) !== '');

                            $monthUrl = '/reports/month/' . rawurlencode($monthKey) . ($query !== [] ? '?' . http_build_query($query) : '');
                            $lastUpload = isset($month['last_upload']) ? (string) $month['last_upload'] : null;
                            ?>
                            <tr class="even:bg-gray-50/50 [data-no-hover]">
                                <td class="py-4 px-5 whitespace-nowrap">
                                    <div class="flex items-center gap-x-3">
                                        <div class="size-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-sm shadow-xs">
                                            <i class="bi bi-calendar3"></i>
                                        </div>
                                        <div>
                                            <strong class="text-sm font-bold text-gray-900 block"><?= e($monthLabel) ?></strong>
                                            <span class="text-xs text-gray-500 font-mono"><?= e($monthKey) ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-5 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                                        <i class="bi bi-files text-gray-400"></i>
                                        <?= number_format($fileCount, 0, ',', '.') ?> Berkas
                                    </span>
                                </td>
                                <td class="py-4 px-5 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                                        <i class="bi bi-calendar-event text-gray-400"></i>
                                        <?= number_format($dateCount, 0, ',', '.') ?> Hari
                                    </span>
                                </td>
                                <td class="py-4 px-5 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                                        <i class="bi bi-people text-gray-400"></i>
                                        <?= number_format($teamCount, 0, ',', '.') ?> Tim
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
                                    <a class="btn btn-primary py-1.5 px-3 text-xs font-medium rounded-lg inline-flex items-center gap-1.5" href="<?= e(url($monthUrl)) ?>">
                                        Buka Bulan
                                        <i class="bi bi-arrow-right"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <?php if ($months === []): ?>
                            <tr>
                                <td colspan="9" class="text-center py-12 px-6">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <i class="bi bi-calendar-x text-3xl text-gray-400"></i>
                                        <strong class="text-sm font-bold text-gray-800">Tidak ada periode bulan report</strong>
                                        <span class="text-xs text-gray-500">Ubah filter atau upload berkas Excel baru untuk mulai mengisi data.</span>
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
            <?php foreach ($months as $month): ?>
                <?php
                if (!is_array($month)) continue;
                $monthKey = trim((string) ($month['month_key'] ?? ''));
                if (preg_match('/^\d{4}-\d{2}$/', $monthKey) !== 1) continue;

                $monthNum = (int) substr($monthKey, 5, 2);
                $year = substr($monthKey, 0, 4);
                $monthLabel = ($monthNames[$monthNum] ?? $monthKey) . ' ' . $year;

                $total = (int) ($month['total_rows'] ?? 0);
                $sent = (int) ($month['sent_rows'] ?? 0);
                $pending = (int) ($month['pending_rows'] ?? 0);
                $skipped = (int) ($month['skipped_rows'] ?? 0);
                $fileCount = (int) ($month['file_count'] ?? 0);
                $dateCount = (int) ($month['date_count'] ?? 0);
                $teamCount = (int) ($month['team_count'] ?? 0);

                $progress = $total > 0 ? min(100, (int) round(($sent / $total) * 100)) : 0;

                $query = array_filter([
                    'team' => (string) ($filters['team'] ?? ''),
                    'date_from' => (string) ($filters['date_from'] ?? ''),
                    'date_to' => (string) ($filters['date_to'] ?? ''),
                    'delivery_status' => (string) ($filters['delivery_status'] ?? ''),
                    'result_status' => (string) ($filters['result_status'] ?? ''),
                ], static fn($val): bool => trim((string) $val) !== '');

                $monthUrl = '/reports/month/' . rawurlencode($monthKey) . ($query !== [] ? '?' . http_build_query($query) : '');
                $lastUpload = isset($month['last_upload']) ? (string) $month['last_upload'] : null;
                ?>
                <article class="bg-white border border-gray-200 rounded-2xl p-5 shadow-xs hover:shadow-md transition-shadow duration-200 flex flex-col justify-between space-y-4">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-x-3 min-w-0">
                                <div class="size-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg shadow-xs shrink-0">
                                    <i class="bi bi-calendar3"></i>
                                </div>
                                <div class="min-w-0">
                                    <strong class="text-base font-bold text-gray-900 block truncate"><?= e($monthLabel) ?></strong>
                                    <span class="inline-flex items-center py-0.5 px-2 rounded-full text-[11px] font-mono font-semibold bg-gray-100 text-gray-600">
                                        <?= e($monthKey) ?>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Stats Pills -->
                        <div class="flex flex-wrap items-center gap-1.5 text-xs">
                            <span class="inline-flex items-center gap-1 py-1 px-2.5 rounded-full bg-gray-100 text-gray-700 font-medium">
                                <i class="bi bi-files text-gray-400"></i>
                                <?= number_format($fileCount, 0, ',', '.') ?> berkas
                            </span>
                            <span class="inline-flex items-center gap-1 py-1 px-2.5 rounded-full bg-gray-100 text-gray-700 font-medium">
                                <i class="bi bi-calendar-event text-gray-400"></i>
                                <?= number_format($dateCount, 0, ',', '.') ?> hari
                            </span>
                            <span class="inline-flex items-center gap-1 py-1 px-2.5 rounded-full bg-gray-100 text-gray-700 font-medium">
                                <i class="bi bi-people text-gray-400"></i>
                                <?= number_format($teamCount, 0, ',', '.') ?> tim
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
                        <a class="btn btn-primary py-1.5 px-3 text-xs font-medium rounded-lg inline-flex items-center gap-1.5 shrink-0" href="<?= e(url($monthUrl)) ?>">
                            Buka Tanggal
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>

            <?php if ($months === []): ?>
                <div class="col-span-full bg-white border border-gray-200 rounded-2xl p-12 text-center shadow-xs">
                    <i class="bi bi-calendar-x text-3xl text-gray-400 block mb-3"></i>
                    <h3 class="text-base font-bold text-gray-800 mb-1">Tidak ada bulan yang cocok</h3>
                    <p class="text-xs text-gray-500 mb-4">Ubah filter atau upload report baru untuk mulai mengisi data.</p>
                    <?php if (can('report.manage')): ?>
                        <a class="btn btn-primary py-2 px-4 text-xs font-medium rounded-lg inline-flex items-center gap-2" href="<?= e(url('/reports/create')) ?>">
                            <i class="bi bi-cloud-arrow-up"></i>
                            Upload Report
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

    </div>

</div>
