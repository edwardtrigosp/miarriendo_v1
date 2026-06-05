<?php
/**
 * Pie de página común + cierre de documento.
 *
 * Variable opcional:
 *   $showFooter bool  Si es false, no se muestra el footer visual (solo cierra tags).
 */
?>
<?php if (!isset($showFooter) || $showFooter): ?>
<footer class="footer_main">
    <nav class="footer_links">
        <a href="/arriendos">Arriendos</a>
        <a href="/blog">Blog</a>
        <a href="/cookies">Cookies</a>
    </nav>
    &copy; <?= date('Y') ?> miarriendo.online. Todos los derechos reservados.
</footer>
<?php endif; ?>

<!-- Banner de consentimiento de cookies (global) -->
<div id="cookie_banner" class="cookie_banner" hidden>
    <p class="cookie_banner_text">
        Usamos cookies para mejorar tu experiencia. Consulta nuestra
        <a href="/cookies" class="text_link">Política de Cookies</a>.
    </p>
    <div class="cookie_banner_actions">
        <button type="button" id="cookie_reject" class="btn_outline btn_sm">Rechazar</button>
        <button type="button" id="cookie_accept" class="btn_primary btn_sm">Aceptar</button>
    </div>
</div>
<script src="/js/cookies.js"></script>
</body>
</html>
