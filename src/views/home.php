<?php
$styles = ['home.css'];
require __DIR__ . '/layouts/header.php';
?>

    <main class="main_container">
        <section class="hero_section">
            <h1 class="hero_title">El ecosistema digital para tu próximo arriendo</h1>
            <p class="hero_subtitle">Conectamos de forma directa, transparente y segura a propietarios con inquilinos. Gestiona contratos, pagos y rutas sin salir de la plataforma.</p>
            <div class="hero_actions">
                <a href="/arriendos" class="btn_primary">Buscar Propiedades</a>
                <a href="/propiedades" class="btn_outline">Publicar un Inmueble</a>
            </div>
        </section>
    </main>

<?php require __DIR__ . '/layouts/footer.php'; ?>
