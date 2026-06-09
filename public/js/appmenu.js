// Menú móvil (drawer): abre/cierra el sidebar como cajón deslizante.
// Solo actúa en móvil; en escritorio el sidebar es fijo y estos botones se ocultan por CSS.
(function () {
    var sidebar = document.getElementById('app_sidebar');
    var overlay = document.querySelector('.app_overlay');
    if (!sidebar || !overlay) return;

    var openBtn = document.querySelector('[data-menu-open]');

    function abrir() {
        sidebar.classList.add('is_open');
        overlay.classList.add('is_open');
        document.body.classList.add('app_menu_open');
        if (openBtn) openBtn.setAttribute('aria-expanded', 'true');
    }

    function cerrar() {
        sidebar.classList.remove('is_open');
        overlay.classList.remove('is_open');
        document.body.classList.remove('app_menu_open');
        if (openBtn) openBtn.setAttribute('aria-expanded', 'false');
    }

    if (openBtn) openBtn.addEventListener('click', abrir);

    // Botones de cierre (la X del cajón y el overlay)
    document.querySelectorAll('[data-menu-close]').forEach(function (el) {
        el.addEventListener('click', cerrar);
    });

    // Al tocar un enlace del menú, cerrar el cajón (navegación móvil)
    sidebar.querySelectorAll('a.app_nav_link, a.app_brand').forEach(function (a) {
        a.addEventListener('click', cerrar);
    });

    // Tecla Escape
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') cerrar();
    });
})();
