<?php

declare(strict_types=1);

use App\Core\App;
use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Flash;

function e(mixed $value): string
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES,
        'UTF-8'
    );
}

function url(string $path = ''): string
{
    return rtrim(
        (string) App::config('app.url'),
        '/'
    ) . '/' . ltrim($path, '/');
}

function asset(string $path): string
{
    return url(
        'assets/' . ltrim($path, '/')
    );
}

function redirect(string $path): void
{
    header(
        'Location: ' .
        (
            str_starts_with($path, 'http')
                ? $path
                : url($path)
        )
    );

    exit;
}

function csrf_field(): string
{
    return '<input type="hidden" name="_token" value="' .
        e(Csrf::token()) .
        '">';
}

function method_field(string $method): string
{
    return '<input type="hidden" name="_method" value="' .
        e(strtoupper($method)) .
        '">';
}

/*
|--------------------------------------------------------------------------
| Session Helper
|--------------------------------------------------------------------------
*/

function session(
    ?string $key = null,
    mixed $default = null
): mixed {
    if ($key === null || $key === '') {
        return $_SESSION;
    }

    $value = $_SESSION;

    foreach (explode('.', $key) as $segment) {
        if (
            !is_array($value) ||
            !array_key_exists($segment, $value)
        ) {
            return $default;
        }

        $value = $value[$segment];
    }

    return $value;
}

function old(
    string $key,
    mixed $default = ''
): mixed {
    return $_SESSION['_old'][$key]
        ?? $default;
}

function remember_old(array $data): void
{
    $_SESSION['_old'] = $data;
}

function clear_old(): void
{
    unset($_SESSION['_old']);
}

function flash(
    string $type,
    string $message
): void {
    Flash::set(
        $type,
        $message
    );
}

function auth_user(): ?array
{
    return Auth::user();
}

function can(string $permission): bool
{
    return Auth::can($permission);
}

function format_date(
    ?string $date,
    string $format = 'd/m/Y'
): string {
    if (!$date) {
        return '-';
    }

    try {
        return (new DateTime($date))
            ->format($format);
    } catch (Throwable) {
        return (string) $date;
    }
}

function format_datetime(
    ?string $date
): string {
    return format_date(
        $date,
        'd/m/Y H:i'
    );
}

function format_report_name_from_date(?string $date): string
{
    $date = trim((string) $date);
    if ($date === '') {
        return '';
    }
    $months = [
        1 => 'JANUARI', 2 => 'FEBRUARI', 3 => 'MARET', 4 => 'APRIL',
        5 => 'MEI', 6 => 'JUNI', 7 => 'JULI', 8 => 'AGUSTUS',
        9 => 'SEPTEMBER', 10 => 'OKTOBER', 11 => 'NOVEMBER', 12 => 'DESEMBER',
    ];
    try {
        $dt = new DateTime($date);
        $d = $dt->format('j');
        $m = $months[(int) $dt->format('n')] ?? strtoupper($dt->format('F'));
        $y = $dt->format('Y');
        return "LAPORAN HARIAN - {$d} {$m} {$y}";
    } catch (Throwable) {
        return '';
    }
}

function format_money(
    float|int|string|null $amount,
    string $currency = 'IDR'
): string {
    if (
        $amount === null ||
        $amount === ''
    ) {
        return '-';
    }

    return $currency .
        ' ' .
        number_format(
            (float) $amount,
            2,
            ',',
            '.'
        );
}

function selected(
    mixed $value,
    mixed $expected
): string {
    return (string) $value === (string) $expected
        ? 'selected'
        : '';
}

function checked(
    bool $condition
): string {
    return $condition
        ? 'checked'
        : '';
}

function status_badge(
    string $status
): string {
    $normalized = strtolower(trim($status));

    $colorClasses = match (true) {
        str_contains($normalized, 'success'),
        str_contains($normalized, 'sukses'),
        $normalized === 'paid',
        $normalized === 'resolved',
        $normalized === 'sent',
        $normalized === 'terkirim'
            => 'bg-emerald-50 text-emerald-700 border-emerald-200',

        str_contains($normalized, 'pending'),
        $normalized === 'open',
        $normalized === 'draft',
        $normalized === 'in_progress'
            => 'bg-amber-50 text-amber-700 border-amber-200',

        str_contains($normalized, 'refund'),
        str_contains($normalized, 'reject'),
        $normalized === 'overdue',
        $normalized === 'cancelled',
        $normalized === 'failed'
            => 'bg-red-50 text-red-700 border-red-200',

        default
            => 'bg-gray-100 text-gray-700 border-gray-200',
    };

    return '<span class="inline-flex items-center gap-1 py-0.5 px-2.5 rounded-full text-[11px] font-semibold border ' .
        $colorClasses .
        '">' .
        e(
            strtoupper(
                str_replace(
                    '_',
                    ' ',
                    $status
                )
            )
        ) .
        '</span>';
}

