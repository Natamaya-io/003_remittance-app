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
    $placeholder = (string) ($options['placeholder'] ?? ($mode === 'month' ? 'Pilih bulan...' : 'Pilih tanggal...'));
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
            class="form-datepicker flex items-center justify-between w-full text-xs text-start cursor-pointer select-none bg-white border border-gray-300 rounded-xl px-3 py-2 shadow-xs transition-colors focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
            :class="{ 'text-gray-900 font-medium': Boolean(value), 'text-gray-400': !value, 'border-blue-500 ring-1 ring-blue-500': open }"
            :aria-expanded="open.toString()"
        >
            <div class="flex items-center gap-2 truncate">
                <i class="bi <?= $iconClass ?> text-gray-400"></i>
                <span x-text="displayLabel || placeholder"></span>
            </div>
            <i class="bi bi-chevron-down text-[10px] text-gray-400 transition-transform duration-200 shrink-0 ms-1.5" :class="{ 'rotate-180': open }"></i>
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
            class="dropdown-menu absolute start-0 top-full mt-1.5 w-64 bg-white border border-gray-200 rounded-2xl p-3 shadow-xl z-50"
            style="display: none;"
        >
            <template x-if="mode === 'date'">
                <div>
                    <div class="flex items-center justify-between mb-2 pb-1 border-b border-gray-100">
                        <button type="button" @click.stop="prevMonth()" class="size-7 flex items-center justify-center rounded-lg hover:bg-gray-100 text-gray-600 transition-colors">
                            <i class="bi bi-chevron-left text-xs"></i>
                        </button>
                        <span class="text-xs font-bold text-gray-900" x-text="currentMonthName + ' ' + viewYear"></span>
                        <button type="button" @click.stop="nextMonth()" class="size-7 flex items-center justify-center rounded-lg hover:bg-gray-100 text-gray-600 transition-colors">
                            <i class="bi bi-chevron-right text-xs"></i>
                        </button>
                    </div>

                    <div class="grid grid-cols-7 gap-1 text-center mb-1">
                        <template x-for="dn in dayNames" :key="dn">
                            <span class="text-[10px] font-semibold text-gray-400" x-text="dn"></span>
                        </template>
                    </div>

                    <div class="grid grid-cols-7 gap-1 text-center">
                        <template x-for="(d, idx) in daysInMonth" :key="idx">
                            <button
                                type="button"
                                @click.stop="d.isCurrentMonth && selectDate(d.dateStr)"
                                :disabled="!d.isCurrentMonth"
                                class="size-7 mx-auto flex items-center justify-center rounded-lg text-xs font-medium transition-colors"
                                :class="{
                                    'text-gray-300 pointer-events-none': !d.isCurrentMonth,
                                    'bg-blue-600 text-white font-semibold shadow-xs': d.isSelected,
                                    'text-blue-600 font-bold hover:bg-blue-50': d.isToday && !d.isSelected,
                                    'text-gray-700 hover:bg-gray-100': d.isCurrentMonth && !d.isSelected && !d.isToday
                                }"
                                x-text="d.day"
                            ></button>
                        </template>
                    </div>

                    <div class="flex items-center justify-between pt-2.5 mt-2.5 border-t border-gray-100">
                        <button type="button" @click.stop="setToday()" class="text-[11px] font-semibold text-blue-600 hover:text-blue-700">Hari ini</button>
                        <button type="button" x-show="value" @click.stop="clear()" class="text-[11px] text-gray-400 hover:text-red-500">Hapus</button>
                    </div>
                </div>
            </template>

            <template x-if="mode === 'month'">
                <div>
                    <div class="flex items-center justify-between mb-3 pb-1 border-b border-gray-100">
                        <button type="button" @click.stop="prevYear()" class="size-7 flex items-center justify-center rounded-lg hover:bg-gray-100 text-gray-600 transition-colors">
                            <i class="bi bi-chevron-left text-xs"></i>
                        </button>
                        <span class="text-xs font-bold text-gray-900" x-text="viewYear"></span>
                        <button type="button" @click.stop="nextYear()" class="size-7 flex items-center justify-center rounded-lg hover:bg-gray-100 text-gray-600 transition-colors">
                            <i class="bi bi-chevron-right text-xs"></i>
                        </button>
                    </div>

                    <div class="grid grid-cols-3 gap-1.5">
                        <template x-for="(mName, mIdx) in monthNames" :key="mIdx">
                            <button
                                type="button"
                                @click.stop="selectMonth(mIdx)"
                                class="py-2 px-1 rounded-lg text-xs font-medium text-center transition-colors truncate"
                                :class="{
                                    'bg-blue-600 text-white font-semibold shadow-xs': value === (viewYear + '-' + String(mIdx + 1).padStart(2, '0')),
                                    'text-gray-700 hover:bg-gray-100': value !== (viewYear + '-' + String(mIdx + 1).padStart(2, '0'))
                                }"
                                x-text="mName"
                            ></button>
                        </template>
                    </div>

                    <div class="flex items-center justify-between pt-2.5 mt-2.5 border-t border-gray-100">
                        <button type="button" @click.stop="setToday()" class="text-[11px] font-semibold text-blue-600 hover:text-blue-700">Bulan ini</button>
                        <button type="button" x-show="value" @click.stop="clear()" class="text-[11px] text-gray-400 hover:text-red-500">Hapus</button>
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
 * Render Search Trigger Button (opens Search Modal)
 *
 * @param array{
 *   label?: string,
 *   icon?: string,
 *   showKbd?: bool,
 *   action?: string,
 *   title?: string,
 *   subtitle?: string,
 *   placeholder?: string,
 *   query?: string,
 *   extraParams?: array<string, string>,
 *   class?: string
 * } $options
 */
