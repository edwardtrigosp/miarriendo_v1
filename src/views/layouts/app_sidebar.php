<?php
/**
 * Menú lateral global (app shell) — solo para usuarios con sesión iniciada.
 * Reemplaza a la barra de navegación superior cuando el usuario está dentro.
 */
$nombreUsuario = $_SESSION['usuario_nombre'] ?? 'Usuario';
$inicialUsuario = strtoupper(mb_substr($nombreUsuario, 0, 1));
$rutaActual = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';

// Marca el enlace activo según la ruta
$activo = static function (array $prefijos) use ($rutaActual): string {
    foreach ($prefijos as $p) {
        if ($p === '/' ? $rutaActual === '/' : str_starts_with($rutaActual, $p)) {
            return ' is_active';
        }
    }
    return '';
};
?>
<aside class="app_sidebar">
    <a href="/" class="app_brand">miarriendo</a>

    <nav class="app_nav app_nav_top" aria-label="Navegación principal">
        <a href="/" class="app_nav_link<?= $activo(['/']) ?>"><span class="material-symbols-outlined">home</span> Inicio</a>
        <a href="/arriendos" class="app_nav_link<?= $activo(['/arriendos', '/propiedad/']) ?>"><span class="material-symbols-outlined">search</span> Explorar</a>
        <a href="/panel" class="app_nav_link<?= $activo(['/panel']) ?>"><span class="material-symbols-outlined">dashboard</span> Mi panel</a>
        <a href="/propiedades" class="app_nav_link<?= $activo(['/propiedades']) ?>"><span class="material-symbols-outlined">add_home</span> Publicar</a>
        <a href="/blog" class="app_nav_link<?= $activo(['/blog']) ?>"><span class="material-symbols-outlined">article</span> Blog</a>
    </nav>

    <div class="app_nav app_nav_bottom">
        <a href="/perfil" class="app_nav_link<?= $activo(['/perfil']) ?>"><span class="material-symbols-outlined">manage_accounts</span> Mi perfil</a>
        <form action="/logout" method="POST" class="app_logout_form">
            <?= csrf_field() ?>
            <button type="submit" class="app_nav_link app_logout_btn"><span class="material-symbols-outlined">logout</span> Cerrar sesión</button>
        </form>
    </div>
</aside>
