<?php
$title  = 'Explorar Arriendos | miarriendo.online';
$styles = ['arriendos.css', 'filtros.css'];
require __DIR__ . '/layouts/header.php';

$propiedades = $propiedades ?? [];
$filtros     = $filtros ?? [];
$f = static fn(string $k): string => e($filtros[$k] ?? '');
$tipos = ['Apartamento', 'Casa', 'Apartaestudio', 'Habitación', 'Local'];
$orden = $filtros['orden'] ?? 'recientes';
?>

    <main class="main_container">
        <header class="section_header">
            <h1 class="page_title">Propiedades disponibles</h1>
            <p class="u_text_muted">Encontramos <?= count($propiedades) ?> propiedad<?= count($propiedades) === 1 ? '' : 'es' ?> para ti.</p>
        </header>

        <!-- Barra de filtros -->
        <form class="filtros" id="filtros_form" method="GET" action="/arriendos">
            <input type="hidden" name="lat" id="filtro_lat" value="<?= $f('lat') ?>">
            <input type="hidden" name="lon" id="filtro_lon" value="<?= $f('lon') ?>">

            <div class="filtros_grid">
                <div class="filtro_campo filtro_busqueda">
                    <span class="material-symbols-outlined icon_sm">search</span>
                    <input type="text" name="q" value="<?= $f('q') ?>" placeholder="Buscar por título…">
                </div>

                <select name="departamento_id" id="filtro_departamento" class="filtro_select">
                    <option value="">Departamento</option>
                </select>

                <select name="ciudad_id" id="filtro_ciudad" class="filtro_select">
                    <option value="">Ciudad</option>
                </select>

                <select name="tipo" class="filtro_select">
                    <option value="">Tipo</option>
                    <?php foreach ($tipos as $t): ?>
                        <option value="<?= e($t) ?>" <?= ($filtros['tipo'] ?? '') === $t ? 'selected' : '' ?>><?= e($t) ?></option>
                    <?php endforeach; ?>
                </select>

                <input type="number" name="precio_min" value="<?= $f('precio_min') ?>" class="filtro_input" placeholder="Precio mín." min="0" step="50000">
                <input type="number" name="precio_max" value="<?= $f('precio_max') ?>" class="filtro_input" placeholder="Precio máx." min="0" step="50000">

                <select name="orden" id="filtro_orden" class="filtro_select">
                    <option value="recientes"   <?= $orden === 'recientes'   ? 'selected' : '' ?>>Más recientes</option>
                    <option value="precio_asc"  <?= $orden === 'precio_asc'  ? 'selected' : '' ?>>Precio: menor a mayor</option>
                    <option value="precio_desc" <?= $orden === 'precio_desc' ? 'selected' : '' ?>>Precio: mayor a menor</option>
                    <option value="cercania"    <?= $orden === 'cercania'    ? 'selected' : '' ?>>Cerca de mí</option>
                </select>
            </div>

            <div class="filtros_acciones">
                <button type="button" id="btn_cerca" class="btn_outline">
                    <span class="material-symbols-outlined icon_sm">my_location</span> Cerca de mí
                </button>
                <div class="filtros_acciones_der">
                    <?php if (array_filter([$filtros['q'] ?? '', $filtros['departamento_id'] ?? '', $filtros['ciudad_id'] ?? '', $filtros['tipo'] ?? '', $filtros['precio_min'] ?? '', $filtros['precio_max'] ?? '']) || $orden !== 'recientes'): ?>
                        <a href="/arriendos" class="filtros_limpiar">Limpiar filtros</a>
                    <?php endif; ?>
                    <button type="submit" class="btn_primary">Aplicar filtros</button>
                </div>
            </div>
            <p class="filtros_estado" id="filtros_estado" role="status"></p>
        </form>

        <?php if (empty($propiedades)): ?>
            <div class="empty_state">
                <span class="material-symbols-outlined icon_xl">search_off</span>
                <p>No encontramos propiedades con esos filtros.</p>
                <a href="/arriendos" class="btn_primary">Ver todas</a>
            </div>
        <?php else: ?>
            <div class="properties_grid">
                <?php foreach ($propiedades as $p): ?>
                    <?php require __DIR__ . '/partials/property_card.php'; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

<?php $showFooter = false; ?>
    <script>
        window.UBICACIONES = <?= json_encode($ubicaciones ?? ['departamentos' => [], 'ciudades' => []], JSON_UNESCAPED_UNICODE) ?>;
        window.FILTROS = <?= json_encode([
            'departamento_id' => $filtros['departamento_id'] ?? '',
            'ciudad_id'       => $filtros['ciudad_id'] ?? '',
        ], JSON_UNESCAPED_UNICODE) ?>;
    </script>
    <script src="/js/filtros.js"></script>
<?php require __DIR__ . '/layouts/footer.php'; ?>
