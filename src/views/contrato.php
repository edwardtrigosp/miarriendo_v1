<?php
$title  = 'Contrato | miarriendo.online';
$styles = ['propiedad.css', 'contrato.css'];
require __DIR__ . '/layouts/header.php';

// Estado legible + color
$estados = [
    'borrador'  => ['Pendiente de aprobación', 'is_pendiente'],
    'enviado'   => ['Aprobado · pendiente de firma', 'is_aprobado'],
    'aceptado'  => ['Firmado · arriendo activo', 'is_aceptado'],
    'rechazado' => ['Rechazado', 'is_rechazado'],
    'anulado'   => ['Anulado', 'is_rechazado'],
];
[$estadoTexto, $estadoClase] = $estados[$contrato['estado']] ?? [$contrato['estado'], ''];

$fmt = static fn($iso) => $iso ? date('d/m/Y', strtotime($iso)) : '—';
$money = static fn($v) => '$' . number_format((float) $v, 0, ',', '.');
?>

    <main class="main_container">
        <a href="/panel" class="detalle_back"><span class="material-symbols-outlined icon_sm">arrow_back</span> Volver al panel</a>

        <div class="contrato_pagina">
            <header class="contrato_cabecera">
                <div>
                    <h1 class="page_title">Contrato de arrendamiento</h1>
                    <p class="u_text_muted"><?= e($contrato['propiedad_titulo']) ?></p>
                </div>
                <div class="contrato_cabecera_acciones">
                    <span class="contrato_estado_badge <?= e($estadoClase) ?>"><?= e($estadoTexto) ?></span>
                    <a href="/contrato/<?= e($contrato['contrato_id']) ?>/pdf" class="btn_outline btn_sm">
                        <span class="material-symbols-outlined icon_sm">picture_as_pdf</span> Descargar PDF
                    </a>
                </div>
            </header>

            <?php if (!empty($exito)): ?>
                <p class="form_success" role="status"><?= e($exito) ?></p>
            <?php endif; ?>

            <!-- Datos clave -->
            <div class="contrato_meta">
                <div><span class="u_text_muted">Arrendador</span><strong><?= e(trim($contrato['propietario_nombre'] . ' ' . $contrato['propietario_apellidos'])) ?></strong></div>
                <div><span class="u_text_muted">Arrendatario</span><strong><?= e(trim($contrato['inquilino_nombre'] . ' ' . $contrato['inquilino_apellidos'])) ?></strong></div>
                <div><span class="u_text_muted">Canon mensual</span><strong><?= e($money($contrato['monto_mensual'])) ?></strong></div>
                <div><span class="u_text_muted">Depósito</span><strong><?= $contrato['deposito'] ? e($money($contrato['deposito'])) : '—' ?></strong></div>
                <div><span class="u_text_muted">Inicio</span><strong><?= e($fmt($contrato['fecha_inicio'])) ?></strong></div>
                <div><span class="u_text_muted">Fin</span><strong><?= e($fmt($contrato['fecha_fin'])) ?></strong></div>
                <div><span class="u_text_muted">Duración</span><strong><?= e($contrato['duracion_meses']) ?> meses</strong></div>
            </div>

            <!-- Cláusulas (instantánea firmada) -->
            <div class="contrato_doc">
                <h3 class="contrato_doc_titulo">CLÁUSULAS DEL CONTRATO</h3>
                <div class="contrato_clausulas_texto"><?= nl2br(e($contrato['clausulas'])) ?></div>
            </div>

            <!-- Firma (si ya fue aceptado) -->
            <?php if ($contrato['estado'] === 'aceptado' && !empty($contrato['firma_inquilino'])): ?>
                <div class="contrato_firma">
                    <span class="material-symbols-outlined">draw</span>
                    <div>
                        <strong>Firmado por <?= e($contrato['firma_inquilino']) ?></strong>
                        <span class="u_text_muted">el <?= e($fmt($contrato['fecha_aceptacion'])) ?></span>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Acciones según rol y estado -->
            <div class="contrato_acciones">
                <?php if ($esDueno && $contrato['estado'] === 'borrador'): ?>
                    <form action="/contrato/<?= e($contrato['contrato_id']) ?>/rechazar" method="POST">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn_outline">Rechazar</button>
                    </form>
                    <form action="/contrato/<?= e($contrato['contrato_id']) ?>/aprobar" method="POST">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn_primary">Aprobar solicitud</button>
                    </form>
                <?php elseif ($esDueno && $contrato['estado'] === 'enviado'): ?>
                    <p class="u_text_muted">Solicitud aprobada. Esperando la firma del inquilino.</p>
                <?php elseif (!$esDueno && $contrato['estado'] === 'borrador'): ?>
                    <p class="u_text_muted">Tu solicitud está pendiente de aprobación del propietario.</p>
                <?php elseif (!$esDueno && $contrato['estado'] === 'enviado'): ?>
                    <p class="u_text_muted">El propietario aprobó tu solicitud. Ya puedes firmar el contrato.</p>
                    <a href="/contrato/<?= e($contrato['contrato_id']) ?>/firmar" class="btn_primary">Firmar contrato</a>
                <?php elseif ($contrato['estado'] === 'rechazado'): ?>
                    <p class="u_text_muted">Esta solicitud fue rechazada.</p>
                <?php endif; ?>
            </div>
        </div>
    </main>

<?php
$showFooter = false;
require __DIR__ . '/layouts/footer.php';
?>
