<?php

/**
 * Funciones de ayuda globales (vistas y escape de HTML).
 */

if (!function_exists('view')) {
    /**
     * Renderiza una vista desde src/views.
     *
     * @param string               $name Nombre de la vista sin extensión (ej. 'home', 'auth/login').
     * @param array<string, mixed> $data Variables disponibles dentro de la vista.
     */
    function view(string $name, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        $viewFile = BASE_PATH . '/src/views/' . $name . '.php';

        if (!is_file($viewFile)) {
            http_response_code(500);
            echo "Vista no encontrada: " . htmlspecialchars($name);
            return;
        }

        require $viewFile;
    }
}

if (!function_exists('e')) {
    /** Escapa una cadena para imprimirla de forma segura en HTML. */
    function e(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('redirect')) {
    /** Redirige a otra ruta y detiene la ejecución. */
    function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }
}

if (!function_exists('auth')) {
    /**
     * Devuelve los datos del usuario en sesión, o null si no hay sesión.
     * @return array{id:int,nombre:string,rol:string}|null
     */
    function auth(): ?array
    {
        if (!isset($_SESSION['usuario_id'])) {
            return null;
        }
        return [
            'id'     => (int) $_SESSION['usuario_id'],
            'nombre' => $_SESSION['usuario_nombre'] ?? '',
            'rol'    => $_SESSION['usuario_rol'] ?? 'usuario',
        ];
    }
}

if (!function_exists('requiereLogin')) {
    /** Si no hay sesión activa, redirige al login. */
    function requiereLogin(): void
    {
        if (!isset($_SESSION['usuario_id'])) {
            redirect('/login');
        }
    }
}

if (!function_exists('requiereAdmin')) {
    /** Si no es admin, redirige al inicio. */
    function requiereAdmin(): void
    {
        requiereLogin();
        if (($_SESSION['usuario_rol'] ?? 'usuario') !== 'admin') {
            redirect('/');
        }
    }
}

if (!function_exists('env')) {
    /**
     * Lee una variable de configuración.
     * Primero busca en las variables de entorno (Docker / cPanel) y,
     * como respaldo, en el archivo .env de la raíz del proyecto.
     *
     * @param string $clave   Nombre de la variable (ej. 'GOOGLE_MAPS_API_KEY').
     * @param mixed  $defecto Valor si no se encuentra.
     */
    function env(string $clave, mixed $defecto = null): mixed
    {
        // 1) Variable de entorno real
        $valor = getenv($clave);
        if ($valor !== false && $valor !== '') {
            return $valor;
        }

        // 2) Respaldo: leer y cachear el archivo .env una sola vez
        static $envCache = null;
        if ($envCache === null) {
            $envCache = [];
            $rutaEnv = BASE_PATH . '/.env';
            if (is_file($rutaEnv)) {
                foreach (file($rutaEnv, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $linea) {
                    $linea = trim($linea);
                    if ($linea === '' || $linea[0] === '#' || !str_contains($linea, '=')) {
                        continue;
                    }
                    [$k, $v] = explode('=', $linea, 2);
                    $envCache[trim($k)] = trim($v);
                }
            }
        }

        return $envCache[$clave] ?? $defecto;
    }
}
