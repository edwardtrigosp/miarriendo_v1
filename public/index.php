<?php

/**
 * Front Controller (punto de entrada único).
 * Todas las peticiones pasan por aquí gracias al .htaccess.
 */

// Ruta raíz del proyecto (carpeta que contiene /src y /public).
define('BASE_PATH', dirname(__DIR__));

// Zona horaria de Colombia (UTC-5) para todas las fechas de PHP
date_default_timezone_set('America/Bogota');

// Helper de configuración (necesario para leer APP_ENV antes que nada).
require_once BASE_PATH . '/src/core/helpers.php';

// --- Manejo de errores según el entorno ---------------------------------
// En producción nunca se muestran detalles al usuario: se registran (log).
error_reporting(E_ALL);
ini_set('log_errors', '1');
$enProduccion = env('APP_ENV', 'production') === 'production';

if ($enProduccion) {
    ini_set('display_errors', '0');
    // Excepción no capturada -> página 500 genérica (sin filtrar detalles).
    set_exception_handler(function (\Throwable $e): void {
        error_log('[miarriendo] ' . $e);
        if (!headers_sent()) {
            http_response_code(500);
        }
        view('error', [
            'title'   => 'Error del servidor | miarriendo.online',
            'codigo'  => '500',
            'mensaje' => 'Ocurrió un error inesperado. Ya estamos al tanto; intenta de nuevo en unos minutos.',
        ]);
        exit;
    });
} else {
    // Desarrollo: mostrar el detalle para poder depurar.
    ini_set('display_errors', '1');
}

// --- Sesión con cookie endurecida ---------------------------------------
$esHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'httponly' => true,            // inaccesible desde JavaScript (anti-XSS)
    'samesite' => 'Lax',           // mitiga CSRF en peticiones cross-site
    'secure'   => $esHttps,        // solo por HTTPS cuando esté disponible
]);
session_start();

// Núcleo
require_once BASE_PATH . '/src/core/Router.php';
require_once BASE_PATH . '/src/core/Geocoder.php';
require_once BASE_PATH . '/src/core/ContratoPlantilla.php';

// Configuración y modelos
require_once BASE_PATH . '/src/config/database.php';
require_once BASE_PATH . '/src/models/Usuario.php';
require_once BASE_PATH . '/src/models/Ubicacion.php';
require_once BASE_PATH . '/src/models/Direccion.php';
require_once BASE_PATH . '/src/models/Propiedad.php';
require_once BASE_PATH . '/src/models/ImagenPropiedad.php';
require_once BASE_PATH . '/src/models/Alquiler.php';
require_once BASE_PATH . '/src/models/Contrato.php';

// Controladores
require_once BASE_PATH . '/src/controllers/HomeController.php';
require_once BASE_PATH . '/src/controllers/AuthController.php';
require_once BASE_PATH . '/src/controllers/PropiedadController.php';
require_once BASE_PATH . '/src/controllers/PerfilController.php';
require_once BASE_PATH . '/src/controllers/PanelController.php';
require_once BASE_PATH . '/src/controllers/ContratoController.php';
require_once BASE_PATH . '/src/controllers/BlogController.php';
require_once BASE_PATH . '/src/controllers/CookiesController.php';

$router = new Router();

// --- Páginas (GET) ---
$router->get('/',            [HomeController::class, 'index']);
$router->get('/arriendos',   [PropiedadController::class, 'index']);
$router->get('/propiedades', [PropiedadController::class, 'create']);
$router->post('/propiedades', [PropiedadController::class, 'store']);
$router->get('/propiedad/{id}', [PropiedadController::class, 'detalle']);
$router->get('/propiedad/{id}/editar',  [PropiedadController::class, 'editar']);
$router->post('/propiedad/{id}/editar', [PropiedadController::class, 'actualizar']);
$router->post('/propiedad/{id}/eliminar', [PropiedadController::class, 'eliminar']);
$router->post('/propiedad/{id}/fotos', [PropiedadController::class, 'agregarFotos']);
$router->post('/propiedad/{id}/foto/{imgId}/eliminar', [PropiedadController::class, 'eliminarFoto']);
$router->get('/propiedad/{id}/solicitar', [PropiedadController::class, 'solicitar']);
$router->post('/propiedad/{id}/solicitar', [ContratoController::class, 'crearSolicitud']);
$router->get('/contrato/{id}',          [ContratoController::class, 'ver']);
$router->post('/contrato/{id}/aprobar', [ContratoController::class, 'aprobar']);
$router->post('/contrato/{id}/rechazar', [ContratoController::class, 'rechazar']);
$router->get('/contrato/{id}/firmar',   [ContratoController::class, 'firmarForm']);
$router->post('/contrato/{id}/firmar',  [ContratoController::class, 'firmar']);
$router->get('/contrato/{id}/pdf',      [ContratoController::class, 'pdf']);
$router->get('/ruta',        [PropiedadController::class, 'ruta']);
$router->get('/login',       [AuthController::class, 'showLogin']);
$router->get('/registro',    [AuthController::class, 'showRegistro']);
$router->get('/perfil',      [PerfilController::class, 'index']);
$router->post('/perfil/actualizar', [PerfilController::class, 'actualizar']);
$router->post('/perfil/foto',       [PerfilController::class, 'subirFoto']);
$router->get('/panel',       [PanelController::class, 'index']);
$router->get('/blog',        [BlogController::class, 'index']);
$router->get('/cookies',     [CookiesController::class, 'index']);

// --- Acciones (POST) ---
$router->post('/login',      [AuthController::class, 'login']);
$router->post('/registro',   [AuthController::class, 'registro']);
$router->post('/logout',     [AuthController::class, 'logout']);

// Protección CSRF: toda petición que cambia estado (POST) debe traer un token válido.
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    verificarCsrf();
}

$router->dispatch();
