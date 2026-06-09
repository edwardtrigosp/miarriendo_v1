<?php
$title  = 'Mi Panel | miarriendo.online';
$styles = ['arriendos.css', 'panel.css', 'contrato.css'];
require __DIR__ . '/layouts/header.php';

$nombre      = $nombre ?? '';
$ver         = $ver ?? 'resumen';
$propiedades = $propiedades ?? [];
$arriendos   = $arriendos ?? [];
$solicitudesRecibidas = $solicitudesRecibidas ?? [];
$solicitudesEnviadas  = $solicitudesEnviadas ?? [];
$ingresoMensual   = $ingresoMensual   ?? 0;
$arriendosActivos = $arriendosActivos ?? 0;
$atencion         = $atencion  ?? [];
$actividad        = $actividad ?? [];
$resumenArriendo  = $resumenArriendo ?? null;
$resumenSolicitudes = $resumenSolicitudes ?? ['pendientes' => 0, 'por_firmar' => 0, 'firmadas' => 0, 'rechazadas' => 0];
$dinero = static fn($n) => '$' . number_format((float) $n, 0, ',', '.');

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
$totalSolic = count($solicitudesRecibidas) + count($solicitudesEnviadas);

// El "resumen" muestra el dashboard; cada sección grande solo aparece en su
// vista dedicada (con su propio título de encabezado).
$esResumen = $ver === 'resumen';
$muestra   = static fn(string $sec): bool => $ver === $sec;

