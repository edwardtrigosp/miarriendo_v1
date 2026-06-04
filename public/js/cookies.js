// Banner de consentimiento de cookies.
// Recuerda la decisión del usuario en localStorage para no volver a mostrarlo.
(function () {
    'use strict';

    var STORAGE_KEY = 'cookie_consent';
    var banner = document.getElementById('cookie_banner');

    if (!banner) {
        return;
    }

    // Si aún no hay decisión guardada, mostramos el banner.
    if (!localStorage.getItem(STORAGE_KEY)) {
        banner.hidden = false;
    }

    function guardarDecision(valor) {
        localStorage.setItem(STORAGE_KEY, valor);
        banner.hidden = true;
    }

    var aceptar = document.getElementById('cookie_accept');
    var rechazar = document.getElementById('cookie_reject');

    if (aceptar) {
        aceptar.addEventListener('click', function () {
            guardarDecision('aceptadas');
        });
    }

    if (rechazar) {
        rechazar.addEventListener('click', function () {
            guardarDecision('rechazadas');
        });
    }
})();
