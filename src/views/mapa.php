<?php
$title  = '¿Cómo llegar? | miarriendo.online';
$styles = ['mapa.css'];
require __DIR__ . '/layouts/header.php';

$destino = $destino ?? null;
$destinoTexto = $destino['texto'] ?? 'Calle 127 # 45 - 10, Bogotá';
?>

    <!-- Leaflet (OpenStreetMap) — CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">

    <div class="ruta_layout">

        <!-- Panel izquierdo (estilo tarjeta) -->
        <aside class="ruta_panel">
            <div class="ruta_card">
                <h1 class="ruta_title">¿Cómo llegar?</h1>
                <p class="ruta_subtitle">Calcula tu trayecto hasta la propiedad.</p>

                <form id="ruta_form">
                    <div class="ruta_origin_group">
                        <div class="ruta_field">
                            <span class="ruta_icon ruta_icon_origin" aria-hidden="true"></span>
                            <input type="text" id="origen" class="ruta_input" placeholder="Punto de partida" autocomplete="off">
                        </div>

                        <!-- Menú de autocompletado (aparece al enfocar el campo) -->
                        <div class="ruta_dropdown" id="origen_dropdown" hidden>
                            <button type="button" class="ruta_option" id="opt_gps">
                                <span class="ruta_option_icon"><span class="material-symbols-outlined">my_location</span></span>
                                <span class="ruta_option_text">
                                    <strong>Permitir acceso a ubicación</strong>
                                    <small>Usa tu dirección de partida actual</small>
                                </span>
                            </button>
                            <button type="button" class="ruta_option" id="opt_map">
                                <span class="ruta_option_icon"><span class="material-symbols-outlined">pin_drop</span></span>
                                <span class="ruta_option_text">
                                    <strong>Fijar ubicación en el mapa</strong>
                                    <small>Haz clic en el mapa para elegir el punto</small>
                                </span>
                            </button>
                            <ul class="ruta_suggestions" id="origen_suggestions"></ul>
                        </div>
                    </div>

                    <div class="ruta_field">
                        <span class="ruta_icon ruta_icon_dest" aria-hidden="true"></span>
                        <input type="text" id="destino" class="ruta_input" placeholder="Destino" value="<?= e($destinoTexto) ?>"<?= $destino ? ' readonly' : '' ?>>
                    </div>

                    <div class="ruta_field">
                        <span class="ruta_icon material-symbols-outlined" aria-hidden="true">directions_car</span>
                        <select id="modo_viaje" class="ruta_input">
                            <option value="DRIVING">Conduciendo</option>
                            <option value="WALKING">A pie</option>
                            <option value="TRANSIT">Transporte público</option>
                            <option value="BICYCLING">En bicicleta</option>
                        </select>
                    </div>

                    <button type="submit" class="btn_primary u_full_width ruta_submit">Calcular ruta</button>
                </form>

                <div class="ruta_resultado" id="ruta_resultado" hidden>
                    <div class="ruta_resultado_row">
                        <span>Tiempo estimado</span>
                        <strong id="ruta_tiempo">—</strong>
                    </div>
                    <div class="ruta_resultado_row">
                        <span>Distancia</span>
                        <strong id="ruta_distancia">—</strong>
                    </div>

                    <div class="ruta_apps">
                        <a id="btn_waze" class="ruta_app_btn" target="_blank" rel="noopener" href="#">
                            <span class="material-symbols-outlined icon_sm">navigation</span> Navegar con Waze
                        </a>
                        <a id="btn_gmaps" class="ruta_app_btn" target="_blank" rel="noopener" href="#">
                            <span class="material-symbols-outlined icon_sm">map</span> Abrir en Google Maps
                        </a>
                    </div>
                </div>

                <p class="ruta_estado" id="ruta_estado" role="status"></p>

                <a href="/arriendos" class="ruta_volver"><span class="material-symbols-outlined icon_sm">arrow_back</span> Volver a explorar</a>
            </div>
        </aside>

        <!-- Mapa -->
        <div id="map_canvas" class="ruta_map"></div>

    </div>

<?php $showFooter = false; ?>
    <!-- Leaflet (OpenStreetMap) — JS, antes de nuestro script -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <?php if ($destino): ?>
    <script>
        window.RUTA_DESTINO = <?= json_encode([
            'texto' => $destino['texto'],
            'lat'   => $destino['lat'],
            'lon'   => $destino['lon'],
            'titulo'=> $destino['titulo'],
        ], JSON_UNESCAPED_UNICODE) ?>;
    </script>
    <?php endif; ?>
    <script src="/js/maps_api.js"></script>
<?php require __DIR__ . '/layouts/footer.php'; ?>
