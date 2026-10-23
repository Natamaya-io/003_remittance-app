<?php

declare(strict_types=1);

namespace App\Core;

final class Router
{
    private array $routes = [];

    public function get(string $path, array $handler): void { $this->add('GET', $path, $handler); }
    public function post(string $path, array $handler): void { $this->add('POST', $path, $handler); }
    public function put(string $path, array $handler): void { $this->add('PUT', $path, $handler); }
    public function delete(string $path, array $handler): void { $this->add('DELETE', $path, $handler); }

    private function add(string $method, string $path, array $handler): void
    {
        $this->routes[] = compact('method', 'path', 'handler');
    }

    public function dispatch(): void
    {
        $method = strtoupper($_POST['_method'] ?? $_SERVER['REQUEST_METHOD'] ?? 'GET');
        $requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        $basePath = parse_url((string) App::config('app.url'), PHP_URL_PATH) ?: '';
        if ($basePath !== '' && str_starts_with($requestPath, $basePath)) {
            $requestPath = substr($requestPath, strlen($basePath)) ?: '/';
        }
        $requestPath = '/' . ltrim($requestPath, '/');
        $requestPath = $requestPath !== '/' ? rtrim($requestPath, '/') : '/';

        if (in_array($method, ['POST', 'PUT', 'DELETE'], true) && !Csrf::verify($_POST['_token'] ?? null)) {
            http_response_code(419);
            echo 'Sesi keamanan kedaluwarsa. Muat ulang halaman dan coba lagi.';
            return;
        }

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }
            $pattern = preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', '(?P<$1>[^/]+)', $route['path']);
            if (preg_match('#^' . $pattern . '$#', $requestPath, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                [$class, $action] = $route['handler'];
                $controller = new $class();
                $controller->{$action}(...array_values($params));
                return;
            }
        }

        http_response_code(404);
        View::render('errors/404', ['title' => 'Halaman Tidak Ditemukan'], Auth::check() ? 'app' : 'guest');
    }
}
