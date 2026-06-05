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

        view('panel', [
            'title'                => 'Mi Panel | miarriendo.online',
            'nombre'               => $_SESSION['usuario_nombre'] ?? '',
            'propiedades'          => $propiedades,
            'arriendos'            => $arriendos,
            'solicitudesRecibidas' => $solicitudesRecibidas,
            'solicitudesEnviadas'  => $solicitudesEnviadas,
            'exito'                => flash('panel_ok'),
        ]);
    }
}
