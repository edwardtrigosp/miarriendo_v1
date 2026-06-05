<?php
$title  = 'Mi Panel | miarriendo.online';
$styles = ['arriendos.css', 'panel.css', 'contrato.css'];
require __DIR__ . '/layouts/header.php';

$nombre      = $nombre ?? '';
$propiedades = $propiedades ?? [];
$arriendos   = $arriendos ?? [];
$solicitudesRecibidas = $solicitudesRecibidas ?? [];
$solicitudesEnviadas  = $solicitudesEnviadas ?? [];

// Etiqueta + clase de color por estado del contrato
$estadoContrato = static function (string $e): array {
    return [
        'borrador'  => ['Pendiente', 'is_pendiente'],
        'enviado'   => ['Aprobado', 'is_aprobado'],
        'aceptado'  => ['Firmado', 'is_aceptado'],
        'rechazado' => ['Rechazado', 'is_rechazado'],
        'anulado'   => ['Anulado', 'is_rechazado'],
    ][$e] ?? [$e, ''];
};
$pendientes = count(array_filter($solicitudesRecibidas, static fn($s) => $s['estado'] === 'borrador'));
?>

    <main class="main_container">
        <header class="panel_header">
            <h1 class="panel_greeting">Hola, <?= e($nombre) ?> 👋</h1>
            <p class="u_text_muted">Este es tu panel. Gestiona tus propiedades y tus arriendos.</p>
        </header>

        <!-- Resumen -->
        <div class="panel_stats">
            <div class="stat_card">
                <span class="stat_icon material-symbols-outlined">home_work</span>
                <div class="stat_data">
                    <strong><?= count($propiedades) ?></strong>
                    <span>Propiedades publicadas</span>
                </div>
            </div>
            <div class="stat_card">
                <span class="stat_icon material-symbols-outlined">vpn_key</span>
                <div class="stat_data">
                    <strong><?= count($arriendos) ?></strong>
                    <span>Arriendos</span>
                </div>
            </div>
            <a href="/propiedades" class="stat_card stat_card_action">
                <span class="stat_icon material-symbols-outlined">add_home</span>
                <span>Publicar inmueble</span>
            </a>
            <a href="/perfil" class="stat_card stat_card_action">
                <span class="stat_icon material-symbols-outlined">manage_accounts</span>
                <span>Configurar perfil</span>
            </a>
        </div>

        <!-- Mis propiedades (rol propietario) -->
        <section class="panel_section">
            <div class="panel_section_head">
                <h2 class="panel_section_title">Mis propiedades</h2>
                <a href="/propiedades" class="btn_outline btn_sm">Publicar nueva</a>
            </div>

            <?php if (empty($propiedades)): ?>
                <div class="empty_state">
                    <span class="material-symbols-outlined">home_work</span>
                    <p>Aún no has publicado ningún inmueble.</p>
                    <a href="/propiedades" class="btn_primary">Publicar mi primer inmueble</a>
                </div>
            <?php else: ?>
                <div class="properties_grid">
                    <?php foreach ($propiedades as $p): ?>
                        <?php require __DIR__ . '/partials/property_card.php'; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <!-- Solicitudes recibidas (rol propietario) -->
        <?php if (!empty($solicitudesRecibidas)): ?>
        <section class="panel_section">
            <div class="panel_section_head">
                <h2 class="panel_section_title">Solicitudes recibidas <?php if ($pendientes > 0): ?><span class="panel_badge_num"><?= $pendientes ?></span><?php endif; ?></h2>
            </div>
            <div class="solicitud_list">
                <?php foreach ($solicitudesRecibidas as $s): [$et, $ec] = $estadoContrato($s['estado']); ?>
                    <a href="/contrato/<?= e($s['contrato_id']) ?>" class="solicitud_row">
                        <div class="solicitud_info">
                            <strong><?= e($s['propiedad_titulo']) ?></strong>
                            <span class="u_text_muted">Solicita <?= e(trim($s['inquilino_nombre'] . ' ' . $s['inquilino_apellidos'])) ?></span>
                        </div>
                        <div class="solicitud_precio">$<?= number_format((float) $s['monto_mensual'], 0, ',', '.') ?> / mes</div>
                        <span class="contrato_estado_badge <?= e($ec) ?>"><?= e($et) ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>

        <!-- Mis solicitudes (rol inquilino) -->
        <?php if (!empty($solicitudesEnviadas)): ?>
        <section class="panel_section">
            <div class="panel_section_head">
                <h2 class="panel_section_title">Mis solicitudes</h2>
            </div>
            <div class="solicitud_list">
                <?php foreach ($solicitudesEnviadas as $s): [$et, $ec] = $estadoContrato($s['estado']); ?>
                    <a href="/contrato/<?= e($s['contrato_id']) ?>" class="solicitud_row">
                        <div class="solicitud_info">
                            <strong><?= e($s['propiedad_titulo']) ?></strong>
                            <span class="u_text_muted">Propietario: <?= e(trim($s['propietario_nombre'] . ' ' . $s['propietario_apellidos'])) ?></span>
                        </div>
                        <div class="solicitud_precio">$<?= number_format((float) $s['monto_mensual'], 0, ',', '.') ?> / mes</div>
                        <span class="contrato_estado_badge <?= e($ec) ?>"><?= e($et) ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>

        <!-- Mis arriendos (rol inquilino) -->
        <section class="panel_section">
            <div class="panel_section_head">
                <h2 class="panel_section_title">Mis arriendos</h2>
                <a href="/arriendos" class="btn_outline btn_sm">Arriendos</a>
            </div>

            <?php if (empty($arriendos)): ?>
                <div class="empty_state">
                    <span class="material-symbols-outlined">vpn_key</span>
                    <p>No tienes arriendos activos.</p>
                    <a href="/arriendos" class="btn_primary">Ver arriendos</a>
                </div>
            <?php else: ?>
                <div class="arriendo_list">
                    <?php foreach ($arriendos as $a): ?>
                        <div class="arriendo_row">
                            <div>
                                <strong><?= e($a['titulo']) ?></strong>
                                <span class="u_text_muted"><?= e($a['ciudad']) ?></span>
                            </div>
                            <div class="arriendo_precio">$<?= number_format((float) $a['precio_mensual'], 0, ',', '.') ?> / mes</div>
                            <span class="arriendo_estado"><?= e($a['estado']) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>

<?php
$showFooter = false;
require __DIR__ . '/layouts/footer.php';
?>
