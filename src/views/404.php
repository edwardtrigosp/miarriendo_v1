<?php
$title   = 'Página no encontrada | 404';
$hideNav = true;
require __DIR__ . '/layouts/header.php';
?>

    <main class="error_page">
        <h1 class="error_code">404</h1>
        <h2>Parece que te has perdido</h2>
        <p>La dirección que buscas no existe o fue movida.</p>
        <a href="/" class="btn_primary">Volver al inicio</a>
    </main>

<?php
$showFooter = false;
require __DIR__ . '/layouts/footer.php';
?>
