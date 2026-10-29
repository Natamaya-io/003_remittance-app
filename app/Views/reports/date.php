<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| LAPORAN - TINGKAT TIM DAN REPORT (PRELINE UI & ALPINE.JS NATIVE)
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

$activeFilterList = [];
$currTeam = (string) ($filters['team'] ?? '');
$currTeamLabel = $currTeam !== '' ? $currTeam : 'Semua tim';

$currDelivery = (string) ($filters['delivery_status'] ?? '');
$deliveryLabels = ['PENDING' => 'Pending', 'SENT' => 'Terkirim'];
$currDeliveryLabel = $deliveryLabels[$currDelivery] ?? 'Semua status';

$currResult = (string) ($filters['result_status'] ?? '');
$resultLabels = ['PENDING' => 'Pending', 'SUCCESS' => 'Success', 'FAILED' => 'Failed'];
$currResultLabel = $resultLabels[$currResult] ?? 'Semua hasil';

if ($currTeam !== '') {
    $activeFilterList[] = 'Tim ' . $currTeam;
}
if ($currDelivery !== '') {
    $activeFilterList[] = 'Status ' . $currDeliveryLabel;
}
if ($currResult !== '') {
    $activeFilterList[] = 'Hasil ' . $currResultLabel;
}
$activeFiltersCount = count($activeFilterList);
$activeFilterSummary = implode(' · ', $activeFilterList);
?>

