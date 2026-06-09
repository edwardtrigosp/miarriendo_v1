-- ================================================================
--  miarriendo.online — Limpieza de DATOS DE PRUEBA
--
--  Borra todos los registros transaccionales (usuarios, propiedades,
--  contratos, etc.) PERO CONSERVA el catálogo de ubicaciones
--  (paises, departamentos, ciudades).
--
--  Uso (local, Docker):
--    docker compose cp database/limpiar_demo.sql db:/tmp/limpiar.sql
--    docker compose exec db sh -c 'mysql -u<USER> -p<PASS> <DB> < /tmp/limpiar.sql'
--
--  Reinicia los AUTO_INCREMENT (TRUNCATE) para empezar en 1.
-- ================================================================

SET FOREIGN_KEY_CHECKS = 0;

-- Orden no importa con FK_CHECKS=0, pero se listan de hijo a padre.
TRUNCATE TABLE pagos;
TRUNCATE TABLE contratos;
TRUNCATE TABLE alquileres;
TRUNCATE TABLE resenas;
TRUNCATE TABLE imagenes_propiedades;
TRUNCATE TABLE propiedades;
TRUNCATE TABLE direcciones;
TRUNCATE TABLE posts;
TRUNCATE TABLE usuarios;

-- NO se tocan: paises, departamentos, ciudades (catálogo).

SET FOREIGN_KEY_CHECKS = 1;
