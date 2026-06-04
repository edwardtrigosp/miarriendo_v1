<?php

/**
 * Controlador del perfil de usuario.
 */
class PerfilController
{
    public function index(): void
    {
        requiereLogin();

        $u = Usuario::buscarPorId((int) $_SESSION['usuario_id']);
        view('perfil', [
            'title'   => 'Configuración de Perfil | miarriendo.online',
            'usuario' => [
                'nombre'    => $u['nombre'] ?? '',
                'apellidos' => $u['apellidos'] ?? '',
                'email'     => $u['email'] ?? '',
                'telefono'  => $u['telefono'] ?? '',
            ],
        ]);
    }
}