/**
 * Resolusi Status Operasional Terpadu untuk Laporan RemitGo!
 * Memetakan kombinasi 2 dimensi (delivery_status & result_status) menjadi 1 status operasional cerdas.
 *
 * @param string $deliveryStatus
 * @param string $resultStatus
 * @return array{key: string, label: string, full_label: string, badge_class: string, icon: string, delivery: string, result: string}
 */
function report_operational_status(string $deliveryStatus, string $resultStatus): array
{
    $delivery = strtoupper(trim($deliveryStatus));
    $result = strtoupper(trim($resultStatus));

    if ($result === 'SUCCESS') {
        return [
            'key' => 'SUCCESS',
            'label' => 'Sukses',
            'full_label' => 'Sukses (Selesai)',
            'badge_class' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'icon' => 'bi-check-lg',
            'delivery' => 'SENT',
            'result' => 'SUCCESS',
        ];
    }

    if ($result === 'FAILED') {
        return [
            'key' => 'FAILED',
            'label' => 'Gagal / Retur',
            'full_label' => 'Gagal / Retur (Ditolak Bank)',
            'badge_class' => 'bg-red-50 text-red-700 border-red-200',
            'icon' => 'bi-x-lg',
            'delivery' => 'SENT',
            'result' => 'FAILED',
        ];
    }

    if ($delivery === 'SENT') {
        return [
            'key' => 'SENT',
            'label' => 'Terkirim (Proses Bank)',
            'full_label' => 'Terkirim (Menunggu Kliring Bank)',
            'badge_class' => 'bg-blue-50 text-blue-700 border-blue-200',
            'icon' => 'bi-send-check',
            'delivery' => 'SENT',
            'result' => 'PENDING',
        ];
    }

    return [
        'key' => 'NEW',
        'label' => 'Antrean Baru',
        'full_label' => 'Antrean Baru (Belum Kirim)',
        'badge_class' => 'bg-amber-50 text-amber-700 border-amber-200',
        'icon' => 'bi-hourglass-split',
        'delivery' => 'PENDING',
        'result' => 'PENDING',
    ];
}

/**
 * Render Badge Status Operasional Terpadu untuk Tabel Laporan
 */
function render_report_status_badge(string $deliveryStatus, string $resultStatus): string
{
    $status = report_operational_status($deliveryStatus, $resultStatus);
    return '<span class="inline-flex items-center gap-1 py-0.5 px-2 rounded-full text-[11px] font-semibold border ' .
        $status['badge_class'] .
        '"><i class="bi ' .
        $status['icon'] .
        '"></i> ' .
        e($status['label']) .
        '</span>';
}

/**
 * Daftar Preset Status Operasional untuk Filter & Form
 *
 * @return array<string, array{label: string, short_label: string, delivery: string, result: string, icon: string, color: string}>
 */
function get_report_status_presets(): array
{
    return [
        'NEW' => [
            'label' => 'Antrean Baru (Belum Kirim)',
            'short_label' => 'Antrean Baru',
            'delivery' => 'PENDING',
            'result' => 'PENDING',
            'icon' => 'bi-hourglass-split',
            'color' => '#d97706',
        ],
        'SENT' => [
            'label' => 'Terkirim (Menunggu Kliring Bank)',
            'short_label' => 'Terkirim (Proses Bank)',
            'delivery' => 'SENT',
            'result' => 'PENDING',
            'icon' => 'bi-send-check',
            'color' => '#2563eb',
        ],
        'SUCCESS' => [
            'label' => 'Sukses (Selesai)',
            'short_label' => 'Sukses',
            'delivery' => 'SENT',
            'result' => 'SUCCESS',
            'icon' => 'bi-check-lg',
            'color' => '#059669',
        ],
        'FAILED' => [
            'label' => 'Gagal / Retur',
            'short_label' => 'Gagal / Retur',
            'delivery' => 'SENT',
            'result' => 'FAILED',
            'icon' => 'bi-x-lg',
            'color' => '#dc2626',
        ],
    ];
}

/**
 * Render Custom Asphalt/Tailwind Datepicker Component (Alpine.js driven)
 * Completely eliminates browser-native OS calendar popups.
 *
 * @param string $name Form field name (hidden input)
 * @param string $value Current value ('YYYY-MM-DD' or 'YYYY-MM')
 * @param array{
 *   mode?: 'date'|'month',
 *   placeholder?: string,
 *   required?: bool,
 *   class?: string,
 *   id?: string
 * } $options
 */
