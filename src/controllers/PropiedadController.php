<?php

/**
 * Controlador de propiedades (listar, publicar, ver ruta).
 *
 * Por ahora las vistas usan datos de ejemplo. La consulta a la DB
 * se implementa en la fase de backend.
 */
class PropiedadController
{
    /** Listado de arriendos. */
    public function index(): void
    {
        // TODO (fase backend): $propiedades = (new Propiedad())->todas();
        view('arriendos', ['title' => 'Explorar Arriendos | miarriendo.online']);
    }

    /** Formulario para publicar un inmueble. */
    public function create(): void
    {
        view('propiedades', ['title' => 'Publicar Propiedad | miarriendo.online']);
    }

    /** Vista del mapa con la ruta hacia una propiedad. */
    public function ruta(): void
    {
        // El mapa usa OpenStreetMap (Leaflet), no requiere API key.
        view('mapa', ['title' => '¿Cómo llegar? | miarriendo.online']);
    }
}
