<?php

/**
 * Laboratorio de diseño: prototipos con Material Web Components (Material 3).
 *
 * Rutas /lab/* — SOLO para evaluar el look de Material 3 con la marca morada.
 * No tocan las vistas reales (login/registro/panel) que están en producción.
 */
class LabM3Controller
{
    public function login(): void
    {
        view('lab/m3_login', ['title' => 'M3 · Login | miarriendo']);
    }

    public function registro(): void
    {
        view('lab/m3_registro', ['title' => 'M3 · Registro | miarriendo']);
    }

    public function panel(): void
    {
        view('lab/m3_panel', ['title' => 'M3 · Panel | miarriendo']);
    }
}
