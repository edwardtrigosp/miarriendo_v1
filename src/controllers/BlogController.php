<?php

/**
 * Controlador del blog (publicaciones de dudas e información de productos).
 */
class BlogController
{
    public function index(): void
    {
        // TODO (fase backend): $posts = (new Post())->todos();
        view('blog', ['title' => 'Blog | miarriendo.online']);
    }
}
