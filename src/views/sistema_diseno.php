<?php
$title = $title ?? 'Sistema de diseño | miarriendo.online';
require __DIR__ . '/layouts/header.php';

// --- Datos de los tokens (una sola fuente, así no se duplica) ---------------
$marca = [
    ['Primary',  '--primary_color', '#8917D4'],
    ['Hover',    '--primary_hover', '#7412B5'],
    ['Accent',   '--accent_color',  '#F4E7FF'],
];
$paleta = [
    ['purple_50',  '--purple_50',  '#F4ECFC'],
    ['purple_100', '--purple_100', '#EDDCF9'],
    ['purple_200', '--purple_200', '#DBB9F2'],
    ['purple_300', '--purple_300', '#C48BE9'],
    ['purple_400', '--purple_400', '#AC5DE1'],
    ['purple_500', '--purple_500', '#8917D4'],
    ['purple_600', '--purple_600', '#7414B4'],
    ['purple_700', '--purple_700', '#601094'],
    ['purple_800', '--purple_800', '#4B0D75'],
    ['purple_900', '--purple_900', '#360954'],
];
$superficies = [
    ['Fondo',     '--bg_color',      '#F5F5F5'],
    ['Superficie','--surface_color', '#FFFFFF'],
    ['Texto',     '--text_main',     '#1E2229'],
    ['Texto tenue','--text_muted',   '#8B96A8'],
    ['Borde',     '--border_color',  '#E5E7EB'],
    ['Peligro',   '--danger_color',  '#EF4444'],
];

