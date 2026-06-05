<?php

/**
 * Modelo ImagenPropiedad: tabla `imagenes_propiedades` (galería de fotos).
 */
class ImagenPropiedad
{
    /** Inserta una imagen y devuelve su ID. */
    public static function crear(array $d): int
    {
        $pdo = Database::conexion();
        $sql = "INSERT INTO imagenes_propiedades
                    (propiedad_id, url_imagen, descripcion, orden, es_principal)
                VALUES
                    (:propiedad_id, :url_imagen, :descripcion, :orden, :es_principal)";
        $pdo->prepare($sql)->execute([
            ':propiedad_id' => $d['propiedad_id'],
            ':url_imagen'   => $d['url_imagen'],
            ':descripcion'  => $d['descripcion'] ?? null,
            ':orden'        => $d['orden'],
            ':es_principal' => $d['es_principal'],
        ]);
        return (int) $pdo->lastInsertId();
    }

    /** Devuelve las imágenes de una propiedad, la principal primero. */
    public static function porPropiedad(int $propiedadId): array
    {
        $stmt = Database::conexion()->prepare(
            "SELECT * FROM imagenes_propiedades
              WHERE propiedad_id = :id
              ORDER BY es_principal DESC, orden ASC"
        );
        $stmt->execute([':id' => $propiedadId]);
        return $stmt->fetchAll();
    }

    /** Busca una imagen por su ID. */
    public static function buscarPorId(int $id): ?array
    {
        $stmt = Database::conexion()->prepare(
            "SELECT * FROM imagenes_propiedades WHERE imagen_id = :id"
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    /** Elimina el registro de una imagen. */
    public static function eliminar(int $id): void
    {
        Database::conexion()->prepare(
            "DELETE FROM imagenes_propiedades WHERE imagen_id = :id"
        )->execute([':id' => $id]);
    }

    /** Cuenta las imágenes de una propiedad. */
    public static function contarPorPropiedad(int $propiedadId): int
    {
        $stmt = Database::conexion()->prepare(
            "SELECT COUNT(*) FROM imagenes_propiedades WHERE propiedad_id = :id"
        );
        $stmt->execute([':id' => $propiedadId]);
        return (int) $stmt->fetchColumn();
    }

    /** Mayor valor de 'orden' usado (para añadir nuevas al final). */
    public static function maxOrden(int $propiedadId): int
    {
        $stmt = Database::conexion()->prepare(
            "SELECT COALESCE(MAX(orden), -1) FROM imagenes_propiedades WHERE propiedad_id = :id"
        );
        $stmt->execute([':id' => $propiedadId]);
        return (int) $stmt->fetchColumn();
    }

    /** Si la propiedad no tiene portada, marca como principal la de menor orden. */
    public static function asegurarPrincipal(int $propiedadId): void
    {
        $pdo = Database::conexion();
        $hay = $pdo->prepare("SELECT 1 FROM imagenes_propiedades WHERE propiedad_id = :id AND es_principal = 1 LIMIT 1");
        $hay->execute([':id' => $propiedadId]);
        if ($hay->fetchColumn()) {
            return;
        }
        $prim = $pdo->prepare("SELECT imagen_id FROM imagenes_propiedades WHERE propiedad_id = :id ORDER BY orden ASC LIMIT 1");
        $prim->execute([':id' => $propiedadId]);
        $idImg = $prim->fetchColumn();
        if ($idImg) {
            $pdo->prepare("UPDATE imagenes_propiedades SET es_principal = 1 WHERE imagen_id = :i")
                ->execute([':i' => $idImg]);
        }
    }
}
