# docs/ — Artefactos de diseño (NO es código de la app)

Esta carpeta contiene documentación y recursos del **sistema de diseño**.
No la usa ningún PHP y **no debe subirse al servidor de producción**.

## Contenido

- **design-tokens.json** — Tokens (colores, radios, sombras, tipografía, espaciado)
  en formato **Tokens Studio for Figma**. Sirve para crear las Variables de Figma.

## Cómo se mantiene

La **fuente de verdad del código** son las variables CSS en
`public/css/global.css` (`:root { --primary_color: ... }`).

`design-tokens.json` es un **espejo manual** de esas variables para Figma.
Cuando cambies un token en `global.css`, actualiza también este archivo
(es la única forma de mantenerlos sincronizados; no hay generación automática).

> Regla práctica: si tocas `:root` en `global.css`, revisa este JSON en el mismo commit.

## Por qué NO se despliega

- No es código ejecutable ni un asset que la web necesite.
- Vive fuera de `public/` (el docroot), así que aunque se subiera por error
  **nunca sería accesible por URL**.
- Al desplegar a cPanel, sube solo lo necesario para correr la app
  (`public/`, `src/`, `database/` si aplica). La carpeta `docs/` se omite.
