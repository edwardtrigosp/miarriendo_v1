<?php
$title = $title ?? 'Propiedad publicada | miarriendo.online';
require __DIR__ . '/layouts/header.php';
$titulo = $titulo ?? '';
?>
    <main class="main_container panel_wrap">
        <section class="exito_card">
            <span class="exito_icon material-symbols-outlined">check_circle</span>
            <h1 class="exito_title">¡Propiedad publicada!</h1>
            <p class="exito_text">
                <?php if ($titulo !== ''): ?><strong>«<?= e($titulo) ?>»</strong> <?php endif; ?>ya aparece en miarriendo.
                Los interesados podrán encontrarla y enviarte solicitudes de arriendo.
            </p>
            <div class="exito_actions">
                <a href="/propiedades" class="btn_outline">
                    <span class="material-symbols-outlined">add_home</span> Publicar otra propiedad
                </a>
                <a href="/panel?ver=mis-propiedades" class="btn_primary">
                    <span class="material-symbols-outlined">home_work</span> Ir a mis propiedades
                </a>
            </div>
        </section>
    </main>

    <style>
        .exito_card {
            max-width: 520px;
            margin: 40px auto 0;
            text-align: center;
            background-color: var(--surface_color);
            border: 1px solid var(--border_color);
            border-radius: 16px;
            box-shadow: var(--shadow_sm);
            padding: 48px 36px;
        }
        .exito_icon {
            font-size: 64px;
            color: #16a34a; /* verde de éxito */
            margin-bottom: 12px;
        }
        .exito_title { font-size: 1.6rem; font-weight: 700; margin-bottom: 10px; }
        .exito_text { color: var(--text_muted); line-height: 1.6; margin-bottom: 28px; }
        .exito_actions { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }
        .exito_actions .btn_primary,
        .exito_actions .btn_outline {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .exito_actions .material-symbols-outlined { font-size: 20px; }
    </style>

<?php
$showFooter = false;
require __DIR__ . '/layouts/footer.php';
?>
