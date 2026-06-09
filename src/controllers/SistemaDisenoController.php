<?php

/**
 * Sistema de diseño (styleguide vivo).
 *
 * Renderiza, con el CSS real del proyecto, todos los componentes que hemos
 * construido: tokens de color, tipografía, botones, formularios, badges,
 * tarjetas, listas, navegación y el modal de confirmación.
 *
 * Es una página de referencia interna: siempre refleja el diseño actual.
 */
class SistemaDisenoController
{
    public function index(): void
    {
        view('sistema_diseno', [
            'title'  => 'Sistema de diseño | miarriendo.online',
            'styles' => ['sistema.css', 'panel.css', 'arriendos.css'],
        ]);
    }
}
