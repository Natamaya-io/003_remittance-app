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
$briJobs = (int) ($briJobs ?? 0);
$bniBatches = (int) ($bniBatches ?? 0);

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

$availableYears = is_array($availableYears ?? null) ? $availableYears : [date('Y')];

$selectedMonth = trim((string) ($filters['month'] ?? ''));
$selectedYear = trim((string) ($filters['year'] ?? ''));

// Normalisasi jika format YYYY-MM
if (preg_match('/^(\d{4})-(\d{2})$/', $selectedMonth, $mMatches)) {
    if ($selectedYear === '') {
        $selectedYear = $mMatches[1];
    }
    $selectedMonth = $mMatches[2];
} elseif (preg_match('/^[1-9]$/', $selectedMonth)) {
    $selectedMonth = sprintf('%02d', (int) $selectedMonth);
}

$monthsList = [
    '01' => 'Januari',
    '02' => 'Februari',
    '03' => 'Maret',
    '04' => 'April',
    '05' => 'Mei',
    '06' => 'Juni',
    '07' => 'Juli',
    '08' => 'Agustus',
    '09' => 'September',
    '10' => 'Oktober',
    '11' => 'November',
    '12' => 'Desember',
];

$activePeriodFilters = array_filter(
    [
        'month' => $selectedMonth,
        'year' => $selectedYear,
    ],
    static fn($val): bool => trim((string) $val) !== ''
);

$filterDescs = [];
if ($selectedMonth !== '' && isset($monthsList[$selectedMonth])) {
    $filterDescs[] = 'Bulan ' . $monthsList[$selectedMonth];
}
if ($selectedYear !== '') {
    $filterDescs[] = 'Tahun ' . $selectedYear;
}
$activeFilterSummary = implode(' · ', $filterDescs);
?>

