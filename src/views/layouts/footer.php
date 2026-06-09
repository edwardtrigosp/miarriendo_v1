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
<!-- Modal de confirmación global (reemplaza el confirm() del navegador) -->
<div class="modal_overlay" id="confirm_modal" hidden>
    <div class="modal_box" role="dialog" aria-modal="true" aria-labelledby="confirm_modal_title">
        <h3 class="modal_title" id="confirm_modal_title">Confirmar</h3>
        <p class="modal_text" id="confirm_modal_text"></p>
        <div class="modal_actions">
            <button type="button" class="btn_outline" data-confirm-cancel>Cancelar</button>
            <button type="button" class="btn_danger" data-confirm-ok>Aceptar</button>
        </div>
    </div>
</div>

<script src="/js/cookies.js"></script>
<script src="/js/confirm.js"></script>
</body>
</html>
