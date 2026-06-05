<?php
/**
 * Pie de página común + cierre de documento.
 *
 * Variable opcional:
 *   $showFooter bool  Si es false, no se muestra el footer visual (solo cierra tags).
 */
?>
<?php
// Footer (se arma una sola vez). En el app shell debe ir DENTRO de .app_main
// para que el menú lateral fijo (sticky) no se "suelte" al hacer scroll.
$mostrarFooter = (!isset($showFooter) || $showFooter);
ob_start();
?>
<footer class="footer_main">
    <nav class="footer_links">
        <a href="/arriendos">Arriendos</a>
        <a href="/blog">Blog</a>
        <a href="/cookies">Cookies</a>
    </nav>
    &copy; <?= date('Y') ?> miarriendo.online. Todos los derechos reservados.
</footer>
<?php $footerHtml = ob_get_clean(); ?>
<?php if (!empty($appShell)): ?>
        <?php if ($mostrarFooter) { echo $footerHtml; } ?>
        </div><!-- .app_main -->
    </div><!-- .app_shell -->
<?php elseif ($mostrarFooter): ?>
<?= $footerHtml ?>
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
