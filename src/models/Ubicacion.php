<?php

/**
 * Modelo Ubicacion: lee países, departamentos y ciudades.
 */
class Ubicacion
{
    /**
     * Devuelve toda la jerarquía para alimentar los selects en cascada del formulario.
     * @return array{paises:array,departamentos:array,ciudades:array}
     */
    public static function paraFormulario(): array
    {
        $pdo = Database::conexion();
        return [
            'paises'        => $pdo->query("SELECT pais_id AS id, nombre FROM paises ORDER BY nombre")->fetchAll(),
            'departamentos' => $pdo->query("SELECT departamento_id AS id, pais_id, nombre FROM departamentos ORDER BY nombre")->fetchAll(),
            'ciudades'      => $pdo->query("SELECT ciudad_id AS id, departamento_id, nombre FROM ciudades ORDER BY nombre")->fetchAll(),
        ];
    }

    /** ¿Existe la ciudad con ese ID? */
    public static function ciudadExiste(int $id): bool
    {
        $stmt = Database::conexion()->prepare("SELECT 1 FROM ciudades WHERE ciudad_id = :id");
        $stmt->execute([':id' => $id]);
        return (bool) $stmt->fetchColumn();
    }

    /**
     * Nombres de ciudad y departamento de una ciudad. Útil para geocodificar.
     * @return array{ciudad:string,departamento:string}|null
     */
    public static function nombresPorCiudad(int $id): ?array
    {
        $sql = "SELECT c.nombre AS ciudad, dep.nombre AS departamento
                FROM ciudades c
                JOIN departamentos dep ON c.departamento_id = dep.departamento_id
                WHERE c.ciudad_id = :id";
        $stmt = Database::conexion()->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }
}
