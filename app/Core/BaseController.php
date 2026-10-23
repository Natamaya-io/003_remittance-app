<?php

declare(strict_types=1);

namespace App\Core;

abstract class BaseController
{
    protected function view(string $view, array $data = [], string $layout = 'app'): void
    {
        View::render($view, $data, $layout);
    }

    protected function permission(string $permission): void
    {
        Auth::requirePermission($permission);
    }

    protected function input(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }
}
