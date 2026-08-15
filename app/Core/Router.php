<?php

declare(strict_types=1);

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $path, mixed $handler): void
    {
        $this->routes['GET'][$this->normalizePath($path)] = $handler;
    }

    public function post(string $path, mixed $handler): void
    {
        $this->routes['POST'][$this->normalizePath($path)] = $handler;
    }

    public function dispatch(string $method, string $uri): void
    {
        $parsedUrl = parse_url($uri, PHP_URL_PATH) ?? '/';

        // Membersihkan subfolder jika diakses via Apache / Laragon
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $baseDir = dirname($scriptName);

        if ($baseDir !== '/' && $baseDir !== '\\' && str_starts_with(strtolower($parsedUrl), strtolower($baseDir))) {
            $parsedUrl = substr($parsedUrl, strlen($baseDir));
        }

        $path = $this->normalizePath($parsedUrl);
        $handler = $this->routes[$method][$path] ?? null;

        if (!$handler) {
            // http_response_code(404);
            // echo "<div style='font-family:sans-serif; text-align:center; padding-top:50px;'>";
            // echo "<h1 style='color:#e11d48;'>404 | Page Not Found</h1>";
            // echo "<p style='color:#64748b;'>URI yang diakses: <code>" . htmlspecialchars($uri) . "</code></p>";
            // echo "<p style='color:#64748b;'>Path terdeteksi: <code>" . htmlspecialchars($path) . "</code></p>";
            // echo "</div>";
            // return;
            http_response_code(404);
            echo "<div style='font-family:sans-serif; text-align:center; padding-top:50px;'>";
            echo "<h1 style='color:#e11d48;'>404 | Page Not Found</h1>";
            echo "<p>Path terdeteksi: <code>" . htmlspecialchars($path) . "</code></p>";
            echo "<h3>Route GET yang Terdaftar:</h3><pre>";
            print_r($this->routes['GET'] ?? []);
            echo "</pre></div>";
            return;
        }

        if (is_callable($handler)) {
            call_user_func($handler);
            return;
        }

        if (is_array($handler)) {
            [$controllerClass, $action] = $handler;
            if (class_exists($controllerClass)) {
                $controller = new $controllerClass();
                if (method_exists($controller, $action)) {
                    $controller->$action();
                    return;
                }
            }
        }

        http_response_code(500);
        echo "Controller [{$controllerClass}] atau Method [{$action}] tidak ditemukan.";
    }

    private function normalizePath(string $path): string
    {
        $path = trim($path, '/');
        return $path === '' ? '/' : '/' . $path;
    }
}
