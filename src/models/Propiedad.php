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

    /** Columnas comunes + ciudad + imagen principal para las tarjetas. */
    private static function selectTarjeta(): string
    {
        return "SELECT
                    p.propiedad_id, p.titulo, p.precio_alquiler_mensual, p.tipo_propiedad,
                    p.disponible, d.calle, d.numero_exterior, c.nombre AS ciudad,
                    (SELECT ip.url_imagen FROM imagenes_propiedades ip
                      WHERE ip.propiedad_id = p.propiedad_id AND ip.es_principal = 1
                      LIMIT 1) AS imagen
                FROM propiedades p
                JOIN direcciones d ON p.direccion_id = d.direccion_id
                JOIN ciudades    c ON d.ciudad_id    = c.ciudad_id";
    }

    /** Lista las propiedades disponibles (para /arriendos). */
    public static function listarDisponibles(): array
    {
        $sql = self::selectTarjeta() . " WHERE p.disponible = 1 ORDER BY p.created_at DESC";
        return Database::conexion()->query($sql)->fetchAll();
    }

    /** Lista TODAS las propiedades de un propietario (disponibles o no). */
    public static function listarPorPropietario(int $propietarioId): array
    {
        $sql = self::selectTarjeta() . " WHERE p.propietario_id = :id ORDER BY p.created_at DESC";
        $stmt = Database::conexion()->prepare($sql);
        $stmt->execute([':id' => $propietarioId]);
        return $stmt->fetchAll();
    }

    /** Busca una propiedad por su ID con todos sus detalles. Devuelve null si no existe. */
    public static function buscarPorId(int $id): ?array
    {
        $sql = "SELECT p.*,
                       d.calle, d.numero_exterior, d.barrio, d.codigo_postal, d.referencia,
                       d.latitud, d.longitud,
                       c.nombre AS ciudad, dep.nombre AS departamento,
                       u.nombre AS propietario_nombre, u.apellidos AS propietario_apellidos,
                       u.telefono AS propietario_telefono, u.email AS propietario_email
                FROM propiedades p
                JOIN direcciones  d   ON p.direccion_id    = d.direccion_id
                JOIN ciudades     c   ON d.ciudad_id       = c.ciudad_id
                JOIN departamentos dep ON c.departamento_id = dep.departamento_id
                JOIN usuarios     u   ON p.propietario_id  = u.usuario_id
                WHERE p.propiedad_id = :id";
        $stmt = Database::conexion()->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    /** Convierte una fila de la BD al formato que esperan las tarjetas. */
    public static function formatearParaTarjeta(array $p): array
    {
        $direccion = trim($p['calle'] . ' ' . ($p['numero_exterior'] ?? '')) . ', ' . $p['ciudad'];
        return [
            'id'        => $p['propiedad_id'],
            'titulo'    => $p['titulo'],
            'precio'    => $p['precio_alquiler_mensual'],
            'direccion' => $direccion,
            'imagen'    => $p['imagen'] ?: 'https://images.unsplash.com/photo-1568605114967-8130f3a36994?auto=format&fit=crop&w=500&q=80',
            'estado'    => ((int) $p['disponible'] === 1) ? 'Disponible' : 'No disponible',
        ];
    }
}
