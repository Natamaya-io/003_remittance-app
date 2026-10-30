<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| LAPORAN - DETAIL BATCH (PRELINE UI & ALPINE.JS NATIVE)
|--------------------------------------------------------------------------
*/

$batch = is_array($batch ?? null) ? $batch : [];
$rows = is_array($rows ?? null) ? $rows : [];
$filters = is_array($filters ?? null) ? $filters : [];
$summary = is_array($summary ?? null) ? $summary : [];
$distinctTeams = is_array($distinctTeams ?? null) ? $distinctTeams : [];
$distinctCurrencies = is_array($distinctCurrencies ?? null) ? $distinctCurrencies : [];

$queryText = (string) ($filters['q'] ?? '');
$currDelivery = (string) ($filters['delivery_status'] ?? '');
$currResult = (string) ($filters['result_status'] ?? '');
$returnQuery = http_build_query($filters);

$currDeliveryLabel = match ($currDelivery) {
    'PENDING' => 'Belum Terkirim',
    'SENT'    => 'Terkirim',
    default   => 'Semua status kirim',
};

$currResultLabel = match ($currResult) {
    'PENDING' => 'Pending',
    'SUCCESS' => 'Success',
    'FAILED'  => 'Failed',
    default   => 'Semua hasil',
};

$activeFilterList = [];
if ($queryText !== '') {
    $activeFilterList[] = 'Cari: ' . $queryText;
}
if ($currDelivery !== '') {
    $activeFilterList[] = 'Kirim: ' . $currDeliveryLabel;
}
if ($currResult !== '') {
    $activeFilterList[] = 'Hasil: ' . $currResultLabel;
}
$activeFiltersCount = count($activeFilterList);
$activeFilterSummary = implode(' · ', $activeFilterList);

$batchDate = (string) ($batch['effective_date'] ?? $effectiveDate ?? $batch['report_date'] ?? '');
$dateBackUrl = $batchDate !== '' ? url('/reports/date/' . $batchDate) : url('/reports');
$teamNameDisplay = (string) ($batch['effective_team'] ?? $batch['team_name'] ?? $batch['client_name'] ?? 'Tanpa Tim');

$monthNamesUpper = [
    1 => 'JANUARI', 2 => 'FEBRUARI', 3 => 'MARET', 4 => 'APRIL',
    5 => 'MEI', 6 => 'JUNI', 7 => 'JULI', 8 => 'AGUSTUS',
    9 => 'SEPTEMBER', 10 => 'OKTOBER', 11 => 'NOVEMBER', 12 => 'DESEMBER',
];

$formattedBatchDateUpper = '';
if ($batchDate !== '') {
    try {
        $dateObj = new \DateTime($batchDate);
        $day = $dateObj->format('j');
        $monthUpper = $monthNamesUpper[(int) $dateObj->format('n')] ?? strtoupper($dateObj->format('F'));
        $year = $dateObj->format('Y');
        $formattedBatchDateUpper = $day . ' ' . $monthUpper . ' ' . $year;
    } catch (\Throwable) {
        $formattedBatchDateUpper = '';
    }
}

$rawBatchName = trim((string) ($batch['name'] ?? ''));
if ($formattedBatchDateUpper !== '') {
    if (preg_match('/^(REPORT|LAPORAN)\s+HARIAN/i', $rawBatchName)) {
        $pageHeadingTitle = 'REPORT HARIAN - ' . $formattedBatchDateUpper;
    } elseif ($rawBatchName !== '') {
        $pageHeadingTitle = $rawBatchName . ' - ' . $formattedBatchDateUpper;
    } else {
        $pageHeadingTitle = 'REPORT HARIAN - ' . $formattedBatchDateUpper;
    }
} else {
    $pageHeadingTitle = $rawBatchName !== '' ? $rawBatchName : 'Detail Batch Report';
}

$totalRows = (int) ($summary['total_rows'] ?? count($rows));
$sentRows = (int) ($summary['sent_rows'] ?? 0);
$pendingRows = (int) ($summary['pending_rows'] ?? 0);
$successRows = (int) ($summary['success_rows'] ?? 0);
$failedRows = (int) ($summary['failed_rows'] ?? 0);
$totalAmount = (float) ($summary['total_amount'] ?? 0.0);
?>

