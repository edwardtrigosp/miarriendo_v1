// Modal de confirmación propio (reemplaza el confirm() nativo del navegador).
// Cualquier <form data-confirm="mensaje"> abre el modal antes de enviarse.
// Opcionales: data-confirm-title, data-confirm-ok.
(function () {
    var modal = document.getElementById('confirm_modal');
    if (!modal) return;

    var titleEl  = document.getElementById('confirm_modal_title');
    var textEl   = document.getElementById('confirm_modal_text');
    var okBtn    = modal.querySelector('[data-confirm-ok]');
    var cancelBtn = modal.querySelector('[data-confirm-cancel]');
    var pendingForm = null;

    function abrir(form) {
        pendingForm = form;
        textEl.textContent  = form.getAttribute('data-confirm') || '¿Confirmar esta acción?';
        titleEl.textContent = form.getAttribute('data-confirm-title') || 'Confirmar';
        okBtn.textContent   = form.getAttribute('data-confirm-ok') || 'Aceptar';
        modal.hidden = false;
        document.body.style.overflow = 'hidden';
        okBtn.focus();
    }

    function cerrar() {
        modal.hidden = true;
        pendingForm = null;
        document.body.style.overflow = '';
    }

    // Intercepta el envío de los formularios marcados.
    document.addEventListener('submit', function (e) {
        var form = e.target;
        if (form && form.hasAttribute && form.hasAttribute('data-confirm')) {
            e.preventDefault();
            abrir(form);
        }
    });

    okBtn.addEventListener('click', function () {
        if (!pendingForm) return;
        var form = pendingForm;
        pendingForm = null;
        cerrar();
        form.submit(); // submit() no dispara el evento 'submit' -> no hay bucle
    });

    cancelBtn.addEventListener('click', cerrar);
    modal.addEventListener('click', function (e) { if (e.target === modal) cerrar(); });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !modal.hidden) cerrar();
    });
})();
