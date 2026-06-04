<?php

/**
 * Controlador de autenticación (login, registro, logout).
 *
 * Por ahora solo renderiza las vistas. La lógica con la base de datos
 * se implementa en la fase de backend.
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

    public function login(): void
    {
        // TODO (fase backend): validar credenciales contra la DB e iniciar sesión.
        redirect('/login');
    }

    public function registro(): void
    {
        // TODO (fase backend): crear el usuario en la DB (password_hash).
        redirect('/registro');
    }

    public function logout(): void
    {
        session_destroy();
        redirect('/');
    }
}
