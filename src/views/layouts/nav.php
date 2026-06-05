<?php
/**
 * Barra de navegación. Muestra distintos enlaces según la sesión.
 */
$autenticado = isset($_SESSION['usuario_id']);
?>
<nav class="nav_bar">
    <a href="/" class="nav_logo">miarriendo</a>
    <div class="nav_links">
        <a href="/arriendos" class="nav_link_item">Arriendos</a>
        <a href="/blog" class="nav_link_item">Blog</a>
        <?php if ($autenticado): ?>
            <a href="/propiedades" class="nav_link_item">Publicar</a>
            <a href="/panel" class="nav_link_item">Mi Panel</a>
            <a href="/perfil" class="btn_outline">Mi Perfil</a>
            <form action="/logout" method="POST" class="nav_logout_form">
                <?= csrf_field() ?>
                <button type="submit" class="nav_link_item nav_logout_btn">Cerrar Sesión</button>
            </form>
        <?php else: ?>
            <a href="/login" class="btn_outline">Iniciar Sesión</a>
            <a href="/registro" class="btn_primary">Registrarse</a>
        <?php endif; ?>
    </div>
</nav>
