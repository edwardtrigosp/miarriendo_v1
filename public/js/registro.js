// Registro: panel de progreso en vivo + medidor de fortaleza de contraseña.
(function () {
    'use strict';

    var form = document.getElementById('registro_form');
    if (!form) {
        return;
    }

    // Campos del formulario
    var nombre    = document.getElementById('nombre');
    var apellidos = document.getElementById('apellidos');
    var email     = document.getElementById('email');
    var contrasena = document.getElementById('contrasena');
    var confirmar = document.getElementById('confirmar');
    var terminos  = document.getElementById('terminos');

    // Elementos del panel y de ayuda
    var progressFill    = document.getElementById('progress_fill');
    var progressPercent = document.getElementById('progress_percent');
    var progressHint    = document.getElementById('progress_hint');
    var submitBtn       = document.getElementById('registro_submit');
    var strengthFill    = document.getElementById('strength_fill');
    var strengthText    = document.getElementById('strength_text');
    var confirmText     = document.getElementById('confirm_text');

    // Reglas de cada requisito (clave = data-req del <li>)
    var reglas = {
        nombre:    function () { return nombre.value.trim().length > 0; },
        apellidos: function () { return apellidos.value.trim().length > 0; },
        email:     function () { return email.value.trim().length > 0 && email.checkValidity(); },
        contrasena: function () { return contrasena.value.length >= 8; },
        confirmar: function () { return confirmar.value.length > 0 && confirmar.value === contrasena.value; },
        terminos:  function () { return terminos.checked; }
    };

    var items = document.querySelectorAll('.progress_item');
    var total = items.length;

    // Calcula la fortaleza de la contraseña (0 a 3)
    function fortaleza(pass) {
        var puntos = 0;
        if (pass.length >= 8) { puntos++; }
        if (/[A-Z]/.test(pass) && /[a-z]/.test(pass)) { puntos++; }
        if (/[0-9]/.test(pass) || /[^A-Za-z0-9]/.test(pass)) { puntos++; }
        return puntos;
    }

    function actualizarFortaleza() {
        strengthFill.className = 'strength_fill';
        if (!contrasena.value) {
            strengthText.textContent = 'Usa al menos 8 caracteres.';
            strengthText.className = 'help_hint';
            return;
        }
        var nivel = fortaleza(contrasena.value);
        if (nivel <= 1) {
            strengthFill.classList.add('weak');
            strengthText.textContent = 'Contraseña débil.';
            strengthText.className = 'help_hint is_error';
        } else if (nivel === 2) {
            strengthFill.classList.add('medium');
            strengthText.textContent = 'Contraseña media: añade mayúsculas y números.';
            strengthText.className = 'help_hint';
        } else {
            strengthFill.classList.add('strong');
            strengthText.textContent = 'Contraseña segura. ¡Bien!';
            strengthText.className = 'help_hint is_ok';
        }
    }

    function actualizarConfirmacion() {
        if (!confirmar.value) {
            confirmText.textContent = '';
            confirmText.className = 'help_hint';
            return;
        }
        if (confirmar.value === contrasena.value) {
            confirmText.textContent = 'Las contraseñas coinciden.';
            confirmText.className = 'help_hint is_ok';
        } else {
            confirmText.textContent = 'Las contraseñas no coinciden.';
            confirmText.className = 'help_hint is_error';
        }
    }

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
            progressHint.textContent = '¡Todo listo! Ya puedes crear tu cuenta.';
            progressHint.classList.add('is_ready');
        } else {
            progressHint.textContent = 'Completa todos los pasos para crear tu cuenta.';
            progressHint.classList.remove('is_ready');
        }

        actualizarFortaleza();
        actualizarConfirmacion();
    }

    // Recalcular en cada cambio del formulario
    form.addEventListener('input', actualizarProgreso);
    form.addEventListener('change', actualizarProgreso);

    // Estado inicial
    actualizarProgreso();
})();