function render_datepicker(string $name, string $value = '', array $options = []): string
{
    $mode = ($options['mode'] ?? 'date') === 'month' ? 'month' : 'date';
    $placeholder = (string) ($options['placeholder'] ?? ($mode === 'month' ? 'Pilih bulan' : 'Pilih tanggal'));
    $required = !empty($options['required']);
    $extraClass = (string) ($options['class'] ?? '');
    $id = (string) ($options['id'] ?? ('dp_' . preg_replace('/[^a-zA-Z0-9_]/', '_', $name) . '_' . bin2hex(random_bytes(2))));

    $jsonConfig = json_encode([
        'name' => $name,
        'value' => $value,
        'mode' => $mode,
        'placeholder' => $placeholder,
        'required' => $required,
    ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);

    $iconClass = $mode === 'month' ? 'bi-calendar-month' : 'bi-calendar3';

    ob_start();
    ?>
    <div class="custom-datepicker-root relative <?= e($extraClass) ?>" x-data="customDatePicker(<?= e($jsonConfig) ?>)" @click.outside="open = false">
        <input type="hidden" :name="name" :value="value" <?= $required ? 'required' : '' ?>>
        <button
            type="button"
            id="<?= e($id) ?>"
            @click="open = !open"
            class="custom-datepicker-trigger"
            :class="{ 'is-open': open }"
            :aria-expanded="open.toString()"
        >
            <div class="flex items-center gap-2 truncate">
                <i class="bi <?= $iconClass ?> text-gray-500 text-sm"></i>
                <span
                    x-text="displayLabel || placeholder"
                    :class="Boolean(value) ? 'datepicker-value' : 'datepicker-placeholder'"
                ></span>
            </div>
            <i class="bi bi-chevron-down text-xs text-gray-400 transition-transform duration-200 shrink-0 ms-1.5" :class="{ 'rotate-180': open }"></i>
        </button>

        <div
            x-show="open"
            x-cloak
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            :class="{ 'block': open, 'hidden': !open }"
            class="datepicker-popover"
            style="display: none;"
        >
            <template x-if="mode === 'date'">
                <div>
                    <div class="datepicker-popover-header">
                        <button type="button" @click.stop="prevMonth()" class="datepicker-nav-btn" aria-label="Bulan sebelumnya">
                            <i class="bi bi-chevron-left"></i>
                        </button>
                        <span class="datepicker-popover-title" x-text="currentMonthName + ' ' + viewYear"></span>
                        <button type="button" @click.stop="nextMonth()" class="datepicker-nav-btn" aria-label="Bulan berikutnya">
                            <i class="bi bi-chevron-right"></i>
                        </button>
                    </div>

                    <div class="datepicker-days-header">
                        <template x-for="dn in dayNames" :key="dn">
                            <span class="datepicker-day-name" x-text="dn"></span>
                        </template>
                    </div>

                    <div class="datepicker-days-grid">
                        <template x-for="(d, idx) in daysInMonth" :key="idx">
                            <button
                                type="button"
                                @click.stop="d.isCurrentMonth && selectDate(d.dateStr)"
                                :disabled="!d.isCurrentMonth"
                                class="datepicker-date-btn"
                                :class="{
                                    'is-outside': !d.isCurrentMonth,
                                    'is-selected': d.isSelected,
                                    'is-today': d.isToday && !d.isSelected
                                }"
                                x-text="d.day"
                            ></button>
                        </template>
                    </div>

                    <div class="datepicker-popover-footer">
                        <button type="button" @click.stop="setToday()" class="datepicker-footer-btn btn-today">Hari ini</button>
                        <button type="button" x-show="value" @click.stop="clear()" class="datepicker-footer-btn btn-clear">Hapus</button>
                    </div>
                </div>
            </template>

            <template x-if="mode === 'month'">
                <div>
                    <div class="datepicker-popover-header">
                        <button type="button" @click.stop="prevYear()" class="datepicker-nav-btn" aria-label="Tahun sebelumnya">
                            <i class="bi bi-chevron-left"></i>
                        </button>
                        <span class="datepicker-popover-title" x-text="viewYear"></span>
                        <button type="button" @click.stop="nextYear()" class="datepicker-nav-btn" aria-label="Tahun berikutnya">
                            <i class="bi bi-chevron-right"></i>
                        </button>
                    </div>

                    <div class="datepicker-months-grid">
                        <template x-for="(mName, mIdx) in monthNames" :key="mIdx">
                            <button
                                type="button"
                                @click.stop="selectMonth(mIdx)"
                                class="datepicker-month-btn"
                                :class="{
                                    'is-selected': value === (viewYear + '-' + String(mIdx + 1).padStart(2, '0')),
                                    'is-current': (new Date().getFullYear() === viewYear && new Date().getMonth() === mIdx) && value !== (viewYear + '-' + String(mIdx + 1).padStart(2, '0'))
                                }"
                                x-text="mName"
                            ></button>
                        </template>
                    </div>

                    <div class="datepicker-popover-footer">
                        <button type="button" @click.stop="setToday()" class="datepicker-footer-btn btn-today">Bulan ini</button>
                        <button type="button" x-show="value" @click.stop="clear()" class="datepicker-footer-btn btn-clear">Hapus</button>
                    </div>
                </div>
            </template>
        </div>
    </div>
    <?php
    return (string) ob_get_clean();
}

