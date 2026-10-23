<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| IDE HELPER
|--------------------------------------------------------------------------
|
| File ini hanya digunakan agar VS Code / Intelephense mengetahui
| definisi helper global aplikasi.
|
| File ini TIDAK perlu require/include ke aplikasi.
|
*/

if (false) {


    function url(
        string $path = ''
    ): string {

        return $path;

    }


    function asset(
        string $path
    ): string {

        return $path;

    }


    function e(
        mixed $value
    ): string {

        return (string) $value;

    }


    function can(
        string $permission
    ): bool {

        return false;

    }


    function auth_user(): ?array
    {
        return null;
    }


    function redirect(
        string $path
    ): void {
    }


    function session(
        ?string $key = null,
        mixed $default = null
    ): mixed {

        return $default;

    }


    function csrf_field(): string
    {
        return '';
    }


    function method_field(
        string $method
    ): string {

        return '';

    }


    function old(
        string $key,
        mixed $default = ''
    ): mixed {

        return $default;

    }


    function flash(
        string $type,
        string $message
    ): void {
    }


    function format_date(
        ?string $date,
        string $format = 'd/m/Y'
    ): string {

        return '';

    }


    function format_datetime(
        ?string $date
    ): string {

        return '';

    }


    function format_money(
        float|int|string|null $amount,
        string $currency = 'IDR'
    ): string {

        return '';

    }


    function selected(
        mixed $value,
        mixed $expected
    ): string {

        return '';

    }


    function checked(
        bool $condition
    ): string {

        return '';

    }


    function status_badge(
        string $status
    ): string {

        return '';

    }

}