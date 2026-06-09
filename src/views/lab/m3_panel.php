<?php require __DIR__ . '/_m3_head.php'; ?>

<style>
  /* Top app bar (Material 3) */
  .m3_topbar {
    display: flex;
    align-items: center;
    gap: 12px;
    height: 64px;
    padding: 0 16px;
    background-color: var(--md-sys-color-surface);
    border-bottom: 1px solid var(--md-sys-color-outline-variant);
    position: sticky;
    top: 0;
    z-index: 5;
  }
  .m3_topbar .brand {
    font-size: 1.3rem;
    font-weight: 700;
    color: var(--md-sys-color-primary);
    letter-spacing: -0.02em;
  }
  .m3_topbar .spacer { flex: 1; }
  .m3_avatar {
    width: 40px; height: 40px; border-radius: 50%;
    background-color: var(--md-sys-color-primary);
    color: var(--md-sys-color-on-primary);
    display: flex; align-items: center; justify-content: center;
    font-weight: 600;
  }

  .m3_main { max-width: 1100px; margin: 0 auto; padding: 24px 20px 96px; }
  .m3_greeting { margin: 8px 0 4px; }
  .m3_muted { color: var(--md-sys-color-on-surface-variant); }

  /* KPIs */
  .kpi_grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    margin: 24px 0;
  }
  .kpi { padding: 20px; display: flex; flex-direction: column; gap: 6px; }
  .kpi_ic {
    width: 44px; height: 44px; border-radius: 14px;
    background-color: var(--md-sys-color-primary-container);
    color: var(--md-sys-color-on-primary-container);
    display: flex; align-items: center; justify-content: center;
    margin-bottom: 6px;
  }
  .kpi_num { font-size: 1.8rem; font-weight: 700; line-height: 1; }
  .kpi_lbl { color: var(--md-sys-color-on-surface-variant); font-size: 0.85rem; }
  .kpi_num.money { color: var(--md-sys-color-primary); }

  .m3_section { margin-top: 28px; }
  .m3_section_head {
    display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;
  }
  .list_card { overflow: hidden; }

  /* FAB fijo */
  .m3_fab { position: fixed; right: 24px; bottom: 24px; z-index: 10; }

  @media (max-width: 900px) { .kpi_grid { grid-template-columns: repeat(2, 1fr); } }
  @media (max-width: 520px) { .kpi_grid { grid-template-columns: 1fr; } }
</style>

<header class="m3_topbar">
  <span class="brand">miarriendo</span>
  <span class="spacer"></span>
  <md-icon-button><md-icon>notifications</md-icon></md-icon-button>
  <div class="m3_avatar">E</div>
</header>

<main class="m3_main">

  <!-- Switch de rol como Tabs Material -->
  <md-tabs aria-label="Modo">
    <md-primary-tab active>
      <md-icon slot="icon">home_work</md-icon>
      Arrendar propiedades
    </md-primary-tab>
    <md-primary-tab>
      <md-icon slot="icon">vpn_key</md-icon>
      Buscar arriendo
    </md-primary-tab>
  </md-tabs>

  <h1 class="m3_greeting md-typescale-headline-medium">Hola, Edward 👋</h1>
  <p class="m3_muted md-typescale-body-large">Este es el resumen de tu actividad.</p>

  <!-- KPIs -->
  <section class="kpi_grid">
    <div class="m3_card kpi">
      <div class="kpi_ic"><md-icon>payments</md-icon></div>
      <span class="kpi_num money">$2.4M</span>
      <span class="kpi_lbl">Ingreso mensual</span>
    </div>
    <div class="m3_card kpi">
      <div class="kpi_ic"><md-icon>home_work</md-icon></div>
      <span class="kpi_num">4</span>
      <span class="kpi_lbl">Propiedades</span>
    </div>
    <div class="m3_card kpi">
      <div class="kpi_ic"><md-icon>vpn_key</md-icon></div>
      <span class="kpi_num">2</span>
      <span class="kpi_lbl">Arriendos activos</span>
    </div>
    <div class="m3_card kpi">
      <div class="kpi_ic"><md-icon>draw</md-icon></div>
      <span class="kpi_num">1</span>
      <span class="kpi_lbl">Por firmar</span>
    </div>
  </section>

  <!-- Acciones -->
  <div style="display:flex; gap:12px; flex-wrap:wrap;">
    <md-filled-button>
      <md-icon slot="icon">add_home</md-icon>
      Publicar propiedad
    </md-filled-button>
    <md-outlined-button>
      <md-icon slot="icon">search</md-icon>
      Buscar arriendos
    </md-outlined-button>
  </div>

  <!-- Lista de solicitudes -->
  <section class="m3_section">
    <div class="m3_section_head">
      <h2 class="md-typescale-title-large" style="margin:0">Necesita tu atención</h2>
      <md-text-button>Ver todo</md-text-button>
    </div>
    <div class="m3_card list_card">
      <md-list>
        <md-list-item type="button">
          <md-icon slot="start">draw</md-icon>
          <div slot="headline">Edward solicitó arriendo</div>
          <div slot="supporting-text">Apartamento en Chapinero · hace 2 días</div>
          <span slot="end" style="font-weight:600; color:var(--md-sys-color-primary)">$1.800.000</span>
        </md-list-item>
        <md-divider></md-divider>
        <md-list-item type="button">
          <md-icon slot="start">description</md-icon>
          <div slot="headline">Contrato por firmar</div>
          <div slot="supporting-text">Casa en El Poblado · hace 5 horas</div>
          <span slot="end" style="font-weight:600; color:var(--md-sys-color-primary)">$3.200.000</span>
        </md-list-item>
        <md-divider></md-divider>
        <md-list-item type="button">
          <md-icon slot="start">apartment</md-icon>
          <div slot="headline">Nueva reseña recibida</div>
          <div slot="supporting-text">Apartaestudio Centro · ayer</div>
          <span slot="end">⭐ 4.8</span>
        </md-list-item>
      </md-list>
    </div>
  </section>

</main>

<md-fab class="m3_fab" label="Publicar" variant="primary">
  <md-icon slot="icon">add</md-icon>
</md-fab>

<?php require __DIR__ . '/_m3_foot.php'; ?>
