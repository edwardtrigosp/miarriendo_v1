<?php
/**
 * Tarjeta de propiedad reutilizable.
 * Espera la variable $p con: id, titulo, precio, direccion, imagen, estado.
 */
?>
<article class="property_card">
    <div class="property_image_container">
        <span class="property_badge"><?= e($p['estado'] ?? 'Disponible') ?></span>
        <?php if (!empty($p['solicitudes'])): ?>
            <span class="property_badge_solicitudes" title="Solicitudes por firmar">
                <span class="material-symbols-outlined icon_sm">draw</span>
                <?= (int) $p['solicitudes'] ?> solicitud<?= $p['solicitudes'] > 1 ? 'es' : '' ?>
            </span>
        <?php endif; ?>
        <img src="<?= e($p['imagen']) ?>" alt="<?= e($p['titulo']) ?>">
    </div>
    <div class="property_content">
        <div class="property_price">$<?= number_format((float) $p['precio'], 0, ',', '.') ?> / mes</div>
        <h3 class="property_title"><?= e($p['titulo']) ?></h3>
        <div class="property_location"><span class="material-symbols-outlined icon_sm">location_on</span> <?= e($p['direccion']) ?></div>
        <?php if (!empty($p['distancia'])): ?>
            <div class="property_distancia"><span class="material-symbols-outlined icon_sm">near_me</span> A <?= e($p['distancia']) ?> de ti</div>
        <?php endif; ?>
        <div class="property_footer">
            <a href="/propiedad/<?= e($p['id']) ?>" class="btn_primary u_full_width">Ver propiedad</a>
            <?php if (!empty($p['solicitudes'])): ?>
                <a href="/panel?ver=solicitudes-recibidas" class="property_solicitudes_link">
                    <span class="material-symbols-outlined icon_sm">draw</span>
                    Firmar <?= (int) $p['solicitudes'] ?> contrato<?= $p['solicitudes'] > 1 ? 's' : '' ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
</article>
