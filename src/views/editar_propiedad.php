<?php
$title  = 'Editar propiedad | miarriendo.online';
$styles = ['auth.css', 'wizard.css'];
require __DIR__ . '/layouts/header.php';

$p = $propiedad;
$val = static fn(string $k) => e($p[$k] ?? '');
$chk = static fn(string $k, int $def = 0) => ((int) ($p[$k] ?? $def) === 1) ? 'checked' : '';
$tipos = ['Apartamento', 'Casa', 'Apartaestudio', 'Habitación', 'Local'];
?>

    <div class="editar_wrap">
        <a href="/propiedad/<?= e($p['propiedad_id']) ?>" class="detalle_back"><span class="material-symbols-outlined icon_sm">arrow_back</span> Volver a la propiedad</a>

        <section class="wizard_main">
            <header class="wizard_header">
                <h1 class="auth_title">Editar propiedad</h1>
                <p class="auth_subtitle">Actualiza la información de <strong><?= $val('titulo') ?></strong>. La ciudad no se puede cambiar aquí.</p>
            </header>

            <?php if (!empty($exito)): ?>
                <p class="form_success" role="status"><?= e($exito) ?></p>
            <?php endif; ?>
            <?php if (!empty($error)): ?>
                <p class="form_error" role="alert"><?= e($error) ?></p>
            <?php endif; ?>

            <form action="/propiedad/<?= e($p['propiedad_id']) ?>/editar" method="POST" novalidate>
                <?= csrf_field() ?>

                <fieldset class="form_fieldset">
                    <legend class="form_legend">Información</legend>
                    <div class="form_group">
                        <label for="titulo" class="form_label">Título</label>
                        <input type="text" id="titulo" name="titulo" class="form_input" value="<?= $val('titulo') ?>" maxlength="200" required>
                    </div>
                    <div class="form_group">
                        <label for="tipo_propiedad" class="form_label">Tipo de propiedad</label>
                        <select id="tipo_propiedad" name="tipo_propiedad" class="form_input" required>
                            <?php foreach ($tipos as $t): ?>
                                <option value="<?= e($t) ?>" <?= ($p['tipo_propiedad'] ?? '') === $t ? 'selected' : '' ?>><?= e($t) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form_group">
                        <label for="descripcion" class="form_label">Descripción <span class="label_hint">(Opcional)</span></label>
                        <textarea id="descripcion" name="descripcion" class="form_input" rows="4"><?= $val('descripcion') ?></textarea>
                    </div>
                </fieldset>

                <fieldset class="form_fieldset">
                    <legend class="form_legend">Características</legend>
                    <div class="form_row_triple">
                        <div class="form_group">
                            <label for="num_habitaciones" class="form_label">Habitaciones</label>
                            <input type="number" id="num_habitaciones" name="num_habitaciones" class="form_input" value="<?= $val('num_habitaciones') ?>" min="0" max="50">
                        </div>
                        <div class="form_group">
                            <label for="num_banos" class="form_label">Baños</label>
                            <input type="number" id="num_banos" name="num_banos" class="form_input" value="<?= $val('num_banos') ?>" min="0" max="50">
                        </div>
                        <div class="form_group">
                            <label for="area_m2" class="form_label">Área (m²)</label>
                            <input type="number" id="area_m2" name="area_m2" class="form_input" value="<?= $val('area_m2') ?>" min="0" step="0.01">
                        </div>
                    </div>
                    <div class="form_check">
                        <input type="checkbox" id="amueblado" name="amueblado" value="1" <?= $chk('amueblado') ?>>
                        <label for="amueblado">La propiedad está amueblada</label>
                    </div>
                    <div class="form_check">
                        <input type="checkbox" id="mascotas_permitidas" name="mascotas_permitidas" value="1" <?= $chk('mascotas_permitidas') ?>>
                        <label for="mascotas_permitidas">Se permiten mascotas</label>
                    </div>
                </fieldset>

                <fieldset class="form_fieldset">
                    <legend class="form_legend">Precio y disponibilidad</legend>
                    <div class="form_row_double">
                        <div class="form_group">
                            <label for="precio_alquiler_mensual" class="form_label">Precio mensual (COP)</label>
                            <input type="number" id="precio_alquiler_mensual" name="precio_alquiler_mensual" class="form_input" value="<?= $val('precio_alquiler_mensual') ?>" min="0" step="1000" required>
                        </div>
                        <div class="form_group">
                            <label for="deposito" class="form_label">Depósito <span class="label_hint">(Opcional)</span></label>
                            <input type="number" id="deposito" name="deposito" class="form_input" value="<?= $val('deposito') ?>" min="0" step="1000">
                        </div>
                    </div>
                    <div class="form_check">
                        <input type="checkbox" id="disponible" name="disponible" value="1" <?= $chk('disponible', 1) ?>>
                        <label for="disponible">Disponible para arriendo</label>
                    </div>
                </fieldset>

                <fieldset class="form_fieldset">
                    <legend class="form_legend">Dirección <span class="label_hint">(<?= $val('ciudad') ?>, <?= $val('departamento') ?>)</span></legend>
                    <div class="form_group">
                        <label for="calle" class="form_label">Dirección (calle)</label>
                        <input type="text" id="calle" name="calle" class="form_input" value="<?= $val('calle') ?>" maxlength="255" required>
                    </div>
                    <div class="form_row_double">
                        <div class="form_group">
                            <label for="numero_exterior" class="form_label">Número <span class="label_hint">(Opcional)</span></label>
                            <input type="text" id="numero_exterior" name="numero_exterior" class="form_input" value="<?= $val('numero_exterior') ?>" maxlength="20">
                        </div>
                        <div class="form_group">
                            <label for="barrio" class="form_label">Barrio <span class="label_hint">(Opcional)</span></label>
                            <input type="text" id="barrio" name="barrio" class="form_input" value="<?= $val('barrio') ?>" maxlength="100">
                        </div>
                    </div>
                    <div class="form_row_double">
                        <div class="form_group">
                            <label for="codigo_postal" class="form_label">Código postal <span class="label_hint">(Opcional)</span></label>
                            <input type="text" id="codigo_postal" name="codigo_postal" class="form_input" value="<?= $val('codigo_postal') ?>" maxlength="10">
                        </div>
                        <div class="form_group">
                            <label for="referencia" class="form_label">Referencia <span class="label_hint">(Opcional)</span></label>
                            <input type="text" id="referencia" name="referencia" class="form_input" value="<?= $val('referencia') ?>" maxlength="255">
                        </div>
                    </div>
                </fieldset>

                <fieldset class="form_fieldset">
                    <legend class="form_legend">Contrato</legend>
                    <div class="form_group">
                        <label for="clausulas_contrato" class="form_label">Cláusulas adicionales <span class="label_hint">(Opcional)</span></label>
                        <textarea id="clausulas_contrato" name="clausulas_contrato" class="form_input" rows="5" maxlength="5000"><?= $val('clausulas_contrato') ?></textarea>
                    </div>
                </fieldset>

                <button type="submit" class="btn_primary u_full_width">Guardar cambios</button>
            </form>

            <!-- Gestión de fotos (formularios independientes) -->
            <fieldset class="form_fieldset fotos_fieldset">
                <legend class="form_legend">Fotos de la propiedad</legend>

                <?php if (!empty($imagenes)): ?>
                    <div class="fotos_grid">
                        <?php foreach ($imagenes as $img): ?>
                            <div class="foto_item">
                                <img src="<?= e($img['url_imagen']) ?>" alt="Foto">
                                <?php if ((int) $img['es_principal'] === 1): ?>
                                    <span class="foto_portada">Portada</span>
                                <?php endif; ?>
                                <form action="/propiedad/<?= e($p['propiedad_id']) ?>/foto/<?= e($img['imagen_id']) ?>/eliminar" method="POST" onsubmit="return confirm('¿Eliminar esta foto?');">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="foto_eliminar" title="Eliminar foto">
                                        <span class="material-symbols-outlined icon_sm">delete</span>
                                    </button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="u_text_muted">Esta propiedad aún no tiene fotos.</p>
                <?php endif; ?>

                <form action="/propiedad/<?= e($p['propiedad_id']) ?>/fotos" method="POST" enctype="multipart/form-data" class="fotos_add">
                    <?= csrf_field() ?>
                    <label for="imagenes" class="upload_zone">
                        <span class="material-symbols-outlined icon_lg">add_photo_alternate</span>
                        <span class="upload_text">Añadir fotos</span>
                        <span class="help_hint">JPG, PNG o WEBP · máx. 3 MB c/u</span>
                        <input type="file" id="imagenes" name="imagenes[]" accept="image/jpeg,image/png,image/webp,image/gif" multiple hidden onchange="this.form.submit()">
                    </label>
                </form>
            </fieldset>
        </section>
    </div>

<?php
$showFooter = false;
require __DIR__ . '/layouts/footer.php';
?>