<div class="max-w-7xl mx-auto space-y-6" x-data="{ openFilterModal: false, openTransactionModal: <?= (isset($_GET['action']) && $_GET['action'] === 'transaction') ? 'true' : 'false' ?> }" x-effect="if (openFilterModal || openTransactionModal) $nextTick(() => { window.HSStaticMethods && window.HSStaticMethods.autoInit(); })">

    <!-- =========================================================
         PAGE ACTIONS (PURE NAKED ARCHITECTURE)
         ========================================================= -->
    <div class="flex items-center justify-end gap-2 flex-wrap">
        <?= render_reports_search_bar([
            'action' => url('/reports/search'),
            'placeholder' => 'Cari transaksi...',
            'query' => (string) ($filters['q'] ?? ''),
        ]) ?>

        <!-- Tombol Filter Modal (Disamping Upload Excel & Mulai Transaksi) -->
        <button
            type="button"
            @click="openFilterModal = true"
            class="btn-primary"
            aria-label="Buka Filter Periode Laporan"
            title="Filter berdasarkan bulan dan tahun"
        >
            <i class="bi bi-sliders text-sm"></i>
            <span>Filter</span>
            <?php if (!empty($activePeriodFilters)): ?>
                <span class="btn-filter-badge" aria-label="<?= count($activePeriodFilters) ?> filter aktif"><?= count($activePeriodFilters) ?></span>
            <?php endif; ?>
        </button>

        <?php if (can('transaction.view') || can('transaction.manage')): ?>
            <button
                type="button"
                @click="openTransactionModal = true"
                class="btn-primary"
                aria-label="Buka Menu Mulai Transaksi"
                title="Mulai Transaksi BRI atau BNI"
            >
                <i class="bi bi-arrow-left-right text-sm"></i>
                <span>Mulai Transaksi</span>
            </button>
        <?php endif; ?>

        <?php if (can('report.manage')): ?>
            <a class="btn-primary" href="<?= e(url('/reports/create')) ?>" title="Upload Excel">
                <i class="bi bi-cloud-arrow-up text-sm"></i>
                <span>Upload Excel</span>
            </a>
        <?php endif; ?>
    </div>

    <!-- =========================================================
         MODAL FILTER TRANSAKSI LAPORAN (ALPINE.JS MODAL TELEPORTED TO BODY)
         ========================================================= -->
    <template x-teleport="body">
        <div
            x-show="openFilterModal"
            x-cloak
            class="fixed inset-0 z-[9999] overflow-y-auto"
            role="dialog"
            aria-modal="true"
            aria-labelledby="modal-filter-title"
            style="display: none;"
            @keydown.escape.window="openFilterModal = false"
        >
            <!-- 1. Full-screen Backdrop (Opacity Menutupi 100% Seluruh Halaman Tanpa Celah) -->
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

            <!-- 2. Modal Positioning Container (Terpusat Vertikal & Horizontal) -->
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
                                <strong id="modal-filter-title" class="text-sm font-semibold text-gray-900 block">Filter Periode Laporan</strong>
                                <?= render_tooltip(
                                    'Saring data laporan berdasarkan bulan dan tahun.',
                                    '<span class="hs-tooltip-toggle info-toggle-btn cursor-help" aria-label="Info filter periode"><i class="bi bi-info-circle text-[10px] leading-none"></i></span>',
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
                    <form method="get" action="<?= e(url('/reports')) ?>" class="m-0 p-0">
                        <?php if (($filters['q'] ?? '') !== ''): ?>
                            <input type="hidden" name="q" value="<?= e($filters['q']) ?>">
                        <?php endif; ?>

                        <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Dropdown Bulan -->
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Bulan</label>
                                <?php
                                $currMonthLabel = 'Semua bulan';
                                if ($selectedMonth !== '' && isset($monthsList[$selectedMonth])) {
                                    $currMonthLabel = $monthsList[$selectedMonth];
                                }
                                ?>
                                <div class="custom-select-root relative" :class="{ 'z-30': open }" x-data="{ open: false, selected: '<?= e(addslashes($selectedMonth)) ?>', label: '<?= e(addslashes($currMonthLabel)) ?>' }" @click.outside="open = false">
                                    <input type="hidden" name="month" :value="selected">
                                    <button
                                        type="button"
                                        @click="open = !open"
                                        class="dropdown-toggle"
                                        :aria-expanded="open.toString()"
                                        aria-label="Filter Bulan"
                                    >
                                        <i class="bi bi-calendar-month text-gray-400"></i>
                                        <span class="truncate text-gray-900" x-text="label"></span>
                                        <i class="bi bi-chevron-down text-xs text-gray-400 ml-auto transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                                    </button>

                                    <div
                                        class="dropdown-menu absolute left-0 right-0 top-full mt-1.5 max-h-60 overflow-y-auto z-50 bg-white"
                                        x-show="open"
                                        x-cloak
                                        x-transition:enter="transition ease-out duration-100"
                                        x-transition:enter-start="opacity-0"
                                        x-transition:enter-end="opacity-100"
                                        x-transition:leave="transition ease-in duration-75"
                                        x-transition:leave-start="opacity-100"
                                        x-transition:leave-end="opacity-0"
                                        :class="{ 'block': open, 'hidden': !open }"
                                        style="display: none;"
                                        role="listbox"
                                    >
                                        <button
                                            type="button"
                                            class="dropdown-item justify-between"
                                            :class="{ 'active': selected === '' }"
                                            @click="selected = ''; label = 'Semua bulan'; open = false"
                                        >
                                            <span>Semua bulan</span>
                                            <i x-show="selected === ''" class="bi bi-check2 text-blue-600 text-sm"></i>
                                        </button>
                                        <?php foreach ($monthsList as $mCode => $mName): ?>
                                            <?php $mCodeStr = sprintf('%02d', (int) $mCode); ?>
                                            <button
                                                type="button"
                                                class="dropdown-item justify-between"
                                                :class="{ 'active': selected === '<?= e(addslashes($mCodeStr)) ?>' }"
                                                @click="selected = '<?= e(addslashes($mCodeStr)) ?>'; label = '<?= e(addslashes((string) $mName)) ?>'; open = false"
                                            >
                                                <span><?= e((string) $mName) ?></span>
                                                <i x-show="selected === '<?= e(addslashes($mCodeStr)) ?>'" class="bi bi-check2 text-blue-600 text-sm"></i>
                                            </button>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Dropdown Tahun -->
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Tahun</label>
                                <?php
                                $currYearLabel = $selectedYear !== '' ? $selectedYear : 'Semua tahun';
                                ?>
                                <div class="custom-select-root relative" :class="{ 'z-20': open }" x-data="{ open: false, selected: '<?= e(addslashes($selectedYear)) ?>', label: '<?= e(addslashes($currYearLabel)) ?>' }" @click.outside="open = false">
                                    <input type="hidden" name="year" :value="selected">
                                    <button
                                        type="button"
                                        @click="open = !open"
                                        class="dropdown-toggle"
                                        :aria-expanded="open.toString()"
                                        aria-label="Filter Tahun"
                                    >
                                        <i class="bi bi-calendar3 text-gray-400"></i>
                                        <span class="truncate text-gray-900" x-text="label"></span>
                                        <i class="bi bi-chevron-down text-xs text-gray-400 ml-auto transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                                    </button>

                                    <div
                                        class="dropdown-menu absolute left-0 right-0 top-full mt-1.5 max-h-60 overflow-y-auto z-50 bg-white"
                                        x-show="open"
                                        x-cloak
                                        x-transition:enter="transition ease-out duration-100"
                                        x-transition:enter-start="opacity-0"
                                        x-transition:enter-end="opacity-100"
                                        x-transition:leave="transition ease-in duration-75"
                                        x-transition:leave-start="opacity-100"
                                        x-transition:leave-end="opacity-0"
                                        :class="{ 'block': open, 'hidden': !open }"
                                        style="display: none;"
                                        role="listbox"
                                    >
                                        <button
                                            type="button"
                                            class="dropdown-item justify-between"
                                            :class="{ 'active': selected === '' }"
                                            @click="selected = ''; label = 'Semua tahun'; open = false"
                                        >
                                            <span>Semua tahun</span>
                                            <i x-show="selected === ''" class="bi bi-check2 text-blue-600 text-sm"></i>
                                        </button>
                                        <?php foreach ($availableYears as $yOption): ?>
                                            <?php $yOption = (string) $yOption; if ($yOption === '') continue; ?>
                                            <button
                                                type="button"
                                                class="dropdown-item justify-between"
                                                :class="{ 'active': selected === '<?= e(addslashes($yOption)) ?>' }"
                                                @click="selected = '<?= e(addslashes($yOption)) ?>'; label = '<?= e(addslashes($yOption)) ?>'; open = false"
                                            >
                                                <span><?= e($yOption) ?></span>
                                                <i x-show="selected === '<?= e(addslashes($yOption)) ?>'" class="bi bi-check2 text-blue-600 text-sm"></i>
                                            </button>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Footer -->
                        <div class="px-5 py-3.5 bg-gray-50/70 border-t border-gray-100 flex items-center justify-between gap-3 rounded-b-2xl">
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

    <!-- =========================================================
         MODAL MULAI TRANSAKSI PERBANKAN (BRI & BNI HUB)
         ========================================================= -->
    <template x-teleport="body">
        <div
            x-show="openTransactionModal"
            x-cloak
            class="fixed inset-0 z-[9999] overflow-y-auto"
            role="dialog"
            aria-modal="true"
            aria-labelledby="modal-transaction-title"
            style="display: none;"
            @keydown.escape.window="openTransactionModal = false"
        >
            <!-- 1. Full-screen Backdrop -->
            <div
                x-show="openTransactionModal"
                x-transition:enter="transition-opacity ease-out duration-150"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity ease-in duration-100"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 w-full h-full bg-gray-900/60 backdrop-blur-sm"
                @click="openTransactionModal = false"
                aria-hidden="true"
            ></div>

            <!-- 2. Modal Positioning Container (Terpusat Vertikal & Horizontal) -->
            <div class="min-h-full flex items-center justify-center p-4 relative z-10 pointer-events-none">
                <div
                    class="bg-white rounded-2xl max-w-xl w-full shadow-2xl border border-gray-200 text-left relative my-8 pointer-events-auto overflow-hidden"
                    x-show="openTransactionModal"
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    x-data="{
                        searchQuery: '',
                        emptyResults: false,
                        canScrollLeft: false,
                        canScrollRight: false,
                        filterCards() {
                            const el = this.$refs.bankSlider;
                            if (!el) return;
                            const cards = el.querySelectorAll('.bank-slider-card');
                            const q = (this.searchQuery || '').toLowerCase().trim();
                            let visibleCount = 0;
                            cards.forEach(card => {
                                const text = (card.innerText || card.textContent || '').toLowerCase();
                                const matches = !q || text.includes(q);
                                card.style.display = matches ? '' : 'none';
                                if (matches) visibleCount++;
                            });
                            this.emptyResults = visibleCount === 0;
                            this.checkScroll();
                        },
                        checkScroll() {
                            this.$nextTick(() => {
                                const el = this.$refs.bankSlider;
                                if (!el) return;
                                const maxScroll = el.scrollWidth - el.clientWidth;
                                if (maxScroll <= 2) {
                                    this.canScrollLeft = false;
                                    this.canScrollRight = false;
                                } else {
                                    this.canScrollLeft = el.scrollLeft > 4;
                                    this.canScrollRight = Math.ceil(el.scrollLeft + el.clientWidth) < (el.scrollWidth - 4);
                                }
                            });
                        },
                        scrollSlider(dir) {
                            const el = this.$refs.bankSlider;
                            if (!el) return;
                            const cards = Array.from(el.querySelectorAll('.bank-slider-card:not([style*=\'display: none\'])'));
                            if (cards.length <= 1) return;
                            const cardWidth = cards[0].offsetWidth + 16;
                            const maxScroll = el.scrollWidth - el.clientWidth;
                            if (maxScroll <= 2) return;

                            const currentScroll = el.scrollLeft;
                            if (dir === 'right') {
                                if (currentScroll >= maxScroll - 10) {
                                    el.scrollTo({ left: 0, behavior: 'smooth' });
                                } else {
                                    el.scrollBy({ left: cardWidth, behavior: 'smooth' });
                                }
                            } else {
                                if (currentScroll <= 10) {
                                    el.scrollTo({ left: maxScroll, behavior: 'smooth' });
                                } else {
                                    el.scrollBy({ left: -cardWidth, behavior: 'smooth' });
                                }
                            }
                            setTimeout(() => this.checkScroll(), 320);
                        }
                    }"
                    x-effect="if (openTransactionModal) { searchQuery = ''; $nextTick(() => { filterCards(); if ($refs.bankSlider) $refs.bankSlider.scrollLeft = 0; setTimeout(() => checkScroll(), 80); }); }"
                    @resize.window.debounce.150ms="checkScroll()"
                >
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 bg-white">
                        <div class="flex items-center gap-1.5">
                            <strong id="modal-transaction-title" class="text-sm sm:text-base font-semibold text-gray-900 block font-heading">Pilih Jalur Transaksi Perbankan</strong>
                            <?= render_tooltip(
                                'Pilih bank koresponden untuk memproses transaksi remitansi.',
                                '<span class="hs-tooltip-toggle info-toggle-btn cursor-help" aria-label="Info jalur transaksi"><i class="bi bi-info-circle text-[10px] leading-none"></i></span>',
                                ['placement' => 'top', 'contentClass' => 'z-[100000]']
                            ) ?>
                        </div>

                        <button
                            type="button"
                            @click="openTransactionModal = false"
                            class="modal-close"
                            aria-label="Tutup modal"
                            title="Tutup modal"
                        >
                            <i class="bi bi-x-lg text-xs leading-none"></i>
                        </button>
                    </div>

                    <!-- Modal Search Bar (Pencarian Jalur Bank) -->
                    <div class="px-6 pt-3.5 pb-3 bg-white border-b border-gray-100">
                        <div class="custom-search-bar relative flex items-center w-full">
                            <span class="search-leading-icon pointer-events-none" aria-hidden="true">
                                <i class="bi bi-search text-gray-400"></i>
                            </span>

                            <input
                                x-ref="bankSearchInput"
                                x-model="searchQuery"
                                @input="filterCards()"
                                @keydown.escape.stop="if (searchQuery) { searchQuery = ''; filterCards(); } else { openTransactionModal = false; }"
                                type="search"
                                placeholder="Cari nama bank atau jalur transaksi..."
                                class="custom-search-input h-10 w-full text-xs sm:text-sm ps-9 pe-9 rounded-xl bg-[#F5F2F2] border border-gray-200 focus:bg-white focus:border-[#315B8C] focus:ring-2 focus:ring-[#315B8C]/15 text-gray-900 placeholder:text-gray-400 font-normal outline-none transition-all"
                                autocomplete="off"
                                spellcheck="false"
                            >

                            <!-- Tombol Clear Pencarian -->
                            <button
                                type="button"
                                x-show="searchQuery && searchQuery.length > 0"
                                x-cloak
                                @click="searchQuery = ''; filterCards(); $refs.bankSearchInput.focus()"
                                class="search-clear-btn"
                                aria-label="Hapus kata kunci pencarian"
                                title="Hapus pencarian"
                            >
                                <i class="bi bi-x-circle-fill text-xs leading-none text-gray-400 hover:text-gray-600"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Modal Body: Opsi Jalur Bank (Slider Carousel Terproteksi) -->
                    <div class="p-6 bg-gray-50/50 relative">
                        <!-- Floating Left Arrow -->
                        <button
                            type="button"
                            @click="scrollSlider('left')"
                            :disabled="!canScrollLeft || emptyResults"
                            x-show="!emptyResults"
                            class="slider-floating-btn slider-floating-left"
                            aria-label="Geser pilihan bank ke kiri"
                            title="Geser ke kiri"
                        >
                            <i class="bi bi-chevron-left text-xs leading-none"></i>
                        </button>

                        <!-- Floating Right Arrow -->
                        <button
                            type="button"
                            @click="scrollSlider('right')"
                            :disabled="!canScrollRight || emptyResults"
                            x-show="!emptyResults"
                            class="slider-floating-btn slider-floating-right"
                            aria-label="Geser pilihan bank ke kanan"
                            title="Geser ke kanan"
                        >
                            <i class="bi bi-chevron-right text-xs leading-none"></i>
                        </button>

                        <!-- Empty State saat pencarian tidak menemukan bank -->
                        <div
                            x-show="emptyResults"
                            x-cloak
                            class="w-full py-10 px-6 text-center bg-white border border-gray-200 rounded-xl shadow-xs"
                        >
                            <i class="bi bi-search text-3xl text-gray-300 mb-2 block"></i>
                            <h4 class="text-sm font-semibold font-heading text-gray-900 mb-1">Jalur Bank Tidak Ditemukan</h4>
                            <p class="text-xs text-gray-500 font-sans max-w-sm mx-auto leading-relaxed">
                                Tidak ada opsi bank yang cocok dengan kata kunci "<span x-text="searchQuery" class="font-medium text-gray-800"></span>". Coba cari dengan singkatan atau nama bank lain.
                            </p>
                            <button
                                type="button"
                                @click="searchQuery = ''; filterCards(); $refs.bankSearchInput.focus()"
                                class="btn-secondary mt-4 inline-flex items-center gap-1.5"
                            >
                                <i class="bi bi-arrow-counterclockwise text-xs"></i>
                                <span>Reset Pencarian</span>
                            </button>
                        </div>

                        <!-- Slider Track -->
                        <div
                            x-show="!emptyResults"
                            x-ref="bankSlider"
                            @scroll.passive="checkScroll()"
                            class="bank-slider-track flex items-stretch gap-4 overflow-x-auto scroll-smooth snap-x snap-mandatory py-1 px-1 -my-1 -mx-1 no-scrollbar w-full"
                        >
                            <!-- KARTU BANK BRI -->
                            <div class="bank-slider-card w-[calc(100%-1.5rem)] sm:w-[calc(50%-0.5rem)] min-w-[250px] sm:min-w-0 shrink-0 snap-start bg-white border border-gray-200 rounded-xl p-4 sm:p-5 shadow-xs hover:border-[#315B8C] transition-all flex flex-col justify-between space-y-3.5">
                                <div>
                                    <img src="<?= e(url('/bri-logo.svg')) ?>" alt="Bank BRI" class="h-8 sm:h-9 w-auto max-w-[110px] mb-2.5 object-contain select-none">
                                    <span class="text-[10.5px] font-semibold tracking-wider text-gray-500 uppercase block">BANK BRI</span>
                                    <h4 class="text-sm font-semibold font-heading text-gray-900 mt-0.5 mb-1">Transaksi BRI</h4>
                                    <p class="text-xs text-gray-600 leading-relaxed font-sans m-0">Input data, bentuk tabel batch, bersihkan tanda baca otomatis, lalu ekspor Excel BRI final.</p>
                                </div>
                                <div class="pt-3 border-t border-gray-100 flex items-center justify-between gap-2 mt-auto">
                                    <span class="text-xs text-gray-500 font-sans tabular-nums"><strong class="font-semibold text-gray-800"><?= number_format($briJobs, 0, ',', '.') ?></strong> batch</span>
                                    <div class="flex items-center gap-1.5">
                                        <a class="py-1.5 px-2.5 text-xs font-medium rounded-lg border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 transition-colors" href="<?= url('/transactions/bri/history') ?>">
                                            Riwayat
                                        </a>
                                        <?php if (can('transaction.manage')): ?>
                                            <a class="btn-primary btn-primary-sm" href="<?= url('/transactions/bri') ?>">
                                                <i class="bi bi-file-earmark-arrow-up text-xs"></i>
                                                <span>Import</span>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- KARTU BANK BNI -->
                            <div class="bank-slider-card w-[calc(100%-1.5rem)] sm:w-[calc(50%-0.5rem)] min-w-[250px] sm:min-w-0 shrink-0 snap-start bg-white border border-gray-200 rounded-xl p-4 sm:p-5 shadow-xs hover:border-[#315B8C] transition-all flex flex-col justify-between space-y-3.5">
                                <div>
                                    <img src="<?= e(url('/bni-logo.svg')) ?>" alt="Bank BNI" class="h-8 sm:h-9 w-auto max-w-[110px] mb-2.5 object-contain select-none">
                                    <span class="text-[10.5px] font-semibold tracking-wider text-gray-500 uppercase block">BANK BNI</span>
                                    <h4 class="text-sm font-semibold font-heading text-gray-900 mt-0.5 mb-1">Transaksi BNI</h4>
                                    <p class="text-xs text-gray-600 leading-relaxed font-sans m-0">Copy-paste tabel Excel, bentuk data formulir Kiriman Uang BNI, lalu cetak PDF atau preview.</p>
                                </div>
                                <div class="pt-3 border-t border-gray-100 flex items-center justify-between gap-2 mt-auto">
                                    <span class="text-xs text-gray-500 font-sans tabular-nums"><strong class="font-semibold text-gray-800"><?= number_format($bniBatches, 0, ',', '.') ?></strong> batch</span>
                                    <div class="flex items-center gap-1.5">
                                        <?php if (can('transaction.manage')): ?>
                                            <a class="btn-primary btn-primary-sm" href="<?= url('/transactions/bni') ?>">
                                                <i class="bi bi-file-earmark-arrow-up text-xs"></i>
                                                <span>Import</span>
                                            </a>
                                        <?php else: ?>
                                            <a class="py-1.5 px-2.5 text-xs font-medium rounded-lg border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 transition-colors" href="<?= url('/transactions/bni') ?>">
                                                Lihat BNI
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 flex items-center justify-end">
                        <button
                            type="button"
                            @click="openTransactionModal = false"
                            class="btn-secondary"
                        >
                            <span>Tutup</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <?php if (!empty($activePeriodFilters)): ?>
        <!-- FLOATING TOAST NOTIFICATION (GEMINI.MD SECTION 16 - ZERO LAYOUT SHIFT) -->
        <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.remitToast && typeof window.remitToast.info === 'function') {
                window.remitToast.info('Menampilkan data periode: <?= e(addslashes($activeFilterSummary)) ?>', {
                    title: 'Filter Periode Aktif',
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

                            $monthUrl = '/reports/month/' . rawurlencode($monthKey);
                            $lastUpload = isset($month['last_upload']) ? (string) $month['last_upload'] : null;
                            ?>
                            <tr class="even:bg-gray-50/80 [data-no-hover]">
                                <td class="py-4 px-5 whitespace-nowrap">
                                    <div class="flex items-center gap-x-3">
                                        <img src="<?= e(url('/reports_date.svg')) ?>" alt="Ikon Tanggal" class="size-9 object-contain select-none shrink-0" width="36" height="36" loading="lazy" decoding="async">
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
                                    <a class="btn-primary btn-primary-sm" href="<?= e(url($monthUrl)) ?>">
                                        <span>Buka Bulan</span>
                                        <i class="bi bi-arrow-right"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <?php if ($months === []): ?>
                            <?= render_table_empty_state(9, [
                                'action_url' => url('/reports/create'),
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

                $monthUrl = '/reports/month/' . rawurlencode($monthKey);
                $lastUpload = isset($month['last_upload']) ? (string) $month['last_upload'] : null;
                ?>
                <article class="bg-white border border-gray-200 rounded-2xl p-5 shadow-xs hover:shadow-md transition-shadow duration-200 flex flex-col justify-between space-y-4">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-x-3 min-w-0">
                                <img src="<?= e(url('/reports_date.svg')) ?>" alt="Ikon Tanggal" class="size-10 object-contain select-none shrink-0" width="40" height="40" loading="lazy" decoding="async">
                                <div class="min-w-0">
                                    <strong class="text-base font-semibold font-heading text-gray-900 block truncate"><?= e($monthLabel) ?></strong>
                                </div>
                            </div>
                        </div>

                        <!-- Stats Pills -->
                        <div class="flex flex-wrap items-center gap-1.5 text-xs">
                            <span class="inline-flex items-center gap-1 py-1 px-2.5 rounded-full bg-gray-100 text-gray-700 font-medium tabular-nums">
                                <i class="bi bi-files text-gray-400"></i>
                                <?= number_format($fileCount, 0, ',', '.') ?> berkas
                            </span>
                            <span class="inline-flex items-center gap-1 py-1 px-2.5 rounded-full bg-gray-100 text-gray-700 font-medium tabular-nums">
                                <i class="bi bi-calendar-event text-gray-400"></i>
                                <?= number_format($dateCount, 0, ',', '.') ?> hari
                            </span>
                            <span class="inline-flex items-center gap-1 py-1 px-2.5 rounded-full bg-gray-100 text-gray-700 font-medium tabular-nums">
                                <i class="bi bi-people text-gray-400"></i>
                                <?= number_format($teamCount, 0, ',', '.') ?> tim
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
                        <a class="btn-primary btn-primary-sm" href="<?= e(url($monthUrl)) ?>">
                            <span>Buka Bulan</span>
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>

            <?php if ($months === []): ?>
                <?= render_empty_state([
                    'action_url' => url('/reports/create'),
                    'action_label' => 'Upload Excel',
                    'action_permission' => 'report.manage',
                ]) ?>
            <?php endif; ?>
        </div>

    </div>

</div>
