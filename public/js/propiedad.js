// Detalle de propiedad: galería de miniaturas + mini-mapa de ubicación.
(function () {
    'use strict';

    // --- Galería: cambiar la imagen principal al hacer clic en una miniatura ---
    var principal = document.getElementById('img_principal');
    var thumbs = document.querySelectorAll('.detalle_thumb');
    if (principal && thumbs.length > 0) {
        thumbs[0].classList.add('activa');
        thumbs.forEach(function (thumb) {
            thumb.addEventListener('click', function () {
                principal.src = thumb.getAttribute('data-full');
                thumbs.forEach(function (t) { t.classList.remove('activa'); });
                thumb.classList.add('activa');
            });
        });
    }

    // --- Mini-mapa de ubicación (Leaflet) ---
    var contenedor = document.getElementById('detalle_mapa');
    if (contenedor && typeof L !== 'undefined' && window.PROP_COORDS) {
        var c = window.PROP_COORDS;
        var mapa = L.map('detalle_mapa', { scrollWheelZoom: false }).setView([c.lat, c.lon], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; colaboradores de OpenStreetMap'
        }).addTo(mapa);

        L.circleMarker([c.lat, c.lon], {
            radius: 10, color: '#8917D4', fillColor: '#8917D4', fillOpacity: 0.85, weight: 3
        }).addTo(mapa).bindPopup(c.titulo || 'Propiedad').openPopup();
    }
})();
