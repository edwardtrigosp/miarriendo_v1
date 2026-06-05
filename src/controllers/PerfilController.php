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
                'foto_url'  => $u['foto_url'] ?? '',
            ],
            'exito'   => flash('perfil_ok'),
            'error'   => flash('perfil_error'),
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
            $actual = Usuario::buscarPorId($id);
            view('perfil', [
                'title'   => 'Configuración de Perfil | miarriendo.online',
                'error'   => $error,
                'usuario' => [
                    'nombre'    => $nombre,
                    'apellidos' => $apellidos,
                    'email'     => $email,
                    'telefono'  => $telefono,
                    'foto_url'  => $actual['foto_url'] ?? '',
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

    /** Sube (o reemplaza) la foto de perfil del usuario. */
    public function subirFoto(): void
    {
        requiereLogin();
        $id = (int) $_SESSION['usuario_id'];

        $archivo = $_FILES['foto'] ?? null;
        if (!$archivo || $archivo['error'] !== UPLOAD_ERR_OK) {
            flash('perfil_error', 'No se recibió ninguna imagen. Inténtalo de nuevo.');
            redirect('/perfil');
        }

        // Tamaño máximo: 3 MB
        if ($archivo['size'] > 3 * 1024 * 1024) {
            flash('perfil_error', 'La imagen supera el tamaño máximo de 3 MB.');
            redirect('/perfil');
        }

        // Verifica que sea una imagen real y obtén su extensión
        $extensiones = [
            IMAGETYPE_JPEG => 'jpg',
            IMAGETYPE_PNG  => 'png',
            IMAGETYPE_WEBP => 'webp',
        ];
        $info = @getimagesize($archivo['tmp_name']);
        if ($info === false || !isset($extensiones[$info[2]])) {
            flash('perfil_error', 'Formato no válido. Usa una imagen JPG, PNG o WEBP.');
            redirect('/perfil');
        }

        $dir = BASE_PATH . '/public/uploads/usuarios';
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }

        $ext     = $extensiones[$info[2]];
        $nombre  = uniqid('user' . $id . '_', true) . '.' . $ext;
        $destino = $dir . '/' . $nombre;

        if (!move_uploaded_file($archivo['tmp_name'], $destino)) {
            flash('perfil_error', 'No se pudo guardar la imagen. Inténtalo de nuevo.');
            redirect('/perfil');
        }

        // Borra la foto anterior del disco (si existía) para no acumular basura.
        $previa = Usuario::buscarPorId($id)['foto_url'] ?? '';
        if ($previa !== '' && str_starts_with($previa, '/uploads/usuarios/')) {
            @unlink(BASE_PATH . '/public' . $previa);
        }

        $url = '/uploads/usuarios/' . $nombre;
        Usuario::actualizarFoto($id, $url);
        $_SESSION['usuario_foto'] = $url; // disponible para el avatar del sidebar

        flash('perfil_ok', 'Tu foto de perfil se actualizó correctamente.');
        redirect('/perfil');
    }
}
