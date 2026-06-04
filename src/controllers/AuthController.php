<?php

/**
 * Controlador de autenticación: registro, login y logout.
 */
class AuthController
{
    public function showLogin(): void
    {
        view('login', ['title' => 'Iniciar Sesión | miarriendo.online']);
    }

    public function showRegistro(): void
    {
        view('registro', ['title' => 'Crea tu cuenta | miarriendo.online']);
    }

    /** Procesa el formulario de registro. */
    public function registro(): void
    {
        $nombre     = trim($_POST['nombre'] ?? '');
        $apellidos  = trim($_POST['apellidos'] ?? '');
        $email      = trim($_POST['email'] ?? '');
        $telefono   = trim($_POST['telefono'] ?? '');
        $contrasena = $_POST['contrasena'] ?? '';

        $datos = compact('nombre', 'apellidos', 'email', 'telefono');

        // Validaciones de servidor (no confiar solo en el JS del cliente)
        $error = null;
        if ($nombre === '' || $apellidos === '' || $email === '' || $contrasena === '') {
            $error = 'Completa todos los campos obligatorios.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'El correo electrónico no es válido.';
        } elseif (mb_strlen($contrasena) < 8) {
            $error = 'La contraseña debe tener al menos 8 caracteres.';
        } elseif (Usuario::emailExiste($email)) {
            $error = 'Ya existe una cuenta con ese correo.';
        }

        if ($error !== null) {
            view('registro', [
                'title' => 'Crea tu cuenta | miarriendo.online',
                'error' => $error,
                'datos' => $datos,
            ]);
            return;
        }

        // Crear el usuario con la contraseña hasheada
        $id = Usuario::crear([
            'nombre'     => $nombre,
            'apellidos'  => $apellidos,
            'email'      => $email,
            'telefono'   => $telefono,
            'contrasena' => password_hash($contrasena, PASSWORD_DEFAULT),
        ]);

        $this->iniciarSesion($id, $nombre, 'usuario');
        redirect('/perfil');
    }

    /** Procesa el formulario de login. */
    public function login(): void
    {
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $error = null;
        if ($email === '' || $password === '') {
            $error = 'Ingresa tu correo y contraseña.';
        } else {
            $usuario = Usuario::buscarPorEmail($email);
            // Mismo mensaje para email inexistente o clave mala (no revelar cuál falló)
            if (!$usuario || !password_verify($password, $usuario['contrasena'])) {
                $error = 'Correo o contraseña incorrectos.';
            } elseif ((int) $usuario['activo'] !== 1) {
                $error = 'Tu cuenta está desactivada.';
            }
        }

        if ($error !== null) {
            view('login', [
                'title' => 'Iniciar Sesión | miarriendo.online',
                'error' => $error,
            ]);
            return;
        }

        Usuario::actualizarUltimoAcceso((int) $usuario['usuario_id']);
        $this->iniciarSesion((int) $usuario['usuario_id'], $usuario['nombre'], $usuario['rol']);
        redirect('/perfil');
    }

    /** Cierra la sesión. */
    public function logout(): void
    {
        $_SESSION = [];
        session_destroy();
        redirect('/');
    }

    /** Inicia la sesión del usuario de forma segura. */
    private function iniciarSesion(int $id, string $nombre, string $rol): void
    {
        session_regenerate_id(true); // evita fijación de sesión
        $_SESSION['usuario_id']     = $id;
        $_SESSION['usuario_nombre'] = $nombre;
        $_SESSION['usuario_rol']    = $rol;
    }
}
