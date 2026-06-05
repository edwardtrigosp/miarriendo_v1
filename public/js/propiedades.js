// Publicar propiedad: selects de ubicación en cascada + panel de progreso en vivo.
(function () {
    'use strict';

    var form = document.getElementById('propiedad_form');
    if (!form) {
        return;
    }

    // Ubicaciones reales inyectadas por PHP desde la base de datos.
    // Los ids vienen como texto en el JSON; el cascade compara con ==.
    var UBICACIONES = window.UBICACIONES || { paises: [], departamentos: [], ciudades: [] };

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
            return parseInt(d.pais_id, 10) === paisId;
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
            return parseInt(c.departamento_id, 10) === deptoId;
        });
        llenarSelect(selCiudad, ciudades, 'Ciudad…');
        selCiudad.disabled = false;
        actualizarProgreso();
    });

    // ------------------------------------------------------------------
    // Panel de progreso
    // ------------------------------------------------------------------
    var titulo    = document.getElementById('titulo');
    var tipo      = document.getElementById('tipo_propiedad');
    var precio    = document.getElementById('precio_alquiler_mensual');
    var calle     = document.getElementById('calle');
    var clausulas = document.getElementById('clausulas_contrato');
    var imagenes  = document.getElementById('imagenes');

    var progressFill    = document.getElementById('progress_fill');
    var progressPercent = document.getElementById('progress_percent');
    var progressHint    = document.getElementById('progress_hint');
    var submitBtn       = document.getElementById('propiedad_submit');

    // Una regla por SECCIÓN. Las opcionales (fotos, contrato) muestran su
    // check al llenarse, pero no cuentan para poder publicar.
    var reglas = {
        informacion:     function () { return titulo.value.trim().length > 0 && tipo.value !== ''; },
        fotos:           function () { return !!imagenes && imagenes.files.length > 0; },
        caracteristicas: function () { return precio.value !== '' && parseFloat(precio.value) > 0; },
        ubicacion:       function () { return selCiudad.value !== '' && calle.value.trim().length > 0; },
        contrato:        function () { return !!clausulas && clausulas.value.trim().length > 0; }
    };

    var items = document.querySelectorAll('.progress_item');

    function actualizarProgreso() {
        var totalReq = 0, completosReq = 0;

        items.forEach(function (item) {
            var req = item.getAttribute('data-req');
            var ok = reglas[req] ? reglas[req]() : false;
            item.classList.toggle('complete', ok);
            // Solo las secciones obligatorias cuentan para el % y el submit.
            if (item.getAttribute('data-optional') === null) {
                totalReq++;
                if (ok) { completosReq++; }
            }
        });

        var porcentaje = totalReq ? Math.round((completosReq / totalReq) * 100) : 0;
        progressFill.style.width = porcentaje + '%';
        progressFill.setAttribute('aria-valuenow', porcentaje);
        progressPercent.textContent = porcentaje;

        var listo = completosReq === totalReq;
        submitBtn.disabled = !listo;

        if (listo) {
            progressHint.textContent = '¡Todo listo! Ya puedes publicar tu propiedad.';
            progressHint.classList.add('is_ready');
        } else {
            progressHint.textContent = 'Completa las secciones obligatorias para publicar.';
            progressHint.classList.remove('is_ready');
        }
    }

    form.addEventListener('input', actualizarProgreso);
    form.addEventListener('change', actualizarProgreso);

    // ------------------------------------------------------------------
    // Previsualización de las fotos seleccionadas
    // ------------------------------------------------------------------
    var inputImagenes = document.getElementById('imagenes');
    var preview = document.getElementById('upload_preview');

    if (inputImagenes && preview) {
        inputImagenes.addEventListener('change', function () {
            preview.innerHTML = '';
            Array.prototype.forEach.call(inputImagenes.files, function (file, i) {
                if (!file.type.startsWith('image/')) { return; }
                var div = document.createElement('div');
                div.className = 'upload_thumb';
                var img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                img.onload = function () { URL.revokeObjectURL(img.src); };
                div.appendChild(img);
                if (i === 0) {
                    var badge = document.createElement('span');
                    badge.className = 'thumb_badge';
                    badge.textContent = 'Portada';
                    div.appendChild(badge);
                }
                preview.appendChild(div);
            });
        });
    }

    // Estado inicial
    actualizarProgreso();
})();
