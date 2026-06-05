<?php
/**
 * Cabecera común: <head> + barra de navegación.
 *
 * Variables esperadas (opcionales):
 *   $title  string   Título de la pestaña.
 *   $styles string[] CSS extra a cargar (ej. ['auth.css']).
 *   $hideNav bool    Si es true, no se muestra la barra de navegación.
 */
$title  = $title  ?? 'miarriendo.online | Encuentra tu arriendo ideal';
$styles = $styles ?? [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title) ?></title>

    <!-- Iconos Material Symbols de Google (Apache 2.0, gratis) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0">

    <link rel="stylesheet" href="/css/global.css">
    <?php foreach ($styles as $css): ?>
    <link rel="stylesheet" href="/css/<?= e($css) ?>">
    <?php endforeach; ?>
</head>
<body>
<?php
$autenticado = isset($_SESSION['usuario_id']);
// "App shell": menú lateral global cuando hay sesión (y la página no oculta el nav).
$appShell = empty($hideNav) && $autenticado;
?>
<?php if ($appShell): ?>
    <div class="app_shell">
        <?php require __DIR__ . '/app_sidebar.php'; ?>
        <div class="app_main">
<?php elseif (empty($hideNav)): ?>
    <?php require __DIR__ . '/nav.php'; ?>
<?php endif; ?>
