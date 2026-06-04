<?php
/**
 * Tarjeta de propiedad reutilizable.
 * Espera la variable $p con: id, titulo, precio, direccion, imagen, estado.
 */
?>
<article class="property_card">
    <div class="property_image_container">
        <span class="property_badge"><?= e($p['estado'] ?? 'Disponible') ?></span>
        <img src="<?= e($p['imagen']) ?>" alt="<?= e($p['titulo']) ?>">
    </div>
    <div class="property_content">
        <div class="property_price">$<?= number_format((float) $p['precio'], 0, ',', '.') ?> / mes</div>
        <h3 class="property_title"><?= e($p['titulo']) ?></h3>
        <div class="property_location"><span class="material-symbols-outlined icon_sm">location_on</span> <?= e($p['direccion']) ?></div>
        <div class="property_footer">
            <a href="/ruta?id=<?= e($p['id']) ?>" class="btn_primary u_full_width">¿Cómo llegar?</a>
        </div>
    </div>
</article>