function render_search_modal_trigger(array $options = []): string
{
    $label = (string) ($options['label'] ?? 'Cari');
    $icon = (string) ($options['icon'] ?? 'bi-search');
    $showKbd = array_key_exists('showKbd', $options) ? (bool) $options['showKbd'] : true;
    $class = (string) ($options['class'] ?? 'btn btn-primary py-2 px-3.5 inline-flex items-center justify-center gap-x-1.5 text-xs font-semibold rounded-lg shrink-0 focus:outline-none');

    $modalConfig = [
        'action' => (string) ($options['action'] ?? url('/reports/search')),
        'title' => (string) ($options['title'] ?? 'Pencarian Transaksi'),
        'subtitle' => (string) ($options['subtitle'] ?? 'Ketik kata kunci lalu tekan Enter untuk mencari.'),
        'placeholder' => (string) ($options['placeholder'] ?? 'Ketik kata kunci transaksi...'),
        'query' => (string) ($options['query'] ?? ''),
        'extraParams' => (array) ($options['extraParams'] ?? []),
    ];

    $jsonConfig = json_encode($modalConfig, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);

    ob_start();
    ?>
    <button
        type="button"
        x-data
        @click.stop="$dispatch('open-search-modal', <?= e($jsonConfig) ?>)"
        onclick="event.stopPropagation(); window.openSearchModal ? window.openSearchModal(JSON.parse(this.dataset.searchConfig || '{}')) : null; return false;"
        data-search-config="<?= e($jsonConfig) ?>"
        class="<?= e($class) ?>"
        aria-label="<?= e($label) ?>"
        title="<?= e($label) ?> (Tekan / atau Ctrl+K)"
    >
        <i class="bi <?= e($icon) ?>"></i>
        <span><?= e($label) ?></span>
        <?php if ($showKbd): ?>
            <kbd class="ms-1 py-0.5 px-1.5 text-[10px] bg-white/20 border border-white/30 rounded text-white font-mono">/</kbd>
        <?php endif; ?>
    </button>
    <?php
    return (string) ob_get_clean();
}

/**
 * Render Heading with On-Demand Toggleable Information (Anti-Slop)
 *
 * Menggantikan teks deskriptif/subtitle yang menumpuk di bawah judul dengan
 * tombol info (i) yang dapat di-toggle secara elegan oleh pengguna.
 *
 * @param string $title Judul utama
 * @param string $info Teks informasi bantuan
 * @param array{
 *   titleTag?: string,
 *   titleClass?: string,
 *   wrapperClass?: string,
 *   infoClass?: string,
 *   icon?: string,
 *   initiallyOpen?: bool
 * } $options
 */
function render_heading_with_info(string $title, string $info, array $options = []): string
{
    $titleTag = (string) ($options['titleTag'] ?? 'strong');
    $titleClass = (string) ($options['titleClass'] ?? 'text-sm font-bold text-gray-900 block');
    $wrapperClass = (string) ($options['wrapperClass'] ?? 'flex-1');
    $infoClass = (string) ($options['infoClass'] ?? 'text-xs text-gray-500 mt-1 flex items-start gap-1.5');
    $icon = (string) ($options['icon'] ?? 'bi-info-circle');
    $initiallyOpen = (bool) ($options['initiallyOpen'] ?? false);
    $openState = $initiallyOpen ? 'true' : 'false';

    ob_start();
    ?>
    <div class="<?= e($wrapperClass) ?>" x-data="{ showInfo: <?= $openState ?> }">
        <div class="inline-flex items-center gap-1.5">
            <<?= $titleTag ?> class="<?= e($titleClass) ?>"><?= $title ?></<?= $titleTag ?>>
            <?php if (trim($info) !== ''): ?>
                <button
                    type="button"
                    @click.stop="showInfo = !showInfo"
                    class="info-toggle-btn"
                    :class="{ 'is-active': showInfo }"
                    :aria-expanded="showInfo.toString()"
                    aria-label="Tampilkan informasi bantuan"
                    title="Informasi bantuan"
                >
                    <i class="bi <?= e($icon) ?> text-[11px] leading-none"></i>
                </button>
            <?php endif; ?>
        </div>
        <?php if (trim($info) !== ''): ?>
            <div
                x-show="showInfo"
                x-cloak
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="<?= e($infoClass) ?>"
                style="display: none;"
            >
                <i class="bi <?= e($icon) ?> text-[11px] text-blue-500 mt-0.5 shrink-0" aria-hidden="true"></i>
                <span><?= $info ?></span>
            </div>
        <?php endif; ?>
    </div>
    <?php
    return (string) ob_get_clean();
}


