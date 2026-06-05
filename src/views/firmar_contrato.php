<?php
$title  = 'Firmar contrato | miarriendo.online';
$styles = ['propiedad.css', 'contrato.css'];
require __DIR__ . '/layouts/header.php';

$fmt   = static fn($iso) => $iso ? date('d/m/Y', strtotime($iso)) : '—';
$money = static fn($v) => '$' . number_format((float) $v, 0, ',', '.');
?>

    <main class="main_container">
        <a href="/contrato/<?= e($contrato['contrato_id']) ?>" class="detalle_back"><span class="material-symbols-outlined icon_sm">arrow_back</span> Volver al contrato</a>

        <div class="contrato_pagina">
            <header class="contrato_cabecera">
                <div>
                    <h1 class="page_title">Firmar contrato</h1>
                    <p class="u_text_muted"><?= e($contrato['propiedad_titulo']) ?></p>
                </div>
            </header>

            <?php if (!empty($error)): ?>
                <p class="form_error" role="alert"><?= e($error) ?></p>
            <?php endif; ?>

            <div class="contrato_meta">
                <div><span class="u_text_muted">Arrendador</span><strong><?= e(trim($contrato['propietario_nombre'] . ' ' . $contrato['propietario_apellidos'])) ?></strong></div>
                <div><span class="u_text_muted">Canon mensual</span><strong><?= e($money($contrato['monto_mensual'])) ?></strong></div>
                <div><span class="u_text_muted">Inicio</span><strong><?= e($fmt($contrato['fecha_inicio'])) ?></strong></div>
                <div><span class="u_text_muted">Fin</span><strong><?= e($fmt($contrato['fecha_fin'])) ?></strong></div>
                <div><span class="u_text_muted">Duración</span><strong><?= e($contrato['duracion_meses']) ?> meses</strong></div>
            </div>

            <div class="contrato_doc">
                <h3 class="contrato_doc_titulo">CLÁUSULAS DEL CONTRATO</h3>
                <div class="contrato_clausulas_texto"><?= nl2br(e($contrato['clausulas'])) ?></div>
            </div>

            <!-- Firma electrónica -->
            <form action="/contrato/<?= e($contrato['contrato_id']) ?>/firmar" method="POST" class="firma_form">
                <?= csrf_field() ?>
                <h3 class="firma_titulo">Firma electrónica</h3>
                <p class="u_text_muted">Al firmar aceptas las condiciones anteriores. Quedará registrada la fecha, tu nombre y tu dirección IP como evidencia legal.</p>

                <label class="firma_check">
                    <input type="checkbox" name="acepto" value="1" required>
                    <span>He leído y <strong>acepto</strong> las condiciones del contrato de arrendamiento.</span>
                </label>

                <div class="form_group">
                    <label for="firma" class="form_label">Escribe tu nombre completo como firma</label>
                    <input type="text" id="firma" name="firma" class="form_input" placeholder="Ej: Carlos Andrés Inquilino Pérez" value="<?= e($_POST['firma'] ?? '') ?>" required>
                </div>

                <button type="submit" class="btn_primary u_full_width">Firmar y activar el arriendo</button>
            </form>
        </div>
    </main>

<?php
$showFooter = false;
require __DIR__ . '/layouts/footer.php';
?>
