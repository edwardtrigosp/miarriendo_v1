// Menú desplegable del avatar (topbar): abrir/cerrar.
(function () {
    var menu = document.querySelector('[data-user-menu]');
    if (!menu) return;

    var trigger  = menu.querySelector('.user_menu_trigger');
    var dropdown = menu.querySelector('.user_menu_dropdown');

    function abrir()  { dropdown.hidden = false; trigger.setAttribute('aria-expanded', 'true'); }
    function cerrar() { dropdown.hidden = true;  trigger.setAttribute('aria-expanded', 'false'); }

    trigger.addEventListener('click', function (e) {
        e.stopPropagation();
        dropdown.hidden ? abrir() : cerrar();
    });

    // Clic fuera del menú -> cerrar.
    document.addEventListener('click', function (e) {
        if (!menu.contains(e.target)) cerrar();
    });

    // Tecla Escape -> cerrar.
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') cerrar();
    });
})();
