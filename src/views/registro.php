<?php
$title  = 'Crea tu cuenta | miarriendo.online';
$styles = ['auth.css', 'wizard.css'];
require __DIR__ . '/layouts/header.php';
?>

    <div class="wizard_layout">

        <!-- Columna izquierda: formulario (con scroll si es necesario) -->
        <section class="wizard_main" aria-labelledby="registro_titulo">
            <header class="wizard_header">
                <h1 id="registro_titulo" class="auth_title">Regístrate gratis</h1>
                <p class="auth_subtitle">Crea tu perfil único de usuario hoy mismo</p>
            </header>

            <?php if (!empty($error)): ?>
                <p class="form_error" role="alert"><?= e($error) ?></p>
            <?php endif; ?>

            <form action="/registro" method="POST" id="registro_form" novalidate>
                <?= csrf_field() ?>

                <fieldset class="form_fieldset">
                    <legend class="form_legend">Datos personales</legend>
                    <div class="form_row_double">
                        <div class="form_group">
                            <label for="nombre" class="form_label">Nombre</label>
                            <input type="text" id="nombre" name="nombre" class="form_input" placeholder="Ej. Juan" value="<?= e($datos['nombre'] ?? '') ?>" autocomplete="given-name" required>
                        </div>
                        <div class="form_group">
                            <label for="apellidos" class="form_label">Apellidos</label>
                            <input type="text" id="apellidos" name="apellidos" class="form_input" placeholder="Ej. Pérez" value="<?= e($datos['apellidos'] ?? '') ?>" autocomplete="family-name" required>
                        </div>
                    </div>
                </fieldset>

                <fieldset class="form_fieldset">
                    <legend class="form_legend">Datos de contacto</legend>
                    <div class="form_group">
                        <label for="email" class="form_label">Correo electrónico</label>
                        <input type="email" id="email" name="email" class="form_input" placeholder="tu@email.com" value="<?= e($datos['email'] ?? '') ?>" autocomplete="email" required>
                    </div>
                    <div class="form_group">
                        <label for="telefono" class="form_label">Teléfono <span class="label_hint">(Opcional)</span></label>
                        <input type="tel" id="telefono" name="telefono" class="form_input" placeholder="+57 300 000 0000" value="<?= e($datos['telefono'] ?? '') ?>" autocomplete="tel" inputmode="tel" maxlength="20">
                    </div>
                </fieldset>

                <fieldset class="form_fieldset">
                    <legend class="form_legend">Seguridad</legend>
                    <div class="form_group">
                        <label for="contrasena" class="form_label">Contraseña</label>
                        <input type="password" id="contrasena" name="contrasena" class="form_input" placeholder="Mínimo 8 caracteres" autocomplete="new-password" minlength="8" required>
                        <div class="strength_meter" aria-hidden="true">
                            <span class="strength_fill" id="strength_fill"></span>
                        </div>
                        <span class="help_hint" id="strength_text">Usa al menos 8 caracteres.</span>
                    </div>
                    <div class="form_group">
                        <label for="confirmar" class="form_label">Confirmar contraseña</label>
                        <input type="password" id="confirmar" name="confirmar" class="form_input" placeholder="Repite tu contraseña" autocomplete="new-password" required>
                        <span class="help_hint" id="confirm_text"></span>
                    </div>
                </fieldset>

                <div class="form_group form_check">
                    <input type="checkbox" id="terminos" name="terminos" required>
                    <label for="terminos">Acepto los términos y la <a href="/cookies" class="text_link" target="_blank" rel="noopener">política de cookies</a> <span class="label_hint">(se abre en otra pestaña)</span>.</label>
                </div>

                <button type="submit" id="registro_submit" class="btn_primary u_full_width" disabled>Crear Cuenta de Usuario</button>

                <p class="auth_footer">¿Ya posees una cuenta registrada? <a href="/login" class="text_link">Inicia sesión</a></p>
            </form>
        </section>

        <!-- Columna derecha: panel de progreso fijo (sticky) -->
        <aside class="progress_panel" aria-label="Progreso del registro">
            <h2 class="progress_title">Tu progreso</h2>

            <div class="progress_bar">
                <div class="progress_fill" id="progress_fill"
                     role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"></div>
            </div>
            <p class="progress_percent"><span id="progress_percent">0</span>% completado</p>

            <ul class="progress_list">
                <li class="progress_item" data-req="nombre">Nombre</li>
                <li class="progress_item" data-req="apellidos">Apellidos</li>
                <li class="progress_item" data-req="email">Correo electrónico válido</li>
                <li class="progress_item" data-req="contrasena">Contraseña (mín. 8 caracteres)</li>
                <li class="progress_item" data-req="confirmar">Las contraseñas coinciden</li>
                <li class="progress_item" data-req="terminos">Aceptar términos</li>
            </ul>

            <p class="progress_hint" id="progress_hint">Completa todos los pasos para crear tu cuenta.</p>
        </aside>

    </div>

<?php $showFooter = false; ?>
    <script src="/js/registro.js"></script>
<?php require __DIR__ . '/layouts/footer.php'; ?>
