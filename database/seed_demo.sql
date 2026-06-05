-- ================================================================
--  DATOS DE EJEMPLO (solo desarrollo, NO producción)
--  Requiere: paises/departamentos/ciudades ya cargados.
--  Ejecutar sobre una base limpia (tablas de datos truncadas).
--
--  Contraseña de TODOS los usuarios demo: clave1234
--  Ciudades usadas: BOGOTA D.C. = 160, MEDELLIN = 12
-- ================================================================

SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE contratos;
TRUNCATE TABLE alquileres;
TRUNCATE TABLE imagenes_propiedades;
TRUNCATE TABLE pagos;
TRUNCATE TABLE resenas;
TRUNCATE TABLE propiedades;
TRUNCATE TABLE direcciones;
TRUNCATE TABLE usuarios;
SET FOREIGN_KEY_CHECKS = 1;

-- ---- 1) USUARIOS (hash = 'clave1234') ----
INSERT INTO usuarios (usuario_id, nombre, apellidos, email, contrasena, telefono, rol, email_verificado) VALUES
(1, 'Edward', 'Parada', 'edwardtrigosp@gmail.com', '$2y$10$wNzJ6lRD.WVMWQwP9gc7V.4IMVzqJLMq1lmcXpgaonnCbPEzleRMu', '+573219092799', 'admin',   1),
(2, 'María',  'Gómez',  'maria@example.com',       '$2y$10$wNzJ6lRD.WVMWQwP9gc7V.4IMVzqJLMq1lmcXpgaonnCbPEzleRMu', '+573001112233', 'usuario', 1),
(3, 'Carlos', 'Ruiz',   'carlos@example.com',      '$2y$10$wNzJ6lRD.WVMWQwP9gc7V.4IMVzqJLMq1lmcXpgaonnCbPEzleRMu', '+573004445566', 'usuario', 1),
(4, 'Ana',    'Torres', 'ana@example.com',         '$2y$10$wNzJ6lRD.WVMWQwP9gc7V.4IMVzqJLMq1lmcXpgaonnCbPEzleRMu', '+573007778899', 'usuario', 1),
(5, 'Laura',  'Méndez', 'laura@example.com',       '$2y$10$wNzJ6lRD.WVMWQwP9gc7V.4IMVzqJLMq1lmcXpgaonnCbPEzleRMu', '+573012223344', 'usuario', 1);

-- ---- 2) DIRECCIONES ----
INSERT INTO direcciones (direccion_id, ciudad_id, calle, numero_exterior, barrio, codigo_postal, latitud, longitud) VALUES
(1, 160, 'Calle 63',      '#15-20', 'Chapinero',  '110231', 4.64860000, -74.06280000),
(2, 12,  'Carrera 70',    '#C2-15', 'Laureles',   '050031', 6.24470000, -75.60100000),
(3, 12,  'Carrera 43A',   '#6-15',  'El Poblado', '050021', 6.20860000, -75.56700000),
(4, 160, 'Avenida 9',     '#120-30','Cedritos',   '110121', 4.72000000, -74.03000000),
(5, 12,  'Calle 30 Sur',  '#32-10', 'Envigado',   '055422', 6.17000000, -75.59000000);

