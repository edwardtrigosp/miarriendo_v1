<?php

/**
 * Modelo Direccion: inserta direcciones (tabla `direcciones`).
 */
class Direccion
{
    /** Crea una dirección y devuelve su ID. */
    public static function crear(array $d): int
    {
        $pdo = Database::conexion();
        $sql = "INSERT INTO direcciones
                    (ciudad_id, calle, numero_exterior, barrio, codigo_postal, referencia, latitud, longitud)
                VALUES
                    (:ciudad_id, :calle, :numero_exterior, :barrio, :codigo_postal, :referencia, :latitud, :longitud)";
        $pdo->prepare($sql)->execute([
            ':ciudad_id'       => $d['ciudad_id'],
            ':calle'           => $d['calle'],
            ':numero_exterior' => $d['numero_exterior'] ?? null,
            ':barrio'          => $d['barrio'] ?? null,
            ':codigo_postal'   => $d['codigo_postal'] ?? null,
            ':referencia'      => $d['referencia'] ?? null,
            ':latitud'         => $d['latitud'] ?? null,
            ':longitud'        => $d['longitud'] ?? null,
        ]);
        return (int) $pdo->lastInsertId();
    }
}
