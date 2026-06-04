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
}