/**
 * Render Custom Asphalt/Tailwind Search Bar Component (Alpine.js driven)
 * Completely eliminates browser-native search pseudo-elements and styles.
 *
 * @param string $name Form field name (default 'q')
 * @param string $value Current search query
 * @param array{
 *   placeholder?: string,
 *   required?: bool,
 *   autofocus?: bool,
 *   size?: 'md'|'sm'|'pill',
 *   wrapperClass?: string,
 *   inputClass?: string,
 *   id?: string,
 *   showKbd?: bool,
 *   clearable?: bool
 * } $options
 */
function render_search_input(string $name = 'q', string $value = '', array $options = []): string
{
    $placeholder = (string) ($options['placeholder'] ?? 'Ketik kata kunci pencarian...');
    $required = !empty($options['required']);
    $autofocus = !empty($options['autofocus']);
    $size = (string) ($options['size'] ?? 'md');
    $wrapperClass = (string) ($options['wrapperClass'] ?? '');
    $inputClass = (string) ($options['inputClass'] ?? '');
    $id = (string) ($options['id'] ?? ('search_' . preg_replace('/[^a-zA-Z0-9_]/', '_', $name) . '_' . bin2hex(random_bytes(2))));
    $showKbd = array_key_exists('showKbd', $options) ? (bool) $options['showKbd'] : true;
    $clearable = array_key_exists('clearable', $options) ? (bool) $options['clearable'] : true;

    $sizeClass = match ($size) {
        'sm' => 'custom-search-input-sm',
        'pill' => 'custom-search-input-pill',
        default => '',
    };

    ob_start();
    ?>
    <div class="custom-search-bar <?= e($wrapperClass) ?>" x-data="{ query: <?= e(json_encode((string) $value)) ?> }">
        <span class="search-leading-icon" aria-hidden="true">
            <i class="bi bi-search"></i>
        </span>
        <input
            x-ref="searchInput"
            x-model="query"
            type="search"
            name="<?= e($name) ?>"
            id="<?= e($id) ?>"
            class="custom-search-input <?= e($sizeClass) ?> <?= e($inputClass) ?>"
            placeholder="<?= e($placeholder) ?>"
            value="<?= e($value) ?>"
            autocomplete="off"
            <?= $required ? 'required' : '' ?>
            <?= $autofocus ? 'autofocus' : '' ?>
        >
        <?php if ($clearable): ?>
            <button
                type="button"
                x-show="query && query.length > 0"
                x-cloak
                @click="query = ''; $refs.searchInput.value = ''; $refs.searchInput.focus(); $refs.searchInput.dispatchEvent(new Event('input'))"
                class="search-clear-btn"
                aria-label="Hapus kata kunci pencarian"
                title="Hapus pencarian"
            >
                <i class="bi bi-x-circle-fill text-xs leading-none"></i>
            </button>
        <?php endif; ?>
        <?php if ($showKbd): ?>
            <kbd x-show="!query || query.length === 0" class="search-kbd-hint" aria-hidden="true">/</kbd>
        <?php endif; ?>
    </div>
    <?php
    return (string) ob_get_clean();
}

/**
 * Render Search Trigger Button (opens Command Palette Search Modal)
 *
 * Mengikuti spesifikasi Cloudflare Command Palette:
 * - Tampilan bar pencarian elegan dengan leading search icon
 * - Placeholder teks yang bersih dan netral
 * - Trailing keyboard shortcut badge (Ctrl K / /)
 *
 * @param array{
 *   style?: 'bar'|'button',
 *   label?: string,
 *   icon?: string,
 *   showKbd?: bool,
 *   kbdText?: string,
 *   action?: string,
 *   placeholder?: string,
 *   query?: string,
 *   extraParams?: array<string, string>,
 *   class?: string
 * } $options
 */
/**
 * Render Real Inline Search Bar with Operation Tips Dropdown
 *
 * Mengikuti prinsip Search Bar Asli (Real Input, No Dummy Button, No Esc Button):
 * - Input pencarian interaktif langsung di halaman (<input type="search">)
 * - Bebas dari tombol pemicu modal palsu (<button>)
 * - Dropdown tips operasional (payer:, benef:, bank:, tim:, date:, inv:) muncul langsung di bawah input saat fokus
 * - Keyboard navigation (panah atas/bawah, Enter, Esc untuk tutup dropdown)
 * - Indikator pintasan keyboard (/) dan tombol clear (x)
 *
 * @param array{
 *   action?: string,
 *   placeholder?: string,
 *   query?: string,
 *   extraParams?: array<string, string>,
 *   class?: string,
 *   widthClass?: string,
 *   showKbd?: bool,
 *   kbdText?: string
 * } $options
 */
