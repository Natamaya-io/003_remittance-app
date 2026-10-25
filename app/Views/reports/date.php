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
?>

<div class="max-w-7xl mx-auto space-y-6">

    <!-- =========================================================
         PAGE ACTIONS
         ========================================================= -->
    <div class="flex flex-wrap items-center justify-end gap-2">
        <a class="py-2 px-3.5 inline-flex items-center gap-x-1.5 text-xs font-medium rounded-lg border border-gray-200 bg-white text-gray-700 shadow-xs hover:bg-gray-50 focus:outline-none focus:bg-gray-50 transition-colors" href="<?= e(url('/reports/month/' . $monthKey)) ?>">
            <i class="bi bi-arrow-left"></i>
            Daftar Tanggal
        </a>
        <a class="py-2 px-3.5 inline-flex items-center gap-x-1.5 text-xs font-medium rounded-lg border border-gray-200 bg-white text-gray-700 shadow-xs hover:bg-gray-50 focus:outline-none focus:bg-gray-50 transition-colors" href="<?= e(url('/reports/date/' . $date . '/export')) ?>">
            <i class="bi bi-file-earmark-excel text-emerald-600"></i>
            Export Semua
        </a>
    </div>

    <!-- =========================================================
         PENCARIAN GLOBAL
         ========================================================= -->
    <section class="bg-white border border-gray-200 rounded-2xl p-4 sm:p-5 shadow-xs">
        <form method="get" action="<?= e(url('/reports/search')) ?>" class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div class="flex-1">
                <strong class="text-sm font-bold text-gray-900 block">Pencarian Transaksi Tanggal Ini</strong>
                <span class="text-xs text-gray-500 mt-0.5 block">Mencari batch tim, payer, beneficiary, bank, dan nomor transaksi pada tanggal <?= e($dateLabel) ?>.</span>
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
                    <a class="btn btn-primary py-2 px-3.5 inline-flex items-center justify-center gap-x-2 text-xs font-semibold rounded-lg shrink-0 focus:outline-none" href="<?= e(url('/reports/create?' . http_build_query(['month' => $monthKey, 'date' => $date]))) ?>">
                        <i class="bi bi-cloud-arrow-up text-sm"></i>
                        Upload Excel
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </section>

    <!-- =========================================================
         FILTER STATUS & TIM
         ========================================================= -->
    <div class="bg-white border border-gray-200 rounded-2xl p-4 sm:p-5 shadow-xs">
        <form class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 items-end" method="get" action="<?= e(url('/reports/date/' . $date)) ?>">
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

            <div class="flex items-center gap-2">
                <a class="py-1.5 px-3 inline-flex justify-center items-center text-xs font-medium rounded-lg border border-gray-200 bg-white text-gray-700 shadow-xs hover:bg-gray-50 focus:outline-none focus:bg-gray-50 transition-colors flex-1" href="<?= e(url('/reports/date/' . $date)) ?>">
                    Reset
                </a>
                <button class="btn btn-primary py-1.5 px-3.5 inline-flex justify-center items-center gap-x-1.5 text-xs font-semibold rounded-lg focus:outline-none flex-1" type="submit">
                    <i class="bi bi-funnel"></i>
                    Filter
                </button>
            </div>
        </form>
    </div>

    <?php if (!empty($filters['team']) || !empty($filters['delivery_status']) || !empty($filters['result_status'])): ?>
        <!-- ALERT STATUS: FILTER TANGGAL AKTIF -->
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
                        Menampilkan batch report pada tanggal <?= e($dateLabel) ?> dengan filter aktif.
                        <a href="<?= e(url('/reports/date/' . $date)) ?>" class="underline font-semibold ml-1 hover:text-blue-900">Reset Semua</a>
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
         HIERARKI TINGKAT 3 (STEPPER PILLS)
         ========================================================= -->
    <div class="flex flex-wrap items-center gap-2 text-xs font-medium text-gray-500">
        <a class="inline-flex items-center gap-1.5 py-1 px-3 rounded-full bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors" href="<?= e(url('/reports')) ?>">
            <span class="size-4 rounded-full bg-gray-300 text-gray-700 text-[10px] inline-flex items-center justify-center font-bold">1</span>
            Bulan
        </a>
        <i class="bi bi-chevron-right text-gray-400 text-xs"></i>
        <a class="inline-flex items-center gap-1.5 py-1 px-3 rounded-full bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors" href="<?= e(url('/reports/month/' . $monthKey)) ?>">
            <span class="size-4 rounded-full bg-gray-300 text-gray-700 text-[10px] inline-flex items-center justify-center font-bold">2</span>
            Tanggal
        </a>
        <i class="bi bi-chevron-right text-gray-400 text-xs"></i>
        <span class="inline-flex items-center gap-1.5 py-1 px-3 rounded-full bg-blue-600 text-white font-bold shadow-xs">
            <span class="size-4 rounded-full bg-white text-blue-600 text-[10px] inline-flex items-center justify-center font-bold">3</span>
            Tim dan Report (<?= e($dateLabel) ?>)
        </span>
    </div>

    <!-- =========================================================
         TOOLBAR MODE TAMPILAN: TABEL VS LIST (ALPINE + PRELINE UI)
         ========================================================= -->
    <div x-data="{ viewMode: localStorage.getItem('remitgo_report_view_mode') || localStorage.getItem('fable_report_view_mode') || 'table', openOptions: false }"
         x-init="$watch('viewMode', val => { localStorage.setItem('remitgo_report_view_mode', val); localStorage.setItem('fable_report_view_mode', val); })"
         class="space-y-4">

        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-gray-700">Batch Report Tim</span>
                <span class="inline-flex items-center py-0.5 px-2.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200 shadow-xs">
                    <?= count($batches) ?> Berkas
                </span>
            </div>

            <div class="flex items-center gap-2">
                <!-- Alpine Dropdown -->
                <div class="relative inline-flex" @click.outside="openOptions = false">
                    <button
                        type="button"
                        class="dropdown-toggle"
                        @click="openOptions = !openOptions"
                        :aria-expanded="openOptions"
                        aria-label="Menu Opsi"
                    >
                        <i class="bi bi-gear"></i>
                        <span>Opsi</span>
                        <span class="text-gray-300 font-normal">·</span>
                        <span class="text-[11px] text-gray-500 font-medium capitalize" x-text="viewMode === 'table' ? 'Tabel' : 'List'"></span>
                        <i class="bi bi-chevron-down text-xs text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': openOptions }"></i>
                    </button>

                    <div
                        class="dropdown-menu absolute right-0 top-full mt-2 w-56 z-50 bg-white"
                        x-show="openOptions"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        style="display: none;"
                        role="menu"
                    >
                        <span class="dropdown-header">Aksi Cepat</span>
                        <a class="dropdown-item" href="<?= e(url('/reports/create?' . http_build_query(['month' => $monthKey, 'date' => $date]))) ?>">
                            <i class="bi bi-cloud-arrow-up text-blue-600"></i>
                            Tambah Berkas Excel
                        </a>
                        <a class="dropdown-item" href="<?= e(url('/reports/date/' . $date . '/export')) ?>">
                            <i class="bi bi-file-earmark-excel text-emerald-600"></i>
                            Export Semua Berkas
                        </a>
                        <div class="dropdown-divider"></div>
                        <span class="dropdown-header">Mode Tampilan</span>
                        <button type="button" @click="viewMode = 'table'; openOptions = false" class="dropdown-item justify-between" :class="viewMode === 'table' ? 'active font-semibold' : ''">
                            <span class="flex items-center gap-x-2.5">
                                <i class="bi bi-table text-indigo-600"></i>
                                Tampilan Tabel
                            </span>
                            <i x-show="viewMode === 'table'" class="bi bi-check2 text-blue-600 text-sm"></i>
                        </button>
                        <button type="button" @click="viewMode = 'list'; openOptions = false" class="dropdown-item justify-between" :class="viewMode === 'list' ? 'active font-semibold' : ''">
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
                            <th scope="col" class="py-3.5 px-5 text-start text-xs font-semibold text-gray-600 uppercase tracking-wider">Batch &amp; Tim</th>
                            <th scope="col" class="py-3.5 px-5 text-start text-xs font-semibold text-gray-600 uppercase tracking-wider">Berkas &amp; Nama Report</th>
                            <th scope="col" class="py-3.5 px-5 text-end text-xs font-semibold text-gray-600 uppercase tracking-wider">Total Baris</th>
                            <th scope="col" class="py-3.5 px-5 text-start text-xs font-semibold text-gray-600 uppercase tracking-wider" style="min-width: 170px;">Status Kirim</th>
                            <th scope="col" class="py-3.5 px-5 text-start text-xs font-semibold text-gray-600 uppercase tracking-wider">Hasil Eksekusi</th>
                            <th scope="col" class="py-3.5 px-5 text-end text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
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
                            <tr class="even:bg-gray-50/50 [data-no-hover]">
                                <td class="py-4 px-5 whitespace-nowrap">
                                    <div class="space-y-1">
                                        <span class="inline-flex items-center py-0.5 px-2 rounded-full text-[11px] font-mono font-semibold bg-gray-100 text-gray-700">
                                            Batch #<?= (int) $batch['id'] ?>
                                        </span>
                                        <strong class="text-xs font-bold text-gray-900 block">
                                            TEAM: <?= e(strtoupper((string) ($batch['effective_team'] ?? 'Tanpa Tim'))) ?>
                                        </strong>
                                    </div>
                                </td>
                                <td class="py-4 px-5 whitespace-nowrap">
                                    <div>
                                        <strong class="text-sm font-bold text-gray-900 block truncate max-w-xs" title="<?= e($batch['source_filename']) ?>">
                                            <i class="bi bi-file-earmark-excel text-emerald-600 me-1"></i>
                                            <?= e($batch['source_filename']) ?>
                                        </strong>
                                        <span class="text-xs text-gray-500 block truncate max-w-xs mt-0.5">
                                            <?= e($batch['name']) ?>
                                        </span>
                                    </div>
                                </td>
                                <td class="py-4 px-5 whitespace-nowrap text-end font-mono font-bold text-gray-900">
                                    <?= number_format($total, 0, ',', '.') ?>
                                </td>
                                <td class="py-4 px-5 whitespace-nowrap">
                                    <div class="space-y-1.5">
                                        <div class="flex items-center justify-between text-xs">
                                            <span class="font-semibold text-emerald-700"><?= number_format($sent, 0, ',', '.') ?> Kirim</span>
                                            <span class="text-gray-500 font-mono"><?= number_format($pending, 0, ',', '.') ?> Pnd</span>
                                        </div>
                                        <div class="w-full h-1.5 bg-gray-200 rounded-full overflow-hidden flex">
                                            <div class="h-full bg-blue-600 rounded-full" style="width: <?= $progress ?>%;"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-5 whitespace-nowrap">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <span class="inline-flex items-center gap-1 py-0.5 px-2 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200" title="Berhasil">
                                            <i class="bi bi-check-lg"></i><?= $success ?>
                                        </span>
                                        <?php if ($failed > 0): ?>
                                            <span class="inline-flex items-center gap-1 py-0.5 px-2 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-200" title="Gagal / Retur">
                                                <i class="bi bi-x-lg"></i><?= $failed ?>
                                            </span>
                                        <?php endif; ?>
                                        <?php if ($skipped > 0): ?>
                                            <span class="inline-flex items-center py-0.5 px-2 rounded-full text-xs font-semibold bg-gray-100 text-gray-600" title="Dilewati">
                                                <?= $skipped ?> skip
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="py-4 px-5 whitespace-nowrap text-end">
                                    <div class="inline-flex items-center gap-1.5 justify-end">
                                        <a class="btn btn-primary py-1 px-2.5 inline-flex items-center gap-x-1 text-xs font-semibold rounded-lg focus:outline-none transition-colors" href="<?= e(url('/reports/' . $batch['id'])) ?>">
                                            <i class="bi bi-table"></i>
                                            Buka
                                        </a>
                                        <a class="size-7 inline-flex justify-center items-center rounded-lg border border-gray-200 bg-white text-gray-700 shadow-xs hover:bg-gray-50 focus:outline-none focus:bg-gray-50 transition-colors" href="<?= e(url('/reports/' . $batch['id'] . '/export')) ?>" title="Export Excel">
                                            <i class="bi bi-file-earmark-excel text-emerald-600"></i>
                                        </a>
                                        <?php if (can('report.manage')): ?>
                                            <a class="size-7 inline-flex justify-center items-center rounded-lg border border-gray-200 bg-white text-gray-700 shadow-xs hover:bg-gray-50 focus:outline-none focus:bg-gray-50 transition-colors" href="<?= e(url('/reports/' . $batch['id'] . '/edit')) ?>" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form class="inline-block m-0" method="post" action="<?= e(url('/reports/' . $batch['id'])) ?>" data-confirm="Hapus report ini beserta seluruh barisnya?">
                                                <?= csrf_field() ?>
                                                <?= method_field('DELETE') ?>
                                                <input type="hidden" name="return_to" value="<?= e($returnPath) ?>">
                                                <button class="size-7 inline-flex justify-center items-center rounded-lg border border-red-200 bg-white text-red-600 shadow-xs hover:bg-red-50 hover:border-red-300 focus:outline-none focus:bg-red-50 transition-colors" type="submit" title="Hapus">
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
                                <td colspan="6" class="text-center py-12 px-6">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <i class="bi bi-file-earmark-x text-3xl text-gray-400"></i>
                                        <strong class="text-sm font-bold text-gray-800">Tidak ada report tim</strong>
                                        <span class="text-xs text-gray-500">Belum ada berkas pada tanggal ini atau data tidak cocok dengan filter yang dipilih.</span>
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
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="inline-flex items-center py-0.5 px-2 rounded-full text-[11px] font-mono font-semibold bg-gray-100 text-gray-700">
                                        Batch #<?= (int) $batch['id'] ?>
                                    </span>
                                    <span class="inline-flex items-center py-0.5 px-2.5 rounded-full text-xs font-bold bg-blue-50 text-blue-800 border border-blue-200">
                                        <?= e(strtoupper((string) ($batch['effective_team'] ?? 'Tanpa Tim'))) ?>
                                    </span>
                                </div>
                                <strong class="text-sm font-bold text-gray-900 block truncate" title="<?= e($batch['source_filename']) ?>">
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
                                <strong class="text-sm font-bold text-gray-900 font-mono"><?= number_format($total, 0, ',', '.') ?></strong>
                            </div>
                            <div class="bg-emerald-50/50 rounded-xl p-2.5 border border-emerald-100">
                                <span class="text-emerald-700 block text-[11px]">Terkirim</span>
                                <strong class="text-sm font-bold text-emerald-800 font-mono"><?= number_format($sent, 0, ',', '.') ?></strong>
                            </div>
                            <div class="bg-emerald-50/50 rounded-xl p-2.5 border border-emerald-100">
                                <span class="text-emerald-700 block text-[11px]">Success</span>
                                <strong class="text-sm font-bold text-emerald-800 font-mono"><?= number_format($success, 0, ',', '.') ?></strong>
                            </div>
                            <div class="bg-red-50/50 rounded-xl p-2.5 border border-red-100">
                                <span class="text-red-700 block text-[11px]">Failed</span>
                                <strong class="text-sm font-bold text-red-800 font-mono"><?= number_format($failed, 0, ',', '.') ?></strong>
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

                    <!-- Card Footer Actions -->
                    <div class="pt-3 border-t border-gray-100 flex items-center justify-between gap-2">
                        <div class="flex items-center gap-1.5">
                            <a class="size-8 inline-flex justify-center items-center rounded-lg border border-gray-200 bg-white text-gray-700 shadow-xs hover:bg-gray-50 focus:outline-none focus:bg-gray-50 transition-colors" href="<?= e(url('/reports/' . $batch['id'] . '/export')) ?>" title="Export Excel">
                                <i class="bi bi-file-earmark-excel text-emerald-600"></i>
                            </a>
                            <?php if (can('report.manage')): ?>
                                <a class="size-8 inline-flex justify-center items-center rounded-lg border border-gray-200 bg-white text-gray-700 shadow-xs hover:bg-gray-50 focus:outline-none focus:bg-gray-50 transition-colors" href="<?= e(url('/reports/' . $batch['id'] . '/edit')) ?>" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form class="inline-block m-0" method="post" action="<?= e(url('/reports/' . $batch['id'])) ?>" data-confirm="Hapus report ini beserta seluruh barisnya?">
                                    <?= csrf_field() ?>
                                    <?= method_field('DELETE') ?>
                                    <input type="hidden" name="return_to" value="<?= e($returnPath) ?>">
                                    <button class="size-8 inline-flex justify-center items-center rounded-lg border border-red-200 bg-white text-red-600 shadow-xs hover:bg-red-50 hover:border-red-300 focus:outline-none focus:bg-red-50 transition-colors" type="submit" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>

                        <a class="btn btn-primary py-1.5 px-3.5 inline-flex items-center gap-x-1.5 text-xs font-semibold rounded-lg focus:outline-none transition-colors" href="<?= e(url('/reports/' . $batch['id'])) ?>">
                            <i class="bi bi-table"></i>
                            Buka Report
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>

            <?php if ($batches === []): ?>
                <div class="col-span-full bg-white border border-gray-200 rounded-2xl p-12 text-center shadow-xs">
                    <i class="bi bi-file-earmark-x text-3xl text-gray-400 block mb-3"></i>
                    <h3 class="text-base font-bold text-gray-800 mb-1">Tidak ada report tim</h3>
                    <p class="text-xs text-gray-500 mb-4">Belum ada berkas pada tanggal ini atau data tidak cocok dengan filter yang dipilih.</p>
                    <?php if (can('report.manage')): ?>
                        <a class="btn btn-primary py-2 px-3.5 inline-flex items-center gap-x-2 text-xs font-semibold rounded-lg focus:outline-none" href="<?= e(url('/reports/create?' . http_build_query(['month' => $monthKey, 'date' => $date]))) ?>">
                            <i class="bi bi-cloud-arrow-up text-sm"></i>
                            Tambah Excel
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

    </div>

</div>
