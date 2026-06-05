<?php

/**
 * Modelo Contrato: tabla `contratos` (contrato legal 1:1 con un alquiler).
 *
 * Ciclo de estados:
 *   borrador  -> el inquilino solicitó (a la espera del propietario)
 *   enviado   -> el propietario aprobó (a la espera de la firma del inquilino)
 *   aceptado  -> el inquilino firmó (alquiler activo)
 *   rechazado -> el propietario rechazó (alquiler cancelado)
 *   anulado   -> anulado posteriormente
 */
class Contrato
{
    /** Crea un contrato y devuelve su ID. */
    public static function crear(array $d): int
    {
        $sql = "INSERT INTO contratos
                    (alquiler_id, propietario_id, inquilino_id, clausulas, monto_mensual,
                     deposito, fecha_inicio, fecha_fin, duracion_meses, estado)
                VALUES
                    (:alquiler_id, :propietario_id, :inquilino_id, :clausulas, :monto_mensual,
                     :deposito, :fecha_inicio, :fecha_fin, :duracion_meses, :estado)";
        $pdo = Database::conexion();
        $pdo->prepare($sql)->execute([
            ':alquiler_id'    => $d['alquiler_id'],
            ':propietario_id' => $d['propietario_id'],
            ':inquilino_id'   => $d['inquilino_id'],
            ':clausulas'      => $d['clausulas'],
            ':monto_mensual'  => $d['monto_mensual'],
            ':deposito'       => $d['deposito'] ?? null,
            ':fecha_inicio'   => $d['fecha_inicio'],
            ':fecha_fin'      => $d['fecha_fin'],
            ':duracion_meses' => $d['duracion_meses'] ?? null,
            ':estado'         => $d['estado'] ?? 'borrador',
        ]);
        return (int) $pdo->lastInsertId();
    }

    /** Cambia el estado de un contrato. */
    public static function cambiarEstado(int $contratoId, string $estado): void
    {
        $stmt = Database::conexion()->prepare(
            "UPDATE contratos SET estado = :estado WHERE contrato_id = :id"
        );
        $stmt->execute([':estado' => $estado, ':id' => $contratoId]);
    }

    /**
     * ¿El inquilino ya tiene una solicitud viva (no rechazada/anulada) para esta propiedad?
     * Evita solicitudes duplicadas.
     */
    public static function existeSolicitudViva(int $propiedadId, int $inquilinoId): bool
    {
        $sql = "SELECT 1
                FROM contratos c
                JOIN alquileres a ON c.alquiler_id = a.alquiler_id
                WHERE a.propiedad_id = :prop AND c.inquilino_id = :inq
                  AND c.estado IN ('borrador','enviado','aceptado')
                LIMIT 1";
        $stmt = Database::conexion()->prepare($sql);
        $stmt->execute([':prop' => $propiedadId, ':inq' => $inquilinoId]);
        return (bool) $stmt->fetchColumn();
    }

    /** Busca un contrato por su ID con datos de propiedad, propietario e inquilino. */
    public static function buscarPorId(int $id): ?array
    {
        $sql = "SELECT c.*, a.propiedad_id, a.estado AS alquiler_estado,
                       p.titulo AS propiedad_titulo,
                       pr.nombre AS propietario_nombre, pr.apellidos AS propietario_apellidos,
                       inq.nombre AS inquilino_nombre, inq.apellidos AS inquilino_apellidos,
                       inq.email AS inquilino_email
                FROM contratos c
                JOIN alquileres a  ON c.alquiler_id    = a.alquiler_id
                JOIN propiedades p ON a.propiedad_id   = p.propiedad_id
                JOIN usuarios pr   ON c.propietario_id = pr.usuario_id
                JOIN usuarios inq  ON c.inquilino_id   = inq.usuario_id
                WHERE c.contrato_id = :id";
        $stmt = Database::conexion()->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    /** Solicitudes/contratos recibidos por un propietario (todas las propiedades). */
    public static function listarPorPropietario(int $propietarioId): array
    {
        $sql = "SELECT c.contrato_id, c.estado, c.monto_mensual, c.fecha_inicio, c.fecha_fin,
                       p.titulo AS propiedad_titulo,
                       inq.nombre AS inquilino_nombre, inq.apellidos AS inquilino_apellidos
                FROM contratos c
                JOIN alquileres a  ON c.alquiler_id  = a.alquiler_id
                JOIN propiedades p ON a.propiedad_id = p.propiedad_id
                JOIN usuarios inq  ON c.inquilino_id = inq.usuario_id
                WHERE c.propietario_id = :id
                ORDER BY c.created_at DESC";
        $stmt = Database::conexion()->prepare($sql);
        $stmt->execute([':id' => $propietarioId]);
        return $stmt->fetchAll();
    }

    /** Contratos/solicitudes hechos por un inquilino. */
    public static function listarPorInquilino(int $inquilinoId): array
    {
        $sql = "SELECT c.contrato_id, c.estado, c.monto_mensual, c.fecha_inicio, c.fecha_fin,
                       p.titulo AS propiedad_titulo,
                       pr.nombre AS propietario_nombre, pr.apellidos AS propietario_apellidos
                FROM contratos c
                JOIN alquileres a  ON c.alquiler_id    = a.alquiler_id
                JOIN propiedades p ON a.propiedad_id   = p.propiedad_id
                JOIN usuarios pr   ON c.propietario_id = pr.usuario_id
                WHERE c.inquilino_id = :id
                ORDER BY c.created_at DESC";
        $stmt = Database::conexion()->prepare($sql);
        $stmt->execute([':id' => $inquilinoId]);
        return $stmt->fetchAll();
    }
}
