<?php
$title  = 'Solicitar arriendo | miarriendo.online';
$styles = ['propiedad.css', 'contrato.css'];
require __DIR__ . '/layouts/header.php';

$precio   = number_format((float) $propiedad['precio_alquiler_mensual'], 0, ',', '.');
$deposito = !empty($propiedad['deposito']) ? number_format((float) $propiedad['deposito'], 0, ',', '.') : null;
$hoy = date('Y-m-d');
?>

    <main class="main_container">
        <a href="/propiedad/<?= e($propiedad['propiedad_id']) ?>" class="detalle_back"><span class="material-symbols-outlined icon_sm">arrow_back</span> Volver a la propiedad</a>

        <div class="solicitar_layout">
            <section class="solicitar_form_card">
                <h1 class="page_title">Solicitar arriendo</h1>
                <p class="u_text_muted">Elige las fechas de tu arriendo de <strong><?= e($propiedad['titulo']) ?></strong>. El propietario revisará tu solicitud.</p>

                <?php if (!empty($error)): ?>
                    <p class="form_error" role="alert"><?= e($error) ?></p>
                <?php endif; ?>

                <form action="/propiedad/<?= e($propiedad['propiedad_id']) ?>/solicitar" method="POST">
                    <?= csrf_field() ?>
                    <div class="form_group">
                        <label for="fecha_inicio" class="form_label">Fecha de inicio</label>
                        <input type="date" id="fecha_inicio" name="fecha_inicio" class="form_input" min="<?= e($hoy) ?>" value="<?= e($_POST['fecha_inicio'] ?? '') ?>" required>
                    </div>
                    <div class="form_group">
                        <label for="duracion_meses" class="form_label">Duración del contrato</label>
                        <select id="duracion_meses" name="duracion_meses" class="form_input" required>
                            <option value="6">6 meses</option>
                            <option value="12" selected>12 meses (1 año)</option>
                            <option value="18">18 meses</option>
                            <option value="24">24 meses (2 años)</option>
                            <option value="36">36 meses (3 años)</option>
                        </select>
                    </div>

                    <div class="solicitar_resumen">
                        <div class="solicitar_resumen_row"><span>Canon mensual</span><strong>$<?= e($precio) ?></strong></div>
                        <?php if ($deposito): ?>
                            <div class="solicitar_resumen_row"><span>Depósito</span><strong>$<?= e($deposito) ?></strong></div>
                        <?php endif; ?>
                    </div>

                    <p class="help_hint">Al enviar la solicitud aceptas revisar el contrato. La firma se realiza cuando el propietario aprueba tu solicitud.</p>
                    <button type="submit" class="btn_primary u_full_width">Enviar solicitud</button>
                </form>
            </section>

            <aside class="solicitar_aside">
                <div class="detalle_card">
                    <span class="detalle_badge"><?= e($propiedad['tipo_propiedad']) ?></span>
                    <h2 class="detalle_titulo"><?= e($propiedad['titulo']) ?></h2>
                    <p class="detalle_ubicacion_mini"><span class="material-symbols-outlined icon_sm">location_on</span> <?= e($propiedad['ciudad']) ?>, <?= e($propiedad['departamento']) ?></p>
                    <a href="/propiedad/<?= e($propiedad['propiedad_id']) ?>#contrato" class="btn_outline u_full_width">Ver el contrato completo</a>
                </div>
            </aside>
        </div>
    </main>

<?php
$showFooter = false;
require __DIR__ . '/layouts/footer.php';
?>
