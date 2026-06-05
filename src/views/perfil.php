<?php
$title  = 'Configuración de Perfil | miarriendo.online';
$styles = ['auth.css', 'perfil.css'];
require __DIR__ . '/layouts/header.php';

// Datos del usuario (vendrán de la sesión / DB). Valores por defecto para la vista.
$usuario = $usuario ?? [
    'nombre'    => 'Edward',
    'apellidos' => '',
    'email'     => '',
    'telefono'  => '',
];
$inicial = strtoupper(substr($usuario['nombre'] ?? 'U', 0, 1));
?>

    <main class="main_container">
        <div class="profile_layout">

            <aside class="profile_sidebar">
                <a href="#" class="sidebar_link active">Datos Personales</a>
                <a href="#" class="sidebar_link">Seguridad y Contraseña</a>
                <a href="#" class="sidebar_link">Mis Propiedades</a>
                <a href="#" class="sidebar_link">Mis Arriendos</a>
            </aside>

            <section class="profile_content">
                <div class="profile_header">
                    <h1 class="profile_title">Datos Personales</h1>
                    <p class="profile_subtitle">Actualiza tu información básica y cómo te ven los demás en la plataforma.</p>
                </div>

                <div class="avatar_section">
                    <div class="avatar_circle"><?= e($inicial) ?></div>
                    <div class="avatar_actions">
                        <div class="avatar_buttons">
                            <button type="button" class="btn_outline btn_sm">Cambiar foto</button>
                            <button type="button" class="btn_outline btn_sm btn_danger">Eliminar</button>
                        </div>
                        <span class="help_text">JPG, GIF o PNG. Tamaño máximo de 2MB.</span>
                    </div>
                </div>

                <?php if (!empty($exito)): ?>
                    <p class="form_success" role="status"><?= e($exito) ?></p>
                <?php endif; ?>
                <?php if (!empty($error)): ?>
                    <p class="form_error" role="alert"><?= e($error) ?></p>
                <?php endif; ?>

                <form action="/perfil/actualizar" method="POST">
                    <?= csrf_field() ?>

                    <div class="form_row_double">
                        <div class="form_group">
                            <label for="nombre" class="form_label">Nombre</label>
                            <input type="text" id="nombre" name="nombre" class="form_input" value="<?= e($usuario['nombre']) ?>" required>
                        </div>
                        <div class="form_group">
                            <label for="apellidos" class="form_label">Apellidos</label>
                            <input type="text" id="apellidos" name="apellidos" class="form_input" value="<?= e($usuario['apellidos']) ?>" placeholder="Tus apellidos" required>
                        </div>
                    </div>

                    <div class="form_row_double">
                        <div class="form_group form_group_grow">
                            <label for="email" class="form_label">Correo electrónico</label>
                            <input type="email" id="email" name="email" class="form_input" value="<?= e($usuario['email']) ?>" placeholder="tu@email.com" required>
                        </div>
                        <div class="form_group">
                            <label for="telefono" class="form_label">Teléfono</label>
                            <input type="tel" id="telefono" name="telefono" class="form_input" value="<?= e($usuario['telefono']) ?>" placeholder="+57 300 000 0000">
                        </div>
                    </div>

                    <div class="form_actions">
                        <button type="submit" class="btn_primary">Guardar Cambios</button>
                    </div>
                </form>

            </section>
        </div>
    </main>

<?php
$showFooter = false;
require __DIR__ . '/layouts/footer.php';
?>
