<?php

/**
 * Modelo Alquiler: tabla `alquileres` (contratos operativos de arriendo).
 */
class Alquiler
{
    /** Lista los arriendos de un inquilino, con datos de la propiedad. */
    public static function listarPorInquilino(int $inquilinoId): array
    {
        $sql = "SELECT a.alquiler_id, a.fecha_inicio, a.fecha_fin, a.precio_mensual, a.estado,
                       p.titulo, c.nombre AS ciudad
                FROM alquileres a
                JOIN propiedades p ON a.propiedad_id = p.propiedad_id
                JOIN direcciones d ON p.direccion_id = d.direccion_id
                JOIN ciudades    c ON d.ciudad_id    = c.ciudad_id
                WHERE a.inquilino_id = :id
                ORDER BY a.fecha_inicio DESC";
        $stmt = Database::conexion()->prepare($sql);
        $stmt->execute([':id' => $inquilinoId]);
        return $stmt->fetchAll();
    }

    /** Cuenta los arriendos activos de un inquilino. */
    public static function contarActivosPorInquilino(int $inquilinoId): int
    {
        $stmt = Database::conexion()->prepare(
            "SELECT COUNT(*) FROM alquileres WHERE inquilino_id = :id AND estado = 'activo'"
        );
        $stmt->execute([':id' => $inquilinoId]);
        return (int) $stmt->fetchColumn();
    }
}
