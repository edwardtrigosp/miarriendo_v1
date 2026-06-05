<?php
/**
 * Menú lateral global (app shell) — solo para usuarios con sesión iniciada.
 * Reemplaza a la barra de navegación superior cuando el usuario está dentro.
 *
 * Las opciones se agrupan por el rol que cumple el usuario:
 *   - "Buscar arriendo": acciones como arrendatario (inquilino).
 *   - "Arrendar mi propiedad": acciones como arrendador (propietario).
 */
$nombreUsuario  = $_SESSION['usuario_nombre'] ?? 'Usuario';
$inicialUsuario = strtoupper(mb_substr($nombreUsuario, 0, 1));
$rutaActual = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
$verActual  = $_GET['ver'] ?? 'resumen';
$enPanel    = $rutaActual === '/panel';

// Activo para enlaces normales (por prefijo de ruta).
$activo = static function (array $prefijos) use ($rutaActual): string {
    foreach ($prefijos as $p) {
        if ($p === '/' ? $rutaActual === '/' : str_starts_with($rutaActual, $p)) {
            return ' is_active';
        }
    }
    return '';
};
// Activo para las secciones del panel (según ?ver=).
$activoPanel = static function (string $ver) use ($enPanel, $verActual): string {
    return ($enPanel && $verActual === $ver) ? ' is_active' : '';
};
?>
<aside class="app_sidebar">
    <a href="/panel" class="app_brand">miarriendo</a>

    <nav class="app_nav app_nav_top" aria-label="Navegación principal">
        <a href="/panel" class="app_nav_link<?= $activoPanel('resumen') ?>"><span class="material-symbols-outlined">home</span> Inicio</a>

        <div class="app_nav_section_label">Arrendar propiedades</div>
        <a href="/panel?ver=mis-propiedades" class="app_nav_link<?= $activoPanel('mis-propiedades') ?>"><span class="material-symbols-outlined">home_work</span> Mis propiedades</a>
        <a href="/propiedades" class="app_nav_link<?= $activo(['/propiedades']) ?>"><span class="material-symbols-outlined">add_home</span> Publicar</a>
        <a href="/panel?ver=solicitudes-recibidas" class="app_nav_link<?= $activoPanel('solicitudes-recibidas') ?>"><span class="material-symbols-outlined">draw</span> Firmar contratos</a>

        <div class="app_nav_section_label">Buscar arriendo</div>
        <a href="/panel?ver=mis-arriendos" class="app_nav_link<?= $activoPanel('mis-arriendos') ?>"><span class="material-symbols-outlined">vpn_key</span> Mis arriendos</a>
        <a href="/panel?ver=mis-solicitudes" class="app_nav_link<?= $activoPanel('mis-solicitudes') ?>"><span class="material-symbols-outlined">description</span> Mis solicitudes</a>

        <div class="app_nav_section_label">General</div>
        <a href="/blog" class="app_nav_link<?= $activo(['/blog']) ?>"><span class="material-symbols-outlined">article</span> Blog</a>
    </nav>
</aside>
