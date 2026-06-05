<?php

/**
 * Controlador del panel principal (dashboard adaptativo del usuario).
 * Muestra lo que el usuario tiene como propietario y como inquilino.
 */
class PanelController
{
    public function index(): void
    {
        requiereLogin();
        $usuarioId = (int) $_SESSION['usuario_id'];

        // Como propietario: sus propiedades publicadas
        $filas = Propiedad::listarPorPropietario($usuarioId);
        $propiedades = array_map([Propiedad::class, 'formatearParaTarjeta'], $filas);

        // Como inquilino: sus arriendos
        $arriendos = Alquiler::listarPorInquilino($usuarioId);

        // Contratos: solicitudes recibidas (como dueño) y enviadas (como inquilino)
        $solicitudesRecibidas = Contrato::listarPorPropietario($usuarioId);
        $solicitudesEnviadas  = Contrato::listarPorInquilino($usuarioId);

        // Cuántas solicitudes pendientes (por firmar) tiene cada propiedad,
        // para mostrar el indicador visual en su tarjeta.
        $solicitudesPorPropiedad = [];
        foreach ($solicitudesRecibidas as $s) {
            if ($s['estado'] === 'borrador') {
                $pid = (int) $s['propiedad_id'];
                $solicitudesPorPropiedad[$pid] = ($solicitudesPorPropiedad[$pid] ?? 0) + 1;
            }
        }
        foreach ($propiedades as &$p) {
            $p['solicitudes'] = $solicitudesPorPropiedad[(int) $p['id']] ?? 0;
        }
        unset($p);

        // Vista activa: 'resumen' (Inicio) o una sección concreta.
        $vistasValidas = ['resumen', 'mis-propiedades', 'solicitudes-recibidas', 'mis-solicitudes', 'mis-arriendos'];
        $ver = $_GET['ver'] ?? 'resumen';
        if (!in_array($ver, $vistasValidas, true)) {
            $ver = 'resumen';
        }

        // --- Métricas del dashboard (solo se usan en el resumen) ---
        // Ingreso mensual: suma de contratos firmados (activos) como propietario.
        $ingresoMensual = 0.0;
        foreach ($solicitudesRecibidas as $s) {
            if ($s['estado'] === 'aceptado') {
                $ingresoMensual += (float) $s['monto_mensual'];
            }
        }
        $arriendosActivos = count(array_filter($arriendos, static fn($a) => $a['estado'] === 'activo'));

        // "Necesita tu atención": solicitudes pendientes de responder (como propietario).
        $atencion = array_values(array_filter(
            $solicitudesRecibidas,
            static fn($s) => $s['estado'] === 'borrador'
        ));

        // Actividad reciente: se deriva del estado y fecha de los contratos.
        $actividad = $this->construirActividad($solicitudesRecibidas, $solicitudesEnviadas);

        // Resumen del arriendo activo (para el panel lateral de "Mis arriendos").
        $resumenArriendo = $this->resumenArriendo($arriendos);

        // Conteo por estado de las solicitudes enviadas (panel de "Mis solicitudes").
        $cuenta = static fn(string $estado) => count(array_filter(
            $solicitudesEnviadas,
            static fn($s) => $s['estado'] === $estado
        ));
        $resumenSolicitudes = [
            'pendientes' => $cuenta('borrador'),
            'por_firmar' => $cuenta('enviado'),
            'firmadas'   => $cuenta('aceptado'),
            'rechazadas' => $cuenta('rechazado'),
        ];

        view('panel', [
            'title'                => 'Mi Panel | miarriendo.online',
            'nombre'               => $_SESSION['usuario_nombre'] ?? '',
            'ver'                  => $ver,
            'propiedades'          => $propiedades,
            'arriendos'            => $arriendos,
            'solicitudesRecibidas' => $solicitudesRecibidas,
            'solicitudesEnviadas'  => $solicitudesEnviadas,
            'ingresoMensual'       => $ingresoMensual,
            'arriendosActivos'     => $arriendosActivos,
            'atencion'             => $atencion,
            'actividad'            => $actividad,
            'resumenArriendo'      => $resumenArriendo,
            'resumenSolicitudes'   => $resumenSolicitudes,
            'exito'                => flash('panel_ok'),
        ]);
    }

