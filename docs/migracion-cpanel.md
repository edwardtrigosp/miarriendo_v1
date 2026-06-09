# Migración a cPanel — miarriendo.online

Guía paso a paso. Marca cada casilla al completarla.

## Fase 1 — Base de datos (phpMyAdmin)

- [ ] **Crear la base** en cPanel → *MySQL® Databases*. Anota el nombre real
      (cPanel le antepone tu usuario, p. ej. `usuario_miarriendo`).
- [ ] **Crear un usuario MySQL** y asignarlo a esa base con **ALL PRIVILEGES**.
      Anota usuario y contraseña.
- [ ] Entrar a **phpMyAdmin**, seleccionar la base, pestaña **Importar** y subir
      **EN ESTE ORDEN**:
  1. [ ] `database/schema.sql` — estructura (tablas vacías)
  2. [ ] `database/seed_ubicaciones.sql` — catálogo: 1 país, 33 deptos, 1.120 ciudades
  3. [ ] `database/seed_prod.sql` — usuario admin (Edward)  ⚠️ *no está en Git, súbelo aparte*
- [ ] Verificar en phpMyAdmin: `usuarios` = 1 fila (rol admin), `ciudades` = 1120.

> ❌ NO importar `seed_demo.sql` (datos de prueba).

## Fase 2 — Archivos de la app (estrategia: SUBCARPETA, sin tocar config global)

Hosting COMPARTIDO: no se crean subdominios ni se cambia config de la cuenta.
La app vive en una subcarpeta y se sirve por el dominio principal:
```
URL pública:  https://<dominio-de-la-cuenta>/expoingtech/20261/miarriendo/public/
```

Estructura final en el servidor:
```
/home/drappsco/public_html/expoingtech/20261/miarriendo/   <- raíz del proyecto
    .htaccess        (protege dotfiles como .env)
    .env             (lo creas en el server, Fase 3)
    public/          <-- punto de entrada (index.php) — es la URL pública
        index.php  css/  js/  uploads/  favicon.svg
    src/   (.htaccess deny all)
    database/ (.htaccess deny all)
```

- [ ] Subir TODO el proyecto dentro de `.../miarriendo/` (File Manager ZIP, Git o FTP).
- [ ] En File Manager activar **Settings → Show Hidden Files (dotfiles)** para que se
      vean/suban los `.htaccess`. Confirmar que existen:
      `miarriendo/.htaccess`, `miarriendo/src/.htaccess`, `miarriendo/database/.htaccess`.
- [ ] (Opcional) Borrar el `index.html` de marcador que había en `.../miarriendo/`.
- [ ] **NO subir**: `docker-compose.yml`, `Dockerfile`, `/docs`, `/.claude`,
      `database/seed_demo.sql`. (El `.env` y `seed_prod.sql` NO están en Git: súbelos aparte.)
- [ ] Permisos de escritura (755/775) a `public/uploads/propiedades` y `public/uploads/usuarios`.
- [ ] Verificar la **versión de PHP** del hosting (read-only): debe ser **8.0+**
      (el código usa `enum`, `match`, `str_starts_with`). Si es menor, pedir al
      administrador que la suba (no se toca desde aquí).

### Prefijo de ruta (ya resuelto en el código)
La app usa rutas absolutas (`/css`, `/panel`...). Al vivir en subcarpeta, se
define en el `.env` el prefijo y el código lo aplica solo (Router + redirect +
filtro de salida). En local queda vacío, así que NO afecta a desarrollo.

### Seguridad ya resuelta en el código
- `.htaccess` raíz: bloquea dotfiles (`.env`) por nombre — no afecta a `public/`.
- `src/.htaccess` y `database/.htaccess`: deniegan todo (esas carpetas no se sirven).
- ⚠️ No se pone "deny all" en la raíz: Apache lee los `.htaccess` por encima del
  docroot (con AllowOverride All) y rompería la app (probado → daba 403).

## Fase 3 — Configuración (.env de producción)

Crear un `.env` EN EL SERVIDOR (no se versiona) con los datos reales:

```
DB_HOST=localhost
DB_PORT=3306
DB_NAME=usuario_miarriendo
DB_USER=usuario_dbuser
DB_PASSWORD=********
DB_ROOT_PASSWORD=
GOOGLE_MAPS_API_KEY=AIzaSy...   # la misma clave (o una restringida al dominio)
APP_ENV=production              # oculta errores y los registra en log
APP_BASE_URL=/expoingtech/20261/miarriendo/public   # prefijo de la subcarpeta
```

> `APP_BASE_URL` debe ser EXACTAMENTE la ruta tras el dominio hasta `public`
> (sin barra final). Si la URL pública fuera otra carpeta, ajústala aquí.

## Fase 4 — Verificación post-deploy

- [ ] Abrir `https://<dominio>/expoingtech/20261/miarriendo/public/` → se ve el landing
      con estilos (si el CSS no carga, revisa que `APP_BASE_URL` sea exacto).
- [ ] Iniciar sesión con `edwardtrigosp@gmail.com` / `clave1234` → entra al panel.
- [ ] **Cambiar la contraseña del admin** (o actualizar el hash en phpMyAdmin).
- [ ] Publicar una propiedad de prueba (verifica subida de imágenes y mapa).
- [ ] Revisar que `/blog` permita gestionar posts como admin.
- [ ] Confirmar que los errores NO se muestran en pantalla (APP_ENV=production).

## Notas de seguridad

- `.env` y `database/seed_prod.sql` contienen secretos → nunca a Git.
- En `production`, `index.php` oculta los errores y solo los registra.
- Cookies de sesión: `secure` se activa solo con HTTPS (asegura el SSL del dominio).
