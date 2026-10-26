<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| LAPORAN - INDEX (PRELINE UI & ALPINE.JS NATIVE)
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

<div class="max-w-7xl mx-auto space-y-6" x-data="{ openFilterModal: false }">

    <!-- =========================================================
         PAGE ACTIONS (PURE NAKED ARCHITECTURE)
         ========================================================= -->
    <div class="flex items-center justify-end gap-2">
        <?= render_reports_search_bar([
            'action' => url('/reports/search'),
            'placeholder' => 'Cari transaksi...',
            'query' => (string) ($filters['q'] ?? ''),
        ]) ?>

        <!-- Tombol Filter Modal (Disamping Upload Excel) -->
        <button
            type="button"
            @click="openFilterModal = true"
            class="py-2 px-3.5 inline-flex items-center gap-x-1.5 text-xs font-semibold rounded-lg border border-gray-200 bg-white text-gray-700 shadow-2xs hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition-colors cursor-pointer select-none shrink-0"
            aria-label="Buka Filter Lanjutan"
        >
            <i class="bi bi-sliders text-sm text-gray-500"></i>
            <span>Filter</span>
            <?php if (!empty($activeAdvancedFilters)): ?>
                <span class="inline-flex items-center justify-center size-4 text-[10px] font-bold text-white bg-blue-600 rounded-full">
                    <?= count($activeAdvancedFilters) ?>
                </span>
            <?php endif; ?>
        </button>

        <?php if (can('report.manage')): ?>
            <a class="btn btn-primary" href="<?= e(url('/reports/create')) ?>">
                <i class="bi bi-cloud-arrow-up text-sm"></i>
                Upload Excel
            </a>
        <?php endif; ?>
    </div>

    <!-- =========================================================
         MODAL FILTER TRANSAKSI LAPORAN (ALPINE.JS MODAL)
         ========================================================= -->
    <div
        x-show="openFilterModal"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-xs overflow-y-auto"
        @click.self="openFilterModal = false"
        @keydown.escape.window="openFilterModal = false"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        role="dialog"
        aria-modal="true"
        aria-labelledby="modal-filter-title"
        style="display: none;"
    >
        <div
            class="bg-white rounded-2xl max-w-xl w-full shadow-2xl border border-gray-200 overflow-hidden text-left relative my-8"
        >
            <!-- Modal Header -->
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 bg-white">
                <div class="flex items-center gap-2.5">
                    <span class="size-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-sm shrink-0">
                        <i class="bi bi-sliders"></i>
                    </span>
                    <div>
                        <strong id="modal-filter-title" class="text-sm font-bold text-gray-900 block">Filter Transaksi Laporan</strong>
                        <span class="text-xs text-gray-500 block">Saring data berdasarkan parameter di bawah ini.</span>
                    </div>
                </div>
                <button
                    type="button"
                    @click="openFilterModal = false"
                    class="size-8 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 flex items-center justify-center transition-colors cursor-pointer"
                    aria-label="Tutup modal"
                >
                    <i class="bi bi-x-lg text-xs"></i>
                </button>
            </div>

            <!-- Modal Form Body -->
            <form method="get" action="<?= e(url('/reports')) ?>" class="m-0 p-0">
                <?php if (($filters['q'] ?? '') !== ''): ?>
                    <input type="hidden" name="q" value="<?= e($filters['q']) ?>">
                <?php endif; ?>

                <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Bulan report</label>
                        <?= render_datepicker('month', (string) ($filters['month'] ?? ''), ['mode' => 'month', 'placeholder' => 'Semua bulan']) ?>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Tim</label>
                        <?php
                        $currTeam = (string) ($filters['team'] ?? '');
                        $currTeamLabel = $currTeam !== '' ? $currTeam : 'Semua tim';
                        ?>
                        <div class="custom-select-root" x-data="{ open: false, selected: '<?= e(addslashes($currTeam)) ?>', label: '<?= e(addslashes($currTeamLabel)) ?>' }" @click.outside="open = false">
                            <input type="hidden" name="team" :value="selected">
                            <button
                                type="button"
                                @click="open = !open"
                                class="dropdown-toggle"
                                :aria-expanded="open.toString()"
                            >
                                <span class="truncate" x-text="label"></span>
                                <i class="bi bi-chevron-down text-xs text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                            </button>
                            <div
                                x-show="open"
                                x-cloak
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-start="opacity-100"
                                x-transition:leave-end="opacity-0"
                                :class="{ 'block': open, 'hidden': !open }"
                                class="dropdown-menu"
                                style="display: none;"
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

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Tanggal mulai</label>
                        <?= render_datepicker('date_from', (string) ($filters['date_from'] ?? ''), ['placeholder' => 'Pilih tanggal mulai']) ?>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Tanggal akhir</label>
                        <?= render_datepicker('date_to', (string) ($filters['date_to'] ?? ''), ['placeholder' => 'Pilih tanggal akhir']) ?>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Status kirim</label>
                        <?php
                        $currDelivery = (string) ($filters['delivery_status'] ?? '');
                        $deliveryLabels = ['PENDING' => 'Pending', 'SENT' => 'Terkirim'];
                        $currDeliveryLabel = $deliveryLabels[$currDelivery] ?? 'Semua status';
                        ?>
                        <div class="custom-select-root" x-data="{ open: false, selected: '<?= e(addslashes($currDelivery)) ?>', label: '<?= e(addslashes($currDeliveryLabel)) ?>' }" @click.outside="open = false">
                            <input type="hidden" name="delivery_status" :value="selected">
                            <button
                                type="button"
                                @click="open = !open"
                                class="dropdown-toggle"
                                :aria-expanded="open.toString()"
                            >
                                <span class="truncate" x-text="label"></span>
                                <i class="bi bi-chevron-down text-xs text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                            </button>
                            <div
                                x-show="open"
                                x-cloak
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-start="opacity-100"
                                x-transition:leave-end="opacity-0"
                                :class="{ 'block': open, 'hidden': !open }"
                                class="dropdown-menu"
                                style="display: none;"
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

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Hasil</label>
                        <?php
                        $currResult = (string) ($filters['result_status'] ?? '');
                        $resultLabels = ['PENDING' => 'Pending', 'SUCCESS' => 'Success', 'FAILED' => 'Failed'];
                        $currResultLabel = $resultLabels[$currResult] ?? 'Semua hasil';
                        ?>
                        <div class="custom-select-root" x-data="{ open: false, selected: '<?= e(addslashes($currResult)) ?>', label: '<?= e(addslashes($currResultLabel)) ?>' }" @click.outside="open = false">
                            <input type="hidden" name="result_status" :value="selected">
                            <button
                                type="button"
                                @click="open = !open"
                                class="dropdown-toggle"
                                :aria-expanded="open.toString()"
                            >
                                <span class="truncate" x-text="label"></span>
                                <i class="bi bi-chevron-down text-xs text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                            </button>
                            <div
                                x-show="open"
                                x-cloak
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-start="opacity-100"
                                x-transition:leave-end="opacity-0"
                                :class="{ 'block': open, 'hidden': !open }"
                                class="dropdown-menu"
                                style="display: none;"
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
                <div class="px-5 py-3.5 bg-gray-50/70 border-t border-gray-100 flex items-center justify-between gap-3">
                    <a
                        class="py-2 px-3.5 inline-flex justify-center items-center text-xs font-medium rounded-lg border border-gray-200 bg-white text-gray-700 shadow-xs hover:bg-gray-50 focus:outline-none transition-colors"
                        href="<?= e(url('/reports')) ?>"
                    >
                        Reset Filter
                    </a>

                    <div class="flex items-center gap-2">
                        <button
                            type="button"
                            @click="openFilterModal = false"
                            class="py-2 px-3.5 inline-flex justify-center items-center text-xs font-medium rounded-lg border border-gray-200 bg-white text-gray-700 shadow-xs hover:bg-gray-50 focus:outline-none transition-colors cursor-pointer"
                        >
                            Batal
                        </button>
                        <button
                            class="btn btn-primary py-2 px-4 inline-flex justify-center items-center gap-x-1.5 text-xs font-semibold rounded-lg focus:outline-none"
                            type="submit"
                        >
                            <i class="bi bi-funnel"></i>
                            Terapkan Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php if ($activeAdvancedFilters !== []): ?>
        <!-- ALERT STATUS: FILTER LANJUTAN AKTIF -->
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
                    <strong class="alert-title">Filter Lanjutan Aktif</strong>
                    <div class="alert-message">
                        Menampilkan data dengan <?= count($activeAdvancedFilters) ?> parameter filter aktif.
                        <a href="<?= e(url('/reports')) ?>" class="underline font-semibold ml-1 hover:text-blue-900">Reset Semua</a>
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
         TOOLBAR MODE TAMPILAN: TABEL VS KARTU (ALPINE.JS)
         ========================================================= -->
    <div x-data="{ viewMode: localStorage.getItem('remitgo_report_view_mode') || 'table' }"
         x-init="$watch('viewMode', val => localStorage.setItem('remitgo_report_view_mode', val))"
         class="space-y-4">

        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-gray-700">Periode Bulan</span>
                <span class="inline-flex items-center py-0.5 px-2.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200 shadow-xs">
                    <?= count($months) ?> Bulan Terdaftar
                </span>
            </div>

            <div class="flex items-center gap-2">
                <!-- Custom Alpine.js Dropdown -->
                <div class="relative inline-flex" x-data="{ openOptions: false }" @click.outside="openOptions = false">
                    <button
                        type="button"
                        @click="openOptions = !openOptions"
                        class="dropdown-toggle"
                        :aria-expanded="openOptions.toString()"
                        aria-label="Menu Opsi Tampilan"
                    >
                        <i class="bi bi-sliders"></i>
                        <span>Tampilan</span>
                        <span class="text-gray-300 font-normal">·</span>
                        <span class="text-[11px] text-gray-500 font-medium capitalize" x-text="viewMode === 'table' ? 'Tabel' : 'Kartu'"></span>
                        <i class="bi bi-chevron-down text-xs text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': openOptions }"></i>
                    </button>

                    <div
                        x-show="openOptions"
                        x-cloak
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        class="dropdown-menu min-w-44 start-auto end-0 top-full mt-1.5 z-50 bg-white"
                        style="display: none;"
                    >
                        <span class="dropdown-header">Mode Tampilan</span>
                        <button type="button" @click="viewMode = 'table'; openOptions = false" class="dropdown-item justify-between" :class="viewMode === 'table' ? 'active font-semibold' : ''">
                            <span class="flex items-center gap-x-2.5">
                                <i class="bi bi-table text-blue-600"></i>
                                Tampilan Tabel
                            </span>
                            <i x-show="viewMode === 'table'" class="bi bi-check2 text-blue-600 text-sm"></i>
                        </button>
                        <button type="button" @click="viewMode = 'list'; openOptions = false" class="dropdown-item justify-between" :class="viewMode === 'list' ? 'active font-semibold' : ''">
                            <span class="flex items-center gap-x-2.5">
                                <i class="bi bi-grid text-gray-500"></i>
                                Tampilan Kartu
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
                <table class="min-w-full text-start">
                    <thead class="bg-gray-50/75 border-b border-gray-200">
                        <tr>
                            <th scope="col" class="py-3.5 px-5 text-start text-xs font-semibold font-heading text-gray-600 uppercase tracking-wider">Periode Bulan</th>
                            <th scope="col" class="py-3.5 px-5 text-start text-xs font-semibold font-heading text-gray-600 uppercase tracking-wider">Berkas</th>
                            <th scope="col" class="py-3.5 px-5 text-start text-xs font-semibold font-heading text-gray-600 uppercase tracking-wider">Hari Aktif</th>
                            <th scope="col" class="py-3.5 px-5 text-start text-xs font-semibold font-heading text-gray-600 uppercase tracking-wider">Tim</th>
                            <th scope="col" class="py-3.5 px-5 text-end text-xs font-semibold font-heading text-gray-600 uppercase tracking-wider">Total Baris</th>
                            <th scope="col" class="py-3.5 px-5 text-start text-xs font-semibold font-heading text-gray-600 uppercase tracking-wider" style="min-width: 170px;">Progress Kirim</th>
                            <th scope="col" class="py-3.5 px-5 text-center text-xs font-semibold font-heading text-gray-600 uppercase tracking-wider">Dilewati</th>
                            <th scope="col" class="py-3.5 px-5 text-start text-xs font-semibold font-heading text-gray-600 uppercase tracking-wider">Upload Terakhir</th>
                            <th scope="col" class="py-3.5 px-5 text-end text-xs font-semibold font-heading text-gray-600 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
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
                            <tr class="even:bg-gray-50/80 [data-no-hover]">
                                <td class="py-4 px-5 whitespace-nowrap">
                                    <div class="flex items-center gap-x-3">
                                        <div class="size-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-sm shadow-xs">
                                            <i class="bi bi-calendar3"></i>
                                        </div>
                                        <div>
                                            <strong class="text-sm font-semibold text-gray-900 block"><?= e($monthLabel) ?></strong>
                                            <span class="text-xs text-gray-500 tabular-nums"><?= e($monthKey) ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-5 whitespace-nowrap">
                                    <?= render_tooltip('Jumlah berkas Excel diimpor', '<span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 cursor-default"><i class="bi bi-files text-gray-400"></i>' . number_format($fileCount, 0, ',', '.') . ' Berkas</span>') ?>
                                </td>
                                <td class="py-4 px-5 whitespace-nowrap">
                                    <?= render_tooltip('Hari transaksi aktif dalam bulan ini', '<span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 cursor-default"><i class="bi bi-calendar-event text-gray-400"></i>' . number_format($dateCount, 0, ',', '.') . ' Hari</span>') ?>
                                </td>
                                <td class="py-4 px-5 whitespace-nowrap">
                                    <?= render_tooltip('Jumlah tim operasional terdaftar', '<span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 cursor-default"><i class="bi bi-people text-gray-400"></i>' . number_format($teamCount, 0, ',', '.') . ' Tim</span>') ?>
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
                                    <a class="btn btn-primary btn-primary-sm" href="<?= e(url($monthUrl)) ?>">
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
                        <a class="btn btn-primary btn-primary-sm" href="<?= e(url($monthUrl)) ?>">
                            Buka Bulan
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
                        <a class="btn btn-primary py-2 px-3.5 inline-flex items-center gap-x-2 text-xs font-semibold rounded-lg focus:outline-none" href="<?= e(url('/reports/create')) ?>">
                            <i class="bi bi-cloud-arrow-up text-sm"></i>
                            Upload Report
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

    </div>

</div>
