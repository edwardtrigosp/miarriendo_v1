<?php
/**
 * Tarjeta de propiedad (estilo minimalista). Toda la tarjeta es un enlace.
 * Espera $p con: id, titulo, precio, ciudad, imagen, estado, habitaciones,
 * banos, area, distancia (opcional), solicitudes (opcional, solo en el panel).
 */
$disponible = ($p['estado'] ?? '') === 'Disponible';
$area = $p['area'] ?? null;
$areaTxt = $area !== null ? rtrim(rtrim(number_format((float) $area, 2, ',', '.'), '0'), ',') : null;
?>
<a href="/propiedad/<?= e($p['id']) ?>" class="pc">
    <div class="pc_img">
        <span class="pc_estado<?= $disponible ? '' : ' is_off' ?>"><?= $disponible ? 'Disponible' : 'Arrendada' ?></span>
        <?php if (!empty($p['solicitudes'])): ?>
            <span class="pc_solic" title="Solicitudes por firmar">
                <span class="material-symbols-outlined">draw</span> <?= (int) $p['solicitudes'] ?>
            </span>
        <?php endif; ?>
        <img src="<?= e($p['imagen']) ?>" alt="<?= e($p['titulo']) ?>" loading="lazy">
    </div>
    <div class="pc_body">
        <div class="pc_title"><?= e($p['titulo']) ?></div>
        <div class="pc_price"><strong>$<?= number_format((float) $p['precio'], 0, ',', '.') ?></strong> / mes</div>
        <div class="pc_meta">
            <?php if (($p['habitaciones'] ?? null) !== null): ?><span class="material-symbols-outlined">bed</span> <?= (int) $p['habitaciones'] ?> · <?php endif; ?>
            <?php if (($p['banos'] ?? null) !== null): ?><span class="material-symbols-outlined">bathtub</span> <?= (int) $p['banos'] ?> · <?php endif; ?>
            <?php if ($areaTxt !== null): ?><span class="material-symbols-outlined">straighten</span> <?= e($areaTxt) ?> m² · <?php endif; ?>
            <?= e($p['ciudad'] ?? '') ?>
        </div>
        <?php if (!empty($p['distancia'])): ?>
            <div class="pc_meta"><span class="material-symbols-outlined">near_me</span> A <?= e($p['distancia']) ?> de ti</div>
        <?php endif; ?>
    </div>
</a>