<div class="max-w-7xl mx-auto space-y-6" x-data="{ openFilterModal: false }" x-effect="if (openFilterModal) $nextTick(() => { window.HSStaticMethods && window.HSStaticMethods.autoInit(); })">

    <!-- =========================================================
         PAGE ACTIONS (PURE NAKED ARCHITECTURE)
         ========================================================= -->
    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
        <a class="py-2 px-3.5 inline-flex items-center gap-x-1.5 text-xs font-medium rounded-lg border border-gray-200 bg-white text-gray-700 shadow-xs hover:bg-gray-50 focus:outline-none focus:bg-gray-50 transition-colors" href="<?= e(url('/reports/month/' . $monthKey)) ?>">
            <i class="bi bi-arrow-left"></i>
            Daftar Tanggal
        </a>

        <div class="flex items-center gap-2 flex-wrap">
            <?= render_reports_search_bar([
                'action' => url('/reports/search'),
                'placeholder' => 'Cari transaksi...',
                'query' => (string) ($filters['q'] ?? ''),
                'extraParams' => ['month' => $monthKey, 'date_from' => $date, 'date_to' => $date],
            ]) ?>

            <!-- Tombol Filter Modal (Selaras dengan Halaman Laporan) -->
            <button
                type="button"
                @click="openFilterModal = true"
                class="btn-primary"
                aria-label="Buka Filter Laporan Tanggal <?= e($dateLabel) ?>"
                title="Filter berdasarkan tim, status kirim, dan hasil"
            >
                <i class="bi bi-sliders text-sm"></i>
                <span>Filter</span>
                <?php if ($activeFiltersCount > 0): ?>
                    <span class="btn-filter-badge" aria-label="<?= $activeFiltersCount ?> filter aktif"><?= $activeFiltersCount ?></span>
                <?php endif; ?>
            </button>

            <!-- Tombol Export Semua (Selaras dengan Tombol Upload Excel) -->
            <a class="btn-primary" href="<?= e(url('/reports/date/' . $date . '/export')) ?>">
                <i class="bi bi-file-earmark-excel text-sm"></i>
                <span>Export Semua</span>
            </a>

            <?php if (can('report.manage')): ?>
                <a class="btn-primary" href="<?= e(url('/reports/create?' . http_build_query(['month' => $monthKey, 'date' => $date]))) ?>">
                    <i class="bi bi-cloud-arrow-up text-sm"></i>
                    <span>Upload Excel</span>
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- =========================================================
         MODAL FILTER TRANSAKSI LAPORAN TANGGAL (ALPINE.JS TELEPORTED TO BODY)
         ========================================================= -->
    <template x-teleport="body">
        <div
            x-show="openFilterModal"
            x-cloak
            class="fixed inset-0 z-[9999] overflow-y-auto"
            role="dialog"
            aria-modal="true"
            aria-labelledby="modal-date-filter-title"
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
                                <strong id="modal-date-filter-title" class="text-sm font-semibold text-gray-900 block">Filter Laporan <?= e($dateLabel) ?></strong>
                                <?= render_tooltip(
                                    'Saring berkas laporan tanggal ' . e($dateLabel) . ' berdasarkan tim, status kirim, dan status hasil.',
                                    '<span class="hs-tooltip-toggle info-toggle-btn cursor-help" aria-label="Info filter tanggal"><i class="bi bi-info-circle text-[10px] leading-none"></i></span>',
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
                    <form method="get" action="<?= e(url('/reports/date/' . $date)) ?>" class="m-0 p-0">
                        <?php if (($filters['q'] ?? '') !== ''): ?>
                            <input type="hidden" name="q" value="<?= e($filters['q']) ?>">
                        <?php endif; ?>

                        <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Dropdown Tim (Full Width) -->
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Tim</label>
                                <div class="custom-select-root relative" :class="{ 'z-30': open }" x-data="{ open: false, selected: '<?= e(addslashes($currTeam)) ?>', label: '<?= e(addslashes($currTeamLabel)) ?>' }" @click.outside="open = false">
                                    <input type="hidden" name="team" :value="selected">
                                    <button
                                        type="button"
                                        @click="open = !open"
                                        class="dropdown-toggle"
                                        :aria-expanded="open.toString()"
                                        aria-label="Filter Tim"
                                    >
                                        <i class="bi bi-people text-gray-400"></i>
                                        <span class="truncate text-gray-900" x-text="label"></span>
                                        <i class="bi bi-chevron-down text-xs text-gray-400 ml-auto transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                                    </button>
                                    <div
                                        x-show="open"
                                        x-cloak
                                        x-transition:leave="transition ease-in duration-100"
                                        x-transition:leave-start="opacity-100"
                                        x-transition:leave-end="opacity-0"
                                        :class="{ 'block': open, 'hidden': !open }"
                                        class="dropdown-menu absolute left-0 right-0 top-full mt-1.5 max-h-60 overflow-y-auto z-50 bg-white"
                                        style="display: none;"
                                        role="listbox"
                                    >
                                        <button
                                            type="button"
                                            class="dropdown-item justify-between"
                                            :class="{ 'active': selected === '' }"
                                            @click="selected = ''; label = 'Semua tim'; open = false"
                                        >
                                            <span>Semua tim</span>
                                            <i x-show="selected === ''" class="bi bi-check2 text-blue-600 text-sm"></i>
                                        </button>
                                        <?php foreach ($teams as $team): ?>
                                            <?php $teamValue = trim((string) $team); if ($teamValue === '') continue; ?>
                                            <button
                                                type="button"
                                                class="dropdown-item justify-between"
                                                :class="{ 'active': selected === '<?= e(addslashes($teamValue)) ?>' }"
                                                @click="selected = '<?= e(addslashes($teamValue)) ?>'; label = '<?= e(addslashes($teamValue)) ?>'; open = false"
                                            >
                                                <span class="truncate"><?= e($teamValue) ?></span>
                                                <i x-show="selected === '<?= e(addslashes($teamValue)) ?>'" class="bi bi-check2 text-blue-600 text-sm"></i>
                                            </button>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Dropdown Status Kirim -->
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Status Kirim</label>
                                <div class="custom-select-root relative" :class="{ 'z-20': open }" x-data="{ open: false, selected: '<?= e(addslashes($currDelivery)) ?>', label: '<?= e(addslashes($currDeliveryLabel)) ?>' }" @click.outside="open = false">
                                    <input type="hidden" name="delivery_status" :value="selected">
                                    <button
                                        type="button"
                                        @click="open = !open"
                                        class="dropdown-toggle"
                                        :aria-expanded="open.toString()"
                                        aria-label="Filter Status Kirim"
                                    >
                                        <i class="bi bi-send text-gray-400"></i>
                                        <span class="truncate text-gray-900" x-text="label"></span>
                                        <i class="bi bi-chevron-down text-xs text-gray-400 ml-auto transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                                    </button>
                                    <div
                                        x-show="open"
                                        x-cloak
                                        x-transition:leave="transition ease-in duration-100"
                                        x-transition:leave-start="opacity-100"
                                        x-transition:leave-end="opacity-0"
                                        :class="{ 'block': open, 'hidden': !open }"
                                        class="dropdown-menu absolute left-0 right-0 top-full mt-1.5 max-h-60 overflow-y-auto z-50 bg-white"
                                        style="display: none;"
                                        role="listbox"
                                    >
                                        <button
                                            type="button"
                                            class="dropdown-item justify-between"
                                            :class="{ 'active': selected === '' }"
                                            @click="selected = ''; label = 'Semua status'; open = false"
                                        >
                                            <span>Semua status</span>
                                            <i x-show="selected === ''" class="bi bi-check2 text-blue-600 text-sm"></i>
                                        </button>
                                        <button
                                            type="button"
                                            class="dropdown-item justify-between"
                                            :class="{ 'active': selected === 'PENDING' }"
                                            @click="selected = 'PENDING'; label = 'Pending'; open = false"
                                        >
                                            <span>Pending</span>
                                            <i x-show="selected === 'PENDING'" class="bi bi-check2 text-blue-600 text-sm"></i>
                                        </button>
                                        <button
                                            type="button"
                                            class="dropdown-item justify-between"
                                            :class="{ 'active': selected === 'SENT' }"
                                            @click="selected = 'SENT'; label = 'Terkirim'; open = false"
                                        >
                                            <span>Terkirim</span>
                                            <i x-show="selected === 'SENT'" class="bi bi-check2 text-blue-600 text-sm"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Dropdown Hasil -->
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Status Hasil</label>
                                <div class="custom-select-root relative" :class="{ 'z-20': open }" x-data="{ open: false, selected: '<?= e(addslashes($currResult)) ?>', label: '<?= e(addslashes($currResultLabel)) ?>' }" @click.outside="open = false">
                                    <input type="hidden" name="result_status" :value="selected">
                                    <button
                                        type="button"
                                        @click="open = !open"
                                        class="dropdown-toggle"
                                        :aria-expanded="open.toString()"
                                        aria-label="Filter Hasil"
                                    >
                                        <i class="bi bi-check-circle text-gray-400"></i>
                                        <span class="truncate text-gray-900" x-text="label"></span>
                                        <i class="bi bi-chevron-down text-xs text-gray-400 ml-auto transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                                    </button>
                                    <div
                                        x-show="open"
                                        x-cloak
                                        x-transition:leave="transition ease-in duration-100"
                                        x-transition:leave-start="opacity-100"
                                        x-transition:leave-end="opacity-0"
                                        :class="{ 'block': open, 'hidden': !open }"
                                        class="dropdown-menu absolute left-0 right-0 top-full mt-1.5 max-h-60 overflow-y-auto z-50 bg-white"
                                        style="display: none;"
                                        role="listbox"
                                    >
                                        <button
                                            type="button"
                                            class="dropdown-item justify-between"
                                            :class="{ 'active': selected === '' }"
                                            @click="selected = ''; label = 'Semua hasil'; open = false"
                                        >
                                            <span>Semua hasil</span>
                                            <i x-show="selected === ''" class="bi bi-check2 text-blue-600 text-sm"></i>
                                        </button>
                                        <button
                                            type="button"
                                            class="dropdown-item justify-between"
                                            :class="{ 'active': selected === 'PENDING' }"
                                            @click="selected = 'PENDING'; label = 'Pending'; open = false"
                                        >
                                            <span>Pending</span>
                                            <i x-show="selected === 'PENDING'" class="bi bi-check2 text-blue-600 text-sm"></i>
                                        </button>
                                        <button
                                            type="button"
                                            class="dropdown-item justify-between"
                                            :class="{ 'active': selected === 'SUCCESS' }"
                                            @click="selected = 'SUCCESS'; label = 'Success'; open = false"
                                        >
                                            <span>Success</span>
                                            <i x-show="selected === 'SUCCESS'" class="bi bi-check2 text-blue-600 text-sm"></i>
                                        </button>
                                        <button
                                            type="button"
                                            class="dropdown-item justify-between"
                                            :class="{ 'active': selected === 'FAILED' }"
                                            @click="selected = 'FAILED'; label = 'Failed'; open = false"
                                        >
                                            <span>Failed</span>
                                            <i x-show="selected === 'FAILED'" class="bi bi-check2 text-blue-600 text-sm"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Footer -->
                        <div class="px-5 py-3.5 bg-gray-50/70 border-t border-gray-100 flex items-center justify-between gap-3 rounded-b-2xl">
                            <a
                                class="py-2 px-3.5 inline-flex justify-center items-center text-xs font-medium rounded-lg border border-gray-200 bg-white text-gray-700 shadow-xs hover:bg-gray-50 focus:outline-none transition-colors"
                                href="<?= e(url('/reports/date/' . $date)) ?>"
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
                            <th scope="col" class="py-3.5 px-5 text-start text-xs font-semibold font-heading text-gray-600 uppercase tracking-wider">Batch &amp; Tim</th>
                            <th scope="col" class="py-3.5 px-5 text-start text-xs font-semibold font-heading text-gray-600 uppercase tracking-wider">Berkas &amp; Nama Report</th>
                            <th scope="col" class="py-3.5 px-5 text-end text-xs font-semibold font-heading text-gray-600 uppercase tracking-wider">Total Baris</th>
                            <th scope="col" class="py-3.5 px-5 text-start text-xs font-semibold font-heading text-gray-600 uppercase tracking-wider" style="min-width: 170px;">Status Kirim</th>
                            <th scope="col" class="py-3.5 px-5 text-start text-xs font-semibold font-heading text-gray-600 uppercase tracking-wider">Hasil Eksekusi</th>
                            <th scope="col" class="py-3.5 px-5 text-end text-xs font-semibold font-heading text-gray-600 uppercase tracking-wider">Aksi</th>
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
                            <tr class="even:bg-[#F5F2F2] [data-no-hover]">
                                <td class="py-4 px-5 whitespace-nowrap">
                                    <div class="space-y-1">
                                        <div class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-900 tabular-nums">
                                            <i class="bi bi-layers text-gray-400" aria-hidden="true"></i>
                                            <span>Batch #<?= (int) $batch['id'] ?></span>
                                        </div>
                                        <div class="flex items-center gap-1.5 text-xs font-medium text-gray-600">
                                            <i class="bi bi-people text-gray-400" aria-hidden="true"></i>
                                            <span><?= e(strtoupper((string) ($batch['effective_team'] ?? 'Tanpa Tim'))) ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-5 whitespace-nowrap">
                                    <div>
                                        <strong class="text-sm font-semibold text-gray-900 block truncate max-w-xs" title="<?= e($batch['source_filename']) ?>">
                                            <i class="bi bi-file-earmark-excel me-1"></i>
                                            <?= e($batch['source_filename']) ?>
                                        </strong>
                                        <span class="text-xs text-gray-500 block truncate max-w-xs mt-0.5">
                                            <?= e($batch['name']) ?>
                                        </span>
                                    </div>
                                </td>
                                <td class="py-4 px-5 whitespace-nowrap text-end tabular-nums font-semibold text-gray-900">
                                    <?= number_format($total, 0, ',', '.') ?>
                                </td>
                                <td class="py-4 px-5 whitespace-nowrap">
                                    <div class="space-y-1.5">
                                        <div class="flex items-center justify-between text-xs">
                                            <span class="font-semibold text-emerald-700"><?= number_format($sent, 0, ',', '.') ?> Kirim</span>
                                            <span class="text-gray-500 tabular-nums"><?= number_format($pending, 0, ',', '.') ?> Pnd</span>
                                        </div>
                                        <div class="w-full h-1.5 bg-gray-200 rounded-full overflow-hidden flex">
                                            <div class="h-full bg-blue-600 rounded-full" style="width: <?= $progress ?>%;"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-5 whitespace-nowrap">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <?= render_tooltip('Berhasil Diproses', '<span class="inline-flex items-center gap-1 py-0.5 px-2 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200"><i class="bi bi-check-lg"></i>' . $success . '</span>') ?>
                                        <?php if ($failed > 0): ?>
                                            <?= render_tooltip('Gagal / Retur', '<span class="inline-flex items-center gap-1 py-0.5 px-2 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-200"><i class="bi bi-x-lg"></i>' . $failed . '</span>') ?>
                                        <?php endif; ?>
                                        <?php if ($skipped > 0): ?>
                                            <?= render_tooltip('Baris Dilewati', '<span class="inline-flex items-center py-0.5 px-2 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">' . $skipped . ' skip</span>') ?>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="py-4 px-5 whitespace-nowrap text-end">
                                    <div class="inline-flex items-center gap-1.5 justify-end">
                                        <a class="btn-primary btn-primary-sm" href="<?= e(url('/reports/' . $batch['id'])) ?>">
                                            <i class="bi bi-table"></i>
                                            <span>Buka</span>
                                        </a>
                                        <?= render_tooltip('Export Excel', '<a class="btn-action-icon" href="' . e(url('/reports/' . $batch['id'] . '/export')) . '" aria-label="Export Excel Batch #' . (int) $batch['id'] . '"><i class="bi bi-file-earmark-excel"></i></a>') ?>
                                        <?php if (can('report.manage')): ?>
                                            <?= render_tooltip('Edit Metadata', '<a class="btn-action-icon" href="' . e(url('/reports/' . $batch['id'] . '/edit')) . '" aria-label="Edit Metadata Batch #' . (int) $batch['id'] . '"><i class="bi bi-pencil"></i></a>') ?>
                                            <form class="inline-block m-0" method="post" action="<?= e(url('/reports/' . $batch['id'])) ?>" data-confirm="Hapus report ini beserta seluruh barisnya?">
                                                <?= csrf_field() ?>
                                                <?= method_field('DELETE') ?>
                                                <input type="hidden" name="return_to" value="<?= e($returnPath) ?>">
                                                <?= render_tooltip('Hapus Batch', '<button class="btn-action-icon btn-action-icon-danger" type="submit" aria-label="Hapus Batch #' . (int) $batch['id'] . '"><i class="bi bi-trash"></i></button>') ?>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <?php if ($batches === []): ?>
                            <?= render_table_empty_state(6, [
                                'action_url' => url('/reports/create?' . http_build_query(['month' => $monthKey, 'date' => $date])),
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
                <article class="bg-white border border-gray-200 rounded-2xl p-5 shadow-xs hover:shadow-md transition-shadow duration-200 flex flex-col justify-between space-y-4">
                    <div class="space-y-3">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2 mb-1.5 flex-wrap">
                                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-900 tabular-nums">
                                        <i class="bi bi-layers text-gray-400" aria-hidden="true"></i>
                                        <span>Batch #<?= (int) $batch['id'] ?></span>
                                    </span>
                                    <span class="text-xs text-gray-300">·</span>
                                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-700">
                                        <i class="bi bi-people text-gray-400" aria-hidden="true"></i>
                                        <span><?= e(strtoupper((string) ($batch['effective_team'] ?? 'Tanpa Tim'))) ?></span>
                                    </span>
                                </div>
                                <strong class="text-sm font-semibold font-heading text-gray-900 block truncate" title="<?= e($batch['source_filename']) ?>">
                                    <?= e($batch['source_filename']) ?>
                                </strong>
                                <span class="text-xs text-gray-500 block truncate mt-0.5">
                                    <?= e($batch['name']) ?>
                                </span>
                            </div>
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
                            <div class="bg-emerald-50/50 rounded-xl p-2.5 border border-emerald-100">
                                <span class="text-emerald-700 block text-[11px]">Success</span>
                                <strong class="text-sm font-semibold text-emerald-800 tabular-nums"><?= number_format($success, 0, ',', '.') ?></strong>
                            </div>
                            <div class="bg-red-50/50 rounded-xl p-2.5 border border-red-100">
                                <span class="text-red-700 block text-[11px]">Failed</span>
                                <strong class="text-sm font-semibold text-red-800 tabular-nums"><?= number_format($failed, 0, ',', '.') ?></strong>
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

                    <!-- Card Footer Actions -->
                    <div class="pt-3 border-t border-gray-100 flex items-center justify-between gap-2">
                        <div class="flex items-center gap-1.5">
                            <?= render_tooltip('Export Excel', '<a class="btn-action-icon" href="' . e(url('/reports/' . $batch['id'] . '/export')) . '" aria-label="Export Excel Batch #' . (int) $batch['id'] . '"><i class="bi bi-file-earmark-excel"></i></a>') ?>
                            <?php if (can('report.manage')): ?>
                                <?= render_tooltip('Edit Metadata', '<a class="btn-action-icon" href="' . e(url('/reports/' . $batch['id'] . '/edit')) . '" aria-label="Edit Metadata Batch #' . (int) $batch['id'] . '"><i class="bi bi-pencil"></i></a>') ?>
                                <form class="inline-block m-0" method="post" action="<?= e(url('/reports/' . $batch['id'])) ?>" data-confirm="Hapus report ini beserta seluruh barisnya?">
                                    <?= csrf_field() ?>
                                    <?= method_field('DELETE') ?>
                                    <input type="hidden" name="return_to" value="<?= e($returnPath) ?>">
                                    <?= render_tooltip('Hapus Batch', '<button class="btn-action-icon btn-action-icon-danger" type="submit" aria-label="Hapus Batch #' . (int) $batch['id'] . '"><i class="bi bi-trash"></i></button>') ?>
                                </form>
                            <?php endif; ?>
                        </div>

                        <a class="btn-primary btn-primary-sm" href="<?= e(url('/reports/' . $batch['id'])) ?>">
                            <i class="bi bi-table"></i>
                            <span>Buka Report</span>
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>

            <?php if ($batches === []): ?>
                <?= render_empty_state([
                    'action_url' => url('/reports/create?' . http_build_query(['month' => $monthKey, 'date' => $date])),
                    'action_label' => 'Upload Excel',
                    'action_permission' => 'report.manage',
                ]) ?>
            <?php endif; ?>
        </div>

    </div>

</div>