function render_reports_search_bar(array $options = []): string
{
    $action = (string) ($options['action'] ?? url('/reports/search'));
    $placeholder = (string) ($options['placeholder'] ?? 'Cari transaksi...');
    $query = (string) ($options['query'] ?? '');
    $extraParams = (array) ($options['extraParams'] ?? []);
    $widthClass = (string) ($options['widthClass'] ?? 'w-full sm:w-72 md:w-80');
    $id = 'reports_search_' . bin2hex(random_bytes(2));

    $config = [
        'action' => $action,
        'query' => $query,
        'extraParams' => $extraParams,
    ];
    $jsonConfig = json_encode($config, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);

    ob_start();
    ?>
    <div
        class="relative <?= e($widthClass) ?>"
        x-data="remitInlineSearch(<?= e($jsonConfig) ?>)"
        @click.outside="isOpen = false"
        @keydown.escape="isOpen = false"
    >
        <form :action="action" method="get" @submit="handleSubmit($event)" class="m-0 p-0">
            <?php foreach ($extraParams as $epKey => $epVal): ?>
                <input type="hidden" name="<?= e($epKey) ?>" value="<?= e($epVal) ?>">
            <?php endforeach; ?>

            <div class="custom-search-bar relative flex items-center w-full">
                <span class="search-leading-icon pointer-events-none" aria-hidden="true">
                    <i class="bi bi-search text-gray-400"></i>
                </span>

                <input
                    x-ref="searchInput"
                    x-model="query"
                    @focus="isOpen = true"
                    @input="isOpen = true"
                    @keydown="handleKeydown($event)"
                    type="search"
                    name="q"
                    id="<?= e($id) ?>"
                    class="custom-search-input h-10 w-full text-xs sm:text-sm ps-9 pe-9 rounded-xl bg-white border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-gray-900 placeholder:text-gray-400 font-normal outline-none transition-colors"
                    placeholder="<?= e($placeholder) ?>"
                    value="<?= e($query) ?>"
                    autocomplete="off"
                    spellcheck="false"
                >

                <!-- Clear button (hanya muncul saat query terisi) -->
                <button
                    type="button"
                    x-show="query && query.length > 0"
                    x-cloak
                    @click="query = ''; $refs.searchInput.focus()"
                    class="search-clear-btn"
                    aria-label="Hapus pencarian"
                    title="Hapus pencarian"
                >
                    <i class="bi bi-x-circle-fill text-xs leading-none"></i>
                </button>

                <!-- Keyboard shortcut hint (/) saat kosong -->
                <kbd
                    x-show="!query || query.length === 0"
                    class="search-kbd-hint pointer-events-none"
                    aria-hidden="true"
                >/</kbd>
            </div>
        </form>

        <!-- Dropdown Cara Operasikan & Live Suggestion Popover (Melekat langsung di bawah search bar) -->
        <div
            x-show="isOpen"
            x-cloak
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="opacity-0 translate-y-1"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-1"
            class="absolute left-0 right-0 sm:right-auto sm:w-[420px] top-full mt-1.5 z-50 bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden text-left"
            style="display: none;"
        >
            <div x-ref="suggestionsList" class="max-h-72 overflow-y-auto p-1.5 select-none">
                <!-- Live Search Action (ketika query diisi) -->
                <template x-if="query.trim().length > 0">
                    <div
                        class="flex items-center justify-between px-3 py-2 rounded-lg text-xs sm:text-sm cursor-pointer transition-colors"
                        :class="selectedIndex === 0 ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-50'"
                        :data-selected="selectedIndex === 0 ? 'true' : 'false'"
                        @mouseenter="selectedIndex = 0"
                        @click="executeSearch(query)"
                    >
                        <div class="flex items-center gap-2.5 min-w-0">
                            <i class="bi bi-search text-sm text-gray-400 shrink-0"></i>
                            <div class="truncate">
                                <span class="font-medium text-gray-900">Cari transaksi</span>
                                <span class="text-gray-400 mx-1">—</span>
                                <span class="text-gray-500 font-normal" x-text="'&quot;' + query.trim() + '&quot;'"></span>
                            </div>
                        </div>
                        <i x-show="selectedIndex === 0" class="bi bi-arrow-right text-gray-400 text-xs shrink-0 ms-2"></i>
                    </div>
                </template>

                <!-- Search tips (Cara Operasikan Search Bar) -->
                <template x-if="filteredTips.length > 0">
                    <div class="mt-1 mb-0.5">
                        <div class="px-2.5 pt-1.5 pb-1 text-[11px] font-medium text-gray-400 uppercase tracking-wider select-none">Search tips</div>
                        <div class="space-y-0.5">
                            <template x-for="(tip, idx) in filteredTips" :key="tip.id">
                                <div
                                    class="flex items-center justify-between px-2.5 py-1.5 rounded-lg text-xs cursor-pointer transition-colors"
                                    :class="selectedIndex === getItemIndex(tip) ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-50'"
                                    :data-selected="selectedIndex === getItemIndex(tip) ? 'true' : 'false'"
                                    @mouseenter="selectedIndex = getItemIndex(tip)"
                                    @click="selectTip(tip)"
                                >
                                    <div class="flex items-center gap-2 min-w-0">
                                        <i class="bi text-xs text-gray-400 shrink-0" :class="tip.icon"></i>
                                        <div class="truncate">
                                            <span class="font-medium text-gray-900 font-mono text-[11px]" x-text="tip.prefix"></span>
                                            <span class="text-gray-400 mx-1">—</span>
                                            <span class="text-gray-500 font-normal text-xs" x-text="tip.desc"></span>
                                        </div>
                                    </div>
                                    <i x-show="selectedIndex === getItemIndex(tip)" class="bi bi-arrow-right text-gray-400 text-xs shrink-0 ms-1.5"></i>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Footer Bar (Cara Operasikan via Keyboard) -->
            <div class="px-3 py-1.5 border-t border-gray-100 bg-gray-50/70 flex items-center justify-between text-[11px] text-gray-400 select-none">
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center gap-1">
                        <span class="font-mono text-gray-500 text-[10px]">↑</span>
                        <span class="font-mono text-gray-500 text-[10px]">↓</span>
                        <span>navigasi</span>
                    </span>
                    <span class="inline-flex items-center gap-1">
                        <span class="font-mono text-gray-500 text-[10px]">↵</span>
                        <span>pilih / cari</span>
                    </span>
                </div>
                <span class="inline-flex items-center gap-1">
                    <span class="font-mono text-gray-500 text-[10px]">esc</span>
                    <span>tutup</span>
                </span>
            </div>
        </div>
    </div>
    <?php
    return (string) ob_get_clean();
}

