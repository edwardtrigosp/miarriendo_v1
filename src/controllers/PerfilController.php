<?php

/**
 * Controlador del perfil de usuario.
 */
class PerfilController
{
    public function index(): void
    {
        // TODO (fase backend): cargar datos reales del usuario en sesión.
        view('perfil', ['title' => 'Configuración de Perfil | miarriendo.online']);
    }
}
