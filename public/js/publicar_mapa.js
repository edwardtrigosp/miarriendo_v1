// Verificación de la ubicación al publicar una propiedad.
// Dos modos excluyentes:
//   - manual: el usuario escribe la dirección (no hay mapa; el servidor geocodifica al guardar).
//   - gps:    el usuario toma su ubicación; marcamos el pin y rellenamos la dirección
//             (geocodificación inversa + auto-selección de departamento/ciudad).
// En modo GPS el pin es arrastrable / se puede fijar con clic para afinar.
(function () {
    'use strict';

    var contenedor = document.getElementById('geo_mapa');
    if (!contenedor || typeof L === 'undefined') {
        return;
    }

    var inputLat = document.getElementById('latitud');
    var inputLon = document.getElementById('longitud');
    var estado   = document.getElementById('geo_estado');
    var btnGPS   = document.getElementById('btn_mi_ubicacion');
    var resumen      = document.getElementById('geo_resumen');
    var resumenTexto = document.getElementById('geo_resumen_texto');

    var calle  = document.getElementById('calle');
    var numero = document.getElementById('numero_exterior');
    var barrio = document.getElementById('barrio');
    var selPais   = document.getElementById('pais');
    var selDepto  = document.getElementById('departamento');
    var selCiudad = document.getElementById('ciudad');

    var marcador = null;
    var modo = 'manual';

    // ------------------------------------------------------------------
    // Mapa base (centrado en Colombia hasta que haya un punto)
    // ------------------------------------------------------------------
    var map = L.map('geo_mapa').setView([4.6097, -74.0817], 11);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; colaboradores de OpenStreetMap'
    }).addTo(map);

    // Fijar el pin con clic en el mapa
    map.on('click', function (e) {
        fijarPin(e.latlng.lat, e.latlng.lng, false);
        reverseGeocode(e.latlng.lat, e.latlng.lng);
    });

    // ------------------------------------------------------------------
    // Selector de modo: manual (escribir) vs GPS (ubicación del dispositivo)
    // ------------------------------------------------------------------
    var botonesModo = document.querySelectorAll('.geo_modo_btn');
    var paneles = document.querySelectorAll('.geo_panel');

    botonesModo.forEach(function (btn) {
        btn.addEventListener('click', function () {
            modo = btn.getAttribute('data-modo');

            botonesModo.forEach(function (b) {
                var activo = b === btn;
                b.classList.toggle('is_active', activo);
                b.setAttribute('aria-selected', activo ? 'true' : 'false');
            });
            paneles.forEach(function (p) {
                p.hidden = p.getAttribute('data-panel') !== modo;
            });

            // Al entrar a GPS, el mapa estaba oculto y mide mal: recalcular.
            if (modo === 'gps') {
                setTimeout(function () { map.invalidateSize(); }, 60);
            }
        });
    });

    // ------------------------------------------------------------------
    // Pin + coordenadas
    // ------------------------------------------------------------------
    function fijarPin(lat, lon, centrar) {
        if (!marcador) {
            marcador = L.marker([lat, lon], { draggable: true }).addTo(map);
            marcador.on('dragend', function () {
                var p = marcador.getLatLng();
                guardarCoords(p.lat, p.lng);
                reverseGeocode(p.lat, p.lng);
            });
        } else {
            marcador.setLatLng([lat, lon]);
        }
        guardarCoords(lat, lon);
        if (centrar) {
            map.setView([lat, lon], 16);
        }
    }

    function guardarCoords(lat, lon) {
        inputLat.value = Number(lat).toFixed(8);
        inputLon.value = Number(lon).toFixed(8);
    }

    // ------------------------------------------------------------------
    // GPS del dispositivo
    // ------------------------------------------------------------------
    btnGPS.addEventListener('click', function () {
        if (!navigator.geolocation) {
            mostrarEstado('Tu navegador no soporta geolocalización.', false);
            return;
        }
        if (window.isSecureContext === false) {
            mostrarEstado('La ubicación requiere HTTPS o localhost.', false);
            return;
        }

        mostrarEstado('Obteniendo tu ubicación…', null);
        btnGPS.disabled = true;

        navigator.geolocation.getCurrentPosition(
            function (pos) {
                btnGPS.disabled = false;
                fijarPin(pos.coords.latitude, pos.coords.longitude, true);
                mostrarEstado('Detectando la dirección…', null);
                reverseGeocode(pos.coords.latitude, pos.coords.longitude);
            },
            function () {
                btnGPS.disabled = false;
                mostrarEstado('No pudimos acceder a tu ubicación. Actívala o usa el modo manual.', false);
            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
        );
    });

    // ------------------------------------------------------------------
    // Geocodificación inversa: coords -> dirección (rellena campos ocultos)
    // ------------------------------------------------------------------
    function reverseGeocode(lat, lon) {
        var url = 'https://nominatim.openstreetmap.org/reverse?format=json&addressdetails=1&lat=' + lat + '&lon=' + lon;
        fetch(url, { headers: { 'Accept-Language': 'es' } })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (!data || !data.address) {
                    mostrarEstado('Pin fijado, pero no pudimos detectar la dirección. Usa el modo manual si falla.', false);
                    return;
                }
                var a = data.address;

                // Calle y barrio
                calle.value = (a.road || a.pedestrian || a.neighbourhood || 'Vía sin nombre')
                    + (a.house_number ? ' ' + a.house_number : '');
                barrio.value = a.suburb || a.neighbourhood || a.quarter || '';

                // Departamento + ciudad (auto-selección en los selects ocultos)
                var ciudadDetectada = a.city || a.town || a.municipality || a.county || '';
                var deptoDetectado  = a.state || '';
                var ok = autoSeleccionar(deptoDetectado, ciudadDetectada);

                // Refresca el panel de progreso del formulario
                calle.dispatchEvent(new Event('input', { bubbles: true }));

                mostrarResumen(a, deptoDetectado, ciudadDetectada, ok);
            })
            .catch(function () {
                mostrarEstado('No se pudo detectar la dirección. Intenta de nuevo o usa el modo manual.', false);
            });
    }

    // ------------------------------------------------------------------
    // Auto-selección de departamento y ciudad por nombre (window.UBICACIONES)
    // ------------------------------------------------------------------
    function norm(s) {
        return (s || '').toString().normalize('NFD').replace(/[̀-ͯ]/g, '')
            .toUpperCase().replace(/\./g, '').replace(/\s+/g, ' ').trim();
    }
    function coincide(a, b) {
        a = norm(a); b = norm(b);
        return a !== '' && b !== '' && (a === b || a.indexOf(b) >= 0 || b.indexOf(a) >= 0);
    }

    function autoSeleccionar(deptoNombre, ciudadNombre) {
        var UB = window.UBICACIONES || { departamentos: [], ciudades: [] };

        // País: Colombia (por texto, o el primero disponible)
        var puso = false;
        for (var i = 0; i < selPais.options.length; i++) {
            if (coincide(selPais.options[i].textContent, 'Colombia')) { selPais.selectedIndex = i; puso = true; break; }
        }
        if (!puso && selPais.options.length > 1) { selPais.selectedIndex = 1; }
        selPais.dispatchEvent(new Event('change', { bubbles: true })); // puebla departamentos

        var dep = (UB.departamentos || []).find(function (d) { return coincide(d.nombre, deptoNombre); });
        if (!dep) { return false; }
        selDepto.value = String(dep.id);
        selDepto.dispatchEvent(new Event('change', { bubbles: true })); // puebla ciudades

        var ciu = (UB.ciudades || [])
            .filter(function (c) { return String(c.departamento_id) === String(dep.id); })
            .find(function (c) { return coincide(c.nombre, ciudadNombre); });
        if (!ciu) { return false; }
        selCiudad.value = String(ciu.id);
        selCiudad.dispatchEvent(new Event('change', { bubbles: true }));
        return true;
    }

    // ------------------------------------------------------------------
    // Resumen visible de lo detectado
    // ------------------------------------------------------------------
    function mostrarResumen(a, deptoNombre, ciudadNombre, ok) {
        var linea = [calle.value, barrio.value, ciudadNombre, deptoNombre]
            .filter(Boolean).join(', ');
        resumenTexto.textContent = linea;
        resumen.hidden = false;

        if (ok) {
            mostrarEstado('✓ Dirección y ciudad detectadas. Arrastra el pin si necesitas ajustar.', true);
        } else {
            mostrarEstado('Detectamos el pin, pero no la ciudad exacta. Si no es correcta, usa el modo "Escribir la dirección".', false);
        }
    }

    function mostrarEstado(texto, ok) {
        estado.textContent = texto;
        estado.className = 'geo_estado' + (ok === true ? ' is_ok' : ok === false ? ' is_err' : '');
    }
})();