/**
 * Backward compatibility alias for render_reports_search_bar
 */
function render_search_modal_trigger(array $options = []): string
{
    return render_reports_search_bar($options);
}

/**
 * Render Heading with On-Demand Hover Information (Hover-Only Law)
 *
 * Menampilkan judul yang bersih dengan ikon info (i) interaktif di sampingnya.
 * Teks informasi bantuan HANYA muncul ketika kursor mouse di-hover di atas ikon (i),
 * bebas klik dan tidak menggeser layout halaman.
 *
 * @param string $title Judul utama
 * @param string $info Teks informasi bantuan
 * @param array{
 *   titleTag?: string,
 *   titleClass?: string,
 *   wrapperClass?: string,
 *   icon?: string,
 *   placement?: 'top'|'bottom'|'left'|'right'
 * } $options
 */
function render_heading_with_info(string $title, string $info, array $options = []): string
{
    $titleTag = (string) ($options['titleTag'] ?? 'strong');
    $titleClass = (string) ($options['titleClass'] ?? 'text-sm font-bold text-gray-900 block');
    $wrapperClass = (string) ($options['wrapperClass'] ?? 'inline-flex items-center gap-1.5');
    $icon = (string) ($options['icon'] ?? 'bi-info-circle');
    $placement = (string) ($options['placement'] ?? 'top');

    ob_start();
    ?>
    <div class="<?= e($wrapperClass) ?>">
        <<?= $titleTag ?> class="<?= e($titleClass) ?>"><?= $title ?></<?= $titleTag ?>>
        <?php if (trim($info) !== ''): ?>
            <?= render_tooltip(
                $info,
                '<span class="hs-tooltip-toggle info-toggle-btn cursor-help" aria-label="Informasi bantuan"><i class="bi ' . e($icon) . ' text-xs leading-none"></i></span>',
                ['placement' => $placement]
            ) ?>
        <?php endif; ?>
    </div>
    <?php
    return (string) ob_get_clean();
}

/**
 * Render Tooltip Kustom (Hover-Only Law - GEMINI.md Bagian 10)
 *
 * Menghasilkan komponen tooltip yang 100% patuh aturan hover-only:
 * - Muncul hanya saat hover, langsung tertutup saat kursor keluar
 * - Bebas pemicu klik (anti-click), pointer-events: none
 * - Zero-transform (hanya memanipulasi opasitas/visibilitas)
 * - Tampilan Asphalt solid (#111827 / gray-900, teks putih, ukuran nyaman)
 *
 * @param string $text Teks petunjuk singkat (1-3 kata) atau kalimat info
 * @param string $slot Konten HTML elemen pemicu (tombol, tautan, badge, atau ikon)
 * @param array{
 *   placement?: 'top'|'bottom'|'left'|'right',
 *   wrapperClass?: string,
 *   contentClass?: string
 * } $options
 */
