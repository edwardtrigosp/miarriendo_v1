<?php
$title  = 'Explorar Arriendos | miarriendo.online';
$styles = ['arriendos.css'];
require __DIR__ . '/layouts/header.php';

$propiedades = $propiedades ?? [];
?>

    <main class="main_container">
        <header class="section_header">
            <h1 class="page_title">Propiedades disponibles</h1>
            <p class="u_text_muted">Encontramos <?= count($propiedades) ?> propiedades disponibles para ti.</p>
        </header>

        <?php if (empty($propiedades)): ?>
            <div class="empty_state">
                <span class="material-symbols-outlined icon_xl">search_off</span>
                <p>Aún no hay propiedades publicadas.</p>
                <a href="/propiedades" class="btn_primary">Publica la primera</a>
            </div>
        <?php else: ?>
            <div class="properties_grid">
                <?php foreach ($propiedades as $p): ?>
                    <?php require __DIR__ . '/partials/property_card.php'; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

<?php
$showFooter = false;
require __DIR__ . '/layouts/footer.php';
?>