    /**
     * Construye el feed de "actividad reciente" a partir de los contratos
     * (sin tablas extra): cada contrato genera un evento según su estado.
     *
     * @return array<int, array{icono:string, texto:string, fecha:string}>
     */
    private function construirActividad(array $recibidas, array $enviadas): array
    {
        $eventos = [];

        // Como propietario (solicitudes recibidas).
        $mapaDueno = [
            'borrador'  => ['inbox',           'solicitó arrendar'],
            'enviado'   => ['mark_email_read', 'fue aprobado para'],
            'aceptado'  => ['check_circle',    'firmó el contrato de'],
            'rechazado' => ['cancel',          'fue rechazado para'],
            'anulado'   => ['cancel',          'anuló el contrato de'],
        ];
        foreach ($recibidas as $s) {
            [$icono, $verbo] = $mapaDueno[$s['estado']] ?? ['history', 'actualizó'];
            $quien = trim(($s['inquilino_nombre'] ?? '') . ' ' . ($s['inquilino_apellidos'] ?? ''));
            $eventos[] = [
                'icono' => $icono,
                'texto' => trim($quien) . ' ' . $verbo . ' «' . ($s['propiedad_titulo'] ?? '') . '»',
                'fecha' => $s['created_at'] ?? '',
            ];
        }

        // Como inquilino (solicitudes enviadas).
        $mapaInq = [
            'borrador'  => ['send',            'Enviaste una solicitud para'],
            'enviado'   => ['mark_email_read', 'Tu solicitud fue aprobada para'],
            'aceptado'  => ['check_circle',    'Firmaste el contrato de'],
            'rechazado' => ['cancel',          'Tu solicitud fue rechazada para'],
            'anulado'   => ['cancel',          'Se anuló tu contrato de'],
        ];
        foreach ($enviadas as $s) {
            [$icono, $frase] = $mapaInq[$s['estado']] ?? ['history', 'Actualizaste'];
            $eventos[] = [
                'icono' => $icono,
                'texto' => $frase . ' «' . ($s['propiedad_titulo'] ?? '') . '»',
                'fecha' => $s['created_at'] ?? '',
            ];
        }

        // Más recientes primero; máximo 6.
        usort($eventos, static fn($a, $b) => strcmp($b['fecha'], $a['fecha']));
        return array_slice($eventos, 0, 6);
    }

    /**
     * Resumen del arriendo activo del inquilino para el panel lateral:
     * avance del contrato, días restantes, próximo pago, propietario, etc.
     * Devuelve null si no hay arriendos activos.
     */
    private function resumenArriendo(array $arriendos): ?array
    {
        $activos = array_values(array_filter($arriendos, static fn($a) => $a['estado'] === 'activo'));
        if (empty($activos)) {
            return null;
        }

        $pagoMensual = array_sum(array_map(static fn($a) => (float) $a['precio_mensual'], $activos));

        // Contrato "principal": el que vence primero.
        usort($activos, static fn($a, $b) => strcmp($a['fecha_fin'], $b['fecha_fin']));
        $p = $activos[0];

        $ini = strtotime($p['fecha_inicio']);
        $fin = strtotime($p['fecha_fin']);
        $hoy = time();
        $totalDias     = max(1, (int) round(($fin - $ini) / 86400));
        $transcurridos = (int) round(($hoy - $ini) / 86400);
        $pct           = max(0, min(100, (int) round($transcurridos / $totalDias * 100)));
        $diasRestantes = max(0, (int) ceil(($fin - $hoy) / 86400));

        // Próximo pago: el mismo día del mes que la fecha de inicio, desde hoy.
        $diaPago = (int) date('j', $ini);
        $base    = new DateTime('today');
        $dia     = min($diaPago, (int) $base->format('t'));
        $prox    = DateTime::createFromFormat('Y-n-j', $base->format('Y') . '-' . $base->format('n') . '-' . $dia);
        if ($prox < $base) {
            $prox->modify('first day of next month');
            $prox->setDate((int) $prox->format('Y'), (int) $prox->format('n'), min($diaPago, (int) $prox->format('t')));
        }

        return [
            'activos'        => count($activos),
            'pago_mensual'   => $pagoMensual,
            'pct'            => $pct,
            'dias_restantes' => $diasRestantes,
            'proximo_pago'   => fecha_corta($prox->getTimestamp()),
            'vencimiento'    => fecha_corta($fin, true),
            'propietario'    => trim($p['propietario_nombre'] . ' ' . $p['propietario_apellidos']),
            'contrato_id'    => $p['contrato_id'] ?? null,
        ];
    }
}
