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

        view('panel', [
            'title'        => 'Mi Panel | miarriendo.online',
            'nombre'       => $_SESSION['usuario_nombre'] ?? '',
            'propiedades'  => $propiedades,
            'arriendos'    => $arriendos,
        ]);
    }
}