$titulos = [
    'mis-propiedades'       => ['Mis propiedades', 'Las propiedades que tienes publicadas en arriendo.'],
    'solicitudes-recibidas' => ['Firmar contratos', 'Revisa las solicitudes a tus propiedades y gestiona la firma del contrato.'],
    'mis-solicitudes'       => ['Mis solicitudes', 'Solicitudes que has enviado para arrendar.'],
    'mis-arriendos'         => ['Mis arriendos', 'Las propiedades que tienes en arriendo actualmente.'],
];
?>

    <main class="main_container panel_wrap">
        <?php if (!empty($exito)): ?>
            <p class="form_success" role="status"><?= e($exito) ?></p>
        <?php endif; ?>

        <?php if ($esResumen): ?>
        <div class="view_layout">
            <!-- Columna izquierda: lo accionable -->
            <div class="view_main">
                <header class="panel_header">
                    <h1 class="panel_greeting">Hola, <?= e($nombre) ?> 👋</h1>
                    <p class="u_text_muted">Este es el resumen de tu actividad en miarriendo.</p>
                </header>

                <!-- Panel: ARRENDAR PROPIEDADES (switch = casa) -->
                <div data-mode-panel="propiedades">
                    <?php if (!empty($atencion)): ?>
                    <section class="dash_panel" style="margin-bottom:20px;">
                        <h2>Necesita tu atención <span class="panel_badge_num"><?= count($atencion) ?></span></h2>
                        <?php foreach ($atencion as $s): ?>
                            <?php $ini = strtoupper(mb_substr($s['inquilino_nombre'] ?? '?', 0, 1)); ?>
                            <div class="req_row">
                                <div class="req_avatar"><?= e($ini) ?></div>
                                <div class="req_info">
                                    <strong><?= e($s['propiedad_titulo']) ?></strong>
                                    <span><?= e(trim($s['inquilino_nombre'] . ' ' . $s['inquilino_apellidos'])) ?> · <?= $dinero($s['monto_mensual']) ?>/mes</span>
                                </div>
                                <a href="/contrato/<?= e($s['contrato_id']) ?>" class="btn_primary btn_sm">Responder</a>
                            </div>
                        <?php endforeach; ?>
                    </section>
                    <?php endif; ?>

                    <section class="panel_section">
                        <div class="panel_section_head">
                            <h2 class="panel_section_title">Mis propiedades</h2>
                            <a href="/panel?ver=mis-propiedades" class="text_link">Ver todas</a>
                        </div>
                        <?php if (empty($propiedades)): ?>
                            <div class="empty_state">
                                <span class="material-symbols-outlined">home_work</span>
                                <p>Aún no has publicado ningún inmueble.</p>
                                <a href="/propiedades" class="btn_primary">Publicar inmueble</a>
                            </div>
                        <?php else: ?>
                            <div class="properties_grid">
                                <?php foreach ($propiedades as $p): ?>
                                    <?php require __DIR__ . '/partials/property_card.php'; ?>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </section>
                </div>

                <!-- Panel: BUSCAR ARRIENDO (switch = llave) -->
                <div data-mode-panel="arriendos" hidden>
                    <section class="panel_section">
                        <div class="panel_section_head">
                            <h2 class="panel_section_title">Mis arriendos</h2>
                            <a href="/arriendos" class="text_link">Buscar arriendos</a>
                        </div>
                        <?php if (empty($arriendos)): ?>
                            <div class="empty_state">
                                <span class="material-symbols-outlined">vpn_key</span>
                                <p>No tienes arriendos activos.</p>
                                <a href="/arriendos" class="btn_primary">Buscar arriendos</a>
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

                    <?php if (!empty($solicitudesEnviadas)): ?>
                    <section class="panel_section">
                        <h2 class="panel_section_title">Mis solicitudes</h2>
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
                </div>

                <!-- Actividad reciente (siempre visible) -->
                <section class="dash_panel" style="margin-top:20px;">
                    <h2>Actividad reciente</h2>
                    <?php if (empty($actividad)): ?>
                        <div class="dash_empty">
                            <span class="material-symbols-outlined">history</span>
                            <p>Aún no hay actividad. Empieza publicando o buscando una propiedad.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($actividad as $ev): ?>
                            <div class="act_item">
                                <span class="material-symbols-outlined"><?= e($ev['icono']) ?></span>
                                <div>
                                    <p><?= e($ev['texto']) ?></p>
                                    <?php if (!empty($ev['fecha'])): ?><time><?= e(tiempo_hace($ev['fecha'])) ?></time><?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </section>
            </div>

            <!-- Columna derecha: resumen -->
            <aside class="side_card">
                <h2>Resumen</h2>
                <ul class="side_stats">
                    <li class="side_stat"><span class="material-symbols-outlined">home_work</span> Propiedades <strong><?= count($propiedades) ?></strong></li>
                    <li class="side_stat"><span class="material-symbols-outlined">vpn_key</span> Arriendos activos <strong><?= (int) $arriendosActivos ?></strong></li>
                    <li class="side_stat"><span class="material-symbols-outlined">inbox</span> Solicitudes <strong><?= count($solicitudesRecibidas) ?></strong></li>
                    <li class="side_stat"><span class="material-symbols-outlined">payments</span> Ingreso/mes <strong class="side_money"><?= $dinero($ingresoMensual) ?></strong></li>
                </ul>
                <div class="side_actions">
                    <a href="/propiedades" class="btn_primary"><span class="material-symbols-outlined">add_home</span> Publicar propiedad</a>
                    <a href="/arriendos" class="btn_outline"><span class="material-symbols-outlined">search</span> Buscar arriendos</a>
                </div>
            </aside>
        </div>
        <?php endif; ?>

        <?php if ($ver === 'mis-propiedades'): ?>
        <?php
            // Métricas del portafolio para el panel lateral.
            $totalProp       = count($propiedades);
            $propDisponibles = count(array_filter($propiedades, static fn($p) => ($p['estado'] ?? '') === 'Disponible'));
            $propArrendadas  = $totalProp - $propDisponibles;
            $ocupacion       = $totalProp > 0 ? (int) round($propArrendadas / $totalProp * 100) : 0;
            $solicPend       = count($atencion);
        ?>
        <div class="view_layout">
            <!-- Columna izquierda: encabezado + propiedades -->
            <div class="view_main">
                <header class="panel_header">
                    <h1 class="panel_greeting"><?= e($titulos['mis-propiedades'][0]) ?></h1>
                    <p class="u_text_muted"><?= e($titulos['mis-propiedades'][1]) ?></p>
                </header>
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
            </div>

            <!-- Columna derecha: panel contextual del portafolio -->
            <aside class="side_card">
                <h2>Resumen de tu portafolio</h2>
                <div class="side_progress_bar"><div class="side_progress_fill" style="width:<?= $ocupacion ?>%"></div></div>
                <p class="side_progress_label"><?= $ocupacion ?>% ocupado · <?= $propArrendadas ?> de <?= $totalProp ?> arrendada<?= $propArrendadas === 1 ? '' : 's' ?></p>

                <ul class="side_stats">
                    <li class="side_stat"><span class="material-symbols-outlined">home_work</span> Publicadas <strong><?= $totalProp ?></strong></li>
                    <li class="side_stat"><span class="material-symbols-outlined">check_circle</span> Disponibles <strong><?= $propDisponibles ?></strong></li>
                    <li class="side_stat"><span class="material-symbols-outlined">vpn_key</span> Arrendadas <strong><?= $propArrendadas ?></strong></li>
                    <li class="side_stat"><span class="material-symbols-outlined">payments</span> Ingreso/mes <strong class="side_money"><?= $dinero($ingresoMensual) ?></strong></li>
                </ul>

                <?php if ($solicPend > 0): ?>
                    <p class="side_hint">
                        Tienes <strong><?= $solicPend ?> solicitud<?= $solicPend > 1 ? 'es' : '' ?></strong> esperando respuesta.
                        <a href="/panel?ver=solicitudes-recibidas">Firmar contratos →</a>
                    </p>
                <?php else: ?>
                    <p class="side_hint">
                        Cuando alguien quiera arrendar una propiedad, lo verás aquí.
                        <a href="/propiedades">Publicar otra →</a>
                    </p>
                <?php endif; ?>
            </aside>
        </div>
        <?php endif; ?>

        <?php if ($ver === 'solicitudes-recibidas'): ?>
        <div class="view_layout">
            <!-- Columna izquierda: encabezado + solicitudes -->
            <div class="view_main">
                <header class="panel_header">
                    <h1 class="panel_greeting"><?= e($titulos['solicitudes-recibidas'][0]) ?> <?php if ($pendientes > 0): ?><span class="panel_badge_num"><?= $pendientes ?></span><?php endif; ?></h1>
                    <p class="u_text_muted"><?= e($titulos['solicitudes-recibidas'][1]) ?></p>
                </header>
                <?php if (empty($solicitudesRecibidas)): ?>
                    <div class="empty_state">
                        <span class="material-symbols-outlined">inbox</span>
                        <p>Todavía no has recibido solicitudes de arriendo.</p>
                    </div>
                <?php else: ?>
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
                <?php endif; ?>
            </div>

            <!-- Columna derecha: pasos de la firma -->
            <aside class="side_card">
                <h2>¿Cómo funciona la firma?</h2>
                <ol class="side_steps">
                    <li class="side_step"><span class="side_step_num"></span><div class="side_step_body"><strong>El inquilino solicita</strong><span>Pide arrendar tu propiedad.</span></div></li>
                    <li class="side_step"><span class="side_step_num"></span><div class="side_step_body"><strong>Tú revisas</strong><span>Ves sus datos y el monto.</span></div></li>
                    <li class="side_step"><span class="side_step_num"></span><div class="side_step_body"><strong>Apruebas y envías</strong><span>Se genera el contrato.</span></div></li>
                    <li class="side_step"><span class="side_step_num"></span><div class="side_step_body"><strong>El inquilino firma</strong><span>Firma electrónica con su nombre.</span></div></li>
                    <li class="side_step"><span class="side_step_num"></span><div class="side_step_body"><strong>Contrato activo</strong><span>La propiedad queda arrendada.</span></div></li>
                </ol>
                <?php if ($pendientes > 0): ?>
                    <p class="side_hint">Tienes <strong><?= $pendientes ?> solicitud<?= $pendientes > 1 ? 'es' : '' ?></strong> por revisar.</p>
                <?php else: ?>
                    <p class="side_hint">No tienes solicitudes pendientes por revisar.</p>
                <?php endif; ?>
            </aside>
        </div>
        <?php endif; ?>

        <?php if ($ver === 'mis-solicitudes'): ?>
        <div class="view_layout">
            <!-- Columna izquierda: encabezado + solicitudes enviadas -->
            <div class="view_main">
                <header class="panel_header">
                    <h1 class="panel_greeting"><?= e($titulos['mis-solicitudes'][0]) ?></h1>
                    <p class="u_text_muted"><?= e($titulos['mis-solicitudes'][1]) ?></p>
                </header>
                <?php if (empty($solicitudesEnviadas)): ?>
                    <div class="empty_state">
                        <span class="material-symbols-outlined">description</span>
                        <p>No has enviado solicitudes de arriendo.</p>
                        <a href="/arriendos" class="btn_primary">Buscar propiedades</a>
                    </div>
                <?php else: ?>
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
                <?php endif; ?>
            </div>

            <!-- Columna derecha: estado de las solicitudes -->
            <aside class="side_card">
                <h2>Estado de tus solicitudes</h2>
                <ul class="side_stats">
                    <li class="side_stat"><span class="material-symbols-outlined">schedule</span> Pendientes <strong><?= (int) $resumenSolicitudes['pendientes'] ?></strong></li>
                    <li class="side_stat"><span class="material-symbols-outlined">mark_email_read</span> Por firmar <strong><?= (int) $resumenSolicitudes['por_firmar'] ?></strong></li>
                    <li class="side_stat"><span class="material-symbols-outlined">check_circle</span> Firmadas <strong><?= (int) $resumenSolicitudes['firmadas'] ?></strong></li>
                    <li class="side_stat"><span class="material-symbols-outlined">cancel</span> Rechazadas <strong><?= (int) $resumenSolicitudes['rechazadas'] ?></strong></li>
                </ul>
                <?php if ($resumenSolicitudes['por_firmar'] > 0): ?>
                    <p class="side_hint">Tienes <strong><?= (int) $resumenSolicitudes['por_firmar'] ?> contrato<?= $resumenSolicitudes['por_firmar'] > 1 ? 's' : '' ?> aprobado<?= $resumenSolicitudes['por_firmar'] > 1 ? 's' : '' ?></strong> listo para firmar. Ábrelo para completar la firma.</p>
                <?php elseif ($resumenSolicitudes['pendientes'] > 0): ?>
                    <p class="side_hint">Una o más solicitudes están <strong>esperando respuesta</strong> del propietario. Te avisaremos cuando las aprueben.</p>
                <?php else: ?>
                    <p class="side_hint">¿Buscas un nuevo hogar? <a href="/arriendos">Explora propiedades →</a></p>
                <?php endif; ?>
            </aside>
        </div>
        <?php endif; ?>

        <?php if ($ver === 'mis-arriendos'): ?>
        <div class="view_layout">
            <!-- Columna izquierda: encabezado + arriendos -->
            <div class="view_main">
                <header class="panel_header">
                    <h1 class="panel_greeting"><?= e($titulos['mis-arriendos'][0]) ?></h1>
                    <p class="u_text_muted"><?= e($titulos['mis-arriendos'][1]) ?></p>
                </header>
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
                                    <span class="u_text_muted"><?= e($a['ciudad']) ?> · Propietario: <?= e(trim($a['propietario_nombre'] . ' ' . $a['propietario_apellidos'])) ?></span>
                                </div>
                                <div class="arriendo_precio">$<?= number_format((float) $a['precio_mensual'], 0, ',', '.') ?> / mes</div>
                                <span class="arriendo_estado"><?= e($a['estado']) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Columna derecha: estado del arriendo -->
            <aside class="side_card">
                <h2>Estado de tu arriendo</h2>
                <?php if ($resumenArriendo === null): ?>
                    <p class="side_hint" style="border:0;margin:0;padding:0;">
                        Cuando firmes un contrato, aquí verás el estado de tu arriendo.
                        <a href="/arriendos">Buscar arriendos →</a>
                    </p>
                <?php else: ?>
                    <div class="side_progress_bar"><div class="side_progress_fill" style="width:<?= (int) $resumenArriendo['pct'] ?>%"></div></div>
                    <p class="side_progress_label"><?= (int) $resumenArriendo['pct'] ?>% del contrato transcurrido · faltan <?= (int) $resumenArriendo['dias_restantes'] ?> días</p>

                    <ul class="side_stats">
                        <li class="side_stat"><span class="material-symbols-outlined">vpn_key</span> Arriendos activos <strong><?= (int) $resumenArriendo['activos'] ?></strong></li>
                        <li class="side_stat"><span class="material-symbols-outlined">payments</span> Pago mensual <strong class="side_money"><?= $dinero($resumenArriendo['pago_mensual']) ?></strong></li>
                        <li class="side_stat"><span class="material-symbols-outlined">event</span> Próximo pago <strong><?= e($resumenArriendo['proximo_pago']) ?></strong></li>
                        <li class="side_stat"><span class="material-symbols-outlined">person</span> Propietario <strong><?= e($resumenArriendo['propietario']) ?></strong></li>
                    </ul>

                    <p class="side_hint">Tu contrato vence el <strong><?= e($resumenArriendo['vencimiento']) ?></strong>.</p>
                    <?php if (!empty($resumenArriendo['contrato_id'])): ?>
                        <a href="/contrato/<?= e($resumenArriendo['contrato_id']) ?>" class="btn_outline btn_sm side_card_btn">Ver contrato</a>
                    <?php endif; ?>
                <?php endif; ?>
            </aside>
        </div>
        <?php endif; ?>
    </main>

<?php
$showFooter = false;
require __DIR__ . '/layouts/footer.php';
?>
