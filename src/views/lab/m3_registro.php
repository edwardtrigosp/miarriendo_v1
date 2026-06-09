<?php require __DIR__ . '/_m3_head.php'; ?>

<style>
  .reg_wrap {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 40px 20px;
  }
  .reg_card {
    width: 100%;
    max-width: 520px;
    padding: 40px 32px;
    display: flex;
    flex-direction: column;
    gap: 18px;
  }
  .reg_head { text-align: center; margin-bottom: 4px; }
  .reg_brand {
    font-size: 1.6rem;
    font-weight: 700;
    color: var(--md-sys-color-primary);
    letter-spacing: -0.02em;
  }
  .reg_sub { color: var(--md-sys-color-on-surface-variant); margin-top: 4px; }
  .reg_grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
  }
  .reg_card md-filled-text-field { width: 100%; }
  .reg_full { grid-column: 1 / -1; }
  .reg_card md-filled-button { width: 100%; --md-filled-button-container-height: 52px; margin-top: 4px; }
  .reg_foot { text-align: center; font-size: 0.9rem; color: var(--md-sys-color-on-surface-variant); }
  @media (max-width: 480px) { .reg_grid { grid-template-columns: 1fr; } }
</style>

<div class="reg_wrap">
  <div class="m3_card reg_card">
    <div class="reg_head">
      <div class="reg_brand">miarriendo</div>
      <h1 class="md-typescale-headline-small" style="margin:8px 0 0">Crear cuenta</h1>
      <p class="reg_sub md-typescale-body-medium">Publica y arrienda en un solo lugar</p>
    </div>

    <div class="reg_grid">
      <md-filled-text-field label="Nombre"></md-filled-text-field>
      <md-filled-text-field label="Apellidos"></md-filled-text-field>
      <md-filled-text-field class="reg_full" label="Correo electrónico" type="email">
        <md-icon slot="leading-icon">mail</md-icon>
      </md-filled-text-field>
      <md-filled-text-field class="reg_full" label="Teléfono" type="tel">
        <md-icon slot="leading-icon">call</md-icon>
      </md-filled-text-field>
      <md-filled-text-field class="reg_full" label="Contraseña" type="password">
        <md-icon slot="leading-icon">lock</md-icon>
      </md-filled-text-field>
    </div>

    <md-filled-button>Crear cuenta</md-filled-button>
    <p class="reg_foot">¿Ya tienes cuenta? <a href="/lab/login">Inicia sesión</a></p>
  </div>
</div>

<?php require __DIR__ . '/_m3_foot.php'; ?>