function render_tooltip(string $text, string $slot, array $options = []): string
{
    $placement = (string) ($options['placement'] ?? 'top');
    $wrapperClass = (string) ($options['wrapperClass'] ?? 'hs-tooltip inline-flex');
    $contentClass = (string) ($options['contentClass'] ?? '');

    // Konfigurasi Preline UI HSTooltip: Scope window untuk teleportasi ke body (anti-kliping tabel)
    if (!str_contains($wrapperClass, '[--scope:')) {
        $wrapperClass .= ' [--scope:window]';
    }
    if (!str_contains($wrapperClass, '[--strategy:')) {
        $wrapperClass .= ' [--strategy:fixed]';
    }
    if (!str_contains($wrapperClass, '[--placement:')) {
        $wrapperClass .= ' [--placement:' . $placement . ']';
    }

    $tooltipOptions = json_encode([
        'placement' => $placement,
        'strategy'  => 'fixed',
        'scope'     => 'window',
    ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);

    $isLong = mb_strlen($text) > 35;
    $layoutClass = $isLong
        ? 'max-w-xs text-center whitespace-normal leading-relaxed'
        : 'whitespace-nowrap';

    ob_start();
    ?>
    <div class="<?= e($wrapperClass) ?>" data-hs-tooltip-options='<?= e($tooltipOptions) ?>'>
        <?= $slot ?>
        <span
            class="hs-tooltip-content hs-tooltip-shown:opacity-100 hs-tooltip-shown:visible opacity-0 transition-opacity inline-block absolute invisible z-[100000] py-1.5 px-3 bg-gray-900 text-white text-xs font-medium rounded-xl shadow-lg border border-gray-800/80 pointer-events-none <?= e($layoutClass) ?> <?= e($contentClass) ?>"
            role="tooltip"
        >
            <?= e($text) ?>
        </span>
    </div>
    <?php
    return (string) ob_get_clean();
}

/**
 * Render Toggle Tampilan Antara Tabel dan Kartu (UI Kustom)
 *
 * Menampilkan opsi Tabel (dengan ikon tabel) dan Kartu (dengan ikon kartu):
 * - Menggunakan Alpine.js reactive state ($modelVar)
 * - Ikon tabel (bi-table) dan ikon kartu (bi-grid-fill) berdiri telanjang berdampingan dengan label
 * - Skema warna solid Deep Brand Blue (#315B8C) & Asphalt Base (#F5F2F2)
 * - Zero-transform, bebas pergeseran liar
 * - Aksesibilitas WCAG (role="group", :aria-pressed)
 *
 * @param string $modelVar Variabel model Alpine (default: 'viewMode')
 * @param array{
 *   table_label?: string,
 *   card_label?: string,
 *   label?: string,
 *   class?: string
 * } $options
 */
/**
 * Ambil preferensi mode tampilan laporan (tabel vs kartu) dari Cookie browser
 */
function get_report_view_mode(): string
{
    $mode = (string) ($_COOKIE['remitgo_report_view_mode'] ?? 'table');
    return in_array($mode, ['table', 'list'], true) ? $mode : 'table';
}

/**
 * Render Toggle Tampilan Antara Tabel dan Kartu (UI Kustom)
 *
 * Menampilkan opsi Tabel (dengan ikon tabel) dan Kartu (dengan ikon kartu):
 * - Status aktif solid #315B8C (Deep Brand Blue) dengan teks putih
 * - Status non-aktif abu-abu transparan dengan hover lembut
 * - Aksesibilitas WCAG (role="group", :aria-pressed)
 *
 * @param string $modelVar Variabel model Alpine (default: 'viewMode')
 * @param array{
 *   table_label?: string,
 *   card_label?: string,
 *   label?: string,
 *   class?: string,
 *   current?: string
 * } $options
 */
function render_table_view_toggle(string $modelVar = 'viewMode', array $options = []): string
{
    $tableLabel = (string) ($options['table_label'] ?? 'Tabel');
    $cardLabel = (string) ($options['card_label'] ?? 'Kartu');
    $wrapperClass = (string) ($options['class'] ?? '');
    $currentMode = (string) ($options['current'] ?? get_report_view_mode());

    ob_start();
    ?>
    <div
        class="custom-table-toggle-btn custom-text-toggle <?= e($wrapperClass) ?>"
        role="group"
        aria-label="Pilih tampilan tabel atau kartu"
    >
        <button
            type="button"
            @click="<?= e($modelVar) ?> = 'table'"
            class="text-toggle-option text-table switch-option <?= $currentMode === 'table' ? 'is-active' : '' ?>"
            :class="{ 'is-active': <?= e($modelVar) ?> === 'table' }"
            :aria-pressed="<?= e($modelVar) ?> === 'table'"
            title="Tampilkan dalam bentuk tabel"
        >
            <i class="bi bi-table text-sm" aria-hidden="true"></i>
            <span><?= e($tableLabel) ?></span>
        </button>

        <button
            type="button"
            @click="<?= e($modelVar) ?> = 'list'"
            class="text-toggle-option text-card switch-option <?= $currentMode === 'list' ? 'is-active' : '' ?>"
            :class="{ 'is-active': <?= e($modelVar) ?> === 'list' }"
            :aria-pressed="<?= e($modelVar) ?> === 'list'"
            title="Tampilkan dalam bentuk kartu"
        >
            <i class="bi bi-grid-fill text-sm" aria-hidden="true"></i>
            <span><?= e($cardLabel) ?></span>
        </button>
    </div>
    <?php
    return (string) ob_get_clean();
}

/**
 * Render Empty State Kartu/Kontainer Kustom (GEMINI.md - Standarisasi UI "Tidak ada data yang cocok")
 *
 * Menghasilkan komponen empty state standar dengan ilustrasi data_not_found.png,
 * judul terstandarisasi "Tidak ada data yang cocok", dan tombol aksi opsional (.btn-primary).
 *
 * @param array{
 *   title?: string,
 *   action_url?: string,
 *   action_label?: string,
 *   action_icon?: string,
 *   action_permission?: string,
 *   wrapper_class?: string,
 *   img_class?: string
 * } $options
 */
function render_empty_state(array $options = []): string
{
    $title = (string) ($options['title'] ?? 'Tidak ada data yang cocok');
    $actionUrl = (string) ($options['action_url'] ?? '');
    $actionLabel = (string) ($options['action_label'] ?? 'Upload Excel');
    $actionIcon = (string) ($options['action_icon'] ?? 'bi-cloud-arrow-up');
    $actionPermission = (string) ($options['action_permission'] ?? 'report.manage');
    $wrapperClass = (string) ($options['wrapper_class'] ?? 'col-span-full bg-white border border-gray-200 rounded-2xl p-12 text-center shadow-xs');
    $imgClass = (string) ($options['img_class'] ?? 'w-32 sm:w-40 h-auto mx-auto mb-4 object-contain select-none pointer-events-none');

    ob_start();
    ?>
    <div class="<?= e($wrapperClass) ?>">
        <img
            src="<?= e(url('/data_not_found.png')) ?>"
            alt="<?= e($title) ?>"
            class="<?= e($imgClass) ?>"
            loading="lazy"
        >
        <h3 class="text-base font-semibold font-heading text-gray-800 mb-4"><?= e($title) ?></h3>
        <?php if ($actionUrl !== '' && ($actionPermission === '' || can($actionPermission))): ?>
            <a class="btn-primary" href="<?= e($actionUrl) ?>">
                <i class="bi <?= e($actionIcon) ?> text-sm"></i>
                <span><?= e($actionLabel) ?></span>
            </a>
        <?php endif; ?>
    </div>
    <?php
    return (string) ob_get_clean();
}

/**
 * Render Empty State Baris Tabel Kustom (GEMINI.md - Standarisasi UI "Tidak ada data yang cocok")
 *
 * Menghasilkan baris tabel (<tr><td colspan="...">) dengan ilustrasi data_not_found.png,
 * judul terstandarisasi "Tidak ada data yang cocok", dan tombol aksi opsional.
 *
 * @param int $colspan Jumlah kolom tabel yang direntangkan
 * @param array{
 *   title?: string,
 *   action_url?: string,
 *   action_label?: string,
 *   action_icon?: string,
 *   action_permission?: string,
 *   img_class?: string
 * } $options
 */
function render_table_empty_state(int $colspan, array $options = []): string
{
    $title = (string) ($options['title'] ?? 'Tidak ada data yang cocok');
    $actionUrl = (string) ($options['action_url'] ?? '');
    $actionLabel = (string) ($options['action_label'] ?? 'Upload Excel');
    $actionIcon = (string) ($options['action_icon'] ?? 'bi-cloud-arrow-up');
    $actionPermission = (string) ($options['action_permission'] ?? 'report.manage');
    $imgClass = (string) ($options['img_class'] ?? 'w-28 sm:w-32 h-auto mx-auto mb-2 object-contain select-none pointer-events-none');

    ob_start();
    ?>
    <tr>
        <td colspan="<?= (int) $colspan ?>" class="text-center py-12 px-6">
            <div class="flex flex-col items-center justify-center gap-2">
                <img
                    src="<?= e(url('/data_not_found.png')) ?>"
                    alt="<?= e($title) ?>"
                    class="<?= e($imgClass) ?>"
                    loading="lazy"
                >
                <strong class="text-sm font-semibold font-heading text-gray-800"><?= e($title) ?></strong>
                <?php if ($actionUrl !== '' && ($actionPermission === '' || can($actionPermission))): ?>
                    <div class="mt-2">
                        <a class="btn-primary" href="<?= e($actionUrl) ?>">
                            <i class="bi <?= e($actionIcon) ?> text-sm"></i>
                            <span><?= e($actionLabel) ?></span>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </td>
    </tr>
    <?php
    return (string) ob_get_clean();
}




