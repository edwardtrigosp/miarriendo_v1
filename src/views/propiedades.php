<?php
$title  = 'Publicar Propiedad | miarriendo.online';
$styles = ['auth.css', 'wizard.css'];
require __DIR__ . '/layouts/header.php';
?>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">

    <div class="wizard_layout">

        <!-- Columna izquierda: formulario (con scroll si es necesario) -->
        <section class="wizard_main" aria-labelledby="propiedad_titulo">
            <header class="wizard_header">
                <h1 id="propiedad_titulo" class="auth_title">Publica tu inmueble</h1>
                <p class="auth_subtitle">Completa la información para que aparezca en las búsquedas y Google Maps trace la ruta.</p>
            </header>

            <?php if (!empty($error)): ?>
                <p class="form_error" role="alert"><?= e($error) ?></p>
            <?php endif; ?>

            <form action="/propiedades" method="POST" id="propiedad_form" enctype="multipart/form-data" novalidate>

                <fieldset class="form_fieldset">
                    <legend class="form_legend">Información de la propiedad</legend>
                    <div class="form_group">
                        <label for="titulo" class="form_label">Título de la publicación</label>
                        <input type="text" id="titulo" name="titulo" class="form_input" placeholder="Ej: Apto amoblado frente al parque" maxlength="200" required>
                    </div>
                    <div class="form_group">
                        <label for="tipo_propiedad" class="form_label">Tipo de propiedad</label>
                        <select id="tipo_propiedad" name="tipo_propiedad" class="form_input" required>
                            <option value="" disabled selected>Selecciona una opción…</option>
                            <option value="Apartamento">Apartamento</option>
                            <option value="Casa">Casa</option>
                            <option value="Apartaestudio">Apartaestudio</option>
                            <option value="Habitación">Habitación</option>
                            <option value="Local">Local comercial</option>
                        </select>
                    </div>
                    <div class="form_group">
                        <label for="descripcion" class="form_label">Descripción <span class="label_hint">(Opcional)</span></label>
                        <textarea id="descripcion" name="descripcion" class="form_input" placeholder="Describe la propiedad, su entorno y lo que la hace especial."></textarea>
                    </div>
                </fieldset>

                <fieldset class="form_fieldset">
                    <legend class="form_legend">Fotos</legend>
                    <div class="form_group">
                        <label class="form_label">Imágenes de la propiedad <span class="label_hint">(la primera será la portada)</span></label>
                        <label for="imagenes" class="upload_zone">
                            <span class="material-symbols-outlined icon_lg">add_photo_alternate</span>
                            <span class="upload_text">Haz clic para seleccionar fotos</span>
                            <span class="help_hint">JPG, PNG o WEBP · máx. 3 MB c/u</span>
                            <input type="file" id="imagenes" name="imagenes[]" accept="image/jpeg,image/png,image/webp,image/gif" multiple hidden>
                        </label>
                        <div class="upload_preview" id="upload_preview"></div>
                    </div>
                </fieldset>

                <fieldset class="form_fieldset">
                    <legend class="form_legend">Características</legend>
                    <div class="form_row_triple">
                        <div class="form_group">
                            <label for="num_habitaciones" class="form_label">Habitaciones</label>
                            <input type="number" id="num_habitaciones" name="num_habitaciones" class="form_input" placeholder="0" min="0" max="50">
                        </div>
                        <div class="form_group">
                            <label for="num_banos" class="form_label">Baños</label>
                            <input type="number" id="num_banos" name="num_banos" class="form_input" placeholder="0" min="0" max="50">
                        </div>
                        <div class="form_group">
                            <label for="area_m2" class="form_label">Área (m²)</label>
                            <input type="number" id="area_m2" name="area_m2" class="form_input" placeholder="0" min="0" step="0.01">
                        </div>
                    </div>
                    <div class="form_check">
                        <input type="checkbox" id="amueblado" name="amueblado" value="1">
                        <label for="amueblado">La propiedad está amueblada</label>
                    </div>
                    <div class="form_check">
                        <input type="checkbox" id="mascotas_permitidas" name="mascotas_permitidas" value="1">
                        <label for="mascotas_permitidas">Se permiten mascotas</label>
                    </div>
                </fieldset>

                <fieldset class="form_fieldset">
                    <legend class="form_legend">Precio</legend>
                    <div class="form_row_double">
                        <div class="form_group">
                            <label for="precio_alquiler_mensual" class="form_label">Precio mensual (COP)</label>
                            <input type="number" id="precio_alquiler_mensual" name="precio_alquiler_mensual" class="form_input" placeholder="0" min="0" step="1000" required>
                        </div>
                        <div class="form_group">
                            <label for="deposito" class="form_label">Depósito <span class="label_hint">(Opcional)</span></label>
                            <input type="number" id="deposito" name="deposito" class="form_input" placeholder="0" min="0" step="1000">
                        </div>
                    </div>
                    <div class="form_check">
                        <input type="checkbox" id="disponible" name="disponible" value="1" checked>
                        <label for="disponible">Disponible para arriendo de inmediato</label>
                    </div>
                </fieldset>

                <fieldset class="form_fieldset">
                    <legend class="form_legend">Ubicación</legend>

                    <!-- Selector de modo: escribir la dirección o usar el GPS -->
                    <label class="form_label">¿Cómo quieres ubicar tu propiedad?</label>
                    <div class="geo_modo" role="tablist" aria-label="Cómo ubicar la propiedad">
                        <button type="button" class="geo_modo_btn is_active" data-modo="manual" role="tab" aria-selected="true">
                            <span class="material-symbols-outlined icon_sm">edit_location</span>
                            Escribir la dirección
                        </button>
                        <button type="button" class="geo_modo_btn" data-modo="gps" role="tab" aria-selected="false">
                            <span class="material-symbols-outlined icon_sm">my_location</span>
                            Usar mi GPS
                        </button>
                    </div>

                    <!-- Panel: modo manual (campos de dirección, sin mapa) -->
                    <div class="geo_panel geo_panel_manual" data-panel="manual">
                        <div class="form_row_triple">
                            <div class="form_group">
                                <label for="pais" class="form_label">País</label>
                                <select id="pais" name="pais" class="form_input">
                                    <option value="" disabled selected>País…</option>
                                </select>
                            </div>
                            <div class="form_group">
                                <label for="departamento" class="form_label">Departamento</label>
                                <select id="departamento" name="departamento" class="form_input" disabled>
                                    <option value="" disabled selected>Departamento…</option>
                                </select>
                            </div>
                            <div class="form_group">
                                <label for="ciudad" class="form_label">Ciudad</label>
                                <select id="ciudad" name="ciudad_id" class="form_input" disabled required>
                                    <option value="" disabled selected>Ciudad…</option>
                                </select>
                            </div>
                        </div>
                        <div class="form_group">
                            <label for="calle" class="form_label">Dirección (calle)</label>
                            <input type="text" id="calle" name="calle" class="form_input" placeholder="Ej: Calle 127" maxlength="255" autocomplete="address-line1" required>
                        </div>
                        <div class="form_row_double">
                            <div class="form_group">
                                <label for="numero_exterior" class="form_label">Número <span class="label_hint">(Opcional)</span></label>
                                <input type="text" id="numero_exterior" name="numero_exterior" class="form_input" placeholder="# 45 - 10" maxlength="20">
                            </div>
                            <div class="form_group">
                                <label for="barrio" class="form_label">Barrio <span class="label_hint">(Opcional)</span></label>
                                <input type="text" id="barrio" name="barrio" class="form_input" placeholder="Ej: Usaquén" maxlength="100">
                            </div>
                        </div>
                        <div class="form_row_double">
                            <div class="form_group">
                                <label for="codigo_postal" class="form_label">Código postal <span class="label_hint">(Opcional)</span></label>
                                <input type="text" id="codigo_postal" name="codigo_postal" class="form_input" placeholder="110111" maxlength="10">
                            </div>
                            <div class="form_group">
                                <label for="referencia" class="form_label">Referencia <span class="label_hint">(Opcional)</span></label>
                                <input type="text" id="referencia" name="referencia" class="form_input" placeholder="Frente al parque" maxlength="255">
                            </div>
                        </div>
                    </div>

                    <!-- Panel: modo GPS (mapa con pin, sin campos) -->
                    <div class="geo_panel geo_panel_gps" data-panel="gps" hidden>
                        <p class="help_hint">¿Estás en la propiedad que vas a arrendar? Toma tu ubicación actual: marcaremos el pin y detectaremos la dirección por ti. Puedes <strong>arrastrar el pin</strong> para afinar.</p>
                        <button type="button" id="btn_mi_ubicacion" class="geo_gps_btn">
                            <span class="material-symbols-outlined icon_sm">my_location</span> Usar mi ubicación actual
                        </button>
                        <div id="geo_mapa" class="geo_mapa"></div>
                        <div class="geo_resumen" id="geo_resumen" hidden>
                            <span class="material-symbols-outlined icon_sm">location_on</span>
                            <span id="geo_resumen_texto"></span>
                        </div>
                        <p class="geo_estado" id="geo_estado" role="status"></p>
                    </div>

                    <!-- Coordenadas confirmadas (las llena el JS) -->
                    <input type="hidden" name="latitud"  id="latitud">
                    <input type="hidden" name="longitud" id="longitud">
                </fieldset>

                <button type="submit" id="propiedad_submit" class="btn_primary u_full_width" disabled>Publicar ahora</button>
            </form>
        </section>

        <!-- Columna derecha: panel de progreso fijo (sticky) -->
        <aside class="progress_panel" aria-label="Progreso de la publicación">
            <h2 class="progress_title">Tu progreso</h2>

            <div class="progress_bar">
                <div class="progress_fill" id="progress_fill"
                     role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"></div>
            </div>
            <p class="progress_percent"><span id="progress_percent">0</span>% completado</p>

            <ul class="progress_list">
                <li class="progress_item" data-req="titulo">Título de la publicación</li>
                <li class="progress_item" data-req="tipo_propiedad">Tipo de propiedad</li>
                <li class="progress_item" data-req="precio_alquiler_mensual">Precio mensual</li>
                <li class="progress_item" data-req="ciudad">Ciudad</li>
                <li class="progress_item" data-req="calle">Dirección (calle)</li>
            </ul>

            <p class="progress_hint" id="progress_hint">Completa los campos obligatorios para publicar.</p>
        </aside>

    </div>

<?php $showFooter = false; ?>
    <script>
        // Ubicaciones reales desde la base de datos (paises/departamentos/ciudades)
        window.UBICACIONES = <?= json_encode($ubicaciones ?? ['paises' => [], 'departamentos' => [], 'ciudades' => []], JSON_UNESCAPED_UNICODE) ?>;
    </script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="/js/propiedades.js"></script>
    <script src="/js/publicar_mapa.js"></script>
<?php require __DIR__ . '/layouts/footer.php'; ?>
