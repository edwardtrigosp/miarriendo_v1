<?php
$title  = ($propiedad['titulo'] ?? 'Propiedad') . ' | miarriendo.online';
$styles = ['propiedad.css'];
require __DIR__ . '/layouts/header.php';

$tieneCoords = isset($propiedad['latitud'], $propiedad['longitud'])
    && $propiedad['latitud'] !== null && $propiedad['longitud'] !== null;
?>
<?php if ($tieneCoords): ?>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
<?php endif; ?>
<?php

$imagenes = $imagenes ?? [];
$placeholder = 'https://images.unsplash.com/photo-1568605114967-8130f3a36994?auto=format&fit=crop&w=900&q=80';
$principal = !empty($imagenes) ? $imagenes[0]['url_imagen'] : $placeholder;

// Ayuda para mostrar características opcionales
$car = [];
if (!empty($propiedad['num_habitaciones'])) { $car[] = ['bed', $propiedad['num_habitaciones'] . ' habitaciones']; }
if (!empty($propiedad['num_banos']))        { $car[] = ['bathtub', $propiedad['num_banos'] . ' baños']; }
if (!empty($propiedad['area_m2']))          { $car[] = ['straighten', rtrim(rtrim(number_format((float) $propiedad['area_m2'], 2, '.', ''), '0'), '.') . ' m²']; }
$car[] = ['chair', !empty($propiedad['amueblado']) ? 'Amueblado' : 'Sin amoblar'];
$car[] = ['pets', !empty($propiedad['mascotas_permitidas']) ? 'Acepta mascotas' : 'Sin mascotas'];

$direccion = trim($propiedad['calle'] . ' ' . ($propiedad['numero_exterior'] ?? ''));
$ubicacion = $direccion
    . (!empty($propiedad['barrio']) ? ', ' . $propiedad['barrio'] : '')
    . ', ' . $propiedad['ciudad'] . ', ' . $propiedad['departamento'];
?>

    <main class="main_container">
        <a href="/arriendos" class="detalle_back"><span class="material-symbols-outlined icon_sm">arrow_back</span> Volver a arriendos</a>

        <div class="detalle_layout">

            <!-- Galería + contenido -->
            <div class="detalle_main">
                <div class="detalle_galeria">
                    <img src="<?= e($principal) ?>" alt="<?= e($propiedad['titulo']) ?>" id="img_principal" class="detalle_img">
                    <?php if (count($imagenes) > 1): ?>
                        <div class="detalle_thumbs">
                            <?php foreach ($imagenes as $img): ?>
                                <img src="<?= e($img['url_imagen']) ?>" alt="Foto" class="detalle_thumb" data-full="<?= e($img['url_imagen']) ?>">
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <section class="detalle_bloque">
                    <h2 class="detalle_subtitulo">Características</h2>
                    <div class="detalle_caracteristicas">
                        <?php foreach ($car as $c): ?>
                            <div class="caracteristica">
                                <span class="material-symbols-outlined"><?= e($c[0]) ?></span>
                                <span><?= e($c[1]) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>

                <?php if (!empty($propiedad['descripcion'])): ?>
                <section class="detalle_bloque">
                    <h2 class="detalle_subtitulo">Descripción</h2>
                    <p class="detalle_descripcion"><?= nl2br(e($propiedad['descripcion'])) ?></p>
                </section>
                <?php endif; ?>

                <section class="detalle_bloque">
                    <h2 class="detalle_subtitulo">Ubicación</h2>
                    <p class="detalle_descripcion"><span class="material-symbols-outlined icon_sm">location_on</span> <?= e($ubicacion) ?></p>
                    <?php if (!empty($propiedad['referencia'])): ?>
                        <p class="u_text_muted">Referencia: <?= e($propiedad['referencia']) ?></p>
                    <?php endif; ?>
                    <?php if ($tieneCoords): ?>
                        <div id="detalle_mapa" class="detalle_mapa"></div>
                    <?php endif; ?>
                </section>
            </div>

            <!-- Panel lateral (precio + dueño + acciones) -->
            <aside class="detalle_sidebar">
                <div class="detalle_card">
                    <span class="detalle_badge"><?= e($propiedad['tipo_propiedad']) ?></span>
                    <div class="detalle_precio">$<?= number_format((float) $propiedad['precio_alquiler_mensual'], 0, ',', '.') ?> <span>/ mes</span></div>
                    <h1 class="detalle_titulo"><?= e($propiedad['titulo']) ?></h1>
                    <p class="detalle_ubicacion_mini"><span class="material-symbols-outlined icon_sm">location_on</span> <?= e($propiedad['ciudad']) ?>, <?= e($propiedad['departamento']) ?></p>

                    <?php if (!empty($propiedad['deposito'])): ?>
                        <p class="u_text_muted">Depósito: $<?= number_format((float) $propiedad['deposito'], 0, ',', '.') ?></p>
                    <?php endif; ?>

                    <div class="detalle_dueno">
                        <div class="dueno_avatar"><?= e(strtoupper(substr($propiedad['propietario_nombre'], 0, 1))) ?></div>
                        <div>
                            <span class="u_text_muted">Publicado por</span>
                            <strong><?= e($propiedad['propietario_nombre'] . ' ' . $propiedad['propietario_apellidos']) ?></strong>
                        </div>
                    </div>

                    <a href="/ruta?id=<?= e($propiedad['propiedad_id']) ?>" class="btn_primary u_full_width">¿Cómo llegar?</a>
                    <a href="mailto:<?= e($propiedad['propietario_email']) ?>?subject=Interesado en <?= rawurlencode($propiedad['titulo']) ?>" class="btn_outline u_full_width u_mt_sm">Contactar al propietario</a>
                </div>
            </aside>

        </div>
    </main>

<?php
$showFooter = false;
?>
    <?php if ($tieneCoords): ?>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        window.PROP_COORDS = {
            lat: <?= json_encode((float) $propiedad['latitud']) ?>,
            lon: <?= json_encode((float) $propiedad['longitud']) ?>,
            titulo: <?= json_encode($propiedad['titulo'], JSON_UNESCAPED_UNICODE) ?>
        };
    </script>
    <?php endif; ?>
    <script src="/js/propiedad.js"></script>
<?php require __DIR__ . '/layouts/footer.php'; ?>
