<?php
$title  = 'Blog | miarriendo.online';
$styles = ['blog.css'];
require __DIR__ . '/layouts/header.php';

// Publicaciones de ejemplo mientras no haya base de datos.
$posts = $posts ?? [
    [
        'id'        => 1,
        'categoria' => 'Guías',
        'titulo'    => '5 cosas que revisar antes de firmar tu contrato de arriendo',
        'extracto'  => 'Evita sorpresas: te contamos las cláusulas clave, los gastos ocultos y cómo verificar al propietario antes de comprometerte.',
        'fecha'     => '2026-05-28',
        'autor'     => 'Equipo miarriendo',
    ],
    [
        'id'        => 2,
        'categoria' => 'Novedades',
        'titulo'    => 'Nuevo: calcula la ruta a tu próxima propiedad en un clic',
        'extracto'  => 'Integramos Google Maps para que veas el tiempo y la distancia desde tu ubicación hasta cualquier inmueble publicado.',
        'fecha'     => '2026-05-20',
        'autor'     => 'Equipo miarriendo',
    ],
    [
        'id'        => 3,
        'categoria' => 'Dudas frecuentes',
        'titulo'    => '¿Cómo publico mi inmueble y a quién llega?',
        'extracto'  => 'Resolvemos las preguntas más comunes de los propietarios: visibilidad, fotos, precio sugerido y seguridad de tus datos.',
        'fecha'     => '2026-05-12',
        'autor'     => 'Equipo miarriendo',
    ],
];

// Nombres de meses en español para formatear las fechas sin depender del locale.
$meses = [1 => 'ene', 2 => 'feb', 3 => 'mar', 4 => 'abr', 5 => 'may', 6 => 'jun',
          7 => 'jul', 8 => 'ago', 9 => 'sep', 10 => 'oct', 11 => 'nov', 12 => 'dic'];
?>

    <main class="main_container">
        <header class="blog_header">
            <h1 class="page_title">Blog miarriendo</h1>
            <p class="u_text_muted">Resolvemos tus dudas y te contamos las novedades de la plataforma.</p>
        </header>

        <div class="blog_grid">
            <?php foreach ($posts as $post): ?>
            <article class="post_card">
                <span class="post_category"><?= e($post['categoria']) ?></span>
                <h2 class="post_title"><?= e($post['titulo']) ?></h2>
                <p class="post_excerpt"><?= e($post['extracto']) ?></p>
                <footer class="post_meta">
                    <?php
                    $ts = strtotime($post['fecha']);
                    $fechaLegible = (int) date('j', $ts) . ' ' . $meses[(int) date('n', $ts)] . ' ' . date('Y', $ts);
                    ?>
                    <span><?= e($post['autor']) ?></span>
                    <time datetime="<?= e($post['fecha']) ?>"><?= e($fechaLegible) ?></time>
                </footer>
                <a href="/blog/<?= e($post['id']) ?>" class="post_link">Leer más <span class="material-symbols-outlined icon_sm">arrow_forward</span></a>
            </article>
            <?php endforeach; ?>
        </div>
    </main>

<?php require __DIR__ . '/layouts/footer.php'; ?>
