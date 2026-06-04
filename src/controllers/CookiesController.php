<?php

/**
 * Controlador de la página de política de cookies.
 */
class CookiesController
{
    public function index(): void
    {
        view('cookies', ['title' => 'Política de Cookies | miarriendo.online']);
    }
}
