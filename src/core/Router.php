<?php

/**
 * Router minimalista (front controller).
 *
 * Soporta parámetros en la ruta con la sintaxis {nombre}, p. ej.:
 *   $router->get('/propiedad/{id}', [PropiedadController::class, 'detalle']);
 * El valor capturado se pasa como argumento al método del controlador.
 */
class Router
{
    /** @var array<string, array<int, array{0:string,1:array|callable}>> */
    private array $routes = [
        'GET'  => [],
        'POST' => [],
    ];

    /** Registra una ruta GET. */
    public function get(string $path, array|callable $handler): void
    {
        $this->routes['GET'][] = [$this->compilar($path), $handler];
    }

    /** Registra una ruta POST. */
    public function post(string $path, array|callable $handler): void
    {
        $this->routes['POST'][] = [$this->compilar($path), $handler];
    }

    /** Despacha la petición actual a su handler o muestra un 404. */
    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri    = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $path   = $this->normalize($uri);

        foreach ($this->routes[$method] ?? [] as [$regex, $handler]) {
            if (preg_match($regex, $path, $coincidencias)) {
                array_shift($coincidencias); // quita la coincidencia completa
                $this->invocar($handler, $coincidencias);
                return;
            }
        }

        http_response_code(404);
        view('404', ['title' => 'Página no encontrada | 404']);
    }

    /** Convierte una ruta con {param} en una expresión regular. */
    private function compilar(string $path): string
    {
        $path  = $this->normalize($path);
        $regex = preg_replace('#\{[a-zA-Z_][a-zA-Z0-9_]*\}#', '([^/]+)', $path);
        return '#^' . $regex . '$#';
    }

    /** Ejecuta el handler ([Clase, 'metodo'] o función) con los parámetros. */
    private function invocar(array|callable $handler, array $params): void
    {
        if (is_array($handler)) {
            [$class, $action] = $handler;
            (new $class())->$action(...$params);
            return;
        }
        $handler(...$params);
    }

    /** Normaliza la ruta: sin slash final, mínimo '/'. */
    private function normalize(string $path): string
    {
        $path = rtrim($path, '/');
        return $path === '' ? '/' : $path;
    }
}
