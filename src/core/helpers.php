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
        // Despliegue en subcarpeta: anteponer BASE_URL a las rutas internas
        // ("/..."), sin tocar URLs externas (http...) ni las ya prefijadas.
        $base = defined('BASE_URL') ? BASE_URL : '';
        if ($base !== '' && isset($path[0]) && $path[0] === '/'
            && !str_starts_with($path, $base . '/') && $path !== $base) {
            $path = $base . $path;
        }
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

if (!function_exists('csrf_token')) {
    /** Devuelve el token CSRF de la sesión (lo genera la primera vez). */
    function csrf_token(): string
    {
        if (empty($_SESSION['_csrf'])) {
            $_SESSION['_csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf'];
    }
}

if (!function_exists('csrf_field')) {
    /** Devuelve el <input> oculto con el token CSRF para incrustar en un formulario. */
    function csrf_field(): string
    {
        return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
    }
}

if (!function_exists('verificarCsrf')) {
    /**
     * Valida el token CSRF de una petición POST. Si no coincide, responde 403 y corta.
     * Usa hash_equals para evitar ataques de temporización.
     */
    function verificarCsrf(): void
    {
        $enviado = $_POST['_csrf'] ?? '';
        if (empty($_SESSION['_csrf']) || !is_string($enviado) || !hash_equals($_SESSION['_csrf'], $enviado)) {
            http_response_code(403); // Forbidden (token CSRF ausente o inválido)
            view('error', [
                'title'   => 'Sesión expirada | miarriendo.online',
                'codigo'  => '403',
                'mensaje' => 'Tu sesión expiró o el formulario no es válido. Vuelve atrás e inténtalo de nuevo.',
            ]);
            exit;
        }
    }
}

if (!function_exists('flash')) {
    /**
     * Mensaje "flash" de un solo uso (se borra al leerlo). Útil tras un redirect.
     * Sin argumento de valor: lee y limpia. Con valor: guarda.
     */
    function flash(string $clave, ?string $valor = null): ?string
    {
        if ($valor !== null) {
            $_SESSION['_flash'][$clave] = $valor;
            return null;
        }
        $msg = $_SESSION['_flash'][$clave] ?? null;
        unset($_SESSION['_flash'][$clave]);
        return $msg;
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

if (!function_exists('fecha_corta')) {
    /**
     * Fecha corta en español: "10 jun" o "10 may 2027" (con $conAnio).
     * Recibe un timestamp o una cadena de fecha.
     */
    function fecha_corta($fecha, bool $conAnio = false): string
    {
        $ts = is_numeric($fecha) ? (int) $fecha : strtotime((string) $fecha);
        if ($ts === false) {
            return '';
        }
        $meses = ['', 'ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'];
        $txt = (int) date('j', $ts) . ' ' . $meses[(int) date('n', $ts)];
        return $conAnio ? $txt . ' ' . date('Y', $ts) : $txt;
    }
}

if (!function_exists('tiempo_hace')) {
    /**
     * Devuelve un texto relativo amigable: "hace un momento", "hace 3 horas",
     * "hace 5 días"... a partir de una fecha (string datetime o timestamp).
     */
    function tiempo_hace(string $fecha): string
    {
        $ts = is_numeric($fecha) ? (int) $fecha : strtotime($fecha);
        if ($ts === false) {
            return '';
        }
        $seg = time() - $ts;
        if ($seg < 60)       return 'hace un momento';
        if ($seg < 3600)     return 'hace ' . (int) ($seg / 60) . ' min';
        if ($seg < 86400)    return 'hace ' . (int) ($seg / 3600) . ' h';
        if ($seg < 2592000)  return 'hace ' . (int) ($seg / 86400) . ' días';
        if ($seg < 31536000) return 'hace ' . (int) ($seg / 2592000) . ' meses';
        return 'hace ' . (int) ($seg / 31536000) . ' años';
    }
}
