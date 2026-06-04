// Detalle de propiedad: cambiar la imagen principal al hacer clic en una miniatura.
(function () {
    'use strict';

    var principal = document.getElementById('img_principal');
    var thumbs = document.querySelectorAll('.detalle_thumb');
    if (!principal || thumbs.length === 0) {
        return;
    }

    // Marca la primera miniatura como activa
    thumbs[0].classList.add('activa');

    thumbs.forEach(function (thumb) {
        thumb.addEventListener('click', function () {
            principal.src = thumb.getAttribute('data-full');
            thumbs.forEach(function (t) { t.classList.remove('activa'); });
            thumb.classList.add('activa');
        });
    });
})();
