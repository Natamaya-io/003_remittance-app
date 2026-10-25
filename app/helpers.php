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