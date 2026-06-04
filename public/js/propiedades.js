// Publicar propiedad: selects de ubicación en cascada + panel de progreso en vivo.
(function () {
    'use strict';

    var form = document.getElementById('propiedad_form');
    if (!form) {
        return;
    }

    // ------------------------------------------------------------------
    // Datos de ubicación de EJEMPLO.
    // TODO (fase backend): reemplazar por datos reales de las tablas
    // PAISES / DEPARTAMENTOS / CIUDADES (vía PHP o fetch a una API).
    // ------------------------------------------------------------------
    var UBICACIONES = {
        paises: [
            { id: 1, nombre: 'Colombia' }
        ],
        departamentos: [
            { id: 1, pais_id: 1, nombre: 'Cundinamarca' },
            { id: 2, pais_id: 1, nombre: 'Antioquia' },
            { id: 3, pais_id: 1, nombre: 'Valle del Cauca' },
            { id: 4, pais_id: 1, nombre: 'Atlántico' }
        ],
        ciudades: [
            { id: 1, departamento_id: 1, nombre: 'Bogotá' },
            { id: 2, departamento_id: 1, nombre: 'Soacha' },
            { id: 3, departamento_id: 2, nombre: 'Medellín' },
            { id: 4, departamento_id: 2, nombre: 'Envigado' },
            { id: 5, departamento_id: 3, nombre: 'Cali' },
            { id: 6, departamento_id: 3, nombre: 'Palmira' },
            { id: 7, departamento_id: 4, nombre: 'Barranquilla' }
        ]
    };

    var selPais = document.getElementById('pais');
    var selDepto = document.getElementById('departamento');
    var selCiudad = document.getElementById('ciudad');

    // Rellena un <select> con una lista de {id, nombre} y un placeholder.
    function llenarSelect(select, items, placeholder) {
        select.innerHTML = '';
        var opt = document.createElement('option');
        opt.value = '';
        opt.disabled = true;
        opt.selected = true;
        opt.textContent = placeholder;
        select.appendChild(opt);

        items.forEach(function (item) {
            var o = document.createElement('option');
            o.value = item.id;
            o.textContent = item.nombre;
            select.appendChild(o);
        });
    }

    // Carga inicial de países
    llenarSelect(selPais, UBICACIONES.paises, 'País…');

    selPais.addEventListener('change', function () {
        var paisId = parseInt(selPais.value, 10);
        var deptos = UBICACIONES.departamentos.filter(function (d) {
            return d.pais_id === paisId;
        });
        llenarSelect(selDepto, deptos, 'Departamento…');
        selDepto.disabled = false;

        // Reinicia ciudad hasta elegir departamento
        llenarSelect(selCiudad, [], 'Ciudad…');
        selCiudad.disabled = true;
        actualizarProgreso();
    });

    selDepto.addEventListener('change', function () {
        var deptoId = parseInt(selDepto.value, 10);
        var ciudades = UBICACIONES.ciudades.filter(function (c) {
            return c.departamento_id === deptoId;
        });
        llenarSelect(selCiudad, ciudades, 'Ciudad…');
        selCiudad.disabled = false;
        actualizarProgreso();
    });

    // ------------------------------------------------------------------
    // Panel de progreso
    // ------------------------------------------------------------------
    var titulo = document.getElementById('titulo');
    var tipo   = document.getElementById('tipo_propiedad');
    var precio = document.getElementById('precio_alquiler_mensual');
    var calle  = document.getElementById('calle');

    var progressFill    = document.getElementById('progress_fill');
    var progressPercent = document.getElementById('progress_percent');
    var progressHint    = document.getElementById('progress_hint');
    var submitBtn       = document.getElementById('propiedad_submit');

    var reglas = {
        titulo:          function () { return titulo.value.trim().length > 0; },
        tipo_propiedad:  function () { return tipo.value !== ''; },
        precio_alquiler_mensual: function () { return precio.value !== '' && parseFloat(precio.value) > 0; },
        ciudad:          function () { return selCiudad.value !== ''; },
        calle:           function () { return calle.value.trim().length > 0; }
    };

    var items = document.querySelectorAll('.progress_item');
    var total = items.length;

    function actualizarProgreso() {
        var completos = 0;

        items.forEach(function (item) {
            var req = item.getAttribute('data-req');
            var ok = reglas[req] ? reglas[req]() : false;
            item.classList.toggle('complete', ok);
            if (ok) { completos++; }
        });

        var porcentaje = Math.round((completos / total) * 100);
        progressFill.style.width = porcentaje + '%';
        progressFill.setAttribute('aria-valuenow', porcentaje);
        progressPercent.textContent = porcentaje;

        var listo = completos === total;
        submitBtn.disabled = !listo;

        if (listo) {
            progressHint.textContent = '¡Todo listo! Ya puedes publicar tu propiedad.';
            progressHint.classList.add('is_ready');
        } else {
            progressHint.textContent = 'Completa los campos obligatorios para publicar.';
            progressHint.classList.remove('is_ready');
        }
    }

    form.addEventListener('input', actualizarProgreso);
    form.addEventListener('change', actualizarProgreso);

    // Estado inicial
    actualizarProgreso();
})();
