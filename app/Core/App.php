<?php

declare(strict_types=1);

namespace App\Core;

use PDO;

final class App
{
    private static array $container = [];

    public static function set(string $key, mixed $value): void
    {
        self::$container[$key] = $value;
    }

    public static function get(string $key): mixed
    {
        if (!array_key_exists($key, self::$container)) {
            throw new \RuntimeException("Service '{$key}' belum terdaftar.");
        }
        return self::$container[$key];
    }

    public static function db(): PDO
    {
        return self::get('db');
    }

    public static function config(?string $key = null, mixed $default = null): mixed
    {
        $config = self::get('config');
        if ($key === null) {
            return $config;
        }
        $segments = explode('.', $key);
        $value = $config;
        foreach ($segments as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            $value = $value[$segment];
        }
        return $value;
    }
}
