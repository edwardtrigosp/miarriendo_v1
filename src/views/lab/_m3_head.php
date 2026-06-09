<?php $title = $title ?? 'Material 3 | miarriendo'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($title) ?></title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0">

<!-- Material Web Components de Google (sin build, vía import map) -->
<script type="importmap">
{ "imports": { "@material/web/": "https://esm.run/@material/web/" } }
</script>
<script type="module">
  import '@material/web/all.js';
  import {styles as typescaleStyles} from '@material/web/typography/md-typescale-styles.js';
  document.adoptedStyleSheets.push(typescaleStyles.styleSheet);
</script>

<style>
  :root {
    /* Esquema Material 3 con #8917D4 (morado de miarriendo) como primario */
    --md-sys-color-primary: #8917D4;
    --md-sys-color-on-primary: #FFFFFF;
    --md-sys-color-primary-container: #EFDBFF;
    --md-sys-color-on-primary-container: #2B0052;
    --md-sys-color-secondary: #645A70;
    --md-sys-color-on-secondary: #FFFFFF;
    --md-sys-color-secondary-container: #EBDDF7;
    --md-sys-color-on-secondary-container: #1F182B;
    --md-sys-color-surface: #FEF7FF;
    --md-sys-color-surface-container-low: #F7F2FA;
    --md-sys-color-surface-container: #F3EDF7;
    --md-sys-color-surface-container-high: #ECE6F0;
    --md-sys-color-surface-container-highest: #E6E0E9;
    --md-sys-color-on-surface: #1D1B20;
    --md-sys-color-on-surface-variant: #49454F;
    --md-sys-color-outline: #79747E;
    --md-sys-color-outline-variant: #CAC4D0;
    --md-sys-color-background: #FEF7FF;
    --md-ref-typeface-brand: 'Roboto';
    --md-ref-typeface-plain: 'Roboto';
  }
  * { box-sizing: border-box; }
  body {
    margin: 0;
    font-family: 'Roboto', sans-serif;
    background-color: var(--md-sys-color-background);
    color: var(--md-sys-color-on-surface);
    min-height: 100vh;
  }
  a { color: var(--md-sys-color-primary); }

  /* Banner del laboratorio (para navegar entre prototipos) */
  .lab_banner {
    display: flex;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
    padding: 10px 20px;
    background-color: var(--md-sys-color-primary-container);
    color: var(--md-sys-color-on-primary-container);
    font-size: 0.85rem;
  }
  .lab_banner strong { font-weight: 700; }
  .lab_banner a { color: var(--md-sys-color-on-primary-container); font-weight: 500; text-decoration: none; }
  .lab_banner a:hover { text-decoration: underline; }
  .lab_banner .spacer { flex: 1; }

  /* Tarjeta M3 (surface + elevación + forma) reutilizable */
  .m3_card {
    background-color: var(--md-sys-color-surface-container-low);
    border-radius: 16px;
    box-shadow: 0 1px 2px rgba(0,0,0,.30), 0 1px 3px 1px rgba(0,0,0,.15);
  }
</style>
</head>
<body>
<div class="lab_banner">
  <strong>🧪 Prototipo Material 3</strong>
  <a href="/lab/login">Login</a>
  <a href="/lab/registro">Registro</a>
  <a href="/lab/panel">Panel</a>
  <span class="spacer"></span>
  <a href="/login">↩ Volver a la app real</a>
</div>
