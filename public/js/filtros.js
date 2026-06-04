// Filtros de /arriendos: cascada departamento -> ciudad y "Cerca de mí" (geolocalización).
(function () {
    'use strict';

    var form = document.getElementById('filtros_form');
    if (!form) {
        return;
    }

    var UBIC = window.UBICACIONES || { departamentos: [], ciudades: [] };
    var SEL  = window.FILTROS || { departamento_id: '', ciudad_id: '' };

    var selDepto  = document.getElementById('filtro_departamento');
    var selCiudad = document.getElementById('filtro_ciudad');

    function opcion(value, texto, selected) {
        var o = document.createElement('option');
        o.value = value;
        o.textContent = texto;
        if (selected) { o.selected = true; }
        return o;
    }

    // Departamentos (ordenados como vienen del backend)
    UBIC.departamentos.forEach(function (d) {
        selDepto.appendChild(opcion(d.id, d.nombre, String(d.id) === String(SEL.departamento_id)));
    });

    // Llena las ciudades del departamento elegido
    function llenarCiudades(deptoId, ciudadSeleccionada) {
        selCiudad.innerHTML = '';
        selCiudad.appendChild(opcion('', 'Ciudad', false));
        UBIC.ciudades
            .filter(function (c) { return String(c.departamento_id) === String(deptoId); })
            .forEach(function (c) {
                selCiudad.appendChild(opcion(c.id, c.nombre, String(c.id) === String(ciudadSeleccionada)));
            });
    }

    if (SEL.departamento_id) {
        llenarCiudades(SEL.departamento_id, SEL.ciudad_id);
    }

    selDepto.addEventListener('change', function () {
        llenarCiudades(selDepto.value, '');
    });

    // ------------------------------------------------------------------
    // "Cerca de mí": pide ubicación y envía el formulario con lat/lon + orden
    // ------------------------------------------------------------------
    var btnCerca = document.getElementById('btn_cerca');
    var estado   = document.getElementById('filtros_estado');
    var inputLat = document.getElementById('filtro_lat');
    var inputLon = document.getElementById('filtro_lon');
    var selOrden = document.getElementById('filtro_orden');

    btnCerca.addEventListener('click', function () {
        if (!navigator.geolocation) {
            estado.textContent = 'Tu navegador no soporta geolocalización.';
            return;
        }
        if (window.isSecureContext === false) {
            estado.textContent = 'La ubicación requiere HTTPS o localhost.';
            return;
        }

        estado.textContent = 'Obteniendo tu ubicación…';
        btnCerca.disabled = true;

        navigator.geolocation.getCurrentPosition(
            function (pos) {
                inputLat.value = pos.coords.latitude;
                inputLon.value = pos.coords.longitude;
                selOrden.value = 'cercania';
                form.submit();
            },
            function () {
                btnCerca.disabled = false;
                estado.textContent = 'No pudimos acceder a tu ubicación. Actívala e intenta de nuevo.';
            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
        );
    });

    // Si el usuario cambia el orden a algo distinto de cercanía, olvida las coords
    selOrden.addEventListener('change', function () {
        if (selOrden.value !== 'cercania') {
            inputLat.value = '';
            inputLon.value = '';
        }
    });
})();
