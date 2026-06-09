<?php
/**
 * Menú lateral global (app shell) — solo para usuarios con sesión iniciada.
 *
 * Un switch arriba alterna el MODO del menú:
 *   - casa  (propiedades) → acciones como arrendador.
 *   - llave (arriendos)   → acciones como arrendatario.
 * Solo se muestra la sección del modo activo.
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

// Modo inicial: si la página pertenece a un modo, se fuerza ese modo;
// si no (Inicio, Blog), se usa 'propiedades' y el JS aplica el recordado.
$paginaArriendos   = $enPanel && in_array($verActual, ['mis-arriendos', 'mis-solicitudes'], true);
$paginaPropiedades = ($enPanel && in_array($verActual, ['mis-propiedades', 'solicitudes-recibidas'], true))
                     || str_starts_with($rutaActual, '/propiedades');
$modoForzado = $paginaArriendos || $paginaPropiedades;
$modo        = $paginaArriendos ? 'arriendos' : 'propiedades';
?>
<aside class="app_sidebar" id="app_sidebar">
    <div class="app_sidebar_head">
        <a href="/panel" class="app_brand">miarriendo</a>
        <button type="button" class="app_menu_close" data-menu-close aria-label="Cerrar menú">
            <span class="material-symbols-outlined">close</span>
        </button>
    </div>

    <div class="role_switch" data-role-switch data-modo="<?= e($modo) ?>" data-forced="<?= $modoForzado ? '1' : '' ?>" role="tablist" aria-label="Modo: arrendar o buscar">
        <button type="button" class="role_switch_btn<?= $modo === 'propiedades' ? ' is_active' : '' ?>" data-role="propiedades" role="tab" aria-selected="<?= $modo === 'propiedades' ? 'true' : 'false' ?>" aria-label="Arrendar propiedades" title="Arrendar propiedades">
            <span class="material-symbols-outlined">home_work</span>
        </button>
        <button type="button" class="role_switch_btn<?= $modo === 'arriendos' ? ' is_active' : '' ?>" data-role="arriendos" role="tab" aria-selected="<?= $modo === 'arriendos' ? 'true' : 'false' ?>" aria-label="Buscar arriendo" title="Buscar arriendo">
            <span class="material-symbols-outlined">vpn_key</span>
        </button>
    </div>

    <nav class="app_nav app_nav_top" aria-label="Navegación principal">
        <div class="app_nav_modes">
            <!-- Modo: ARRENDAR PROPIEDADES -->
            <div data-mode-panel="propiedades"<?= $modo === 'propiedades' ? '' : ' hidden' ?>>
                <div class="app_nav_section_label">Arrendar propiedades</div>
                <a href="/panel?ver=mis-propiedades" class="app_nav_link<?= $activoPanel('mis-propiedades') ?>"><span class="material-symbols-outlined">home_work</span> Mis propiedades</a>
                <a href="/propiedades" class="app_nav_link<?= $activo(['/propiedades']) ?>"><span class="material-symbols-outlined">add_home</span> Publicar</a>
                <a href="/panel?ver=solicitudes-recibidas" class="app_nav_link<?= $activoPanel('solicitudes-recibidas') ?>"><span class="material-symbols-outlined">draw</span> Firmar contratos</a>
            </div>

            <!-- Modo: BUSCAR ARRIENDO -->
            <div data-mode-panel="arriendos"<?= $modo === 'arriendos' ? '' : ' hidden' ?>>
                <div class="app_nav_section_label">Buscar arriendo</div>
                <a href="/panel?ver=mis-arriendos" class="app_nav_link<?= $activoPanel('mis-arriendos') ?>"><span class="material-symbols-outlined">vpn_key</span> Mis arriendos</a>
                <a href="/panel?ver=mis-solicitudes" class="app_nav_link<?= $activoPanel('mis-solicitudes') ?>"><span class="material-symbols-outlined">description</span> Mis solicitudes</a>
            </div>
        </div>

        <div class="app_nav_general">
            <div class="app_nav_section_label">General</div>
            <a href="/blog" class="app_nav_link<?= $activo(['/blog']) ?>"><span class="material-symbols-outlined">article</span> Blog</a>
        </div>
    </nav>
</aside>

<script>
// Switch de modo del sidebar (recordado con localStorage).
(function () {
    var sw = document.querySelector('[data-role-switch]');
    if (!sw) return;
    var KEY = 'miarriendo_modo';
    var btns = sw.querySelectorAll('.role_switch_btn');

    function aplicar(modo) {
        btns.forEach(function (b) {
            var on = b.getAttribute('data-role') === modo;
            b.classList.toggle('is_active', on);
            b.setAttribute('aria-selected', on ? 'true' : 'false');
        });
        document.querySelectorAll('[data-mode-panel]').forEach(function (p) {
            p.hidden = p.getAttribute('data-mode-panel') !== modo;
        });
    }

    var forzado = sw.getAttribute('data-forced') === '1';
    var modoServidor = sw.getAttribute('data-modo');
    if (forzado) {
        localStorage.setItem(KEY, modoServidor); // la página manda; recuérdalo
    } else {
        var guardado = localStorage.getItem(KEY);
        if (guardado && guardado !== modoServidor) aplicar(guardado);
    }

    btns.forEach(function (b) {
        b.addEventListener('click', function () {
            var modo = b.getAttribute('data-role');
            localStorage.setItem(KEY, modo);
            aplicar(modo);
        });
    });
})();
</script>
