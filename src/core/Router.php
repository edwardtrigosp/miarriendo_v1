<?php

/**
 * Router minimalista (front controller).
 *
 * Registra rutas por método HTTP y despacha la petición actual
 * al controlador/acción correspondiente.
 */
class Router
{
    /** @var array<string, array<string, callable|array>> */
    private array $routes = [
        'GET'  => [],
        'POST' => [],
    ];

    /** Registra una ruta GET. */
    public function get(string $path, array|callable $handler): void
    {
        $this->routes['GET'][$this->normalize($path)] = $handler;
    }

    /** Registra una ruta POST. */
    public function post(string $path, array|callable $handler): void
    {
        $this->routes['POST'][$this->normalize($path)] = $handler;
    }

    /** Despacha la petición actual a su handler o muestra un 404. */
    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri    = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $path   = $this->normalize($uri);

        $handler = $this->routes[$method][$path] ?? null;

        if ($handler === null) {
            http_response_code(404);
            view('404', ['title' => 'Página no encontrada | 404']);
            return;
        }

        // Handler tipo [Clase::class, 'metodo']
        if (is_array($handler)) {
            [$class, $action] = $handler;
            (new $class())->$action();
            return;
        }

        // Handler tipo función anónima
        $handler();
    }

    /** Normaliza la ruta: sin slash final, mínimo '/'. */
    private function normalize(string $path): string
    {
        $path = rtrim($path, '/');
        return $path === '' ? '/' : $path;
    }
}
