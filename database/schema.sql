-- ================================================================
--  miarriendo.online — Esquema de base de datos (MySQL)
--  Generado a partir del diagrama ER del proyecto.
--
--  Importar en phpMyAdmin:
--    1) Crea/selecciona la base de datos
--    2) Pestaña "Importar" → sube este archivo
--
--  Notas:
--   - Motor InnoDB (soporta claves foráneas)
--   - utf8mb4 (acentos, ñ y emojis)
--   - Los CHECK requieren MySQL 8.0.16+ (en versiones viejas se ignoran)
-- ================================================================

-- Si desarrollas en local (Docker) puedes descomentar estas dos líneas.
-- En cPanel normalmente la base ya está creada, así que se omiten.
-- CREATE DATABASE IF NOT EXISTS miarriendo CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE miarriendo;

SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------------------------------------------
-- 1) PAISES
-- ----------------------------------------------------------------
CREATE TABLE paises (
    pais_id     INT AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(100) NOT NULL UNIQUE,
    codigo_iso  CHAR(2)      NOT NULL UNIQUE,
    created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------
-- 2) DEPARTAMENTOS  (pertenece a un país)
-- ----------------------------------------------------------------
CREATE TABLE departamentos (
    departamento_id INT AUTO_INCREMENT PRIMARY KEY,
    pais_id         INT          NOT NULL,
    nombre          VARCHAR(100) NOT NULL,
    codigo          VARCHAR(10)  NOT NULL,
    created_at      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_departamentos_pais
        FOREIGN KEY (pais_id) REFERENCES paises (pais_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------
-- 3) CIUDADES  (pertenece a un departamento)
-- ----------------------------------------------------------------
CREATE TABLE ciudades (
    ciudad_id        INT AUTO_INCREMENT PRIMARY KEY,
    departamento_id  INT          NOT NULL,
    nombre           VARCHAR(100) NOT NULL,
    created_at       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_ciudades_departamento
        FOREIGN KEY (departamento_id) REFERENCES departamentos (departamento_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------
-- 4) DIRECCIONES  (pertenece a una ciudad; guarda lat/long)
-- ----------------------------------------------------------------
CREATE TABLE direcciones (
    direccion_id     INT AUTO_INCREMENT PRIMARY KEY,
    ciudad_id        INT           NOT NULL,
    calle            VARCHAR(255)  NOT NULL,
    numero_exterior  VARCHAR(20),
    barrio           VARCHAR(100),
    codigo_postal    VARCHAR(10),
    referencia       VARCHAR(255),
    latitud          DECIMAL(10,8),
    longitud         DECIMAL(11,8),
    created_at       DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at       DATETIME      DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_direcciones_ciudad
        FOREIGN KEY (ciudad_id) REFERENCES ciudades (ciudad_id),
    INDEX idx_direcciones_geo (latitud, longitud) -- acelera la búsqueda por cercanía
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------
-- 5) USUARIOS
-- ----------------------------------------------------------------
CREATE TABLE usuarios (
    usuario_id        INT AUTO_INCREMENT PRIMARY KEY,
    nombre            VARCHAR(100) NOT NULL,
    apellidos         VARCHAR(100) NOT NULL,
    email             VARCHAR(255) NOT NULL UNIQUE,
    contrasena        VARCHAR(256) NOT NULL,        -- hash con password_hash()
    telefono          VARCHAR(20),
    rol               ENUM('usuario','admin') NOT NULL DEFAULT 'usuario', -- permisos (blog/admin)
    email_verificado  TINYINT(1)   NOT NULL DEFAULT 0,
    activo            TINYINT(1)   NOT NULL DEFAULT 1,
    ultimo_acceso     DATETIME,
    fecha_registro    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_at        DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at        DATETIME     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------
-- 6) PROPIEDADES  (de un propietario, en una dirección)
-- ----------------------------------------------------------------
CREATE TABLE propiedades (
    propiedad_id            INT AUTO_INCREMENT PRIMARY KEY,
    propietario_id          INT           NOT NULL,
    direccion_id            INT           NOT NULL,
    titulo                  VARCHAR(200)  NOT NULL,
    descripcion             TEXT,
    tipo_propiedad          VARCHAR(50)   NOT NULL,
    num_habitaciones        INT,
    num_banos               INT,
    area_m2                 DECIMAL(10,2),
    precio_alquiler_mensual DECIMAL(10,2) NOT NULL,
    deposito                DECIMAL(10,2),
    disponible              TINYINT(1)    NOT NULL DEFAULT 1,
    amueblado               TINYINT(1)    NOT NULL DEFAULT 0,
    mascotas_permitidas     TINYINT(1)    NOT NULL DEFAULT 0,
    clausulas_contrato      MEDIUMTEXT,                 -- cláusulas extra del contrato (las define el dueño al publicar)
    archivada               TINYINT(1)    NOT NULL DEFAULT 0,  -- borrado lógico (no se elimina, se oculta)
    created_at              DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at              DATETIME      DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_propiedades_propietario
        FOREIGN KEY (propietario_id) REFERENCES usuarios (usuario_id),
    CONSTRAINT fk_propiedades_direccion
        FOREIGN KEY (direccion_id) REFERENCES direcciones (direccion_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------
-- 7) IMAGENES_PROPIEDADES  (galería de fotos de una propiedad)
-- ----------------------------------------------------------------
CREATE TABLE imagenes_propiedades (
    imagen_id     INT AUTO_INCREMENT PRIMARY KEY,
    propiedad_id  INT          NOT NULL,
    url_imagen    VARCHAR(500) NOT NULL,
    descripcion   VARCHAR(255),
    orden         INT          NOT NULL DEFAULT 0,
    es_principal  TINYINT(1)   NOT NULL DEFAULT 0,
    fecha_subida  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_imagenes_propiedad
        FOREIGN KEY (propiedad_id) REFERENCES propiedades (propiedad_id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------
-- 8) RESENAS  (un usuario reseña una propiedad; N:M)
-- ----------------------------------------------------------------
CREATE TABLE resenas (
    resena_id     INT AUTO_INCREMENT PRIMARY KEY,
    propiedad_id  INT       NOT NULL,
    usuario_id    INT       NOT NULL,
    calificacion  INT       NOT NULL,
    comentario    TEXT,
    fecha_resena  DATETIME  NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_resenas_propiedad
        FOREIGN KEY (propiedad_id) REFERENCES propiedades (propiedad_id),
    CONSTRAINT fk_resenas_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios (usuario_id),
    CONSTRAINT chk_resenas_calificacion
        CHECK (calificacion BETWEEN 1 AND 5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------
-- 9) ALQUILERES  (contrato operativo entre propiedad e inquilino; N:M)
-- ----------------------------------------------------------------
CREATE TABLE alquileres (
    alquiler_id     INT AUTO_INCREMENT PRIMARY KEY,
    propiedad_id    INT           NOT NULL,
    inquilino_id    INT           NOT NULL,
    fecha_inicio    DATE          NOT NULL,
    fecha_fin       DATE          NOT NULL,
    precio_mensual  DECIMAL(10,2) NOT NULL,
    deposito        DECIMAL(10,2),
    estado          ENUM('pendiente','activo','finalizado','cancelado') NOT NULL DEFAULT 'pendiente',
    created_at      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME      DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_alquileres_propiedad
        FOREIGN KEY (propiedad_id) REFERENCES propiedades (propiedad_id),
    CONSTRAINT fk_alquileres_inquilino
        FOREIGN KEY (inquilino_id) REFERENCES usuarios (usuario_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------
-- 10) PAGOS  (pagos de un alquiler)
-- ----------------------------------------------------------------
CREATE TABLE pagos (
    pago_id             INT AUTO_INCREMENT PRIMARY KEY,
    alquiler_id         INT           NOT NULL,
    monto               DECIMAL(10,2) NOT NULL,
    fecha_pago          DATE          NOT NULL,
    metodo_pago         VARCHAR(50)   NOT NULL,
    estado_pago         ENUM('pendiente','completado','fallido') NOT NULL DEFAULT 'pendiente',
    referencia_externa  VARCHAR(100),
    created_at          DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_pagos_alquiler
        FOREIGN KEY (alquiler_id) REFERENCES alquileres (alquiler_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------
-- 11) CONTRATOS  (contrato legal firmable; 1:1 con un alquiler)
-- ----------------------------------------------------------------
CREATE TABLE contratos (
    contrato_id       INT AUTO_INCREMENT PRIMARY KEY,
    alquiler_id       INT           NOT NULL UNIQUE,   -- 1:1 con alquileres
    propietario_id    INT           NOT NULL,
    inquilino_id      INT           NOT NULL,
    clausulas         MEDIUMTEXT    NOT NULL,
    monto_mensual     DECIMAL(10,2) NOT NULL,
    deposito          DECIMAL(10,2),
    fecha_inicio      DATE          NOT NULL,
    fecha_fin         DATE          NOT NULL,
    duracion_meses    INT,
    estado            ENUM('borrador','enviado','aceptado','rechazado','anulado') NOT NULL DEFAULT 'borrador',
    aceptado          TINYINT(1)    NOT NULL DEFAULT 0,
    fecha_aceptacion  DATETIME,
    firma_inquilino   VARCHAR(255),                    -- nombre completo tecleado
    ip_aceptacion     VARCHAR(45),                     -- rastro legal
    hash_documento    CHAR(64),                        -- SHA-256 de las cláusulas firmadas
    url_pdf           VARCHAR(500),                    -- PDF descargable del contrato
    created_at        DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at        DATETIME      DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_contratos_alquiler
        FOREIGN KEY (alquiler_id) REFERENCES alquileres (alquiler_id),
    CONSTRAINT fk_contratos_propietario
        FOREIGN KEY (propietario_id) REFERENCES usuarios (usuario_id),
    CONSTRAINT fk_contratos_inquilino
        FOREIGN KEY (inquilino_id) REFERENCES usuarios (usuario_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------
-- 12) POSTS  (blog / mini-CMS; escritos por un usuario admin)
-- ----------------------------------------------------------------
CREATE TABLE posts (
    post_id            INT AUTO_INCREMENT PRIMARY KEY,
    autor_id           INT          NOT NULL,
    titulo             VARCHAR(200) NOT NULL,
    slug               VARCHAR(220) NOT NULL UNIQUE,   -- URL amigable: /blog/mi-articulo
    categoria          VARCHAR(50),
    extracto           VARCHAR(300),                   -- resumen para las tarjetas
    contenido          MEDIUMTEXT   NOT NULL,          -- HTML del editor Quill
    imagen_portada     VARCHAR(255),
    estado             ENUM('borrador','publicado') NOT NULL DEFAULT 'borrador',
    fecha_publicacion  DATETIME,
    created_at         DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at         DATETIME     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_posts_autor
        FOREIGN KEY (autor_id) REFERENCES usuarios (usuario_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
