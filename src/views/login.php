<?php
$title  = 'Iniciar Sesión | miarriendo.online';
$styles = ['auth.css'];
require __DIR__ . '/layouts/header.php';
?>

    <main class="main_container">
        <div class="auth_wrapper">
            <div class="auth_card">
                <h1 class="auth_title">Ingresa a tu cuenta</h1>
                <p class="auth_subtitle">Accede para gestionar tus arriendos activos</p>

                <?php if (!empty($error)): ?>
                    <p class="form_error"><?= e($error) ?></p>
                <?php endif; ?>

                <form action="/login" method="POST">
                    <div class="form_group">
                        <label for="email" class="form_label">Correo electrónico</label>
                        <input type="email" id="email" name="email" class="form_input" placeholder="nombre@correo.com" required>
                    </div>

                    <div class="form_group">
                        <label for="password" class="form_label">Contraseña</label>
                        <input type="password" id="password" name="password" class="form_input" placeholder="••••••••" required>
                    </div>

                    <button type="submit" class="btn_primary u_full_width u_mt_sm">Iniciar Sesión</button>
                </form>

                <div class="auth_footer">
                    ¿Eres nuevo en la plataforma? <a href="/registro" class="text_link">Crea una cuenta</a>
                </div>
            </div>
        </div>
    </main>

<?php
$showFooter = false;
require __DIR__ . '/layouts/footer.php';
?>
