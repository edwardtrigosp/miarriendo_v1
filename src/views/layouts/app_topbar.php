<?php
/**
 * Barra superior del app shell: avatar de usuario (arriba a la derecha)
 * con menú desplegable (perfil / cerrar sesión). Solo con sesión iniciada.
 */
$nombreUsuario  = $_SESSION['usuario_nombre'] ?? 'Usuario';
$inicialUsuario = strtoupper(mb_substr($nombreUsuario, 0, 1));

// Foto: primero la sesión; si no está, se consulta una vez y se cachea.
if (!array_key_exists('usuario_foto', $_SESSION) && isset($_SESSION['usuario_id'])) {
    $_SESSION['usuario_foto'] = Usuario::buscarPorId((int) $_SESSION['usuario_id'])['foto_url'] ?? null;
}
$fotoPerfil = $_SESSION['usuario_foto'] ?? null;
?>
<header class="app_topbar">
    <div class="app_topbar_left">
        <button type="button" class="app_menu_toggle" data-menu-open aria-label="Abrir menú" aria-controls="app_sidebar" aria-expanded="false">
            <span class="material-symbols-outlined">menu</span>
        </button>
        <a href="/panel" class="app_topbar_brand">miarriendo</a>
    </div>

    <div class="user_menu" data-user-menu>
        <button type="button" class="user_menu_trigger" aria-haspopup="true" aria-expanded="false">
            <span class="user_avatar">
                <?php if (!empty($fotoPerfil)): ?>
                    <img src="<?= e($fotoPerfil) ?>" alt="Foto de perfil">
                <?php else: ?>
                    <?= e($inicialUsuario) ?>
                <?php endif; ?>
            </span>
            <span class="material-symbols-outlined user_menu_caret">expand_more</span>
        </button>

        <div class="user_menu_dropdown" hidden>
            <div class="user_menu_greeting">Hola <?= e($nombreUsuario) ?></div>
            <a href="/perfil" class="user_menu_item">
                <span class="material-symbols-outlined">person</span> Ver mi perfil
            </a>
            <div class="user_menu_sep"></div>
            <form action="/logout" method="POST" class="user_menu_logout_form">
                <?= csrf_field() ?>
                <button type="submit" class="user_menu_item user_menu_logout">
                    <span class="material-symbols-outlined">logout</span> Cerrar sesión
                </button>
            </form>
        </div>
    </div>
</header>
<div class="app_overlay" data-menu-close></div>
<script src="/js/usermenu.js" defer></script>
<script src="/js/appmenu.js" defer></script>
