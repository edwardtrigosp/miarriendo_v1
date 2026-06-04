<?php

/**
 * Front Controller (punto de entrada único).
 * Todas las peticiones pasan por aquí gracias al .htaccess.
 */

// Zona horaria de Colombia (UTC-5) para todas las fechas de PHP
date_default_timezone_set('America/Bogota');

session_start();

// Ruta raíz del proyecto (carpeta que contiene /src y /public).
define('BASE_PATH', dirname(__DIR__));

// Núcleo
require_once BASE_PATH . '/src/core/helpers.php';
require_once BASE_PATH . '/src/core/Router.php';

// Configuración y modelos
require_once BASE_PATH . '/src/config/database.php';
require_once BASE_PATH . '/src/models/Usuario.php';
require_once BASE_PATH . '/src/models/Ubicacion.php';
require_once BASE_PATH . '/src/models/Direccion.php';
require_once BASE_PATH . '/src/models/Propiedad.php';
require_once BASE_PATH . '/src/models/ImagenPropiedad.php';
require_once BASE_PATH . '/src/models/Alquiler.php';

// Controladores
require_once BASE_PATH . '/src/controllers/HomeController.php';
require_once BASE_PATH . '/src/controllers/AuthController.php';
require_once BASE_PATH . '/src/controllers/PropiedadController.php';
require_once BASE_PATH . '/src/controllers/PerfilController.php';
require_once BASE_PATH . '/src/controllers/PanelController.php';
require_once BASE_PATH . '/src/controllers/BlogController.php';
require_once BASE_PATH . '/src/controllers/CookiesController.php';

$router = new Router();

// --- Páginas (GET) ---
$router->get('/',            [HomeController::class, 'index']);
$router->get('/arriendos',   [PropiedadController::class, 'index']);
$router->get('/propiedades', [PropiedadController::class, 'create']);
$router->post('/propiedades', [PropiedadController::class, 'store']);
$router->get('/ruta',        [PropiedadController::class, 'ruta']);
$router->get('/login',       [AuthController::class, 'showLogin']);
$router->get('/registro',    [AuthController::class, 'showRegistro']);
$router->get('/perfil',      [PerfilController::class, 'index']);
$router->get('/panel',       [PanelController::class, 'index']);
$router->get('/blog',        [BlogController::class, 'index']);
$router->get('/cookies',     [CookiesController::class, 'index']);

// --- Acciones (POST) — se implementan en la fase de backend ---
$router->post('/login',      [AuthController::class, 'login']);
$router->post('/registro',   [AuthController::class, 'registro']);
$router->get('/logout',      [AuthController::class, 'logout']);

$router->dispatch();