$swatch = static function (array $items): void {
    echo '<div class="sg_swatches">';
    foreach ($items as [$nombre, $var, $hex]) {
        $oscuro = in_array($var, ['--purple_400','--purple_500','--purple_600','--purple_700','--purple_800','--purple_900','--primary_color','--primary_hover','--text_main','--danger_color'], true);
        $borde  = in_array($var, ['--surface_color','--bg_color','--purple_50','--accent_color'], true);
        printf(
            '<div class="sg_swatch"><div class="sg_swatch_color" style="background-color:var(%s)%s"></div>'
            . '<div class="sg_swatch_meta"><span class="sg_swatch_name">%s</span>'
            . '<span class="sg_swatch_var">%s</span><br><span class="sg_swatch_hex">%s</span></div></div>',
            $var,
            $borde ? ';border-bottom:1px solid var(--border_color)' : '',
            e($nombre),
            e($var),
            e($hex)
        );
    }
    echo '</div>';
};
?>
    <main class="main_container sg_wrap">

        <header class="sg_hero">
            <h1>Sistema de diseño</h1>
            <p>
                Referencia viva de <strong>miarriendo.online</strong>. Todos los componentes se
                muestran con el CSS real del proyecto, así que esta página siempre refleja el
                diseño actual. Bajo cada ejemplo verás la clase que debes usar.
            </p>
        </header>

        <div class="sg_layout">

            <!-- Índice -->
            <nav class="sg_toc" aria-label="Secciones">
                <span class="sg_toc_title">Contenido</span>
                <a href="#colores">Colores</a>
                <a href="#tipografia">Tipografía</a>
                <a href="#iconos">Iconografía</a>
                <a href="#tokens">Sombras y radios</a>
                <a href="#botones">Botones</a>
                <a href="#enlaces">Enlaces</a>
                <a href="#formularios">Formularios</a>
                <a href="#mensajes">Mensajes</a>
                <a href="#badges">Badges y estados</a>
                <a href="#tarjetas">Tarjetas</a>
                <a href="#listas">Listas</a>
                <a href="#navegacion">Navegación</a>
                <a href="#modal">Modal</a>
                <a href="#vacios">Estados vacíos</a>
            </nav>

            <div class="sg_content">

                <!-- ============ COLORES ============ -->
                <section class="sg_section" id="colores">
                    <div class="sg_section_head">
                        <h2>Colores</h2>
                        <p>Definidos como variables CSS en <code>:root</code> (global.css).</p>
                    </div>

                    <div class="sg_demo"><?php $swatch($marca); ?></div>
                    <span class="sg_label">Marca y acción — <code>--primary_color</code>, <code>--primary_hover</code>, <code>--accent_color</code></span>

                    <div class="sg_demo"><?php $swatch($paleta); ?></div>
                    <span class="sg_label">Paleta de morados derivada — <code>--purple_50</code> … <code>--purple_900</code></span>

                    <div class="sg_demo"><?php $swatch($superficies); ?></div>
                    <span class="sg_label">Superficies, texto y estado — <code>--bg_color</code>, <code>--surface_color</code>, <code>--text_main</code>, <code>--text_muted</code>, <code>--border_color</code>, <code>--danger_color</code></span>
                </section>

                <!-- ============ TIPOGRAFÍA ============ -->
                <section class="sg_section" id="tipografia">
                    <div class="sg_section_head">
                        <h2>Tipografía</h2>
                        <p>Fuente <strong>Inter</strong> (400–700). <code>--font_family</code></p>
                    </div>
                    <div class="sg_demo">
                        <div class="sg_type_row"><span class="sg_type_tag">h1 / 2.2rem · 700</span><span style="font-size:2.2rem;font-weight:700;letter-spacing:-0.02em">Encuentra tu arriendo ideal</span></div>
                        <div class="sg_type_row"><span class="sg_type_tag">h2 / 1.4rem · 700</span><span style="font-size:1.4rem;font-weight:700">Título de sección</span></div>
                        <div class="sg_type_row"><span class="sg_type_tag">h3 / 1.2rem · 700</span><span style="font-size:1.2rem;font-weight:700">Subtítulo de tarjeta</span></div>
                        <div class="sg_type_row"><span class="sg_type_tag">body / 1rem · 400</span><span>Texto de párrafo normal para descripciones y contenido.</span></div>
                        <div class="sg_type_row"><span class="sg_type_tag">muted / 0.9rem</span><span class="u_text_muted">Texto secundario y descripciones discretas.</span></div>
                        <div class="sg_type_row"><span class="sg_type_tag">label / 0.7rem · 700</span><span class="app_nav_section_label" style="padding:0">Etiqueta de grupo</span></div>
                    </div>
                </section>

                <!-- ============ ICONOS ============ -->
                <section class="sg_section" id="iconos">
                    <div class="sg_section_head">
                        <h2>Iconografía</h2>
                        <p>Material Symbols Outlined. <code>&lt;span class="material-symbols-outlined"&gt;</code></p>
                    </div>
                    <div class="sg_demo">
                        <div class="sg_icons">
                            <span class="sg_icon_demo"><span class="material-symbols-outlined icon_sm">home_work</span><span>.icon_sm · 1rem</span></span>
                            <span class="sg_icon_demo"><span class="material-symbols-outlined">vpn_key</span><span>base · 1.25rem</span></span>
                            <span class="sg_icon_demo"><span class="material-symbols-outlined icon_lg">add_home</span><span>.icon_lg · 1.5rem</span></span>
                            <span class="sg_icon_demo"><span class="material-symbols-outlined icon_xl">draw</span><span>.icon_xl · 2rem</span></span>
                        </div>
                    </div>
                    <span class="sg_label">Iconos usados en el menú: <code>home_work</code>, <code>vpn_key</code>, <code>add_home</code>, <code>draw</code>, <code>description</code>, <code>article</code></span>
                </section>

                <!-- ============ SOMBRAS Y RADIOS ============ -->
                <section class="sg_section" id="tokens">
                    <div class="sg_section_head">
                        <h2>Sombras y radios</h2>
                        <p>Constantes de estructura reutilizables.</p>
                    </div>
                    <div class="sg_demo">
                        <div class="sg_tokens_grid">
                            <div class="sg_token_box" style="box-shadow:var(--shadow_sm)">--shadow_sm</div>
                            <div class="sg_token_box" style="box-shadow:var(--shadow_md)">--shadow_md</div>
                            <div class="sg_token_box" style="border-radius:var(--border_radius)">--border_radius<br>10px</div>
                            <div class="sg_token_box" style="border-radius:16px">16px<br>(modales/tarjetas)</div>
                            <div class="sg_token_box" style="border-radius:999px">999px<br>(píldoras)</div>
                        </div>
                    </div>
                </section>

                <!-- ============ BOTONES ============ -->
                <section class="sg_section" id="botones">
                    <div class="sg_section_head">
                        <h2>Botones</h2>
                        <p>Acciones primarias, secundarias y destructivas.</p>
                    </div>
                    <div class="sg_demo">
                        <div class="sg_demo_row">
                            <a href="#botones" class="btn_primary">Botón primario</a>
                            <a href="#botones" class="btn_outline">Botón outline</a>
                            <a href="#botones" class="btn_primary btn_sm">Primario sm</a>
                            <a href="#botones" class="btn_outline btn_sm">Outline sm</a>
                            <a href="#botones" class="btn_outline btn_danger">Outline peligro</a>
                        </div>
                    </div>
                    <span class="sg_label"><code>.btn_primary</code> · <code>.btn_outline</code> · modificadores <code>.btn_sm</code>, <code>.btn_danger</code></span>

                    <div class="sg_demo">
                        <div class="sg_demo_row">
                            <a href="#botones" class="btn_primary"><span class="material-symbols-outlined" style="font-size:20px;margin-right:8px">add_home</span>Publicar</a>
                            <a href="#botones" class="btn_outline"><span class="material-symbols-outlined" style="font-size:20px;margin-right:8px">home_work</span>Mis propiedades</a>
                        </div>
                    </div>
                    <span class="sg_label">Con icono — <code>.material-symbols-outlined</code> dentro del botón</span>
                </section>

                <!-- ============ ENLACES ============ -->
                <section class="sg_section" id="enlaces">
                    <div class="sg_section_head">
                        <h2>Enlaces</h2>
                    </div>
                    <div class="sg_demo">
                        <p>Un párrafo con un <a href="#enlaces" class="text_link">enlace de marca</a> que se subraya al pasar el cursor.</p>
                    </div>
                    <span class="sg_label"><code>.text_link</code></span>
                </section>

                <!-- ============ FORMULARIOS ============ -->
                <section class="sg_section" id="formularios">
                    <div class="sg_section_head">
                        <h2>Formularios</h2>
                        <p>Campos del wizard de publicar y de autenticación.</p>
                    </div>
                    <div class="sg_demo sg_narrow">
                        <label class="form_group" style="display:block;margin-bottom:16px">
                            <span style="display:block;font-weight:600;font-size:0.9rem;margin-bottom:6px">Título</span>
                            <input type="text" placeholder="Apartamento luminoso en Chapinero" style="width:100%;padding:11px 14px;border:1px solid var(--border_color);border-radius:var(--border_radius);font-family:inherit;font-size:0.95rem">
                        </label>
                        <label class="form_group" style="display:block">
                            <span style="display:block;font-weight:600;font-size:0.9rem;margin-bottom:6px">Descripción</span>
                            <textarea rows="3" placeholder="Describe la propiedad…" style="width:100%;padding:11px 14px;border:1px solid var(--border_color);border-radius:var(--border_radius);font-family:inherit;font-size:0.95rem;resize:vertical"></textarea>
                        </label>
                    </div>
                    <span class="sg_label">Input y textarea estándar — borde <code>--border_color</code>, radio <code>--border_radius</code></span>
                </section>

                <!-- ============ MENSAJES ============ -->
                <section class="sg_section" id="mensajes">
                    <div class="sg_section_head">
                        <h2>Mensajes de estado</h2>
                    </div>
                    <div class="sg_demo">
                        <div class="form_error">Las credenciales no coinciden. Verifica e intenta de nuevo.</div>
                        <div class="form_success">Tus cambios se guardaron correctamente.</div>
                    </div>
                    <span class="sg_label"><code>.form_error</code> · <code>.form_success</code></span>
                </section>

                <!-- ============ BADGES Y ESTADOS ============ -->
                <section class="sg_section" id="badges">
                    <div class="sg_section_head">
                        <h2>Badges y estados</h2>
                        <p>Etiquetas pequeñas para estado y conteos.</p>
                    </div>
                    <div class="sg_demo">
                        <div class="sg_demo_row">
                            <span class="arriendo_estado">activo</span>
                            <span class="kpi_badge">3</span>
                            <span class="panel_badge_num">5</span>
                            <span class="property_badge">Destacado</span>
                            <span class="property_badge_solicitudes"><span class="material-symbols-outlined">draw</span> 2</span>
                        </div>
                    </div>
                    <span class="sg_label"><code>.arriendo_estado</code> · <code>.kpi_badge</code> · <code>.panel_badge_num</code> · <code>.property_badge</code> · <code>.property_badge_solicitudes</code></span>
                </section>

                <!-- ============ TARJETAS ============ -->
                <section class="sg_section" id="tarjetas">
                    <div class="sg_section_head">
                        <h2>Tarjetas</h2>
                        <p>Bloques de contenido del dashboard y vistas de panel.</p>
                    </div>

                    <!-- KPI -->
                    <div class="sg_demo">
                        <div class="kpi_row" style="margin-bottom:0">
                            <div class="kpi_card is_money">
                                <span class="material-symbols-outlined">payments</span>
                                <strong>$2.400.000</strong>
                                <span>Ingreso mensual</span>
                            </div>
                            <div class="kpi_card">
                                <span class="material-symbols-outlined">home_work</span>
                                <strong>4</strong>
                                <span>Propiedades</span>
                            </div>
                            <div class="kpi_card">
                                <span class="material-symbols-outlined">vpn_key</span>
                                <strong>2</strong>
                                <span>Arriendos activos</span>
                            </div>
                            <div class="kpi_card">
                                <span class="material-symbols-outlined">draw</span>
                                <strong>1<span class="kpi_badge">1</span></strong>
                                <span>Por firmar</span>
                            </div>
                        </div>
                    </div>
                    <span class="sg_label"><code>.kpi_row</code> &gt; <code>.kpi_card</code> (modificador <code>.is_money</code>)</span>

                    <!-- stat_card -->
                    <div class="sg_demo">
                        <div class="panel_stats" style="margin-bottom:0">
                            <a href="#tarjetas" class="stat_card">
                                <span class="stat_icon material-symbols-outlined">apartment</span>
                                <span class="stat_data"><strong>12</strong><span>Propiedades publicadas</span></span>
                            </a>
                            <a href="#tarjetas" class="stat_card">
                                <span class="stat_icon material-symbols-outlined">description</span>
                                <span class="stat_data"><strong>8</strong><span>Solicitudes</span></span>
                            </a>
                        </div>
                    </div>
                    <span class="sg_label"><code>.stat_card</code> con <code>.stat_icon</code> + <code>.stat_data</code></span>

                    <!-- property card (pc) -->
                    <div class="sg_demo">
                        <div class="properties_grid" style="grid-template-columns:repeat(2,minmax(0,1fr));max-width:520px;padding:0">
                            <a href="#tarjetas" class="pc">
                                <div class="pc_img">
                                    <div style="width:100%;height:100%;background:linear-gradient(135deg,var(--purple_200),var(--purple_400))"></div>
                                    <span class="pc_estado">Disponible</span>
                                </div>
                                <div class="pc_body">
                                    <div class="pc_title">Apartamento en Chapinero</div>
                                    <div class="pc_price"><strong>$1.800.000</strong> / mes</div>
                                    <div class="pc_meta"><span class="material-symbols-outlined">bed</span> 2 · <span class="material-symbols-outlined">bathtub</span> 2 · <span class="material-symbols-outlined">straighten</span> 65 m² · Bogotá</div>
                                </div>
                            </a>
                            <a href="#tarjetas" class="pc">
                                <div class="pc_img">
                                    <div style="width:100%;height:100%;background:linear-gradient(135deg,var(--purple_300),var(--purple_600))"></div>
                                    <span class="pc_estado is_off">Arrendada</span>
                                    <span class="pc_solic"><span class="material-symbols-outlined">draw</span> 2</span>
                                </div>
                                <div class="pc_body">
                                    <div class="pc_title">Casa en El Poblado</div>
                                    <div class="pc_price"><strong>$3.200.000</strong> / mes</div>
                                    <div class="pc_meta"><span class="material-symbols-outlined">bed</span> 3 · <span class="material-symbols-outlined">bathtub</span> 3 · <span class="material-symbols-outlined">straighten</span> 120 m² · Medellín</div>
                                </div>
                            </a>
                        </div>
                    </div>
                    <span class="sg_label">Tarjeta de propiedad minimalista — <code>.pc</code> (<code>.pc_img</code>, <code>.pc_estado</code>, <code>.pc_solic</code>, <code>.pc_body</code>, <code>.pc_meta</code>)</span>

                    <!-- side_card -->
                    <div class="sg_demo">
                        <div class="side_card" style="position:static;max-width:320px">
                            <h2>Resumen de tu portafolio</h2>
                            <div class="side_progress_bar"><div class="side_progress_fill" style="width:75%"></div></div>
                            <p class="side_progress_label">75% de ocupación</p>
                            <ul class="side_stats">
                                <li class="side_stat"><span class="material-symbols-outlined">home_work</span> Propiedades <strong>4</strong></li>
                                <li class="side_stat"><span class="material-symbols-outlined">payments</span> Ingreso <strong class="side_money">$2.4M</strong></li>
                            </ul>
                            <p class="side_hint">Completa las fotos para destacar tus avisos. <a href="#tarjetas">Ver guía</a></p>
                        </div>
                    </div>
                    <span class="sg_label">Panel lateral contextual — <code>.side_card</code> (<code>.side_progress_bar</code>, <code>.side_stats</code>, <code>.side_hint</code>)</span>
                </section>

                <!-- ============ LISTAS ============ -->
                <section class="sg_section" id="listas">
                    <div class="sg_section_head">
                        <h2>Listas</h2>
                        <p>Filas para arriendos, solicitudes y actividad.</p>
                    </div>
                    <div class="sg_demo">
                        <div class="arriendo_list">
                            <div class="arriendo_row">
                                <div><strong>Apartamento en Chapinero</strong><span class="u_text_muted">Bogotá D.C.</span></div>
                                <span class="arriendo_precio">$1.800.000</span>
                                <span class="arriendo_estado">activo</span>
                            </div>
                        </div>
                    </div>
                    <span class="sg_label"><code>.arriendo_list</code> &gt; <code>.arriendo_row</code></span>

                    <div class="sg_demo">
                        <div class="solicitud_list">
                            <a href="#listas" class="solicitud_row">
                                <div class="req_avatar">E</div>
                                <div class="solicitud_info"><strong>Edward Trigos solicitó arriendo</strong><span class="u_text_muted">Hace 2 días</span></div>
                                <span class="solicitud_precio">$1.800.000</span>
                            </a>
                        </div>
                    </div>
                    <span class="sg_label"><code>.solicitud_row</code> con <code>.req_avatar</code> + <code>.solicitud_info</code></span>

                    <div class="sg_demo">
                        <div class="act_item">
                            <span class="material-symbols-outlined">draw</span>
                            <div><p>Firmaste el contrato de <strong>Casa en El Poblado</strong></p><time>Hace 5 horas</time></div>
                        </div>
                        <div class="act_item">
                            <span class="material-symbols-outlined">add_home</span>
                            <div><p>Publicaste <strong>Apartamento en Chapinero</strong></p><time>Ayer</time></div>
                        </div>
                    </div>
                    <span class="sg_label">Timeline de actividad — <code>.act_item</code></span>
                </section>

                <!-- ============ NAVEGACIÓN ============ -->
                <section class="sg_section" id="navegacion">
                    <div class="sg_section_head">
                        <h2>Navegación</h2>
                        <p>Elementos del menú lateral (app shell).</p>
                    </div>

                    <div class="sg_demo sg_narrow">
                        <div class="role_switch" style="margin:0 0 14px">
                            <button type="button" class="role_switch_btn is_active"><span class="material-symbols-outlined">home_work</span></button>
                            <button type="button" class="role_switch_btn"><span class="material-symbols-outlined">vpn_key</span></button>
                        </div>
                        <div class="app_nav_section_label">Arrendar propiedades</div>
                        <a href="#navegacion" class="app_nav_link is_active"><span class="material-symbols-outlined">home_work</span> Mis propiedades</a>
                        <a href="#navegacion" class="app_nav_link"><span class="material-symbols-outlined">add_home</span> Publicar</a>
                        <a href="#navegacion" class="app_nav_link"><span class="material-symbols-outlined">draw</span> Firmar contratos</a>
                    </div>
                    <span class="sg_label">Switch de modo <code>.role_switch</code> + enlaces <code>.app_nav_link</code> (estado <code>.is_active</code>)</span>

                    <div class="sg_demo">
                        <div class="user_menu_dropdown" style="position:static;display:block;max-width:240px">
                            <div class="user_menu_greeting">Hola, Edward</div>
                            <a href="#navegacion" class="user_menu_item"><span class="material-symbols-outlined">person</span> Ver mi perfil</a>
                            <div class="user_menu_sep"></div>
                            <button type="button" class="user_menu_item user_menu_logout"><span class="material-symbols-outlined">logout</span> Cerrar sesión</button>
                        </div>
                    </div>
                    <span class="sg_label">Menú del avatar — <code>.user_menu_dropdown</code> &gt; <code>.user_menu_item</code></span>
                </section>

                <!-- ============ MODAL ============ -->
                <section class="sg_section" id="modal">
                    <div class="sg_section_head">
                        <h2>Modal de confirmación</h2>
                        <p>Reemplaza al <code>confirm()</code> del navegador. Pruébalo:</p>
                    </div>
                    <div class="sg_demo">
                        <form method="get" action="/sistema-diseno"
                              data-confirm="¿Seguro que quieres eliminar esta propiedad? Esta acción no se puede deshacer."
                              data-confirm-title="Eliminar propiedad"
                              data-confirm-ok="Sí, eliminar">
                            <button type="submit" class="btn_outline btn_danger">
                                <span class="material-symbols-outlined" style="font-size:20px;margin-right:8px">delete</span>
                                Eliminar propiedad
                            </button>
                        </form>
                    </div>
                    <span class="sg_label">Atributos <code>data-confirm</code>, <code>data-confirm-title</code>, <code>data-confirm-ok</code> (interceptados por confirm.js)</span>
                </section>

                <!-- ============ ESTADOS VACÍOS ============ -->
                <section class="sg_section" id="vacios">
                    <div class="sg_section_head">
                        <h2>Estados vacíos</h2>
                    </div>
                    <div class="sg_demo">
                        <div class="empty_state">
                            <span class="material-symbols-outlined">search_off</span>
                            <p>Aún no tienes propiedades publicadas.</p>
                            <a href="#vacios" class="btn_primary">Publicar la primera</a>
                        </div>
                    </div>
                    <span class="sg_label"><code>.empty_state</code></span>
                </section>

            </div><!-- .sg_content -->
        </div><!-- .sg_layout -->
    </main>

<?php require __DIR__ . '/layouts/footer.php'; ?>
