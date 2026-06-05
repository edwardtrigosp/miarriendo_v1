<?php

/**
 * Modelo Usuario: acceso a la tabla `usuarios`.
 * Todas las consultas usan sentencias preparadas (anti SQL injection).
 */
class Usuario
{
    /** Crea un usuario y devuelve su ID. La contraseña debe venir YA hasheada. */
    public static function crear(array $datos): int
    {
        $pdo = Database::conexion();
        $sql = "INSERT INTO usuarios (nombre, apellidos, email, contrasena, telefono)
                VALUES (:nombre, :apellidos, :email, :contrasena, :telefono)";
        $pdo->prepare($sql)->execute([
            ':nombre'     => $datos['nombre'],
            ':apellidos'  => $datos['apellidos'],
            ':email'      => $datos['email'],
            ':contrasena' => $datos['contrasena'],
            ':telefono'   => ($datos['telefono'] ?? '') !== '' ? $datos['telefono'] : null,
        ]);
        return (int) $pdo->lastInsertId();
    }

    /** Busca un usuario por su correo. Devuelve el array o null. */
    public static function buscarPorEmail(string $email): ?array
    {
        $stmt = Database::conexion()->prepare(
            "SELECT * FROM usuarios WHERE email = :email LIMIT 1"
        );
        $stmt->execute([':email' => $email]);
        return $stmt->fetch() ?: null;
    }

    /** Busca un usuario por su ID. Devuelve el array o null. */
    public static function buscarPorId(int $id): ?array
    {
        $stmt = Database::conexion()->prepare(
            "SELECT * FROM usuarios WHERE usuario_id = :id LIMIT 1"
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    /** ¿Ya existe un usuario con ese correo? */
    public static function emailExiste(string $email): bool
    {
        $stmt = Database::conexion()->prepare(
            "SELECT 1 FROM usuarios WHERE email = :email LIMIT 1"
        );
        $stmt->execute([':email' => $email]);
        return (bool) $stmt->fetchColumn();
    }

    /** Actualiza los datos básicos del perfil. */
    public static function actualizar(int $id, array $d): void
    {
        $sql = "UPDATE usuarios
                   SET nombre = :nombre, apellidos = :apellidos, email = :email, telefono = :telefono
                 WHERE usuario_id = :id";
        Database::conexion()->prepare($sql)->execute([
            ':nombre'    => $d['nombre'],
            ':apellidos' => $d['apellidos'],
            ':email'     => $d['email'],
            ':telefono'  => ($d['telefono'] ?? '') !== '' ? $d['telefono'] : null,
            ':id'        => $id,
        ]);
    }

    /** Actualiza la ruta de la foto de perfil (o null para quitarla). */
    public static function actualizarFoto(int $id, ?string $url): void
    {
        $stmt = Database::conexion()->prepare(
            "UPDATE usuarios SET foto_url = :url WHERE usuario_id = :id"
        );
        $stmt->execute([':url' => $url, ':id' => $id]);
    }

    /** ¿El correo lo usa OTRO usuario distinto de $excluyeId? */
    public static function emailEnUsoPorOtro(string $email, int $excluyeId): bool
    {
        $stmt = Database::conexion()->prepare(
            "SELECT 1 FROM usuarios WHERE email = :email AND usuario_id <> :id LIMIT 1"
        );
        $stmt->execute([':email' => $email, ':id' => $excluyeId]);
        return (bool) $stmt->fetchColumn();
    }

    /** Registra el momento del último acceso. */
    public static function actualizarUltimoAcceso(int $id): void
    {
        $stmt = Database::conexion()->prepare(
            "UPDATE usuarios SET ultimo_acceso = NOW() WHERE usuario_id = :id"
        );
        $stmt->execute([':id' => $id]);
    }
}
