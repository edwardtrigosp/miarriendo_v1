<?php

/**
 * Modelo Propiedad: tabla `propiedades`.
 */
class Propiedad
{
    /** Crea una propiedad y devuelve su ID. */
    public static function crear(array $d): int
    {
        $pdo = Database::conexion();
        $sql = "INSERT INTO propiedades
                    (propietario_id, direccion_id, titulo, descripcion, tipo_propiedad,
                     num_habitaciones, num_banos, area_m2, precio_alquiler_mensual, deposito,
                     disponible, amueblado, mascotas_permitidas)
                VALUES
                    (:propietario_id, :direccion_id, :titulo, :descripcion, :tipo_propiedad,
                     :num_habitaciones, :num_banos, :area_m2, :precio_alquiler_mensual, :deposito,
                     :disponible, :amueblado, :mascotas_permitidas)";
        $pdo->prepare($sql)->execute([
            ':propietario_id'          => $d['propietario_id'],
            ':direccion_id'            => $d['direccion_id'],
            ':titulo'                  => $d['titulo'],
            ':descripcion'             => $d['descripcion'] ?? null,
            ':tipo_propiedad'          => $d['tipo_propiedad'],
            ':num_habitaciones'        => $d['num_habitaciones'] ?? null,
            ':num_banos'               => $d['num_banos'] ?? null,
            ':area_m2'                 => $d['area_m2'] ?? null,
            ':precio_alquiler_mensual' => $d['precio_alquiler_mensual'],
            ':deposito'                => $d['deposito'] ?? null,
            ':disponible'              => $d['disponible'],
            ':amueblado'               => $d['amueblado'],
            ':mascotas_permitidas'     => $d['mascotas_permitidas'],
        ]);
        return (int) $pdo->lastInsertId();
    }

    /**
     * Lista las propiedades disponibles con su ciudad e imagen principal.
     * @return array
     */
    public static function listarDisponibles(): array
    {
        $sql = "SELECT
                    p.propiedad_id,
                    p.titulo,
                    p.precio_alquiler_mensual,
                    p.tipo_propiedad,
                    d.calle,
                    d.numero_exterior,
                    c.nombre AS ciudad,
                    (SELECT ip.url_imagen
                       FROM imagenes_propiedades ip
                      WHERE ip.propiedad_id = p.propiedad_id AND ip.es_principal = 1
                      LIMIT 1) AS imagen
                FROM propiedades p
                JOIN direcciones d ON p.direccion_id = d.direccion_id
                JOIN ciudades    c ON d.ciudad_id    = c.ciudad_id
                WHERE p.disponible = 1
                ORDER BY p.created_at DESC";
        return Database::conexion()->query($sql)->fetchAll();
    }
}
