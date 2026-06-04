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
     * Columnas comunes + ciudad + imagen principal para las tarjetas.
     * $columnaExtra permite añadir una columna calculada al SELECT (ej. distancia).
     */
    private static function selectTarjeta(string $columnaExtra = ''): string
    {
        $extra = $columnaExtra !== '' ? ', ' . $columnaExtra : '';
        return "SELECT
                    p.propiedad_id, p.titulo, p.precio_alquiler_mensual, p.tipo_propiedad,
                    p.disponible, d.calle, d.numero_exterior, d.latitud, d.longitud,
                    c.nombre AS ciudad, c.departamento_id,
                    (SELECT ip.url_imagen FROM imagenes_propiedades ip
                      WHERE ip.propiedad_id = p.propiedad_id AND ip.es_principal = 1
                      LIMIT 1) AS imagen" . $extra . "
                FROM propiedades p
                JOIN direcciones d ON p.direccion_id = d.direccion_id
                JOIN ciudades    c ON d.ciudad_id    = c.ciudad_id";
    }

    /**
     * Lista las propiedades disponibles (para /arriendos), con filtros opcionales.
     *
     * Filtros admitidos en $f:
     *   q (texto), departamento_id, ciudad_id, tipo, precio_min, precio_max,
     *   orden ('recientes'|'precio_asc'|'precio_desc'|'cercania'),
     *   lat, lon (para ordenar por cercanía con Haversine).
     */
    public static function listarDisponibles(array $f = []): array
    {
        $where  = ['p.disponible = 1'];
        $params = [];
        $columnaExtra = '';

        if (!empty($f['q'])) {
            $where[] = 'p.titulo LIKE :q';
            $params[':q'] = '%' . $f['q'] . '%';
        }
        if (!empty($f['departamento_id'])) {
            $where[] = 'c.departamento_id = :depto';
            $params[':depto'] = (int) $f['departamento_id'];
        }
        if (!empty($f['ciudad_id'])) {
            $where[] = 'd.ciudad_id = :ciudad';
            $params[':ciudad'] = (int) $f['ciudad_id'];
        }
        if (!empty($f['tipo'])) {
            $where[] = 'p.tipo_propiedad = :tipo';
            $params[':tipo'] = $f['tipo'];
        }
        if (isset($f['precio_min']) && $f['precio_min'] !== '') {
            $where[] = 'p.precio_alquiler_mensual >= :pmin';
            $params[':pmin'] = (float) $f['precio_min'];
        }
        if (isset($f['precio_max']) && $f['precio_max'] !== '') {
            $where[] = 'p.precio_alquiler_mensual <= :pmax';
            $params[':pmax'] = (float) $f['precio_max'];
        }

        // Orden por cercanía (Haversine) si llegan coordenadas válidas
        $orden = $f['orden'] ?? 'recientes';
        $tieneCoords = isset($f['lat'], $f['lon']) && is_numeric($f['lat']) && is_numeric($f['lon']);
        if ($orden === 'cercania' && $tieneCoords) {
            $columnaExtra = "(6371 * acos(
                            cos(radians(:lat1)) * cos(radians(d.latitud)) *
                            cos(radians(d.longitud) - radians(:lon1)) +
                            sin(radians(:lat2)) * sin(radians(d.latitud))
                        )) AS distancia";
            $params[':lat1'] = (float) $f['lat'];
            $params[':lon1'] = (float) $f['lon'];
            $params[':lat2'] = (float) $f['lat'];
            // Solo propiedades geolocalizadas pueden ordenarse por distancia
            $where[] = 'd.latitud IS NOT NULL AND d.longitud IS NOT NULL';
            $orderBy = 'distancia ASC';
        } else {
            $orderBy = match ($orden) {
                'precio_asc'  => 'p.precio_alquiler_mensual ASC',
                'precio_desc' => 'p.precio_alquiler_mensual DESC',
                default       => 'p.created_at DESC',
            };
        }

        $sql = self::selectTarjeta($columnaExtra) . ' WHERE ' . implode(' AND ', $where) . ' ORDER BY ' . $orderBy;
        $stmt = Database::conexion()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
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

        // Distancia legible si la consulta la calculó (orden por cercanía)
        $distancia = null;
        if (isset($p['distancia']) && is_numeric($p['distancia'])) {
            $km = (float) $p['distancia'];
            $distancia = $km < 1 ? round($km * 1000) . ' m' : number_format($km, 1, ',', '.') . ' km';
        }

        return [
            'id'        => $p['propiedad_id'],
            'titulo'    => $p['titulo'],
            'precio'    => $p['precio_alquiler_mensual'],
            'direccion' => $direccion,
            'imagen'    => $p['imagen'] ?: 'https://images.unsplash.com/photo-1568605114967-8130f3a36994?auto=format&fit=crop&w=500&q=80',
            'estado'    => ((int) $p['disponible'] === 1) ? 'Disponible' : 'No disponible',
            'distancia' => $distancia,
        ];
    }
}
