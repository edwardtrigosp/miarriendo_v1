<?php
/**
 * Página de error genérica (500, 419, 403, etc.).
 * No depende de la base de datos: es segura aunque algo haya fallado.
 */
$codigo  = $codigo  ?? '500';
$mensaje = $mensaje ?? 'Ocurrió un error inesperado.';
$title   = $title   ?? 'Error | miarriendo.online';
$hideNav = true;
require __DIR__ . '/layouts/header.php';
?>

    <main class="error_page">
        <h1 class="error_code"><?= e($codigo) ?></h1>
        <h2>Algo salió mal</h2>
        <p><?= e($mensaje) ?></p>
        <a href="/" class="btn_primary">Volver al inicio</a>
    </main>

<?php
$showFooter = false;
require __DIR__ . '/layouts/footer.php';
?>
