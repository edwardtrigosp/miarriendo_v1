<?php
$title  = 'Explorar Arriendos | miarriendo.online';
$styles = ['arriendos.css'];
require __DIR__ . '/layouts/header.php';

// Datos de ejemplo mientras no haya base de datos.
// Cuando el controlador pase $propiedades desde la DB, se usará esa lista.
$propiedades = $propiedades ?? [
    [
        'id'        => 1,
        'titulo'    => 'Casa Campestre Norte',
        'precio'    => 2500000,
        'direccion' => 'Calle 127 # 45 - 10',
        'imagen'    => 'https://images.unsplash.com/photo-1570129477492-45c003edd2be?auto=format&fit=crop&w=500&q=80',
        'estado'    => 'Disponible',
    ],
];
?>

    <main class="main_container">
        <header class="section_header">
            <h1 class="page_title">Propiedades en Bogotá</h1>
            <p class="u_text_muted">Encontramos <?= count($propiedades) ?> propiedades disponibles para ti.</p>
        </header>

        <div class="properties_grid">
            <?php foreach ($propiedades as $p): ?>
            <article class="property_card">
                <div class="property_image_container">
                    <span class="property_badge"><?= e($p['estado'] ?? 'Disponible') ?></span>
                    <img src="<?= e($p['imagen']) ?>" alt="<?= e($p['titulo']) ?>">
                </div>
                <div class="property_content">
                    <div class="property_price">$<?= number_format($p['precio'], 0, ',', '.') ?> / mes</div>
                    <h3 class="property_title"><?= e($p['titulo']) ?></h3>
                    <div class="property_location"><span class="material-symbols-outlined icon_sm">location_on</span> <?= e($p['direccion']) ?></div>
                    <div class="property_footer">
                        <a href="/ruta?id=<?= e($p['id']) ?>" class="btn_primary u_full_width">¿Cómo llegar?</a>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </main>

<?php
$showFooter = false;
require __DIR__ . '/layouts/footer.php';
?>
