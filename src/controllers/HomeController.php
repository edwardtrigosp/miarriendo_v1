<?php

/**
 * Controlador de la página de inicio.
 */
class HomeController
{
    public function index(): void
    {
        // Si el usuario ya inició sesión, la landing no aplica: va a su panel.
        if (isset($_SESSION['usuario_id'])) {
            header('Location: /panel');
            exit;
        }

        view('home');
    }
}