<div class="max-w-7xl mx-auto space-y-6"
     x-data="{
        selectedCount: 0,
        allSelected: false,
        editModalOpen: false,
        openFilterModal: false,
        activeRow: {
            id: 0,
            order: '1',
            payer: '',
            beneficiary: '',
            amount: '',
            delivery: 'PENDING',
            result: 'PENDING',
            notes: ''
        },
        updateCount() {
            const checked = document.querySelectorAll('input[name=\'row_ids[]\']:checked').length;
            const total = document.querySelectorAll('input[name=\'row_ids[]\']').length;
            this.selectedCount = checked;
            this.allSelected = total > 0 && checked === total;
        },
        toggleAll(checked) {
            document.querySelectorAll('input[name=\'row_ids[]\']').forEach(el => el.checked = checked);
            this.updateCount();
        },
        openEditFromButton(btn) {
            this.activeRow = {
                id: parseInt(btn.dataset.rowId, 10) || 0,
                order: btn.dataset.rowOrder || '1',
                payer: btn.dataset.rowPayer || '',
                beneficiary: btn.dataset.rowBeneficiary || '',
                amount: btn.dataset.rowAmount || '',
                delivery: btn.dataset.rowDelivery || 'PENDING',
                result: btn.dataset.rowResult || 'PENDING',
                notes: btn.dataset.rowNotes || ''
            };
            this.editModalOpen = true;
        }
     }">

    <!-- =========================================================
         PAGE HEADING & ACTIONS (PURE NAKED ARCHITECTURE)
         ========================================================= -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <?= render_heading_with_info(
                $pageHeadingTitle,
                'Detail seluruh baris transaksi dan status pengiriman untuk batch laporan ini.',
                [
                    'titleTag'     => 'h1',
                    'titleClass'   => 'text-xl font-semibold font-heading text-gray-900 m-0',
                    'wrapperClass' => 'inline-flex items-center gap-2',
                ]
            ) ?>
            <div class="flex items-center gap-2 mt-1.5 flex-wrap">
                <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-900 tabular-nums">
                    <i class="bi bi-layers text-gray-400" aria-hidden="true"></i>
                    <span>Batch #<?= (int) ($batch['id'] ?? 0) ?></span>
                </span>
                <span class="text-xs text-gray-300">·</span>
                <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-700">
                    <i class="bi bi-people text-gray-400" aria-hidden="true"></i>
                    <span><?= e($teamNameDisplay) ?></span>
                </span>
                <span class="text-xs text-gray-300">·</span>
                <span class="text-xs text-gray-500">Berkas: <span class="font-medium text-gray-700"><?= e((string) ($batch['source_filename'] ?? '')) ?></span></span>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a class="btn-secondary" href="<?= e($dateBackUrl) ?>">
                <i class="bi bi-arrow-left"></i>
                <span>Report per Tim</span>
            </a>

            <?php if (can('report.manage')): ?>
                <a class="btn-secondary" href="<?= e(url('/reports/' . $batch['id'] . '/edit')) ?>" aria-label="Edit Metadata Report" title="Edit nama, tanggal, dan metadata report batch">
                    <i class="bi bi-pencil"></i>
                    <span>Edit Metadata Report</span>
                </a>
            <?php endif; ?>

            <!-- Tombol Filter Modal di Samping Download Excel -->
            <button
                type="button"
                @click="openFilterModal = true"
                class="btn-primary"
                aria-label="Buka Filter Transaksi Batch"
                title="Filter berdasarkan kata kunci, status kirim, dan hasil transaksi"
            >
                <i class="bi bi-sliders text-sm"></i>
                <span>Filter</span>
                <?php if ($activeFiltersCount > 0): ?>
                    <span class="btn-filter-badge" aria-label="<?= $activeFiltersCount ?> filter aktif"><?= $activeFiltersCount ?></span>
                <?php endif; ?>
            </button>

            <a class="btn-primary" href="<?= e(url('/reports/' . $batch['id'] . '/export')) ?>">
                <i class="bi bi-download"></i>
                <span>Download Excel</span>
            </a>
        </div>
    </div>

    <!-- =========================================================
         MODAL FILTER TRANSAKSI LAPORAN BATCH (ALPINE.JS TELEPORTED TO BODY)
         ========================================================= -->
    <template x-teleport="body">
        <div
            x-show="openFilterModal"
            x-cloak
            class="fixed inset-0 z-[9999] overflow-y-auto"
            role="dialog"
            aria-modal="true"
            aria-labelledby="modal-batch-filter-title"
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
                            <i class="bi bi-sliders text-gray-800 text-lg"></i>
                            <div class="flex items-center gap-1.5">
                                <strong id="modal-batch-filter-title" class="text-sm font-semibold text-gray-900 block">Filter Transaksi Batch</strong>
                                <?= render_tooltip(
                                    'Saring transaksi batch laporan berdasarkan kata kunci pencarian, status kirim, dan hasil eksekusi.',
                                    '<span class="hs-tooltip-toggle info-toggle-btn cursor-help" aria-label="Info filter transaksi"><i class="bi bi-info-circle text-[10px] leading-none"></i></span>',
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
                    <form method="get" action="<?= e(url('/reports/' . $batch['id'])) ?>" class="m-0 p-0">
                        <div class="p-5 space-y-4">
                            <!-- Input Pencarian (Full Width) -->
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Cari Transaksi</label>
                                <?= render_search_input('q', $queryText, [
                                    'placeholder' => 'Cari payer, beneficiary, bank, invoice, SWIFT, akun...',
                                ]) ?>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
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
                                                @click="selected = ''; label = 'Semua status kirim'; open = false"
                                            >
                                                <span>Semua status kirim</span>
                                                <i x-show="selected === ''" class="bi bi-check2 text-[#315B8C] text-sm"></i>
                                            </button>
                                            <button
                                                type="button"
                                                class="dropdown-item justify-between"
                                                :class="{ 'active': selected === 'PENDING' }"
                                                @click="selected = 'PENDING'; label = 'Belum Terkirim'; open = false"
                                            >
                                                <span>Belum Terkirim</span>
                                                <i x-show="selected === 'PENDING'" class="bi bi-check2 text-[#315B8C] text-sm"></i>
                                            </button>
                                            <button
                                                type="button"
                                                class="dropdown-item justify-between"
                                                :class="{ 'active': selected === 'SENT' }"
                                                @click="selected = 'SENT'; label = 'Terkirim'; open = false"
                                            >
                                                <span>Terkirim</span>
                                                <i x-show="selected === 'SENT'" class="bi bi-check2 text-[#315B8C] text-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Dropdown Status Hasil -->
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1">Hasil Transaksi</label>
                                    <div class="custom-select-root relative" :class="{ 'z-20': open }" x-data="{ open: false, selected: '<?= e(addslashes($currResult)) ?>', label: '<?= e(addslashes($currResultLabel)) ?>' }" @click.outside="open = false">
                                        <input type="hidden" name="result_status" :value="selected">
                                        <button
                                            type="button"
                                            @click="open = !open"
                                            class="dropdown-toggle"
                                            :aria-expanded="open.toString()"
                                            aria-label="Filter Hasil Transaksi"
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
                                                <i x-show="selected === ''" class="bi bi-check2 text-[#315B8C] text-sm"></i>
                                            </button>
                                            <button
                                                type="button"
                                                class="dropdown-item justify-between"
                                                :class="{ 'active': selected === 'PENDING' }"
                                                @click="selected = 'PENDING'; label = 'Pending'; open = false"
                                            >
                                                <span>Pending</span>
                                                <i x-show="selected === 'PENDING'" class="bi bi-check2 text-[#315B8C] text-sm"></i>
                                            </button>
                                            <button
                                                type="button"
                                                class="dropdown-item justify-between"
                                                :class="{ 'active': selected === 'SUCCESS' }"
                                                @click="selected = 'SUCCESS'; label = 'Success'; open = false"
                                            >
                                                <span>Success</span>
                                                <i x-show="selected === 'SUCCESS'" class="bi bi-check2 text-[#315B8C] text-sm"></i>
                                            </button>
                                            <button
                                                type="button"
                                                class="dropdown-item justify-between"
                                                :class="{ 'active': selected === 'FAILED' }"
                                                @click="selected = 'FAILED'; label = 'Failed'; open = false"
                                            >
                                                <span>Failed</span>
                                                <i x-show="selected === 'FAILED'" class="bi bi-check2 text-[#315B8C] text-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Footer -->
                        <div class="px-5 py-3.5 bg-gray-50/70 border-t border-gray-100 flex items-center justify-between gap-3 rounded-b-2xl">
                            <a
                                class="py-2 px-3.5 inline-flex justify-center items-center text-xs font-medium rounded-lg border border-gray-200 bg-white text-gray-700 shadow-xs hover:bg-gray-50 focus:outline-none transition-colors"
                                href="<?= e(url('/reports/' . $batch['id'])) ?>"
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
                window.remitToast.info('Menampilkan transaksi batch: <?= e(addslashes($activeFilterSummary)) ?>', {
                    title: 'Filter Transaksi Aktif',
                    duration: 5000
                });
            }
        });
        </script>
    <?php endif; ?>

    <?php if ($pendingRows > 0): ?>
        <!-- ALERT STATUS: PENDING TRANSAKSI -->
        <div
            class="alert alert-warning"
            role="alert"
            x-data="{ show: true }"
            x-show="show"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
        >
            <div class="alert-content">
                <span class="alert-icon">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </span>
                <div class="alert-body">
                    <strong class="alert-title font-semibold">Transaksi Pending</strong>
                    <div class="alert-message">Terdapat <?= number_format($pendingRows, 0, ',', '.') ?> baris transaksi yang belum terkirim (pending) pada berkas report ini.</div>
                </div>
            </div>
            <button
                class="alert-close"
                type="button"
                @click="show = false"
                aria-label="Tutup notifikasi"
                title="Tutup notifikasi"
            >
                <i class="bi bi-x-lg text-xs leading-none"></i>
            </button>
        </div>
    <?php endif; ?>

    <!-- =========================================================
         SUMMARY KPI GRID (6 METRIK OPERASIONAL)
         ========================================================= -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <div class="bg-white border border-gray-200 rounded-2xl p-4 shadow-xs">
            <span class="text-xs font-medium text-gray-500 block mb-1">Total Baris</span>
            <strong class="text-xl font-semibold text-gray-900 block tabular-nums font-heading"><?= number_format($totalRows, 0, ',', '.') ?></strong>
        </div>
        <div class="bg-white border border-gray-200 rounded-2xl p-4 shadow-xs">
            <span class="text-xs font-medium text-gray-500 block mb-1">Total Nominal</span>
            <strong class="text-base font-semibold text-gray-900 block tabular-nums font-heading truncate" title="<?= number_format($totalAmount, 2, ',', '.') ?>">
                <?= number_format($totalAmount, 0, ',', '.') ?>
            </strong>
        </div>
        <div class="bg-white border border-gray-200 rounded-2xl p-4 shadow-xs">
            <span class="text-xs font-medium text-gray-500 block mb-1">Sukses</span>
            <strong class="text-xl font-semibold text-emerald-700 block tabular-nums font-heading"><?= number_format($successRows, 0, ',', '.') ?></strong>
        </div>
        <div class="bg-white border border-gray-200 rounded-2xl p-4 shadow-xs">
            <span class="text-xs font-medium text-gray-500 block mb-1">Terkirim</span>
            <strong class="text-xl font-semibold text-[#315B8C] block tabular-nums font-heading"><?= number_format($sentRows, 0, ',', '.') ?></strong>
        </div>
        <div class="bg-white border border-gray-200 rounded-2xl p-4 shadow-xs">
            <span class="text-xs font-medium text-gray-500 block mb-1">Pending</span>
            <strong class="text-xl font-semibold text-amber-700 block tabular-nums font-heading"><?= number_format($pendingRows, 0, ',', '.') ?></strong>
        </div>
        <div class="bg-white border border-gray-200 rounded-2xl p-4 shadow-xs">
            <span class="text-xs font-medium text-gray-500 block mb-1">Gagal / Retur</span>
            <strong class="text-xl font-semibold text-red-700 block tabular-nums font-heading"><?= number_format($failedRows, 0, ',', '.') ?></strong>
        </div>
    </div>

    <!-- =========================================================
         WIDE TRANSACTION DATA TABLE (PRELINE UI + THE NAKED ZEBRA)
         ========================================================= -->
    <section class="bg-white border border-gray-200 rounded-2xl shadow-xs overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-2 flex-wrap">
                <h3 class="text-sm font-semibold font-heading text-gray-900"><?= number_format(count($rows), 0, ',', '.') ?> Baris Transaksi</h3>
                <?php if ($activeFiltersCount > 0): ?>
                    <span class="text-xs text-gray-400">·</span>
                    <span class="text-xs text-gray-600">Filter aktif: <strong class="text-gray-900 font-medium"><?= e($activeFilterSummary) ?></strong></span>
                    <a href="<?= e(url('/reports/' . $batch['id'])) ?>" class="text-xs text-[#315B8C] hover:underline font-medium ml-1" title="Hapus semua filter transaksi">(Reset)</a>
                <?php endif; ?>
            </div>
            <?php if (can('report.manage')): ?>
                <div class="text-xs text-gray-500">
                    <span x-text="selectedCount">0</span> baris dipilih
                </div>
            <?php endif; ?>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-xs border-collapse report-zebra-table">
                <thead class="bg-gray-50/75 border-b border-gray-200">
                    <tr class="text-gray-700 font-semibold font-heading text-xs tracking-normal">
                        <?php if (can('report.manage')): ?>
                            <th class="py-3.5 px-3 w-10 text-center">
                                <input type="checkbox" :checked="allSelected" @change="toggleAll($el.checked)" class="rounded border-gray-300 text-[#315B8C] focus:ring-[#315B8C]">
                            </th>
                        <?php endif; ?>
                        <th class="py-3.5 px-3">TEAM</th>
                        <th class="py-3.5 px-3">No.</th>
                        <th class="py-3.5 px-3">Tanggal</th>
                        <th class="py-3.5 px-3">Bill To/Payer</th>
                        <th class="py-3.5 px-3">Beneficiary</th>
                        <th class="py-3.5 px-3">Account</th>
                        <th class="py-3.5 px-3">Beneficiary Bank</th>
                        <th class="py-3.5 px-3">Bank Address</th>
                        <th class="py-3.5 px-3 text-right">Amounts</th>
                        <th class="py-3.5 px-3">Country</th>
                        <th class="py-3.5 px-3">City</th>
                        <th class="py-3.5 px-3">Beneficiary Address</th>
                        <th class="py-3.5 px-3">SWIFT</th>
                        <th class="py-3.5 px-3">Payment Details</th>
                        <th class="py-3.5 px-3">Currency</th>
                        <th class="py-3.5 px-3">Invoice</th>
                        <th class="py-3.5 px-3">Contract</th>
                        <th class="py-3.5 px-3">Correspondent</th>
                        <th class="py-3.5 px-3">Swift Corr</th>
                        <th class="py-3.5 px-3">Country Corr</th>
                        <th class="py-3.5 px-3">Bank of Payer</th>
                        <th class="py-3.5 px-3">Invoice Link</th>
                        <th class="py-3.5 px-3">Bank Proses</th>
                        <th class="py-3.5 px-3">Status Kirim</th>
                        <th class="py-3.5 px-3">Hasil</th>
                        <th class="py-3.5 px-3">Notes</th>
                        <th class="py-3.5 px-3">Sheet</th>
                        <th class="py-3.5 px-4 text-right sticky right-0 bg-gray-50/95 z-20 shadow-[-4px_0_10px_rgba(0,0,0,0.04)]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                <?php foreach ($rows as $row): ?>
                    <?php
                    $rowId = (int) ($row['id'] ?? 0);
                    $itemNumber = (int) ($row['item_no'] ?: ($row['source_row'] ?? 1));
                    $teamLabel = (string) ($row['team_label'] ?: $teamNameDisplay ?: '-');
                    $trxDate = (string) ($row['transaction_date'] ?? '');
                    $payerName = (string) ($row['bill_to'] ?? '');
                    $beneficiaryName = (string) ($row['beneficiary_name'] ?? '');
                    $beneficiaryAccount = (string) ($row['beneficiary_account'] ?? '');
                    $beneficiaryBank = (string) ($row['beneficiary_bank'] ?? '');
                    $bankAddress = (string) ($row['beneficiary_bank_address'] ?? '');
                    $amountNum = (float) ($row['amount'] ?? 0);
                    $currencyStr = (string) ($row['currency'] ?: 'IDR');
                    $amountFormatted = format_money($amountNum, $currencyStr);
                    $countryStr = (string) ($row['beneficiary_country'] ?? '');
                    $cityStr = (string) ($row['beneficiary_city'] ?? '');
                    $beneficiaryAddress = (string) ($row['beneficiary_address'] ?? '');
                    $swiftCode = (string) ($row['swift'] ?? '');
                    $paymentDetails = (string) ($row['payment_details'] ?? '');
                    $invoiceNo = (string) ($row['invoice_no'] ?? '');
                    $contractNo = (string) ($row['contract_no'] ?? '');
                    $corrBank = (string) ($row['correspondent_bank'] ?? '');
                    $corrSwift = (string) ($row['correspondent_swift'] ?? '');
                    $corrCountry = (string) ($row['correspondent_country'] ?? '');
                    $bankOfPayer = (string) ($row['bank_of_payer'] ?? '');
                    $invoiceLink = (string) ($row['invoice_link'] ?? '');
                    $processingBank = (string) ($row['processing_bank'] ?? '');
                    $deliveryStatus = (string) ($row['delivery_status'] ?? 'PENDING');
                    $resultStatus = (string) ($row['result_status'] ?? 'PENDING');
                    $notesStr = (string) ($row['notes'] ?? '');
                    $sourceSheet = (string) ($row['source_sheet'] ?? '-');
                    ?>
                    <tr class="even:bg-[#F5F2F2] [data-no-hover] group">
                        <?php if (can('report.manage')): ?>
                            <td class="py-3 px-3 text-center">
                                <input type="checkbox" name="row_ids[]" value="<?= $rowId ?>" form="bulkStatusForm" @change="updateCount()" class="rounded border-gray-300 text-[#315B8C] focus:ring-[#315B8C]">
                            </td>
                        <?php endif; ?>
                        <td class="py-3 px-3 font-semibold text-gray-900 whitespace-nowrap"><?= e($teamLabel) ?></td>
                        <td class="py-3 px-3 tabular-nums text-gray-500"><?= $itemNumber ?></td>
                        <td class="py-3 px-3 whitespace-nowrap"><?= e(format_date($trxDate)) ?></td>
                        <td class="py-3 px-3 font-medium text-gray-900 whitespace-nowrap max-w-xs truncate" title="<?= e($payerName) ?>"><?= e($payerName) ?></td>
                        <td class="py-3 px-3 font-medium text-gray-900 whitespace-nowrap max-w-xs truncate" title="<?= e($beneficiaryName) ?>"><?= e($beneficiaryName) ?></td>
                        <td class="py-3 px-3 tabular-nums text-xs whitespace-nowrap"><?= e($beneficiaryAccount) ?></td>
                        <td class="py-3 px-3 whitespace-nowrap max-w-xs truncate" title="<?= e($beneficiaryBank) ?>"><?= e($beneficiaryBank) ?></td>
                        <td class="py-3 px-3 whitespace-nowrap max-w-xs truncate text-gray-500" title="<?= e($bankAddress) ?>"><?= e($bankAddress) ?></td>
                        <td class="py-3 px-3 text-right tabular-nums font-semibold text-gray-900 whitespace-nowrap"><?= $amountFormatted ?></td>
                        <td class="py-3 px-3 whitespace-nowrap"><?= e($countryStr) ?></td>
                        <td class="py-3 px-3 whitespace-nowrap"><?= e($cityStr) ?></td>
                        <td class="py-3 px-3 whitespace-nowrap max-w-xs truncate text-gray-500" title="<?= e($beneficiaryAddress) ?>"><?= e($beneficiaryAddress) ?></td>
                        <td class="py-3 px-3 tabular-nums whitespace-nowrap"><?= e($swiftCode) ?></td>
                        <td class="py-3 px-3 whitespace-nowrap max-w-xs truncate text-gray-500" title="<?= e($paymentDetails) ?>"><?= e($paymentDetails) ?></td>
                        <td class="py-3 px-3 tabular-nums whitespace-nowrap"><?= e($currencyStr) ?></td>
                        <td class="py-3 px-3 tabular-nums whitespace-nowrap"><?= e($invoiceNo) ?></td>
                        <td class="py-3 px-3 whitespace-nowrap"><?= e($contractNo) ?></td>
                        <td class="py-3 px-3 whitespace-nowrap max-w-xs truncate text-gray-500" title="<?= e($corrBank) ?>"><?= e($corrBank) ?></td>
                        <td class="py-3 px-3 tabular-nums whitespace-nowrap"><?= e($corrSwift) ?></td>
                        <td class="py-3 px-3 whitespace-nowrap"><?= e($corrCountry) ?></td>
                        <td class="py-3 px-3 whitespace-nowrap"><?= e($bankOfPayer) ?></td>
                        <td class="py-3 px-3 whitespace-nowrap">
                            <?php if ($invoiceLink !== ''): ?>
                                <a class="text-[#315B8C] hover:underline inline-flex items-center gap-1" href="<?= e($invoiceLink) ?>" target="_blank" rel="noopener">
                                    <i class="bi bi-box-arrow-up-right"></i> Link
                                </a>
                            <?php else: ?>
                                <span class="text-gray-300">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3 px-3 whitespace-nowrap"><?= e($processingBank ?: '-') ?></td>
                        <td class="py-3 px-3 whitespace-nowrap">
                            <?php if ($deliveryStatus === 'SENT'): ?>
                                <span class="inline-flex items-center gap-1 py-0.5 px-2 rounded-full text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                    <i class="bi bi-send-check"></i> Terkirim
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center gap-1 py-0.5 px-2 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                    <i class="bi bi-clock"></i> Belum
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3 px-3 whitespace-nowrap">
                            <?php if ($resultStatus === 'SUCCESS'): ?>
                                <span class="inline-flex items-center gap-1 py-0.5 px-2 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <i class="bi bi-check-lg"></i> Sukses
                                </span>
                            <?php elseif ($resultStatus === 'FAILED'): ?>
                                <span class="inline-flex items-center gap-1 py-0.5 px-2 rounded-full text-[11px] font-semibold bg-red-50 text-red-700 border border-red-200">
                                    <i class="bi bi-x-lg"></i> Gagal
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center gap-1 py-0.5 px-2 rounded-full text-[11px] font-semibold bg-gray-100 text-gray-700">
                                    Pending
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3 px-3 whitespace-nowrap max-w-xs truncate text-gray-500" title="<?= e($notesStr) ?>"><?= e($notesStr ?: '-') ?></td>
                        <td class="py-3 px-3 text-gray-400 whitespace-nowrap"><?= e($sourceSheet) ?></td>
                        
                        <!-- STICKY ACTION COLUMN (ALWAYS PINNED TO THE RIGHT) -->
                        <td class="py-3 px-4 text-right sticky right-0 bg-white group-even:bg-[#F5F2F2] z-10 shadow-[-4px_0_10px_rgba(0,0,0,0.04)] whitespace-nowrap">
                            <div class="inline-flex items-center gap-1.5 justify-end">
                                <?php if (!empty($row['transaction_id'])): ?>
                                    <?= render_tooltip('Buka Transaksi Sistem', '<a class="p-1 rounded-md text-gray-500 hover:text-[#315B8C] hover:bg-gray-100" href="' . e(url('/transactions/' . $row['transaction_id'])) . '"><i class="bi bi-link-45deg text-base"></i></a>', ['placement' => 'left']) ?>
                                <?php endif; ?>

                                <?php if (can('report.manage')): ?>
                                    <?= render_tooltip('Ubah Status & Catatan', '<button
                                        type="button"
                                        data-row-id="' . $rowId . '"
                                        data-row-order="' . $itemNumber . '"
                                        data-row-payer="' . e($payerName) . '"
                                        data-row-beneficiary="' . e($beneficiaryName) . '"
                                        data-row-amount="' . e($amountFormatted) . '"
                                        data-row-delivery="' . e($deliveryStatus) . '"
                                        data-row-result="' . e($resultStatus) . '"
                                        data-row-notes="' . e($notesStr) . '"
                                        @click="openEditFromButton($el)"
                                        class="btn-secondary !h-7 !px-2.5 !text-xs inline-flex items-center gap-1"
                                    >
                                        <i class="bi bi-pencil-square"></i>
                                        <span>Ubah</span>
                                    </button>', ['placement' => 'left']) ?>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($rows === []): ?>
                    <?= render_table_empty_state(30, [
                        'img_class' => 'w-24 sm:w-28 h-auto mx-auto mb-2 object-contain select-none pointer-events-none',
                    ]) ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <!-- =========================================================
         FLOATING STICKY BULK BAR (SLIDES UP WHEN ROWS ARE CHECKED)
         ========================================================= -->
    <?php if (can('report.manage')): ?>
        <div
            x-show="selectedCount > 0"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed bottom-16 sm:bottom-20 inset-x-0 z-40 max-w-2xl mx-auto px-4 pointer-events-none"
            style="display: none;"
        >
            <div class="pointer-events-auto bg-[#2B2A2A] text-white p-3 sm:p-4 rounded-2xl shadow-2xl border border-gray-700 flex flex-wrap items-center justify-between gap-3">
                <form id="bulkStatusForm" method="post" action="<?= e(url('/reports/' . $batch['id'] . '/bulk-status')) ?>" class="flex flex-wrap items-center justify-between gap-3 w-full text-xs">
                    <?= csrf_field() ?>
                    <input type="hidden" name="return_query" value="<?= e($returnQuery) ?>">

                    <div class="flex items-center gap-2">
                        <span class="size-6 rounded-full bg-[#315B8C] text-white flex items-center justify-center font-semibold text-xs" x-text="selectedCount">0</span>
                        <span class="font-semibold text-gray-100">baris dipilih</span>
                    </div>

                    <div class="flex items-center gap-2 flex-wrap">
                        <select class="h-9 px-3 rounded-[9px] text-xs font-sans bg-gray-800 text-white border border-gray-600 focus:border-[#315B8C] focus:outline-none" name="delivery_status">
                            <option value="">Status Kirim...</option>
                            <option value="PENDING">Belum Terkirim</option>
                            <option value="SENT">Terkirim</option>
                        </select>

                        <select class="h-9 px-3 rounded-[9px] text-xs font-sans bg-gray-800 text-white border border-gray-600 focus:border-[#315B8C] focus:outline-none" name="result_status">
                            <option value="">Hasil...</option>
                            <option value="PENDING">Pending</option>
                            <option value="SUCCESS">Success</option>
                            <option value="FAILED">Failed</option>
                        </select>

                        <button class="btn-primary" type="submit">
                            <span>Terapkan</span>
                        </button>

                        <button type="button" @click="toggleAll(false)" class="btn-secondary !bg-gray-800 !text-gray-200 !border-gray-600 hover:!bg-gray-700">
                            <span>Batal</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <!-- =========================================================
         UNIVERSAL ROW STATUS EDIT MODAL (CLEAN & NON-CLIPPING)
         ========================================================= -->
    <?php if (can('report.manage')): ?>
        <template x-teleport="body">
            <div
                x-show="editModalOpen"
                x-cloak
                class="fixed inset-0 z-[9999] overflow-y-auto"
                role="dialog"
                aria-modal="true"
                aria-labelledby="modal-edit-row-title"
                style="display: none;"
                @keydown.escape.window="editModalOpen = false"
            >
                <!-- 1. Full-screen Backdrop -->
                <div
                    x-show="editModalOpen"
                    x-transition:enter="transition-opacity ease-out duration-150"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition-opacity ease-in duration-100"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 w-full h-full bg-gray-900/60 backdrop-blur-sm"
                    @click="editModalOpen = false"
                    aria-hidden="true"
                ></div>

                <!-- 2. Modal Positioning Container -->
                <div class="min-h-full flex items-center justify-center p-4 relative z-10 pointer-events-none">
                    <div
                        class="bg-white rounded-2xl max-w-lg w-full shadow-2xl border border-gray-200 text-left relative my-8 pointer-events-auto"
                        x-show="editModalOpen"
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
                                <i class="bi bi-pencil-square text-gray-800 text-lg"></i>
                                <div class="flex items-center gap-1.5">
                                    <strong id="modal-edit-row-title" class="text-sm font-semibold text-gray-900 block">
                                        Ubah Status Transaksi #<span x-text="activeRow.order"></span>
                                    </strong>
                                    <?= render_tooltip(
                                        'Perbarui status pengiriman, hasil transaksi, atau catatan operasional baris report ini.',
                                        '<span class="hs-tooltip-toggle info-toggle-btn cursor-help" aria-label="Info ubah status"><i class="bi bi-info-circle text-[10px] leading-none"></i></span>',
                                        ['placement' => 'top', 'contentClass' => 'z-[100000]']
                                    ) ?>
                                </div>
                            </div>
                            <button
                                type="button"
                                @click="editModalOpen = false"
                                class="modal-close"
                                aria-label="Tutup modal"
                                title="Tutup modal"
                            >
                                <i class="bi bi-x-lg text-xs leading-none"></i>
                            </button>
                        </div>

                        <!-- Modal Form Body -->
                        <form method="post" :action="'<?= e(url('/reports/' . $batch['id'] . '/rows/')) ?>' + activeRow.id + '/status'" class="m-0 p-0">
                            <?= csrf_field() ?>
                            <input type="hidden" name="return_query" value="<?= e($returnQuery) ?>">

                            <div class="p-5 space-y-4">
                                <!-- Transaction Preview Card -->
                                <div class="bg-[#F5F2F2] rounded-xl p-3.5 border border-gray-200/80 text-xs space-y-2">
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <span class="text-gray-500 block text-[11px] mb-0.5">Payer (Pengirim):</span>
                                            <strong class="text-gray-900 font-medium truncate block" x-text="activeRow.payer || '-'"></strong>
                                        </div>
                                        <div>
                                            <span class="text-gray-500 block text-[11px] mb-0.5">Beneficiary (Penerima):</span>
                                            <strong class="text-gray-900 font-medium truncate block" x-text="activeRow.beneficiary || '-'"></strong>
                                        </div>
                                    </div>
                                    <div class="pt-2 border-t border-gray-200/60 flex items-center justify-between">
                                        <span class="text-gray-500 text-[11px]">Nominal Transaksi:</span>
                                        <strong class="text-[#315B8C] font-semibold text-sm tabular-nums font-heading" x-text="activeRow.amount || '-'"></strong>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <!-- Custom Dropdown: Status Kirim -->
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 mb-1">Status Kirim</label>
                                        <div class="custom-select-root relative" :class="{ 'z-20': open }" x-data="{ open: false }" @click.outside="open = false">
                                            <input type="hidden" name="delivery_status" :value="activeRow.delivery">
                                            <button
                                                type="button"
                                                @click="open = !open"
                                                class="dropdown-toggle"
                                                :aria-expanded="open.toString()"
                                                aria-label="Status Kirim"
                                            >
                                                <i class="bi bi-send text-gray-400"></i>
                                                <span class="truncate text-gray-900" x-text="activeRow.delivery === 'SENT' ? 'Terkirim' : 'Belum Terkirim'"></span>
                                                <i class="bi bi-chevron-down text-xs text-gray-400 ml-auto transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                                            </button>
                                            <div
                                                x-show="open"
                                                x-cloak
                                                x-transition:leave="transition ease-in duration-100"
                                                x-transition:leave-start="opacity-100"
                                                x-transition:leave-end="opacity-0"
                                                :class="{ 'block': open, 'hidden': !open }"
                                                class="dropdown-menu absolute left-0 right-0 top-full mt-1.5 z-50 bg-white"
                                                style="display: none;"
                                                role="listbox"
                                            >
                                                <button
                                                    type="button"
                                                    class="dropdown-item justify-between"
                                                    :class="{ 'active': activeRow.delivery === 'PENDING' }"
                                                    @click="activeRow.delivery = 'PENDING'; open = false"
                                                >
                                                    <span>Belum Terkirim</span>
                                                    <i x-show="activeRow.delivery === 'PENDING'" class="bi bi-check2 text-[#315B8C] text-sm"></i>
                                                </button>
                                                <button
                                                    type="button"
                                                    class="dropdown-item justify-between"
                                                    :class="{ 'active': activeRow.delivery === 'SENT' }"
                                                    @click="activeRow.delivery = 'SENT'; open = false"
                                                >
                                                    <span>Terkirim</span>
                                                    <i x-show="activeRow.delivery === 'SENT'" class="bi bi-check2 text-[#315B8C] text-sm"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Custom Dropdown: Hasil Transaksi -->
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 mb-1">Hasil Transaksi</label>
                                        <div class="custom-select-root relative" :class="{ 'z-20': open }" x-data="{ open: false }" @click.outside="open = false">
                                            <input type="hidden" name="result_status" :value="activeRow.result">
                                            <button
                                                type="button"
                                                @click="open = !open"
                                                class="dropdown-toggle"
                                                :aria-expanded="open.toString()"
                                                aria-label="Hasil Transaksi"
                                            >
                                                <i class="bi bi-check-circle text-gray-400"></i>
                                                <span class="truncate text-gray-900" x-text="activeRow.result === 'SUCCESS' ? 'Success' : (activeRow.result === 'FAILED' ? 'Failed' : 'Pending')"></span>
                                                <i class="bi bi-chevron-down text-xs text-gray-400 ml-auto transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                                            </button>
                                            <div
                                                x-show="open"
                                                x-cloak
                                                x-transition:leave="transition ease-in duration-100"
                                                x-transition:leave-start="opacity-100"
                                                x-transition:leave-end="opacity-0"
                                                :class="{ 'block': open, 'hidden': !open }"
                                                class="dropdown-menu absolute left-0 right-0 top-full mt-1.5 z-50 bg-white"
                                                style="display: none;"
                                                role="listbox"
                                            >
                                                <button
                                                    type="button"
                                                    class="dropdown-item justify-between"
                                                    :class="{ 'active': activeRow.result === 'PENDING' }"
                                                    @click="activeRow.result = 'PENDING'; open = false"
                                                >
                                                    <span>Pending</span>
                                                    <i x-show="activeRow.result === 'PENDING'" class="bi bi-check2 text-[#315B8C] text-sm"></i>
                                                </button>
                                                <button
                                                    type="button"
                                                    class="dropdown-item justify-between"
                                                    :class="{ 'active': activeRow.result === 'SUCCESS' }"
                                                    @click="activeRow.result = 'SUCCESS'; open = false"
                                                >
                                                    <span>Success</span>
                                                    <i x-show="activeRow.result === 'SUCCESS'" class="bi bi-check2 text-[#315B8C] text-sm"></i>
                                                </button>
                                                <button
                                                    type="button"
                                                    class="dropdown-item justify-between"
                                                    :class="{ 'active': activeRow.result === 'FAILED' }"
                                                    @click="activeRow.result = 'FAILED'; open = false"
                                                >
                                                    <span>Failed</span>
                                                    <i x-show="activeRow.result === 'FAILED'" class="bi bi-check2 text-[#315B8C] text-sm"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Catatan Operasional -->
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1">Catatan Operasional</label>
                                    <textarea
                                        class="custom-form-textarea"
                                        name="notes"
                                        rows="2"
                                        x-model="activeRow.notes"
                                        placeholder="Catatan opsional operasional transaksi..."
                                    ></textarea>
                                </div>
                            </div>

                            <!-- Modal Footer -->
                            <div class="px-5 py-3.5 bg-gray-50/70 border-t border-gray-100 flex items-center justify-end gap-2 rounded-b-2xl">
                                <button
                                    type="button"
                                    @click="editModalOpen = false"
                                    class="btn-secondary"
                                >
                                    <span>Batal</span>
                                </button>
                                <button
                                    class="btn-primary"
                                    type="submit"
                                >
                                    <i class="bi bi-check2"></i>
                                    <span>Simpan Perubahan</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </template>
    <?php endif; ?>

</div>