-- ---- 3) PROPIEDADES ----
-- Edward (1) es propietario de 1,2,3.  Laura (5) de 4,5.
INSERT INTO propiedades
(propiedad_id, propietario_id, direccion_id, titulo, descripcion, tipo_propiedad, num_habitaciones, num_banos, area_m2, precio_alquiler_mensual, deposito, disponible, amueblado, mascotas_permitidas) VALUES
(1, 1, 1, 'Apartamento en Chapinero',   'Acogedor apartamento cerca de la Zona G, con excelente iluminación.', 'apartamento', 2, 2, 75.00,  1800000, 1800000, 1, 1, 0),
(2, 1, 2, 'Casa en Laureles',           'Amplia casa familiar en uno de los mejores barrios de Medellín.',     'casa',        4, 3, 180.00, 2500000, 2500000, 1, 0, 1),
(3, 1, 3, 'Apartaestudio en El Poblado','Moderno apartaestudio ideal para profesionales.',                     'apartaestudio',1, 1, 40.00,  1200000, 1200000, 0, 1, 0),
(4, 5, 4, 'Apartamento en Cedritos',    'Tranquilo apartamento en el norte de Bogotá, cerca de todo.',         'apartamento', 3, 2, 95.00,  1500000, 1500000, 0, 1, 0),
(5, 5, 5, 'Casa en Envigado',           'Hermosa casa con jardín y zona BBQ en Envigado.',                     'casa',        3, 3, 150.00, 2000000, 2000000, 1, 1, 1);

-- ---- 4) ALQUILERES (uno por contrato; 1:1) ----
INSERT INTO alquileres (alquiler_id, propiedad_id, inquilino_id, fecha_inicio, fecha_fin, precio_mensual, deposito, estado) VALUES
(1, 3, 2, '2026-05-20', '2027-05-20', 1200000, 1200000, 'activo'),     -- C1: María arrienda El Poblado (de Edward)
(2, 1, 2, '2026-07-01', '2027-07-01', 1800000, 1800000, 'pendiente'),  -- C2: María solicita Chapinero
(3, 1, 3, '2026-07-01', '2027-07-01', 1800000, 1800000, 'pendiente'),  -- C3: Carlos solicita Chapinero
(4, 2, 4, '2026-07-15', '2027-07-15', 2500000, 2500000, 'pendiente'),  -- C4: Ana solicita Laureles
(5, 4, 1, '2026-05-10', '2027-05-10', 1500000, 1500000, 'activo'),     -- C5: Edward arrienda Cedritos (de Laura)
(6, 5, 1, '2026-08-01', '2027-08-01', 2000000, 2000000, 'pendiente');  -- C6: Edward solicita Envigado

-- ---- 5) CONTRATOS ----
SET @cl = 'Contrato de arrendamiento de vivienda urbana. El arrendatario se obliga a pagar el canon mensual dentro de los primeros cinco (5) días de cada mes y a conservar el inmueble en buen estado.';
INSERT INTO contratos
(alquiler_id, propietario_id, inquilino_id, clausulas, monto_mensual, deposito, fecha_inicio, fecha_fin, duracion_meses, estado, aceptado, fecha_aceptacion, firma_inquilino, ip_aceptacion, created_at) VALUES
(1, 1, 2, @cl, 1200000, 1200000, '2026-05-20', '2027-05-20', 12, 'aceptado',  1, NOW() - INTERVAL 15 DAY, 'María Gómez',   '127.0.0.1', NOW() - INTERVAL 15 DAY),
(2, 1, 2, @cl, 1800000, 1800000, '2026-07-01', '2027-07-01', 12, 'borrador',  0, NULL, NULL, NULL, NOW() - INTERVAL 4 DAY),
(3, 1, 3, @cl, 1800000, 1800000, '2026-07-01', '2027-07-01', 12, 'borrador',  0, NULL, NULL, NULL, NOW() - INTERVAL 3 DAY),
(4, 1, 4, @cl, 2500000, 2500000, '2026-07-15', '2027-07-15', 12, 'borrador',  0, NULL, NULL, NULL, NOW() - INTERVAL 2 DAY),
(5, 5, 1, @cl, 1500000, 1500000, '2026-05-10', '2027-05-10', 12, 'aceptado',  1, NOW() - INTERVAL 10 DAY, 'Edward Parada', '127.0.0.1', NOW() - INTERVAL 10 DAY),
(6, 5, 1, @cl, 2000000, 2000000, '2026-08-01', '2027-08-01', 12, 'borrador',  0, NULL, NULL, NULL, NOW() - INTERVAL 1 DAY);
