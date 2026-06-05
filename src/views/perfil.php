<?php
$title  = 'Mi perfil | miarriendo.online';
$styles = ['auth.css', 'perfil.css', 'panel.css'];
require __DIR__ . '/layouts/header.php';

$usuario = $usuario ?? ['nombre' => '', 'apellidos' => '', 'email' => '', 'telefono' => '', 'foto_url' => ''];
$inicial = strtoupper(mb_substr($usuario['nombre'] ?? 'U', 0, 1));
$foto    = $usuario['foto_url'] ?? '';

// Completitud del perfil para el panel lateral.
$campos = [
    'Foto de perfil' => $foto !== '',
    'Nombre'         => trim($usuario['nombre'] ?? '') !== '',
    'Apellidos'      => trim($usuario['apellidos'] ?? '') !== '',
    'Correo'         => trim($usuario['email'] ?? '') !== '',
    'Teléfono'       => trim($usuario['telefono'] ?? '') !== '',
];
$completos   = count(array_filter($campos));
$totalCampos = count($campos);
$pctPerfil   = (int) round($completos / $totalCampos * 100);
?>

    <main class="main_container panel_wrap">
        <div class="view_layout">
        <div class="view_main">
        <section class="profile_content">
            <div class="profile_header">
                <h1 class="profile_title">Mi perfil</h1>
                <p class="profile_subtitle">Actualiza tu información de contacto.</p>
            </div>

            <div class="avatar_section">
                <div class="avatar_circle">
                    <?php if ($foto !== ''): ?>
                        <img src="<?= e($foto) ?>" alt="Foto de perfil" class="avatar_img">
                    <?php else: ?>
                        <?= e($inicial) ?>
                    <?php endif; ?>
                </div>
                <div class="avatar_actions">
                    <strong><?= e(trim(($usuario['nombre'] ?? '') . ' ' . ($usuario['apellidos'] ?? ''))) ?></strong>
                    <span class="help_text"><?= e($usuario['email'] ?? '') ?></span>

                    <form action="/perfil/foto" method="POST" enctype="multipart/form-data" class="avatar_upload_form">
                        <?= csrf_field() ?>
                        <input type="file" id="foto" name="foto" accept="image/jpeg,image/png,image/webp"
                               class="avatar_file_input" style="display:none" onchange="this.form.submit()">
                        <label for="foto" class="btn_outline btn_sm avatar_upload_btn">
                            <span class="material-symbols-outlined">photo_camera</span>
                            <?= $foto !== '' ? 'Cambiar foto' : 'Subir foto' ?>
                        </label>
                        <span class="help_text">JPG, PNG o WEBP · máx. 3 MB</span>
                    </form>
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
                    <button type="submit" class="btn_primary">Guardar cambios</button>
                </div>
            </form>
        </section>
        </div><!-- /.view_main -->

        <aside class="side_card">
            <h2>Completitud de tu perfil</h2>
            <div class="side_progress_bar"><div class="side_progress_fill" style="width:<?= $pctPerfil ?>%"></div></div>
            <p class="side_progress_label"><?= $pctPerfil ?>% completo · <?= $completos ?> de <?= $totalCampos ?></p>

            <ul class="side_checklist">
                <?php foreach ($campos as $label => $ok): ?>
                    <li class="<?= $ok ? 'is_done' : '' ?>">
                        <span class="material-symbols-outlined"><?= $ok ? 'check_circle' : 'radio_button_unchecked' ?></span>
                        <?= e($label) ?>
                    </li>
                <?php endforeach; ?>
            </ul>

            <p class="side_hint">Un perfil completo genera más confianza con propietarios e inquilinos.</p>
        </aside>
        </div><!-- /.view_layout -->
    </main>

<?php
$showFooter = false;
require __DIR__ . '/layouts/footer.php';
?>
