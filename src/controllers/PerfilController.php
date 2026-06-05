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
            'exito'   => flash('perfil_ok'),
        ]);
    }

    /** Guarda los cambios del perfil. */
    public function actualizar(): void
    {
        requiereLogin();
        $id = (int) $_SESSION['usuario_id'];

        $nombre    = trim($_POST['nombre'] ?? '');
        $apellidos = trim($_POST['apellidos'] ?? '');
        $email     = trim($_POST['email'] ?? '');
        $telefono  = trim($_POST['telefono'] ?? '');

        $error = null;
        if ($nombre === '' || $apellidos === '' || $email === '') {
            $error = 'Completa nombre, apellidos y correo.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'El correo electrónico no es válido.';
        } elseif (Usuario::emailEnUsoPorOtro($email, $id)) {
            $error = 'Ese correo ya está en uso por otra cuenta.';
        }

        if ($error !== null) {
            view('perfil', [
                'title'   => 'Configuración de Perfil | miarriendo.online',
                'error'   => $error,
                'usuario' => [
                    'nombre'    => $nombre,
                    'apellidos' => $apellidos,
                    'email'     => $email,
                    'telefono'  => $telefono,
                ],
            ]);
            return;
        }

        Usuario::actualizar($id, [
            'nombre'    => $nombre,
            'apellidos' => $apellidos,
            'email'     => $email,
            'telefono'  => $telefono,
        ]);

        // Mantener el nombre mostrado en la sesión al día
        $_SESSION['usuario_nombre'] = $nombre;

        flash('perfil_ok', 'Tus datos se guardaron correctamente.');
        redirect('/perfil');
    }
}
