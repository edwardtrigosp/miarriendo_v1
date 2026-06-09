<?php require __DIR__ . '/_m3_head.php'; ?>

<style>
  .auth_wrap {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 48px 20px;
  }
  .auth_card {
    width: 100%;
    max-width: 400px;
    padding: 40px 32px;
    display: flex;
    flex-direction: column;
    gap: 20px;
    text-align: center;
  }
  .auth_brand {
    font-size: 1.6rem;
    font-weight: 700;
    color: var(--md-sys-color-primary);
    letter-spacing: -0.02em;
  }
  .auth_card md-filled-text-field { width: 100%; text-align: left; }
  .auth_card md-filled-button { width: 100%; --md-filled-button-container-height: 52px; }
  .auth_sub { color: var(--md-sys-color-on-surface-variant); margin: -8px 0 4px; }
  .auth_foot { font-size: 0.9rem; color: var(--md-sys-color-on-surface-variant); }
</style>

<div class="auth_wrap">
  <div class="m3_card auth_card">
    <div class="auth_brand">miarriendo</div>
    <h1 class="md-typescale-headline-small" style="margin:0">Iniciar sesión</h1>
    <p class="auth_sub md-typescale-body-medium">Bienvenido de nuevo</p>

    <md-filled-text-field label="Correo electrónico" type="email" value="edwardtrigosp@gmail.com">
      <md-icon slot="leading-icon">mail</md-icon>
    </md-filled-text-field>

    <md-filled-text-field label="Contraseña" type="password" value="clave1234">
      <md-icon slot="leading-icon">lock</md-icon>
    </md-filled-text-field>

    <md-filled-button>Entrar</md-filled-button>

    <p class="auth_foot">¿No tienes cuenta? <a href="/lab/registro">Regístrate</a></p>
  </div>
</div>

<?php require __DIR__ . '/_m3_foot.php'; ?>
