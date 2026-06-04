<?php
$title  = 'Política de Cookies | miarriendo.online';
$styles = ['cookies.css'];
require __DIR__ . '/layouts/header.php';
?>

    <main class="main_container">
        <article class="legal_page">
            <header class="legal_header">
                <h1 class="page_title">Política de Cookies</h1>
                <p class="u_text_muted">Última actualización: 1 de junio de 2026</p>
            </header>

            <section class="legal_section">
                <h2>¿Qué son las cookies?</h2>
                <p>Las cookies son pequeños archivos de texto que un sitio web guarda en tu dispositivo cuando lo visitas. Sirven para recordar tus preferencias, mantener tu sesión iniciada y mejorar tu experiencia en <strong>miarriendo.online</strong>.</p>
            </section>

            <section class="legal_section">
                <h2>¿Qué cookies utilizamos?</h2>
                <table class="cookie_table">
                    <thead>
                        <tr>
                            <th>Tipo</th>
                            <th>Finalidad</th>
                            <th>Duración</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Esenciales</td>
                            <td>Mantener tu sesión iniciada y la seguridad del sitio.</td>
                            <td>Sesión</td>
                        </tr>
                        <tr>
                            <td>Preferencias</td>
                            <td>Recordar la ciudad seleccionada y tu consentimiento de cookies.</td>
                            <td>1 año</td>
                        </tr>
                        <tr>
                            <td>Analíticas</td>
                            <td>Entender cómo se usa la plataforma para mejorarla.</td>
                            <td>2 años</td>
                        </tr>
                    </tbody>
                </table>
            </section>

            <section class="legal_section">
                <h2>¿Cómo gestionar las cookies?</h2>
                <p>Puedes aceptar o rechazar las cookies no esenciales desde el banner que aparece al entrar al sitio. También puedes eliminarlas o bloquearlas en cualquier momento desde la configuración de tu navegador.</p>
            </section>

            <section class="legal_section">
                <h2>Contacto</h2>
                <p>Si tienes dudas sobre esta política, escríbenos a <a href="mailto:soporte@miarriendo.online" class="text_link">soporte@miarriendo.online</a>.</p>
            </section>
        </article>
    </main>

<?php require __DIR__ . '/layouts/footer.php'; ?>
