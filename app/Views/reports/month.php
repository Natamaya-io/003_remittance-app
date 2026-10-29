<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| LAPORAN - TINGKAT TANGGAL (PRELINE UI & ALPINE.JS NATIVE)
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

$activeFilterList = [];
if (!empty($filters['date_from'])) {
    $activeFilterList[] = 'Mulai ' . $filters['date_from'];
}
if (!empty($filters['date_to'])) {
    $activeFilterList[] = 'Sampai ' . $filters['date_to'];
}
$activeFiltersCount = count($activeFilterList);
$activeFilterSummary = implode(' · ', $activeFilterList);
?>

<div class="max-w-7xl mx-auto space-y-6" x-data="{ openFilterModal: false }" x-effect="if (openFilterModal) $nextTick(() => { window.HSStaticMethods && window.HSStaticMethods.autoInit(); })">

    <!-- =========================================================
         PAGE ACTIONS (PURE NAKED ARCHITECTURE)
         ========================================================= -->
    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
        <a class="py-2 px-3.5 inline-flex items-center gap-x-1.5 text-xs font-medium rounded-lg border border-gray-200 bg-white text-gray-700 shadow-xs hover:bg-gray-50 focus:outline-none focus:bg-gray-50 transition-colors" href="<?= e(url('/reports')) ?>">
            <i class="bi bi-arrow-left"></i>
            Daftar Bulan
        </a>

        <div class="flex items-center gap-2">
            <?= render_reports_search_bar([
                'action' => url('/reports/search'),
                'placeholder' => 'Cari transaksi...',
                'query' => (string) ($filters['q'] ?? ''),
                'extraParams' => ['month' => $month],
            ]) ?>

            <!-- Tombol Filter Modal (Selaras dengan Halaman Laporan) -->
            <button
                type="button"
                @click="openFilterModal = true"
                class="btn-primary"
                aria-label="Buka Filter Laporan Bulan <?= e($monthLabel) ?>"
                title="Filter berdasarkan rentang tanggal"
            >
                <i class="bi bi-sliders text-sm"></i>
                <span>Filter</span>
                <?php if ($activeFiltersCount > 0): ?>
                    <span class="btn-filter-badge" aria-label="<?= $activeFiltersCount ?> filter aktif"><?= $activeFiltersCount ?></span>
                <?php endif; ?>
            </button>

            <?php if (can('report.manage')): ?>
                <a class="btn-primary" href="<?= e(url('/reports/create?month=' . urlencode($month))) ?>">
                    <i class="bi bi-cloud-arrow-up text-sm"></i>
                    Upload Excel
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- =========================================================
         MODAL FILTER TRANSAKSI LAPORAN BULANAN (ALPINE.JS TELEPORTED TO BODY)
         ========================================================= -->
    <template x-teleport="body">
        <div
            x-show="openFilterModal"
            x-cloak
            class="fixed inset-0 z-[9999] overflow-y-auto"
            role="dialog"
            aria-modal="true"
            aria-labelledby="modal-month-filter-title"
            style="display: none;"
            @keydown.escape.window="openFilterModal = false"
        >
            <!-- 1. Full-screen Backdrop -->
            <div
                x-show="openFilterModal"
                x-transition:enter="transition-opacity ease-out duration-150"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity ease-in duration-100"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 w-full h-full bg-gray-900/60 backdrop-blur-sm"
                @click="openFilterModal = false"
                aria-hidden="true"
            ></div>

            <!-- 2. Modal Positioning Container -->
            <div class="min-h-full flex items-center justify-center p-4 relative z-10 pointer-events-none">
                <div
                    class="bg-white rounded-2xl max-w-xl w-full shadow-2xl border border-gray-200 text-left relative my-8 pointer-events-auto"
                    x-show="openFilterModal"
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                >
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 bg-white rounded-t-2xl">
                        <div class="flex items-center gap-2.5">
                            <i class="bi bi-calendar3 text-gray-800 text-lg"></i>
                            <div class="flex items-center gap-1.5">
                                <strong id="modal-month-filter-title" class="text-sm font-semibold text-gray-900 block">Filter Laporan <?= e($monthLabel) ?></strong>
                                <?= render_tooltip(
                                    'Saring data laporan bulan ' . e($monthLabel) . ' berdasarkan rentang tanggal mulai dan tanggal akhir.',
                                    '<span class="hs-tooltip-toggle info-toggle-btn cursor-help" aria-label="Info filter bulan"><i class="bi bi-info-circle text-[10px] leading-none"></i></span>',
                                    ['placement' => 'top', 'contentClass' => 'z-[100000]']
                                ) ?>
                            </div>
                        </div>
                        <button
                            type="button"
                            @click="openFilterModal = false"
                            class="modal-close"
                            aria-label="Tutup modal"
                            title="Tutup modal"
                        >
                            <i class="bi bi-x-lg text-xs leading-none"></i>
                        </button>
                    </div>

                    <!-- Modal Form Body -->
                    <form method="get" action="<?= e(url('/reports/month/' . $month)) ?>" class="m-0 p-0">
                        <?php if (($filters['q'] ?? '') !== ''): ?>
                            <input type="hidden" name="q" value="<?= e($filters['q']) ?>">
                        <?php endif; ?>

                        <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Tanggal Mulai -->
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Tanggal Mulai</label>
                                <?= render_datepicker('date_from', (string) ($filters['date_from'] ?? ''), ['placeholder' => 'Pilih tanggal mulai']) ?>
                            </div>

                            <!-- Tanggal Akhir -->
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Tanggal Akhir</label>
                                <?= render_datepicker('date_to', (string) ($filters['date_to'] ?? ''), [
                                    'placeholder' => 'Pilih tanggal akhir',
                                    'class' => '[&_.datepicker-popover]:sm:left-auto [&_.datepicker-popover]:sm:right-0',
                                ]) ?>
                            </div>
                        </div>

                        <!-- Modal Footer -->
                        <div class="px-5 py-3.5 bg-gray-50/70 border-t border-gray-100 flex items-center justify-between gap-3 rounded-b-2xl">
                            <a
                                class="py-2 px-3.5 inline-flex justify-center items-center text-xs font-medium rounded-lg border border-gray-200 bg-white text-gray-700 shadow-xs hover:bg-gray-50 focus:outline-none transition-colors"
                                href="<?= e(url('/reports/month/' . $month)) ?>"
                            >
                                Reset Filter
                            </a>

                            <div class="flex items-center gap-2">
                                <button
                                    type="button"
                                    @click="openFilterModal = false"
                                    class="btn-secondary"
                                >
                                    <span>Batal</span>
                                </button>
                                <button
                                    class="btn-primary"
                                    type="submit"
                                >
                                    <i class="bi bi-funnel"></i>
                                    <span>Terapkan Filter</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </template>

    <?php if ($activeFiltersCount > 0): ?>
        <!-- FLOATING TOAST NOTIFICATION (GEMINI.MD SECTION 16 - ZERO LAYOUT SHIFT) -->
        <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.remitToast && typeof window.remitToast.info === 'function') {
                window.remitToast.info('Menampilkan data laporan: <?= e(addslashes($activeFilterSummary)) ?>', {
                    title: 'Filter Laporan Aktif',
                    duration: 5000
                });
            }
        });
        </script>
    <?php endif; ?>

    <!-- =========================================================
         TOOLBAR MODE TAMPILAN: TABEL VS KARTU (ALPINE.JS)
         ========================================================= -->
    <?php
    $currentViewMode = get_report_view_mode();
    ?>
    <div x-data="remitReportView('<?= e($currentViewMode) ?>')" class="space-y-4">

        <div class="flex items-center justify-end">
            <!-- Toggle Tampilan Teks Antara Tabel dan Kartu (UI Kustom) -->
            <?= render_table_view_toggle('viewMode', ['current' => $currentViewMode]) ?>
        </div>

        <!-- =========================================================
             1. TAMPILAN TABEL ZEBRA (PRELINE UI + NAKED ZEBRA TABLE)
             ========================================================= -->
        <div data-report-view="table" x-show="viewMode === 'table'" <?= $currentViewMode !== 'table' ? 'style="display: none;" x-cloak' : '' ?>>
            <div class="overflow-x-auto border border-gray-200 rounded-2xl bg-white shadow-xs">
                <table class="min-w-full text-start">
                    <thead class="bg-gray-50/75 border-b border-gray-200">
                        <tr>
                            <th scope="col" class="py-3.5 px-5 text-start text-xs font-semibold font-heading text-gray-600 uppercase tracking-wider">Tanggal Transaksi</th>
                            <th scope="col" class="py-3.5 px-5 text-start text-xs font-semibold font-heading text-gray-600 uppercase tracking-wider">Berkas</th>
                            <th scope="col" class="py-3.5 px-5 text-start text-xs font-semibold font-heading text-gray-600 uppercase tracking-wider">Tim</th>
                            <th scope="col" class="py-3.5 px-5 text-end text-xs font-semibold font-heading text-gray-600 uppercase tracking-wider">Total Baris</th>
                            <th scope="col" class="py-3.5 px-5 text-start text-xs font-semibold font-heading text-gray-600 uppercase tracking-wider" style="min-width: 170px;">Progress Kirim</th>
                            <th scope="col" class="py-3.5 px-5 text-center text-xs font-semibold font-heading text-gray-600 uppercase tracking-wider">Dilewati</th>
                            <th scope="col" class="py-3.5 px-5 text-start text-xs font-semibold font-heading text-gray-600 uppercase tracking-wider">Upload Terakhir</th>
                            <th scope="col" class="py-3.5 px-5 text-end text-xs font-semibold font-heading text-gray-600 uppercase tracking-wider">Aksi</th>
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
                            <tr class="even:bg-gray-50/50 [data-no-hover]">
                                <td class="py-4 px-5 whitespace-nowrap">
                                    <div class="flex items-center gap-x-3">
                                        <div class="size-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-sm shadow-xs">
                                            <i class="bi bi-calendar-event"></i>
                                        </div>
                                        <div>
                                            <strong class="text-sm font-semibold text-gray-900 block"><?= e($dateLabel) ?></strong>
                                            <span class="text-xs text-gray-500 tabular-nums"><?= e($dateValue) ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-5 whitespace-nowrap">
                                    <?= render_tooltip('Jumlah berkas Excel pada tanggal ini', '<span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 cursor-default"><i class="bi bi-files text-gray-400"></i>' . (int) $dateCard['file_count'] . ' Berkas</span>') ?>
                                </td>
                                <td class="py-4 px-5 whitespace-nowrap">
                                    <?= render_tooltip('Jumlah tim aktif pada tanggal ini', '<span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 cursor-default"><i class="bi bi-people text-gray-400"></i>' . (int) $dateCard['team_count'] . ' Tim</span>') ?>
                                </td>
                                <td class="py-4 px-5 whitespace-nowrap text-end tabular-nums font-semibold text-gray-900">
                                    <?= number_format($total, 0, ',', '.') ?>
                                </td>
                                <td class="py-4 px-5 whitespace-nowrap">
                                    <div class="space-y-1.5">
                                        <div class="flex items-center justify-between text-xs">
                                            <span class="font-semibold text-emerald-700"><?= $progress ?>% Kirim</span>
                                            <span class="text-gray-500 tabular-nums"><?= number_format($pending, 0, ',', '.') ?> Pnd</span>
                                        </div>
                                        <div class="w-full h-1.5 bg-gray-200 rounded-full overflow-hidden flex">
                                            <div class="h-full bg-blue-600 rounded-full" style="width: <?= $progress ?>%;"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-5 whitespace-nowrap text-center">
                                    <?php if ($skipped > 0): ?>
                                        <?= render_tooltip('Baris dilewati saat import', '<span class="inline-flex items-center py-0.5 px-2 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200 cursor-default">' . number_format($skipped, 0, ',', '.') . '</span>') ?>
                                    <?php else: ?>
                                        <span class="text-xs text-gray-400">0</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-4 px-5 whitespace-nowrap text-xs text-gray-500">
                                    <?= e(format_datetime($lastUpload)) ?>
                                </td>
                                <td class="py-4 px-5 whitespace-nowrap text-end">
                                    <a class="btn-primary btn-primary-sm" href="<?= e(url($dateUrl)) ?>">
                                        <span>Buka Tim</span>
                                        <i class="bi bi-arrow-right"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <?php if ($dates === []): ?>
                            <?= render_table_empty_state(8, [
                                'action_url' => url('/reports/create?month=' . urlencode($month)),
                                'action_label' => 'Upload Excel',
                                'action_permission' => 'report.manage',
                            ]) ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- =========================================================
             2. TAMPILAN LIST CARDS (PRELINE RESPONSIVE CARDS)
             ========================================================= -->
        <div data-report-view="list" x-show="viewMode === 'list'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5" <?= $currentViewMode !== 'list' ? 'style="display: none;" x-cloak' : '' ?>>
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
                                    <strong class="text-base font-semibold font-heading text-gray-900 block truncate"><?= e($dateLabel) ?></strong>
                                </div>
                            </div>
                        </div>

                        <!-- Meta Info -->
                        <div class="flex items-center gap-2 text-xs text-gray-500 font-medium">
                            <span class="inline-flex items-center gap-1 tabular-nums">
                                <i class="bi bi-files text-gray-400"></i>
                                <?= (int) $dateCard['file_count'] ?> berkas
                            </span>
                            <span>·</span>
                            <span class="inline-flex items-center gap-1 tabular-nums">
                                <i class="bi bi-people text-gray-400"></i>
                                <?= (int) $dateCard['team_count'] ?> tim
                            </span>
                        </div>

                        <!-- KPI Grid -->
                        <div class="grid grid-cols-2 gap-2 pt-2 border-t border-gray-100 text-xs">
                            <div class="bg-gray-50/75 rounded-xl p-2.5">
                                <span class="text-gray-500 block text-[11px]">Total Baris</span>
                                <strong class="text-sm font-semibold text-gray-900 tabular-nums"><?= number_format($total, 0, ',', '.') ?></strong>
                            </div>
                            <div class="bg-emerald-50/50 rounded-xl p-2.5 border border-emerald-100">
                                <span class="text-emerald-700 block text-[11px]">Terkirim</span>
                                <strong class="text-sm font-semibold text-emerald-800 tabular-nums"><?= number_format($sent, 0, ',', '.') ?></strong>
                            </div>
                            <div class="bg-amber-50/50 rounded-xl p-2.5 border border-amber-100">
                                <span class="text-amber-700 block text-[11px]">Pending</span>
                                <strong class="text-sm font-semibold text-amber-800 tabular-nums"><?= number_format($pending, 0, ',', '.') ?></strong>
                            </div>
                            <div class="bg-gray-50/75 rounded-xl p-2.5">
                                <span class="text-gray-500 block text-[11px]">Dilewati</span>
                                <strong class="text-sm font-semibold text-gray-700 tabular-nums"><?= number_format($skipped, 0, ',', '.') ?></strong>
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        <div class="space-y-1.5 pt-1">
                            <div class="flex items-center justify-between text-xs font-medium">
                                <span class="text-emerald-700 font-semibold tabular-nums"><?= $progress ?>% Terkirim</span>
                                <span class="text-gray-500 tabular-nums text-[11px]"><?= number_format($sent, 0, ',', '.') ?> dari <?= number_format($total, 0, ',', '.') ?></span>
                            </div>
                            <div class="w-full h-1.5 bg-gray-200 rounded-full overflow-hidden flex">
                                <div class="h-full bg-blue-600 rounded-full" style="width: <?= $progress ?>%;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Card Footer -->
                    <div class="pt-3 border-t border-gray-100 flex items-center justify-between gap-2">
                        <span class="text-[11px] text-gray-500 truncate tabular-nums">
                            Upload: <?= e(format_datetime($lastUpload)) ?>
                        </span>
                        <a class="btn-primary btn-primary-sm" href="<?= e(url($dateUrl)) ?>">
                            <span>Buka Tim</span>
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>

            <?php if ($dates === []): ?>
                <?= render_empty_state([
                    'action_url' => url('/reports/create?month=' . urlencode($month)),
                    'action_label' => 'Upload Excel',
                    'action_permission' => 'report.manage',
                ]) ?>
            <?php endif; ?>
        </div>

    </div>

</div>
